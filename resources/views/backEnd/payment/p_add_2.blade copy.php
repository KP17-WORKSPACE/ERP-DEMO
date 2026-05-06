<?php try { ?>




<input type="hidden" id="currency1" value="{{ $currency1 }}" />
<input type="hidden" id="currency2" value="{{ $currency2 }}" />

{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-create-form']) }}
{{-- @endif --}}
<input type="hidden" id="receipt_process_id" name="process_id" value="{{ Auth::user()->id . date('YmdHis') }}">
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">

<input type="hidden" name="cheque_id" id="cheque_id" value="0">


<?php
    //$invno_cash=@App\SysHelper::get_new_maxid_2('sys_payment','cash','id');
    //$invno_bank=@App\SysHelper::get_new_maxid_2('sys_payment','bank','id');

    $invno_cash = @App\SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
    $invno_bank = @App\SysHelper::get_new_code_err('sys_payment', 'BP', 'doc_number');
    
    ?>

<style>
    #plist .checkbox-row {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 2px 0;
    }

    #plist .checkbox-row input[type="checkbox"] {
        margin: 0;
    }

    #plist .checkbox-row label {
        margin: 0;
    }
</style>
<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        <span class="font-weight-600" id="doc_number_display_cash" style="display: none">New ({{ $invno_cash }})</span>
        <span class="font-weight-600" id="doc_number_display_bank">New ({{ $invno_bank }})</span>
        <span class="font-weight-600" id="doc_number_display_stl" style="display: none">New ({{
            @App\SysHelper::get_new_code('sys_stl','STL','doc_number') }})</span>


    </h4>
    <div class="purchase-order-content-header-right">
        <!-- <button type="button" class="btn btn-light add-cheque-btn" onclick="popup_model()" id="add_cheque_btn">
                <i class="ico icon-outline-banknote text-success"></i> Print Cheque
            </button> -->

        <button type="submit" class="btn btn-light">
            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
        </button>


    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row gap-rows">
            <div class="col">
                <label class="form-label">Mode</label>
                <div class="form-group">
                    <select class="form-control" name="mode" id="mode" required>
                        <option value="1">Cash</option>
                        <option value="2" selected>Bank</option>
                        <option value="3">STL</option>
                    </select>
                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>


                </div>
            </div>
            <div class="col mb-4" id="div_payment_through">

                <label>@lang('Payment Through')<span>*</span></label>

                <div class="form-group">
                    <select class="form-control" name="payment_through" id="payment_through">
                        <option value="3">Cheque</option>
                        <option value="1">Bank Transfer</option>
                        {{-- <option value="2">CDC Cheque</option> --}}
                    </select>
                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                </div>

            </div>
            <div class="col">
                <label class="form-label">Doc Number</label>
                <div class="form-group">

                    <input type="hidden" id="cash_doc_number" value="{{ $invno_cash }}" />
                    <input type="hidden" id="bank_doc_number" value="{{ $invno_bank }}" />
                    <input type="hidden" id="stl_doc_number"
                        value="{{ @App\SysHelper::get_new_code('sys_stl','STL','doc_number') }}" />
                    <input class="form-control" style="display: none" type="text" id="doc_number_cash" name="doc_number"
                        value="{{ $invno_cash }}" readonly>

                    <input class="form-control" type="text" id="doc_number_bank" name="doc_number"
                        value="{{ $invno_bank }}" readonly>

                    <input class="form-control" style="display: none" type="text" id="doc_number_stl" name="doc_number"
                        value="{{ @App\SysHelper::get_new_code('sys_stl','STL','doc_number') }}" readonly>
                </div>
            </div>

            <script>
                // delegated handler — works when element is added/hidden dynamically
                $(document).on('change', '#mode', function () {
                    var mode = $(this).val();
                    if (mode == 1) {
                        $('#payment_mode_cash').prop('required', true);
                        $('#payment_mode_bank').prop('required', false);
                        $('#payment_mode_cash').css("display", "block");
                        $('#payment_mode_bank').css("display", "none");
                        $('#div_payment_through').css("display", "none");

                        $('#bill_wise_heading').text('@lang("Cash Amount")');



                        $('#div_payment_mode').css('display', '');
                        $('#div_currency').css('display', '');
                        $('.stl_div').css('display', 'none');
                        $('#extra-fields').css('display', '');
                        $('.table-container').css('display', '');
                        $('#div_payment_date').css('display', '');
                        $('#div_deal_id').css('display', '');
                        $('#div_remarks').css('display', '');
                        $('#doc_number_stl').css('display', 'none');
                        $('#doc_number_display_stl').css('display', 'none');

                        $('#div_cheque_date').css("display", "none");
                        $('#div_cheque_number').css("display", "none");

                        // hide table columns related to cheque details
                        $('.cheque_date_col').css('display', 'none');
                        $('.cheque_number_col').css('display', 'none');
                        $('.status_col').css('display', 'none');
                        $('.no_days_col').css('display', 'none');


                        $('#div_payment_days').css("display", "none");
                        $('#div_chequebook').css("display", "none");
                        $('#div_cheque_status').css("display", "none");
                        $('#cheque_number').prop('required', false).val('');
                        $('#payment_days').prop('required', false).val('');
                        $('#cheque_date').prop('required', false).val('');
                        $('#addCheque').css('display', 'none');

                        $('#doc_number_bank').css('display', 'none');
                        $('#doc_number_cash').css('display', 'block');

                        $('#doc_number_display_cash').css('display', 'block');
                        $('#doc_number_display_bank').css('display', 'none');

                        $('#doc_number').val($('#cash_doc_number').val()).trigger('change');
                        $('#btn_submit').text('Add Cash Payment');
                    } else if (mode == 2) {
                        $('#payment_mode_cash').prop('required', false);
                        $('#payment_mode_bank').prop('required', true);
                        $('#payment_mode_cash').css("display", "none");
                        $('#payment_mode_bank').css("display", "block");
                        $('#div_payment_through').css("display", "");
                        $('#add_cheque_btn').show();
                        $('.stl_div').css('display', 'none');
                        $('#extra-fields').css('display', '');
                        $('.table-container').css('display', '');
                        $('#div_payment_date').css('display', '');
                        $('#div_deal_id').css('display', '');
                        $('#div_remarks').css('display', '');
                        $('#doc_number_stl').css('display', 'none');
                        $('#doc_number_display_stl').css('display', 'none');

                        $('#bill_wise_heading').text('@lang("Cheque Amount")');



                        $('#div_cheque_date').css("display", "");
                        $('#div_cheque_number').css("display", "");


                            // show table columns related to cheque details
                        $('.cheque_date_col').css('display', '');
                        $('.cheque_number_col').css('display', '');
                        $('.status_col').css('display', '');
                        $('.no_days_col').css('display', '');

                        $('#div_chequebook').css("display", "");
                        $('#div_cheque_status').css("display", "");
                        $('#div_payment_days').css("display", "");
                        $('#cheque_number').prop('required', true);
                        $('#payment_days').prop('required', true);
                        $('#cheque_date').prop('required', true);
                        $('#addCheque').css('display', '');

                        $('#doc_number_bank').css('display', 'block');
                        $('#doc_number_cash').css('display', 'none');
                        $('#doc_number_display_cash').css('display', 'none');
                        $('#doc_number_display_bank').css('display', 'block');

                        $('#doc_number').val($('#bank_doc_number').val()).trigger('change');
                        // ensure payment_through handler runs after mode change so cheque fields are correct
                        $('#payment_through').trigger('change');
                        $('#btn_submit').text('Add Bank Payment');
                    } else if (mode == 3) {
                        // STL mode — hide payment-specific fields, show STL div
                        $('#div_payment_mode').css('display', 'none');
                        $('#payment_mode_cash').prop('required', false);
                        $('#payment_mode_bank').prop('required', false);
                        $('#payment_mode_cash').css('display', 'none');
                        $('#payment_mode_bank').css('display', 'none');
                        $('#div_payment_through').css('display', 'none');
                        $('#div_cheque_date').css('display', 'none');
                        $('#div_cheque_number').css('display', 'none');
                        $('#div_payment_days').css('display', 'none');
                        $('#div_chequebook').css('display', 'none');
                        $('#div_cheque_status').css('display', 'none');
                        $('#cheque_number').prop('required', false).val('');
                        $('#payment_days').prop('required', false).val('');
                        $('#cheque_date').prop('required', false).val(''); $('#addCheque').css('display', 'none');
                        $('#add_cheque_btn').hide();

                        // Hide table columns related to cheque details
                        $('.cheque_date_col').css('display', 'none');
                        $('.cheque_number_col').css('display', 'none');
                        $('.status_col').css('display', 'none');
                        $('.no_days_col').css('display', 'none');

                        // Hide payment date, deal id, remarks, currency
                        $('#div_payment_date').css('display', 'none');
                        $('#div_deal_id').css('display', 'none');
                        $('#div_remarks').css('display', 'none');
                        $('#div_currency').css('display', 'none');

                        // Make STL fields not required
                        $('#bank').prop('required', false);
                        $('#exchange_rate').prop('required', false);
                        $('#amount_usd').prop('required', false);
                        $('#amount_aed').prop('required', false);
                        $('#vendor').prop('required', false);
                        $('#payment_type').prop('required', false);
                        $('#pi_no').prop('required', false);
                        $('#submition_date').prop('required', false);
                        $('#narration').prop('required', false);

                        // Show STL doc number, hide cash/bank
                        $('#doc_number_bank').css('display', 'none');
                        $('#doc_number_cash').css('display', 'none');
                        $('#doc_number_stl').css('display', 'block');
                        $('#doc_number_display_cash').css('display', 'none');
                        $('#doc_number_display_bank').css('display', 'none');
                        $('#doc_number_display_stl').css('display', 'block');
                        $('#doc_number').val($('#stl_doc_number').val()).trigger('change');

                        // Show STL fields div, hide extra-fields tab
                        $('.stl_div').css('display', '');
                        $('#extra-fields').css('display', 'none');
                        $('.table-container').css('display', 'none');

                        $('#bill_wise_heading').text('@lang("STL Amount")');
                        $('#btn_submit').text('Add STL');
                    }
                });

                // ensure UI is correct on initial load
                $(document).ready(function () {
                    $('#mode').trigger('change');
                });
            </script>

            <div class="col" id="div_payment_mode">
                <label class="form-label">Payment Mode</label>
                <div class="form-group">
                    <select class="form-control" name="payment_mode_cash" id="payment_mode_cash" style="display: none;">
                        @if (isset($paymentmode_cash))
                            @foreach ($paymentmode_cash as $val)
                                <option value="{{ @$val->id }}" @if (isset($editData)) @if (@$editData->payment_mode == @$val->id)
                                selected @endif @endif>{{ @$val->account_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <select class="form-control" name="payment_mode_bank" id="payment_mode_bank" required>
                        @if (isset($paymentmode_bank))
                            @foreach ($paymentmode_bank as $val)
                                <option value="{{ @$val->id }}" @if (isset($editData)) @if (@$editData->payment_mode == @$val->id)
                                selected @endif @endif>{{ @$val->account_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                </div>
            </div>





            <div class="col">
                <label class="form-label">Doc Date:</label>
                <div class="form-group">
                    @php
                        if (isset($editData) && !empty($editData->doc_date)) {
                            $value = date('d/m/Y', strtotime($editData->doc_date));
                        } elseif (!empty(old('doc_date'))) {
                            $value = date('d/m/Y', strtotime(old('doc_date')));
                        } else {
                            $value = \Carbon\Carbon::now()->format('d/m/Y');
                        }
                    @endphp

                    <input class="form-control date-picker" id="doc_date" type="text" name="doc_date"
                        value="{{ @$value }}">
                </div>
            </div>
            @php
                $bank = @App\SysHelper::get_stl_bank_account();
            @endphp

            <div class="col-lg-2 mb-2 stl_div" style="display: none;">
                <label class="form-label">@lang('Bank') <span>*</span></label>
                <select class="form-control js-example-basic-single" name="bank" id="bank" onchange="set_rate()">
                    <option value=""></option>
                    @foreach ($bank as $value)
                        <option value="{{ @$value->id }}">
                            {{ @$value->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <script>
                function set_rate() {

                    var bank = $('#bank').val();
                    if (bank == 7996) { //RAK BANK
                        $('#exchange_rate').val('3.674');
                    } else {
                        $('#exchange_rate').val('3.675');
                    }
                    $('#amount_usd').val('');
                    $('#amount_aed').val('');
                }
                function set_amount_usd() {
                    var rate = $('#exchange_rate').val();
                    var usd = $('#amount_usd').val();
                    var aed = $('#amount_aed').val();
                    if (usd != "" || usd != "0" || usd != "0.00") {
                        //$('#amount_aed').val(usd*rate);
                        $('#amount_aed').val(formatAmount(usd * rate));
                    }
                    $('#amount_usd').val(formatAmount(usd));
                }
                function set_amount_aed() {
                    var rate = $('#exchange_rate').val();
                    var usd = $('#amount_usd').val();
                    var aed = $('#amount_aed').val();
                    if (aed != "" || aed != "0" || aed != "0.00") {
                        //$('#amount_usd').val(aed/rate);
                        $('#amount_usd').val(formatAmount(aed / rate));
                    }
                    $('#amount_aed').val(formatAmount(aed));
                }
            </script>

            <div class="col mb-2 stl_div" style="display: none;">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="form-label">Syscom Representative</label>
                            <input class="form-control" id="owner_name" type="text" autocomplete="off" name="owner_name"
                                value="Hamidudin Kutbuddin Ansari" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col" id="div_currency">
                <label class="form-label">Currency</label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="currency">
                        @foreach ($currency as $value)
                            <option value="{{ @$value->id }}" @if ($company->currency_id == $value->id) selected @endif>
                                {{ @$value->code }}
                            </option>
                        @endforeach
                    </select>


                </div>
            </div>
            <div class="col">
                <label class="form-label">Created By</label>
                <div class="form-group">
                    <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by"
                        value="{{ Auth::user()->full_name }}" readonly>
                </div>
            </div>

        </div>
    </div>
</div>



<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields"
                type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
        </li>
    </ul>
    <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
        <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
            <div class="row gap-rows">


                <div class="col-12 mb-2">
                    <div class="row gap-rows">


                   
                        <!-- <div class="col-1-5 mb-4" id="div_payment_days">
                            <label>@lang('No of Days')<span>*</span></label>
                            <input class="form-control" type="number" name="payment_days" id="payment_days"
                                onchange="days_fun()">
                            <script>
                                function days_fun() {
                                    var daysToAdd = parseInt($('#payment_days').val());

                                    if (isNaN(daysToAdd) || daysToAdd <= 0) {
                                        alert("Please enter a valid positive number of days.");
                                        return;
                                    }

                                    // Start from today
                                    var currentDate = new Date();
                                    currentDate.setDate(currentDate.getDate() + daysToAdd);

                                    // Format as dd/mm/yyyy
                                    var day = ("0" + currentDate.getDate()).slice(-2);
                                    var month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
                                    var year = currentDate.getFullYear();

                                    var formattedDate = day + "/" + month + "/" + year;

                                    // Set into inputs
                                    $('#cheque_date').val(formattedDate);
                                    $('#payment_date').val(formattedDate);
                                }
                            </script>
                        </div>
                        <div class="col-1-5 mb-4" id="div_cheque_date">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label>@lang('Cheque Date')</label>
                                        @php
                                            $value = \Carbon\Carbon::now()->format('d/m/Y');
                                        @endphp
                                        <input class="form-control date-picker" id="cheque_date" type="text"
                                            name="cheque_date" value="{{ @$value }}">
                                        <script>
                                            $('#cheque_date').on('change', function () {
                                                $('#payment_date').val($('#cheque_date').val());
                                                $('#payment_date').focus();
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-2 mb-4" id="div_cheque_number">
                            <div class="input-effect">
                                <label> @lang('Cheque Number') <span>*</span> </label>

                                <input class="form-control" type="text" id="cheque_number" name="cheque_number"
                                    value="{{ isset($editData) ? @$editData->cheque_number : old('cheque_number') }}">
                            </div>
                        </div>

                        <div class="col-2 mb-4" id="div_cheque_status">
                            <div class="input-effect">
                                <label> @lang('Status') <span>*</span> </label>
                                <select class="form-control js-example-basic-single" name="cheque_status"
                                    id="cheque_status">
                                    <option value="4">Issued</option>
                                    <option value="2">Cleared</option>
                                    <option value="1">Cancelled</option>
                                    <option value="3">Missed</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-1-5 mb-4" id="div_payment_date">
                            <div class="input-effect">
                                <label> @lang('Payment Date') <span>*</span> </label>
                                @php
                                    $value = \Carbon\Carbon::now()->format('d/m/Y');
                                @endphp
                                <input class="form-control date-picker" type="text" id="payment_date"
                                    name="payment_date" value="{{ $value }}" required>
                            </div>
                        </div>

                        <div class="col-1-5 mb-4" id="div_deal_id">
                            <div class="input-effect">
                                <label>@lang('Deal ID')<span>*</span></label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="Without Deal">
                            </div>
                        </div> -->
                        <!-- <div class="col-12 mb-4" id="div_remarks">
                            <div class="input-effect">
                                <label>@lang('Remarks') <span></span></label>
                                <input class="form-control" type="text" name="narration" autocomplete="off"
                                    value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                    id="narration">
                                <input type="hidden" id="narration_1" />
                                <input type="hidden" id="narration_2" />
                                <input type="hidden" id="narration_row_id" />
                                <input type="hidden" id="narration_actual" />
                            </div>
                        </div> -->
                        <script>
                            // ── Narration ────────────────────────────────────────────
                            function generate_narration() {
                                var gn_mode = $('#mode').val();
                                if (gn_mode == 1) {
                                    $('#narration_1').val('Paid Cash');
                                    var n1 = $('#narration_1').val();
                                    var n2 = $('#narration_2').val();
                                    $('#narration').val(n1 + ' ' + n2);
                                    $('#narration_actual').val(n1 + ' ' + n2);
                                }
                                if (gn_mode == 2) {
                                    var gn_bank_name = $("#payment_mode_bank option:selected").text();
                                    $('#narration_1').val('Paid From ' + gn_bank_name);
                                    var n1 = $('#narration_1').val();
                                    var n2 = $('#narration_2').val();
                                    $('#narration').val(n1 + ' ' + n2);
                                    $('#narration_actual').val(n1 + ' ' + n2);
                                }
                            }

                            function generate_narration_fa() {
                                var remarksArr = $('input[name="remarks[]"]').map(function () {
                                    var val = $.trim($(this).val());
                                    return val ? val : null;
                                }).get();
                                if (remarksArr.length === 0) return;
                                var gn_remarks = remarksArr.join(' | ');
                                var n1 = $('#narration_actual').val();
                                $('#narration').val(n1 + ' ' + gn_remarks);
                            }

                            // ── Chequebook helpers ────────────────────────────────────
                            function resetChequeNumber() {
                                $('#cheque_number').empty().append('<option value="">Select Cheque Book First</option>');
                            }

                            function fetchChequeBooksByBank(bankId) {
                                $('#chequebook').empty().append('<option value="">Loading...</option>');
                                resetChequeNumber();
                                $('#cheque_id').val('0');

                                if (!bankId) {
                                    $('#chequebook').empty().append('<option value="">Select Bank First</option>');
                                    return;
                                }

                                $.ajax({
                                    url: '{{ url("api/chequebooks-by-bank") }}/' + bankId,
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function (response) {
                                        $('#chequebook').empty();
                                        if (response.success && response.data && response.data.length > 0) {
                                            $('#chequebook').append('<option value="">Select Cheque Book</option>');
                                            $.each(response.data, function (index, book) {
                                                var displayText = book.doc_number + ' (' + book.start_no + ' - ' + book.end_no + ')';
                                                $('<option>')
                                                    .val(book.id)
                                                    .text(displayText)
                                                    .attr('data-start', book.start_no)
                                                    .attr('data-end', book.end_no)
                                                    .appendTo('#chequebook');
                                            });
                                        } else {
                                            $('#chequebook').append('<option value="">No Cheque Books Available</option>');
                                        }
                                    },
                                    error: function () {
                                        $('#chequebook').empty().append('<option value="">Error Loading Cheque Books</option>');
                                    }
                                });
                            }

                            // ── Document Ready ────────────────────────────────────────
                            $(document).ready(function () {
                                // Initial narration
                                generate_narration();

                                // Load cheque books if a bank is already selected (e.g. edit mode)
                                var initialBankId = $('#payment_mode_bank').val();
                                if (initialBankId) {
                                    fetchChequeBooksByBank(initialBankId);
                                }

                                // Narration: re-generate on mode / bank / payment_through change
                                $(document).on('change', '#mode', function () {
                                    generate_narration();
                                });
                                $(document).on('change', '#payment_mode_bank', function () {
                                    generate_narration();
                                    fetchChequeBooksByBank($(this).val());
                                });
                                $(document).on('change', '#payment_through', function () {
                                    generate_narration();
                                });

                                function fetchUsedChequeNumbers(chequebookId) {
                                    return $.ajax({
                                        url: '{{ url("api/chequebook-used-numbers") }}/' + chequebookId,
                                        type: 'GET',
                                        dataType: 'json'
                                    });
                                }

                                // Chequebook selected → populate cheque number range
                                $(document).on('change', '#chequebook', function () {
                                    var chequebookId = $(this).val();
                                    $('#cheque_id').val(chequebookId || '0');

                                    var $selected = $(this).find('option:selected');
                                    var startNoRaw = $selected.attr('data-start');
                                    var endNoRaw = $selected.attr('data-end');

                                    var $chequeNum = $('#cheque_number');
                                    $chequeNum.empty();

                                    if (!chequebookId || !startNoRaw || !endNoRaw) {
                                        $chequeNum.append('<option value="">Select Cheque Book First</option>');
                                        return;
                                    }

                                    // BigInt for large cheque numbers
                                    var startNo, endNo;
                                    try {
                                        startNo = BigInt(startNoRaw);
                                        endNo = BigInt(endNoRaw);
                                    } catch (e) {
                                        startNo = BigInt(parseInt(startNoRaw, 10));
                                        endNo = BigInt(parseInt(endNoRaw, 10));
                                    }

                                    if (endNo < startNo) {
                                        $chequeNum.append('<option value="">Invalid cheque range</option>');
                                        return;
                                    }

                                    var totalOptions = Number(endNo - startNo + 1n);
                                    var maxOptions = 5000;

                                    if (totalOptions > maxOptions) {
                                        $chequeNum.append('<option value="">Range too big (' + totalOptions + ' entries)</option>');
                                        return;
                                    }

                                    $chequeNum.append('<option value="">-- Select Cheque No --</option>');

                                    fetchUsedChequeNumbers(chequebookId).done(function (resp) {
                                        var usedCheques = (resp.success && Array.isArray(resp.used)) ? resp.used : [];
                                        var existing = '{{ isset($editData) ? @$editData->cheque_number : "" }}';

                                        // batch DOM insertion for performance
                                        var fragment = document.createDocumentFragment();
                                        for (var i = startNo; i <= endNo; i += 1n) {
                                            var chequeNo = i.toString();

                                            // On edit, allow currently selected cheque number even if used by this record
                                            if (existing && chequeNo === existing) {
                                                var opt = document.createElement('option');
                                                opt.value = chequeNo;
                                                opt.text = chequeNo;
                                                fragment.appendChild(opt);
                                                continue;
                                            }

                                            if (usedCheques.includes(chequeNo)) {
                                                continue; // skip already used cheque numbers
                                            }

                                            var opt = document.createElement('option');
                                            opt.value = chequeNo;
                                            opt.text = chequeNo;
                                            fragment.appendChild(opt);
                                        }

                                        if (!fragment.hasChildNodes()) {
                                            $chequeNum.html('<option value="">No available cheque numbers</option>');
                                            return;
                                        }

                                        $chequeNum.get(0).appendChild(fragment);

                                        if (existing) {
                                            $chequeNum.val(existing);
                                        }
                                    }).fail(function () {
                                        $chequeNum.append('<option value="">Unable to check used cheque numbers</option>');
                                    });
                                });
                            });
                        </script>


                        @if (session('logged_session_data.company_id') == 6)
                            <script>
                                $('#mode').val(2);
                                $('#mode').change();
                                $('#payment_mode_bank').val(8064);
                            </script>
                        @endif





                    </div>
                </div>
            </div>
        </div>


        <div class="stl_div" style="display: none;padding:15px">
            @php
                $currency = @App\SysCurrencySettings::select('id', 'code')->get();
                $currency_code = $currency->where('id', $company->currency_id)->first();
                $currency_code = $currency_code->code;
                $vendor = @App\SysHelper::get_stl_supplier_list($company_id);
                $bank = @App\SysHelper::get_stl_bank_account();
                $r = @App\SysHelper::get_data_by_role();
                $company_id = $r[0];
                $stl = @App\SysSTL::where('company_id', $company_id)->orderby('id', 'desc')->get();
                $company = @App\SysCompany::where('id', session('logged_session_data.company_id'))->first();
                $product = DB::table('sm_items as items')->select('items.id', 'items.part_number', 'items.description', 'cat.category_name as cat_name')
                    ->join('sm_item_categories as cat', 'cat.id', 'items.category_name')->where('items.status', 1)->orderby('items.part_number', 'asc')->get();
            @endphp

            <div class="row gap-rows">
                <div class="col-lg-10">
                    <div class="row row-cols-5">





                        <div class="col mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Ex Rate</label>
                                        <input class="form-control" id="exchange_rate" type="number" step="Any"
                                            autocomplete="off" name="exchange_rate" value=""
                                            onchange="set_amount_usd()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency_m" id="currency_m">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}" @if(5 == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <script>
                            function get_currency_code() {
                                $('#amt_txt').text('Amount in ' + $('#currency :selected').text());
                            }
                        </script>
                        <div class="col mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Amount</label>
                                        <input class="form-control" id="amount_usd" type="text" autocomplete="off"
                                            name="amount_usd" value="" onchange="set_amount_usd()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Currency</label>
                                <select class="form-control js-example-basic-single" name="currency_stl" id="currency"
                                    onchange="get_currency_code()">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}" @if($company->currency_id == $value->id) selected
                                        @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label" id="amt_txt">Amount in {{ $currency_code }}</label>
                                        <input class="form-control" id="amount_aed" type="text" autocomplete="off"
                                            name="amount_aed" value="" onchange="set_amount_aed()">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Bank Representative</label>
                                        <input class="form-control" id="bank_representative" type="text"
                                            autocomplete="off" name="bank_representative" value="Philemon George"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label">@lang('Vendor Name') <span>*</span></label>
                            <select class="form-control js-example-basic-single" name="vendor" id="vendor">
                                <option value=""></option>
                                @foreach ($vendor as $value)
                                    <option value="{{ @$value->id }}">
                                        {{ @$value->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label">@lang('Payment Type') <span>*</span></label>
                            <select class="form-control js-example-basic-single" name="payment_type" id="payment_type"
                                onchange="if_partial()">
                                <option value=""></option>
                                <option value="Partial">Partial</option>
                                <option value="Full">Full</option>
                            </select>
                        </div>
                        <script>
                            function if_partial() {
                                if ($('#payment_type').val() == "Partial") {
                                    $('#div_partial_remarks').css('display', ''); $('#partial_remarks').prop('required', true);
                                    var usd = $('#amount_usd').val();
                                    var vend = $('#vendor :selected').text();
                                    var curr = $('#currency :selected').text();
                                    var rem_text = "Special Instruction:\n\
•	Mudaraba Financing is required for "+ curr.trim() + " " + usd.trim() + "\n\
•	Balance amount we will pay from our own sources.\n\
TT Value to "+ vend.trim() + " to be " + curr.trim() + " " + usd.trim() + " as per below details";
                                    $('#partial_remarks').val(rem_text);

                                } else {
                                    $('#div_partial_remarks').css('display', 'none'); $('#partial_remarks').prop('required', false);
                                    var rem_text = "";
                                    $('#partial_remarks').val(rem_text);
                                }
                            }
                        </script>
                        <div class="col mb-2">
                            <label class="form-label">@lang('PI / PI / PO') <span>*</span></label>
                            <select class="form-control js-example-basic-single" name="pi_no" id="pi_no"
                                onchange="get_pending_list()">
                                <option value=""></option>
                                <option value="1">Purchase Invoice</option>
                                <option value="2">Proforma Invoice</option>
                                <option value="3">Purchase Order</option>
                            </select>
                        </div>
                        <div class="col mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Submission Date</label>
                                        <input class="form-control date-picker" id="submition_date" type="text"
                                            autocomplete="off" name="submition_date" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-2" id="div_partial_remarks" style="display: none;">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Remarks (Partial Payment)</label>
                                        <textarea class="form-control" id="partial_remarks" rows="5" autocomplete="off"
                                            name="partial_remarks">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label">@lang('With / Without Amount') <span>*</span></label>
                            <select class="form-control" name="with_amount" id="with_amount">
                                <option value="0">Without Amount</option>
                                <option value="1">With Amount</option>
                            </select>
                        </div>
                        <div class="col-auto mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">Narration</label>
                                        <input class="form-control" id="narration" type="text" autocomplete="off"
                                            name="narration_stl" value="">
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Pending list</label>
                        <div id="plist"
                            style="width: 100%; height: 140px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                        </div>
                        <a data-bs-toggle="modal" data-bs-target="#pi_pending_popup_win" id="addPIPendingSTL"
                            data-toggle="modal"></a>
                        <a data-bs-toggle="modal" data-bs-target="#po_pending_popup_win" id="addPOPendingSTL"
                            data-toggle="modal"></a>
                        <input type="hidden" id="pi_id" name="pi_id">
                        <input type="hidden" id="po_id" name="po_id">
                    </div>
                </div>
            </div>





            <div class="card mb-3">
                <div class="card-body">
                    <div class="row gap-rows">


                        <div class="col-lg-12"><b>List of <label id="list_name"></label></b>
                            <hr />
                        </div>
                    </div>
                    <div class="row gap-rows">
                        <div class="col-lg-12">
                            <div class="table-striped" id="ptable">

                            </div>
                        </div>
                    </div>
                    <div class="row gap-rows mt-3">

                        <div class="col-lg-12">
                            <table class="table-striped" style="width: 350px;">
                                <tr>
                                    <td style="width: 130px;">&nbsp;Value of Hardware</td>
                                    <td class="text-end"><label class="mt-1" id="total_hardware">0.00</label></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;Value of Licence</td>
                                    <td class="text-end"><label class="mt-1" id="total_license">0.00</label></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;<b>Total</b></td>
                                    <td class="text-end"><label class="mt-1 font-weight-bold" id="total_hl">0.00</label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

            <script>
                function get_pending_list() {

                    document.getElementById('total_hardware').textContent = '0.00';
                    document.getElementById('total_license').textContent = '0.00';
                    document.getElementById('total_hl').textContent = '0.00';

                    if ($('#pi_no').val() == 2) {
                        $("#plist").empty();
                        $("#ptable").empty();
                        $('#btn_performa_invoice_modal').click();
                        $("#list_name").text('Proforma Invoice');
                        return false;
                    }
                    if ($('#pi_no').val() == 1) {
                        get_pi_list();
                        $("#list_name").text('Purchase Invoice');
                    }
                    if ($('#pi_no').val() == 3) {
                        get_po_list();
                        $("#list_name").text('Purchase Order');
                    }
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
                        success: function (dataResult) {
                            var dataResult = JSON.parse(dataResult);
                            var len = 0;
                            var len = 0;
                            if (dataResult['data'] != null) {
                                len = dataResult['data'].length;
                            }
                            if (len > 0) {
                                $("#plist").empty();
                                for (var i = 0; i < len; i++) {
                                    var id = dataResult['data'][i].id;
                                    var doc_number = dataResult['data'][i].doc_number;
                                    var bill_number = dataResult['data'][i].bill_number;
                                    var option = "<option value='" + id + "'>" + doc_number + "</option>";
                                    var innerHtml =
                                        "<div class='checkbox-row'>" +
                                        "<input type='checkbox' onclick='popup_pi_pending(" + id + ")' id='pending_pi_" + i +
                                        "' name='pending_pi' value='" + doc_number + "'>" +
                                        "<label class='truncate-text' for='pending_pi_" + i + "'> " + doc_number + ' - ' + bill_number + "</label>" +
                                        "</div>";

                                    var innerTable =
                                        "<table  id='table_id_stl_" + id + "' class='table table-hover form-item-table' cellspacing='0' width='100%' style='display:none; border: solid 1px #f2f2f2;'>\
                                        <thead><tr><th class='mt-2'></th><th><span id='table_id_stl_docno_"+ id + "'></span></th><th><span id='table_id_stl_billno_" + id + "'></span>&nbsp;|&nbsp;<span id='table_id_stl_awbno_" + id + "'></span>&nbsp;|&nbsp;<span id='table_id_stl_boeno_" + id + "'></span></th><th></th><th class='text-center'><a class='btn-sm btn-light' onclick='deleteTable(this)'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a></th></tr>\
                                                <tr><th style='width: 50px;'>Sr. No</th>\
                                                <th style='width: 250px;'>Item Part Number</th>\
                                                <th>Description of Goods</th>\
                                                <th style='width: 150px;' class='text-end'>Amount</th>\
                                                <th style='width: 150px;' class='text-center'>&nbsp;&nbsp;&nbsp;&nbsp; Action</th></tr></thead><tbody></tbody>\
                                                <tfoot><tr><th></th><th></th><th></th><th class='text-end'><span id='table_id_total_"+ id + "'></span></th><th></th></tr></tfoot></table>";

                                    $("#plist").append(innerHtml);
                                    $("#ptable").append(innerTable);
                                }
                            }
                            else {
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
                        success: function (dataResult) {
                            var dataResult = JSON.parse(dataResult);
                            var len = 0;
                            var len = 0;
                            if (dataResult['data'] != null) {
                                len = dataResult['data'].length;
                            }
                            if (len > 0) {
                                $("#plist").empty();
                                for (var i = 0; i < len; i++) {
                                    var id = dataResult['data'][i].id;
                                    var doc_number = dataResult['data'][i].doc_number;
                                    var bill_number = dataResult['data'][i].bill_number;
                                    var option = "<option value='" + id + "'>" + doc_number + "</option>";
                                    var innerHtml =
                                        "<div class='checkbox-row'>" +
                                        "<input type='checkbox' onclick='popup_po_pending(" + id + ")' id='pending_po_" + i +
                                        "' name='pending_po' value='" + doc_number + "'>" +
                                        "<label for='pending_po_" + i + "'> " + doc_number + "</label>" +
                                        "</div>";

                                    var innerTable =
                                        "<table  id='po_table_id_stl_" + id + "' class='table table-hover form-item-table' cellspacing='0' width='100%' style='display:none; border: solid 1px #f2f2f2;'>\
                                        <thead><tr><th class='mt-2'></th><th><span id='table_id_stl_docno_"+ id + "'></span></th><th><span id='table_id_stl_billno_" + id + "'></span>&nbsp;|&nbsp;<span id='table_id_stl_awbno_" + id + "'></span>&nbsp;|&nbsp;<span id='table_id_stl_boeno_" + id + "'></span></th><th></th><th class='text-center'>&nbsp;&nbsp;&nbsp;&nbsp;<a class='btn-sm btn-light' onclick='deleteTable(this)'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a></th></tr>\
                                                <tr><th style='width: 50px;'>Sr. No</th>\
                                                <th style='width: 250px;'>Item Part Number</th>\
                                                <th>Description of Goods</th>\
                                                <th style='width: 150px;' class='text-end'>Amount</th>\
                                                <th style='width: 150px;' class='text-center'>&nbsp;&nbsp;&nbsp;&nbsp;Action</th></tr></thead><tbody></tbody>\
                                                <tfoot><tr><th></th><th></th><th></th><th class='text-end'><span id='table_id_total_"+ id + "'></span></th><th></th></tr></tfoot></table>";

                                    $("#plist").append(innerHtml);
                                    $("#ptable").append(innerTable);
                                }
                            }
                            else {
                                $("#plist").empty();
                            }
                            $('#po_pending_popup_win').modal('hide');
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

                $(document).ready(function () {
                    // Handle click event for edit/save toggle button
                    $('body').on('click', '.edit-btn', function () {
                        var $row = $(this).closest('tr');
                        var $inputs = $row.find('input[type="text"], input[type="number"]');

                        if ($(this).hasClass('is-saving')) {
                            // Save state (back to edit icon)
                            $inputs.prop('readonly', true);
                            $(this).removeClass('is-saving btn-success').addClass('btn-info').html('<i class="ico icon-outline-pen-2 text-success" style="font-size: 16px;"></i>');
                        } else {
                            // Edit state (show save icon)
                            $inputs.not(':first').prop('readonly', false);
                            $inputs.first().attr('onclick', 'get_item(this)');
                            $(this).addClass('is-saving btn-success').removeClass('btn-info').html('<i class="ico icon-outline-bookmark-opened text-success" style="font-size: 16px;"></i>');
                        }
                    });

                    // Disable misuse of generic button class for row save action
                    $('body').on('click', '.btn-light', function (e) {
                        // preserve action for global Save/other buttons, not row edit control
                        if ($(this).closest('tr').length && $(this).hasClass('edit-btn')) {
                            e.preventDefault();
                            return false;
                        }
                    });

                    // Handle click event for delete button (optional, already provided)
                    $('body').on('click', '.delete-btn', function () {
                        // Show confirmation popup
                        var confirmed = confirm("Are you sure you want to delete this row?");

                        if (confirmed) {
                            // If confirmed, remove the row
                            $(this).closest('tr').remove();
                        }
                    });
                });

            </script>



            <form id="pi">
                <div class="modal side-panel fade" id="pi_pending_popup_win" data-bs-backdrop="false" tabindex="-1"
                    aria-labelledby="addPIPendingSTL" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="height: 250px !important;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Purchase Invoice Pending List</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body m-0 p-0">
                                <input type="hidden" id="hd_pending_pi_id" />
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="equipment comon-status row mt-40 d-block">
                                                <table id="table_id" class="table table-hover form-item-table"
                                                    cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 25px;">@lang('#') </th>
                                                            <th style="width: 250px;">@lang('Item Part Number')</th>
                                                            <th>@lang('Description of Goods')</th>
                                                            <th class="text-end" style="width: 150px;">@lang('Amount')
                                                            </th>
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

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light add-btn ms-2"
                                            id="addPIPendingSTLItems">
                                            <i class="ico icon-outline-bookmark-opened text-success"></i> Add Selected
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form id="po">
                <div class="modal side-panel fade" id="po_pending_popup_win" data-bs-backdrop="false" tabindex="-1"
                    aria-labelledby="addPOPendingSTL" aria-hidden="true">
                    <div class="modal-dialog modal-lg" style="height: 250px !important;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Purchase Order Pending List</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body m-0 p-0">
                                <input type="hidden" id="hd_pending_po_id" />
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="equipment comon-status row mt-40 d-block">
                                                <table id="po_table_id" class="table table-hover form-item-table"
                                                    cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 25px;">@lang('#') </th>
                                                            <th style="width: 250px;">@lang('Item Part Number')</th>
                                                            <th>@lang('Description of Goods')</th>
                                                            <th class="text-end" style="width: 150px;">@lang('Amount')
                                                            </th>
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

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light add-btn ms-2"
                                            id="addPOPendingSTLItems">
                                            <i class="ico icon-outline-bookmark-opened text-success"></i> Add Selected
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <button data-bs-toggle="modal" data-bs-target="#PerformaInvoiceModal" id="btn_performa_invoice_modal"
                data-toggle="modal" hidden></button>
            <div class="modal side-panel fade" id="PerformaInvoiceModal" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="addPOPendingSTL" aria-hidden="true">
                <div class="modal-dialog modal-lg" style="height: 250px !important;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Proforma Invoice</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-3 pt-2">Invoice Count</div>
                                <div class="col-lg-9">
                                    <input class="form-control" id="invoice_count" type="number" autocomplete="off"
                                        name="invoice_count" value="" onchange="set_invoice_count()">
                                    <br />
                                    <div id="invoice_boxes"></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" onclick="addInvoices()">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                            </button>
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

                function addInvoices() {
                    var invoiceCount = document.getElementById("invoice_count").value;
                    var invoiceInputs = document.querySelectorAll('input[name^="invoiceno_"]');
                    var invoiceNumbers = [];
                    invoiceInputs.forEach(function (input) {
                        if (input.value.trim() !== "") {
                            invoiceNumbers.push(input.value.trim());
                        }
                    });
                    var invoiceValues = invoiceNumbers.join(',');
                    console.log(invoiceValues);
                    var invoiceNumbers = invoiceValues.split(',');

                    var tableBody = document.getElementById("ptable");

                    tableBody.innerHTML = "";

                    for (var i = 0; i < invoiceCount; i++) {
                        if (i < invoiceNumbers.length) {
                            var invoice = invoiceNumbers[i].trim();
                            var invoiceNo = i;
                            var ctrl_id = invoiceNo + '_' + i;
                            var p_c = "cl_" + invoiceNo;
                            var rowHTML = "<table width='100%' id='profoma_table_" + invoiceNo + "' style='border: solid 1px #f2f2f2;'>\
                        <thead><tr><td></td><td><input type='text' class='form-control' name='purchase_inv[]' value='"+ invoice + "'></td><td></td><td><a class='btn-sm' style='color: #fff; background-color: #1cc88a; border-color: #1cc88a;' onclick='open_import(" + invoiceNo + ")'>Import</a></td><td class='text-center'><a class='btn-sm btn-light' onclick='deleteTable(this)'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a></td></tr>\
                        <tr><th style='width: 50px;'>Sr. No</th>\
                                                <th style='width: 250px;'>Item Part Number</th>\
                                                <th>Description of Goods</th>\
                                                <th style='width: 150px;' class='text-end'>Amount</th>\
                                                <th style='width: 150px;' class='text-center'>&nbsp;&nbsp;&nbsp;Action</th></tr></thead>\
                                                <tbody><tr><td></td>\
                                    <td><input class='form-control' type='text' name='part_number[]' onclick='get_item(this)' placeholder='Part Number'><input type='hidden' name='partno[]'><input type='hidden' id='pi_inv_no_" + invoiceNo + "' name='pi_inv_no[]' value='" + invoice + "'/><input type='hidden' name='awbno[]' value=''/><input type='hidden' name='boeno[]' value=''/></td>\
                                    <td><input class='form-control' type='text' name='description[]' placeholder='Description of Goods'></td>\
                                    <td><input class='form-control text-end "+ p_c + "' type='number' name='amount[]' placeholder='Amount' onchange='set_total2(" + invoiceNo + ")'></td>\
                                    <td><a class='btn-sm btn-light edit-btn'><i class='ico icon-outline-pen-2 text-success' style='font-size: 16px;'></i></a>\
                                        <a class='btn-sm btn-light delete-btn' onclick='deleteRow(this)'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a>\
                                        <button type='button' class='btn-sm btn-primary' onclick='addRow(this)'>+</button>\
                                    </td>\
                                </tr></tbody><tfoot><tr><th></th><th></th><th></th><th class='text-end'><span id='table_id_total_"+ invoiceNo + "'></span></th></tr></tfoot></table><br />";
                            tableBody.innerHTML += rowHTML;
                        }
                    }

                    $('#closeInvoices').click();
                    $('#PerformaInvoiceModal').modal('hide');
                }

                function initDatePickers(scope) {
                    scope = scope || document;

                    if (typeof flatpickr !== 'undefined') {
                        $(scope).find('.date-picker').each(function () {
                            if (!this._flatpickr) {
                                flatpickr(this, {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true
                                });
                            }
                        });
                    } else if (typeof $.fn.datepicker !== 'undefined') {
                        $(scope).find('.date-picker').not('.hasDatepicker').datepicker({
                            dateFormat: 'dd/mm/yy',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '1900:2100'
                        });
                    }
                }

                function addRow(button) {
                    var currentRow = button.closest('tr');
                    var tableBody = currentRow.parentNode;
                    var newRow = currentRow.cloneNode(true);
                    var inputs = newRow.querySelectorAll('input');
                    inputs.forEach(function (input) {
                        if (input.name !== 'pi_inv_no[]') {
                            input.value = '';
                        }
                    });
                    var elementsToRemoveClass = newRow.querySelectorAll('.license, .networking');
                    elementsToRemoveClass.forEach(function (element) {
                        element.classList.remove('license');
                        element.classList.remove('networking');
                    });

                    // Re-initialize datepicker for cloned date fields
                    initDatePickers(newRow);

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
                        $(btn).closest('table').remove();
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

                    var description = $('#part_no_des_' + id).val();
                    if (description.toLowerCase().includes('license'.toLowerCase())) {
                        description = "Networking license ";
                        clicked_amount_input.addClass('license');
                    }
                    else if (description.toLowerCase().includes('licence'.toLowerCase())) {
                        description = "Networking License ";
                        clicked_amount_input.addClass('license');
                    } else {
                        description = "Networking " + $('#part_no_cat_' + id).val();
                        clicked_amount_input.addClass('networking');
                    }

                    clicked_part_number_input.val($('#part_number_' + id).val());
                    clicked_description_input.val(description);
                    clicked_pno_input.val($('#part_no').val());

                    $('#productlistModal').modal('hide');
                }


                function set_total() {
                    let license_amounts = document.querySelectorAll('.license');
                    let networking_amounts = document.querySelectorAll('.networking');
                    let license_total = 0;
                    let networking_total = 0;

                    license_amounts.forEach(function (input) {
                        // Remove commas and convert to float
                        let value = input.value.replace(/,/g, '');
                        license_total += parseFloat(value) || 0;
                    });

                    networking_amounts.forEach(function (input) {
                        // Remove commas and convert to float
                        let value = input.value.replace(/,/g, '');
                        networking_total += parseFloat(value) || 0;
                    });

                    let decimalPoint = @json(session('logged_session_data.decimal_point'));
                    document.getElementById('total_hardware').textContent = formatAmount(networking_total.toFixed(decimalPoint));
                    document.getElementById('total_license').textContent = formatAmount(license_total.toFixed(decimalPoint));
                    document.getElementById('total_hl').textContent = formatAmount((networking_total + license_total).toFixed(decimalPoint));
                }
                function set_total2(id) {

                    let amts = document.querySelectorAll('.cl_' + id);
                    let amt_total = 0;
                    amts.forEach(function (input) {
                        // Remove commas and convert to float
                        let value = input.value.replace(/,/g, '');
                        amt_total += parseFloat(value) || 0;
                    });
                    let decimalPoint = @json(session('logged_session_data.decimal_point'));
                    document.getElementById('table_id_total_' + id).textContent = formatAmount(amt_total.toFixed(decimalPoint));

                    let license_amounts = document.querySelectorAll('.license');
                    let networking_amounts = document.querySelectorAll('.networking');
                    let license_total = 0;
                    let networking_total = 0;

                    license_amounts.forEach(function (input) {
                        // Remove commas and convert to float
                        let value = input.value.replace(/,/g, '');
                        license_total += parseFloat(value) || 0;
                    });

                    networking_amounts.forEach(function (input) {
                        // Remove commas and convert to float
                        let value = input.value.replace(/,/g, '');
                        networking_total += parseFloat(value) || 0;
                    });

                    document.getElementById('total_hardware').textContent = networking_total.toFixed(@json(session('logged_session_data.decimal_point')));
                    document.getElementById('total_license').textContent = license_total.toFixed(@json(session('logged_session_data.decimal_point')));
                    document.getElementById('total_hl').textContent = (networking_total + license_total).toFixed(@json(session('logged_session_data.decimal_point')));
                }



            </script>


            <a data-toggle="modal" id="btn_product_list_modal" data-target="#productlistModal" data-toggle="modal"
                data-backdrop="static" data-keyboard="false"></a>


            <div class="modal fade bd-example-modal-lg" id="productlistModal" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <input type="hidden" id="part_number_{{ $pr->id }}"
                                            value="{{ $pr->part_number }}" />
                                        <input type="hidden" id="part_no_cat_{{ $pr->id }}" value="{{ $pr->cat_name }}" />
                                        <input type="hidden" id="part_no_des_{{ $pr->id }}"
                                            value="{{ $pr->description }}" />

                                        @if (str_contains(strtolower($pr->description), 'license'))
                                            <input type="hidden"
                                                id="dyna_part_no_des_{{ strtolower(preg_replace('/[^a-z0-9]/i', '', trim($pr->part_number))) }}"
                                                value="Networking License" />
                                        @elseif (str_contains(strtolower($pr->description), 'licence'))
                                            <input type="hidden"
                                                id="dyna_part_no_des_{{ strtolower(preg_replace('/[^a-z0-9]/i', '', trim($pr->part_number))) }}"
                                                value="Networking License" />
                                        @else
                                            <input type="hidden"
                                                id="dyna_part_no_des_{{ strtolower(preg_replace('/[^a-z0-9]/i', '', trim($pr->part_number))) }}"
                                                value="Networking {{ $pr->cat_name }}" />
                                        @endif

                                    @endforeach



                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="closeInvoices"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="add_get_item()">Add</button>
                        </div>
                    </div>
                </div>
            </div>



            <a data-toggle="modal" id="btn_import_modal" data-target="#importModal" data-toggle="modal"
                data-backdrop="static" data-keyboard="false"></a>
            <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
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
                                    <input type="file" id="excel-file" /> (<a
                                        href="{{ url('public/uploads/product_upload/profoma_invoice_import_sample.xlsx') }}"
                                        target="_blank">Sample File</a>)
                                    <input type="hidden" id="profoma_id" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="closeimport"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>



            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

            <script>
                function open_import(id) {
                    $("#profoma_id").val(id);
                    $('#btn_import_modal').click();
                }

                $(document).ready(function () {
                    $("#excel-file").change(function (e) {
                        var file = e.target.files[0];

                        if (file && file.name.endsWith(".xlsx")) {
                            var reader = new FileReader();

                            reader.onload = function (event) {
                                var data = event.target.result;

                                // Use SheetJS to read the Excel file
                                var workbook = XLSX.read(data, { type: 'array' });

                                // Get the first sheet
                                var sheet = workbook.Sheets[workbook.SheetNames[0]];

                                // Convert the sheet to a JSON object (array of rows)
                                var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                                var id = $("#profoma_id").val();
                                // Populate the table with the rows
                                var tableBody = $("#profoma_table_" + id + " tbody");
                                tableBody.empty(); // Clear any existing rows

                                for (var i = 1; i < jsonData.length; i++) { // Skip the header row
                                    var rowData = jsonData[i];

                                    var dyna_des = $('#dyna_part_no_des_' + rowData[0].trim().toLowerCase().replace(/\s+/g, '').replace(/[^a-z0-9]/g, '')).val();
                                    //alert(dyna_des);
                                    //alert(rowData[0].trim().toLowerCase());

                                    var row = $("<tr></tr>");

                                    // Part Number (Cell 1)
                                    row.append('<td>' + i + '</td>');
                                    row.append('<td><input class="form-control" type="text" name="part_number[]" value="' + rowData[0] + '" onclick="get_item(this)" placeholder="Part Number"><input type="hidden" value="0" name="partno[]"></td>');

                                    // Description (Cell 2)
                                    row.append('<td><input class="form-control" type="text" name="description[]" value="' + dyna_des + '" placeholder="Description of Goods"></td>');

                                    // Amount (Cell 3)
                                    row.append('<td><input class="form-control text-end cl_' + (i - 1) + '" type="text" name="amount[]" value="' + formatAmount(rowData[1]) + '" placeholder="Amount" onchange="set_total2(' + id + ')"></td>');

                                    // Actions (Cell 4)
                                    row.append('<td><a class="btn-sm btn-light edit-btn"><i class="ico icon-outline-pen-2 text-success" style="font-size: 16px;"></i></a><a class="btn-sm btn-light delete-btn" onclick="deleteRow(this)"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></a><button type="button" class="btn-sm btn-primary" onclick="addRow(this)">+</button></td>');

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

        </div>



    </div>

    <div class="table-container" style="border: solid 1px #d9d9d9;">
        <table class="table table-hover form-item-table" id="myTable">
            <thead>
                <tr>
                    <th class="resizable text-center" width="50px">@lang('No')
                        <div class="resizer">
                        </div>
                    </th>
                    <th class="resizable text-center" width="250px">@lang('Account Name')
                        <div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="150px">@lang('Amount')
                        <div class="resizer">
                        </div>
                    </th>

                    <th class="resizable text-center no_days_col" width="80px">@lang('No of Days')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center cheque_date_col" width="150px">@lang('Cheque Date')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center cheque_number_col" width="150px">@lang('Cheque Number')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center status_col" width="150px">@lang('Status')
                        <div class="resizer"></div>
                    </th>



                    <th class="resizable text-center" width="150px">@lang('Payment Date')
                        <div class="resizer"></div>
                    </th>

                    <th class="resizable text-center" width="150px">@lang('Deal ID')
                        <div class="resizer"></div>
                    </th>



                    <th class="resizable text-center" width="250px">@lang('Narration')
                        <div class="resizer"></div>
                    </th>



                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control text-center" name="sort_id[]" value="1" /></td>
                    <td class="noborder">
                        <select class="form-control" name="account_id[]">
                            <option value=""></option>
                        </select>
                    </td>
                    <td>
                        <input class="form-control text-end" type="decimal" name="amount[]" autocomplete="off"
                            onchange="update_totals()" onblur="formatCurrency(this)">
                    </td>


                    <td class="no_days_col"><input type="text" class="form-control text-center " name="no_of_days_grid[]"></td>
                    <td class="cheque_date_col"><input type="text" class="form-control date-picker text-center" name="cheque_date_grid[]"></td>
                    <td class="cheque_number_col"><input type="text" class="form-control text-center" name="cheque_number_grid[]"></td>
                    <td class="status_col">
                        <select class="form-control" name="status_grid[]">
                            <option value=""></option>
                            <option value="4">Issued</option>
                            <option value="2">Cleared</option>
                            <option value="1">Cancelled</option>
                            <option value="3">Missed</option>
                        </select>
                    </td>

                    <td><input type="text" class="form-control date-picker text-center" name="payment_date_grid[]"></td>
                    <td><input type="text" class="form-control text-center" name="deal_id_grid[]"></td>
                    <td><input type="text" class="form-control" name="remarks[]"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" scope="col">Total</th>
                    <th class="text-end"><label id="lbl_total_amount">0</label></th>
                    <th class="text-end" scope="col"></th>
                    <th class="text-end" scope="col"></th>
                    <th class="text-end" scope="col"></th>
                    <th class="text-end no_days_col" scope="col"></th>
                    <th class="text-end no_days_col" scope="col"></th>
                    <th class="text-end no_days_col" scope="col"></th>
                    <th class="text-end no_days_col" scope="col"></th>
                    <th class="text-end no_days_col" scope="col"></th>
                </tr>
            </tfoot>
        </table>
        <div id="contextMenu">
            <button type="button" id="addRow">Add Row</button>
            <button type="button" id="deleteRow">Delete Row</button>
        </div>
    </div>
    {{ Form::close() }}



    <?php    if (isset($cheque_detail)) { ?>
    <script>
        $('#cheque_id').val({{ $cheque_detail->id }});
        $('#mode').val(2);
        $('#mode').change();
        $('#payment_mode_bank').val({{ $cheque_detail->bank_name }});
        $('#payment_through').val(3);
        $('#payment_through').change();
        $('#cheque_date').val(
            '{{ $cheque_detail->cheque_date ? \Carbon\Carbon::parse($cheque_detail->cheque_date)->format('d/m/Y') : '' }}'
        );
        $('#payment_date').val(
            '{{ $cheque_detail->cheque_date ? \Carbon\Carbon::parse($cheque_detail->cheque_date)->format('d/m/Y') : '' }}'
        );

        $('#cheque_number').val('{{ $cheque_detail->cheque_number }}');
        $('#deal_id').val('{{ $cheque_detail->deal_code->code }}');
        $('#account_id_1').val({{ $cheque_detail->supplier_name }});
        $('#amount_1').val({{ $cheque_detail->amount }});
    </script>
    <?php    } ?>

    <button type="button" data-bs-toggle="modal" data-bs-target="#cr_popup_win" id="addCtrlPaymentAdjest"
        hidden></button>
    <form id="ta">
        <div class="modal side-panel fade" id="cr_popup_win" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">Bill Wise Selection</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <input type="hidden" id="br_account_id">
                    <input type="hidden" id="br_account_id_amount">
                    <div class="card-body">

                        <div class="row">
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label id="bill_wise_heading"> @lang('Cash Amount') <span>*</span> </label>
                                    <input class="primary-input form-control text-end" type="text" id="bi_cheque_amount"
                                        name="bi_cheque_amount" value="0">
                                    <span class="focus-border"></span>
                                    <!-- <span class="modal_input_validation_2 red_alert"></span> -->
                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label> @lang('Amount Adjusted') <span>*</span> </label>
                                    <input class="primary-input form-control text-end" type="text"
                                        id="bi_amount_adjusted" name="bi_amount_adjusted" value="0">
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>

                                    <input type="hidden" id="bi_balance_adjest" value="">

                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label> @lang('Balance to Adjust') <span>*</span> </label>
                                    <input class="primary-input form-control text-end" type="text" id="bi_extra_amount"
                                        name="bi_extra_amount" value="0">
                                    <div style="display: none;">
                                        <input class="primary-input form-control" type="text" id="bi_balance_to_adjust"
                                            name="bi_balance_to_adjust" value="0">
                                    </div>
                                    <span class="focus-border"></span>
                                    <!-- <span class="modal_input_validation_2 red_alert"></span> -->
                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label> @lang('Search in table') </label>
                                    <input class="primary-input form-control" type="text" id="tableSearchBill"
                                        name="tableSearchBill" value="">
                                </div>
                            </div>
                        </div>




                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table class="table table-hover form-item-table data-table-bill" cellspacing="0"
                                        width="100%" id="crListBankBookAdjest">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;" class="text-start">&nbsp; @lang('Deal ID')</th>
                                                <th style="width:100px;" class="text-start">&nbsp; @lang('Doc No')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                                <th style="width:100px;" class="text-center">@lang('LPO NO')</th>
                                                <th style="width:100px;" class="text-center">@lang('Bill NO')</th>
                                                <th style="width:100px;" class="text-center">@lang('Total')</th>
                                                <th style="width:100px;" class="text-center">@lang('Paid')</th>
                                                <th style="width:100px;" class="text-center">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-center">@lang('Adjustment')</th>
                                                <th style="width:100px;" class="text-center">@lang('Narration')</th>
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
                            </div>
                        </div>

                        <script>

                            function get_set_amount(id) {
                                id = id || null;

                                var form_amt = Number($('#bi_cheque_amount').val().replace(/,/g, '')) || 0;

                                // sum of all adjustment inputs entered by the user
                                var adjusted_sum = 0;
                                $(".tot_amt").each(function () {
                                    var v = Number($(this).val().replace(/,/g, '')) || 0;
                                    adjusted_sum += v;
                                });

                                // update adjusted totals and remaining balance (do NOT overwrite user inputs)
                                $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));

                                // allow negative remaining so UI shows over-allocation (adjusted > cheque)
                                var remaining = (form_amt - adjusted_sum);
                                $('#bi_balance_adjest').val(formatAmount(remaining));
                                // visible balance the UI expects
                                $('#bi_extra_amount').val(formatAmount(remaining));
                                // keep hidden field in sync for server/AJAX payloads
                                $('#bi_balance_to_adjust').val(formatAmount(remaining));

                                // visual indicator when over-allocated
                                if (remaining < 0) {
                                    $('#bi_extra_amount, #bi_balance_adjest').addClass('text-danger').attr('title', 'Over-allocated: adjusted &gt; cheque amount');
                                } else {
                                    $('#bi_extra_amount, #bi_balance_adjest').removeClass('text-danger').removeAttr('title');
                                    // hide inline modal validation hint if previously shown
                                    $('.modal_input_validation_2').hide();
                                }

                                // realtime validation disabled per UX request — show errors only when user saves (validateBankBookAdjestForm handles this)
                                // (do not alter user-entered values here)

                                // Footer total (reflects user-entered values)
                                var num_tot_amt = $('.tot_amt').length;
                                var total = 0;
                                for (var i = 1; i <= num_tot_amt; i++) {
                                    var v = Number($('#bi_amount_' + i).val().replace(/,/g, '')) || 0;
                                    total += v;
                                }
                                $('#footer_adjustment').text(formatAmount(total));

                                // Remarks (document numbers) — unchanged
                                var docs = [];
                                for (var i = 1; i <= num_tot_amt; i++) {
                                    var val = Number($('#bi_amount_' + i).val().replace(/,/g, '')) || 0;
                                    if (val > 0) {
                                        docs.push($('#bi_doc_no_' + i).val().replace(/,/g, ''));
                                    }
                                }

                                var re_id = $('#narration_row_id').val();
                                if (re_id) {
                                    $('#remarks_' + re_id).val(docs.join(', '));
                                }
                            }

                            // re-run calculations when user edits any adjustment input
                            $(document).on('input change', '.tot_amt', function () {
                                var idMatch = $(this).attr('id') ? $(this).attr('id').match(/(\d+)$/) : null;
                                if (idMatch) {
                                    get_set_amount(idMatch[1]);
                                } else {
                                    get_set_amount();
                                }
                            });

                            // recalc when cheque amount or adjusted total are edited (or set programmatically)
                            $(document).on('input change', '#bi_cheque_amount, #bi_amount_adjusted', function () {
                                get_set_amount();
                            });

                            // when user focuses or clicks an adjustment field, preload the maximum allowable amount
                            // but do not exceed the remaining cheque balance (so resulting bi_balance_to_adjust becomes 0)
                            $(document).on('focus click', '.tot_amt', function () {
                                var idMatch = $(this).attr('id') ? $(this).attr('id').match(/(\d+)$/) : null;
                                if (idMatch) {
                                    var idx = idMatch[1];
                                    var bal = Number($('#bi_balance_' + idx).val().replace(/,/g, '')) || 0;
                                    var rem = Number($('#bi_extra_amount').val().replace(/,/g, '')) || 0; // remaining cheque amount
                                    var curVal = Number($(this).val().replace(/,/g, '')) || 0;
                                    // choose lesser of row balance and remaining amount
                                    var toFill = Math.min(bal, rem);
                                    if (curVal === 0 && toFill > 0) {
                                        $(this).val(formatAmount(toFill));
                                        get_set_amount(idx);
                                    }
                                }
                            });
                        </script>





                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light add-btn ms-2" type="submit"
                            onclick="validateAttachForm()">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                        </button>
                        <script>
                            function popup_form_submit() {
                                $("#loading_bg").css("display", "block");
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        // Run cr_popup_fun only once (no repeated bindings)
        $(document).on('keypress', 'input[name="amount[]"]', function (e) {
            if (e.which === 13) {
                $("#loading_bg").css("display", "");
                const currentInput = $(this);
                const currentRow = currentInput.closest('tr');

                $('#narration_row_id').val(currentRow.index());

                let br_account_id = currentRow.find('select[name="account_id[]"]').val();
                const br_amount = currentRow.find('input[name="amount[]"]').val();
                if (br_account_id !== "" && br_amount !== "") {
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_amount);
                    $('#bi_cheque_amount').val(formatAmount(br_amount)).focus();
                    // update dependent fields immediately so "Balance to Adjust" shows correct value
                    get_set_amount();
                    $('#addCtrlPaymentAdjest').click().prop("disabled", true);
                } else {
                    alert("Account / Amount Missing");
                }
                $("#loading_bg").css("display", "none");
                return false; // prevent default behavior in this case
            }
        });

        // Prevent form submission on Enter for all fields EXCEPT amount[]
        $('#payment-create-form').on('keypress', function (e) {
            if (e.which === 13 && !$(e.target).is('input[name="amount[]"]')) {
                e.preventDefault();
                return false;
            }
        });

        function validateAttachForm() {
            $("#loading_bg").css("display", "block");
            var numRows = $('.row_ctrl').length;
            var natt_txt = "";
            for (i = 1; i <= numRows; i++) {
                // perform validation on rows that have an adjustment amount
                if ($('#bi_amount_' + i).length && $("#bi_amount_" + i).val() != "" && $("#bi_amount_" + i).val() != 0) {
                    var ok = validateBankBookAdjestForm(i);
                    if (ok === false) {
                        $("#loading_bg").css("display", "none");
                        return false;
                    }
                }
                var lpo = $('#bi_lpo_no_' + i);
                var nar = $('#bi_narration_' + i);
                var invo = $('#bi_doc_no_' + i);
                var amt = $('#bi_amount_' + i).val();

                // only collect description for rows where an amount was entered
                if (lpo.length && nar.length && invo.length && amt && amt != 0) {
                    if (natt_txt == "") {
                        natt_txt += invo.val() + " (" + lpo.val() + ") " + nar.val();
                    } else {
                        natt_txt += ", " + invo.val() + " (" + lpo.val() + ") " + nar.val();
                    }
                }
            }

            // collect deal codes only from rows where an adjustment amount was entered
            var deal_codes = [];
            // start with any value already present so we append rather than overwrite
            var existing = $('#deal_id').val();
            if (existing) {
                existing.split(',').forEach(function (c) {
                    c = $.trim(c);
                    if (c) deal_codes.push(c);
                });
            }
            $('[id^="bi_deal_code_"]').each(function () {
                var idxMatch = $(this).attr('id').match(/_(\d+)$/);
                if (!idxMatch) return; // unexpected
                var idx = idxMatch[1];
                var amt = $('#bi_amount_' + idx).val();
                if (amt && amt != 0) {
                    var v = $.trim($(this).val());
                    if (v) deal_codes.push(v);
                }
            });
            // fallback to numeric ids if still empty
            if (deal_codes.length === 0) {
                $('[id^="bi_deal_id_"]').each(function () {
                    var idxMatch = $(this).attr('id').match(/_(\d+)$/);
                    if (!idxMatch) return;
                    var idx = idxMatch[1];
                    var amt = $('#bi_amount_' + idx).val();
                    if (amt && amt != 0) {
                        var v = $.trim($(this).val());
                        if (v) deal_codes.push(v);
                    }
                });
            }
            var unique_deal_codes = [...new Set(deal_codes)];
            // remove placeholder entries like 'Without Deal'
            unique_deal_codes = unique_deal_codes.filter(function (c) {
                return c.toLowerCase() !== 'without deal';
            });
            if (unique_deal_codes.length) {
                $('#deal_id').val(unique_deal_codes.join(', '));
            }

            // all rows validated => proceed with add
            alert("Added!!");

            $('input[name="remarks[]"]').eq($('#narration_row_id').val()).val(natt_txt);
            generate_narration_fa();

            $("#cr_popup_win").hide();
            $("#loading_bg").css("display", "none");
            return true;
        }

        function delete_before_update() {
            var doc_number = $("#doc_number").val();
            var account_id = $('#br_account_id').val();
            var url = $('#url').val();
            $.ajax({
                url: url + '/' + 'payables-outstanding-store-temp-delete',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    doc_number: doc_number,
                    account_id: account_id,

                },
                cache: false,
                success: function (response) {
                    var response = JSON.parse(response);
                    var len = 0;
                    if (response['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        //$("#btn_close2").click();
                        //$("#addCtrlBankBookAdjest").click();
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) { }
            });
        }

        function BankBookAdjestBalance(id) {
            var bi_total = $('#bi_total_' + id).val();
            var bi_paid = $('#bi_paid_' + id).val();
            var tot = (parseFloat(bi_total) - parseFloat(bi_paid)).toFixed(@json(session('logged_session_data.decimal_point')));
            $('#bi_balance_' + id).val(tot);

            // do NOT autofill adjustment input — show suggested value as placeholder instead
            if ($('#bi_amount_' + id).length) {
                $('#bi_amount_' + id).attr('placeholder', formatAmount(bi_paid));
            }
        }

        function validateBankBookAdjestForm(id) {
            var val1 = $("#bi_cheque_amount").val();
            var val2 = $("#bi_amount_adjusted").val();
            var val3 = $("#bi_extra_amount").val();
            var val4 = $("#bi_balance_to_adjust").val();


            var bi_doc_no = $('#bi_doc_no_' + id).val();
            var bi_doc_date = $('#bi_doc_date_' + id).val();
            var bi_lpo_no = $('#bi_lpo_no_' + id).val();
            var bi_bill_number = $('#bi_bill_number_' + id).val();
            var bi_total = $('#bi_total_' + id).val();
            var bi_paid = $('#bi_paid_' + id).val();
            var bi_balance = $('#bi_balance_' + id).val();
            var bi_amount = $('#bi_amount_' + id).val();
            var bi_narration = $('#bi_narration_' + id).val();
            var account_id = $('#br_account_id').val();
            var entry_date = $('#doc_date').val();
            var bi_currency = $('#currency').val();

            if ($('#mode').val() == 1) {
                transaction_type = 'cashreceipt';
            } else {
                transaction_type = 'bankreceipt';
            }
            var entry_type = 2; //1 Debit, 2 Credit
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

            // Prevent adjusted total from exceeding cheque amount
            var numericCheque = Number(String(val1).replace(/,/g, '')) || 0;
            var numericAdjusted = Number(String(val2).replace(/,/g, '')) || 0;
            if (numericAdjusted > numericCheque) {
                $('.modal_input_validation_2').show();
                $(".modal_input_validation_2").html("<font style='color:red;'>Adjusted amount cannot exceed amount.</font>");
                $("span.modal_input_validation_2").addClass("red_alert");
                if (typeof toastr !== 'undefined') {
                    toastr.error('Adjusted amount cannot exceed  amount.');
                }
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
                url: url + '/' + 'payables-outstanding-store-temp',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    bi_cheque_amount: val1,
                    bi_amount_adjusted: val2,
                    bi_extra_amount: val3,
                    bi_balance_to_adjust: val4,
                    bi_currency: bi_currency,
                    bi_doc_no: bi_doc_no,
                    bi_doc_number: $("#doc_number").val(),
                    bi_doc_date: bi_doc_date,
                    bi_lpo_no: bi_lpo_no,
                    bi_bill_number: bi_bill_number,
                    bi_total: bi_total,
                    bi_paid: bi_paid,
                    bi_balance: bi_balance,
                    bi_amount: bi_amount,
                    bi_narration: bi_narration,
                    /* deal info from modal row (if present) */
                    bi_deal_code: ($('#bi_deal_code_' + id).length ? $('#bi_deal_code_' + id).val() : ''),
                    bi_deal_id: ($('#bi_deal_id_' + id).length ? $('#bi_deal_id_' + id).val() : ''),
                    /* current form-level deal_id (deal code string) */
                    form_deal_id: $('#deal_id').val(),
                    account_id: account_id,
                    entry_date: entry_date,
                    transaction_type: transaction_type,
                    entry_type: entry_type,
                    process_id: process_id,

                },
                cache: false,
                success: function (response) {
                    var response = JSON.parse(response);
                    var len = 0;
                    if (response['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        //$("#btn_close2").click();
                        //$("#addCtrlBankBookAdjest").click();
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) { }
            });

            //preventDefault();
            $("#loading_bg").css("display", "none");
            return true;
        }
    </script>

    <script>
        function update_totals() {
            let total_amount = 0;

            const decimal_point = @json(session('logged_session_data.decimal_point'));

            $('#myTable tbody tr').each(function () {
                const $row = $(this);

                total_amount += parseFloat($row.find('input[name="amount[]"]').val().replace(/,/g, '')) || 0;
            });

            $('#lbl_total_amount').text(formatAmount(total_amount.toFixed(decimal_point)));
        }
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
        const SHOW_SUPPLIER_CODE = {{ @App\SysHelper:: getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};

        $(document).ready(function () {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_supp_account_list_ajax') }}',
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
                                    let text = "";

                                    if (SHOW_SUPPLIER_CODE) {
                                        text = item.account_name + " (" + item.account_code + ")";
                                    } else {
                                        text = item.account_name;  // no code
                                    }

                                    return {
                                        id: item.id,
                                        text: text
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

                    // Set values using "name" attribute selectors inside the same row

                });


            }

            initAccountSelect2('.js-account-select');

            // Auto-open first account select on page load
            let $firstAccountSelect = $('select[name="account_id[]"]').first();
            if ($firstAccountSelect.length) {
                if (!$firstAccountSelect.hasClass('js-account-select')) {
                    $firstAccountSelect.addClass('js-account-select');
                    initAccountSelect2($firstAccountSelect);
                }
                // open the first row dropdown
                $firstAccountSelect.select2('open');
            }

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
            const pageHeight = window.innerHeight - 65;
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

                // Reset state and initialize cloned date picker row
                initDatePickers(newRow);

                tbody.appendChild(newRow);
            }

            // initialize datepickers for all generated rows
            initDatePickers(tbody);
        };
        /*table row fill based on layout height*/
    </script>

    <script>
        $(document).ready(function () {
            initDatePickers();
        });
    </script>

    <div class="modal  fade" data-bs-backdrop="false" id="addModel" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;left:17%;top:10%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Print Cheque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="model_close"></button>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-store']) }}
                <div class="modal-body">
                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                    <input type="hidden" name="cid" id="cid">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name_text" value="" readonly>
                                <input type="hidden" name="bank_name" id="bank_name" value="">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Cheque Number</label>
                                <input class="form-control" type="text" name="cheque_number" autocomplete="off"
                                    id="cheque_number2" value="" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Cheque Date</label>
                                <input class="form-control date-picker" type="text" name="cheque_date"
                                    autocomplete="off" id="cheque_date2" value="" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Supplier Name</label>
                                <input type="hidden" name="supplier_name" id="supplier_name" value="">
                                <input type="text" class="form-control" id="supplier_name_text" value="" readonly>
                                <input type="hidden" name="other_supplier_name" id="other_supplier_name" value="">
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Amount</label>
                                <input class="form-control" type="text" name="amount" autocomplete="off" id="amount"
                                    onchange="amount_w()" value="" required>

                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Amount in Words</label>
                                <input class="form-control" type="text" name="amount_words" autocomplete="off"
                                    id="amount_words" value="" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Deal ID</label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id2"
                                    value="" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Reference</label>
                                <input class="form-control" type="text" name="reference" autocomplete="off"
                                    id="reference" value="" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-group">
                                <label for="">Attachment</label>
                                <input class="form-control" type="file" name="attachment" autocomplete="off"
                                    id="attachment">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" value="pr" name="submit_btn" class="btn btn-light"
                        onclick="close_model()"><span class="ti-check"></span>Save & Print</button>
                    <button type="submit" value="sa" name="submit_btn" class="btn btn-light" id="btnSubmit"><span
                            class="ti-check"></span>Save</button>
                </div>
                {{ Form::close() }}
                <script>
                    function close_model() {
                        $('#model_close').click();
                    }
                </script>
            </div>
        </div>
    </div>

    <script>
        function popup_model() {
            // header values
            $('#bank_name').val($('#payment_mode_bank').val());
            $('#bank_name_text').val($("#payment_mode_bank option:selected").text());
            $('#cheque_number2').val($('#cheque_number').val());
            $('#cheque_date2').val($('#cheque_date').val());

            // extract first row account and amount (ids are not present on dynamic rows)
            var firstAcc = $('select[name="account_id[]"]').first();
            var supVal = firstAcc.val() || '';
            var supText = firstAcc.find('option:selected').text() || '';
            $('#supplier_name').val(supVal);
            $('#supplier_name_text').val(supText);

            var firstAmt = $('input[name="amount[]"]').first().val() || '';
            $('#amount').val(firstAmt);

            // other static fields
            var deal_id = $('#deal_id').val() || '';
            console.log("Setting deal_id in modal to: " + deal_id);
            $('#deal_id2').val(deal_id);
            $('#reference').val($('#narration').val());
            amount_w();
            $('#addModel').modal('show');
        }
        var th = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];

        var dg = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        var tn = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        var tw = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        function toWords(s) {
            s = s.toString();
            s = s.replace(/[\, ]/g, '');
            if (s != parseFloat(s)) return 'not a number';
            var x = s.indexOf('.');
            if (x == -1) x = s.length;
            if (x > 15) return 'too big';
            var n = s.split('');
            var str = '';
            var sk = 0;
            for (var i = 0; i < x; i++) {
                if ((x - i) % 3 == 2) {
                    if (n[i] == '1') {
                        str += tn[Number(n[i + 1])] + ' ';
                        i++;
                        sk = 1;
                    } else if (n[i] != 0) {
                        str += tw[n[i] - 2] + ' ';
                        sk = 1;
                    }
                } else if (n[i] != 0) {
                    str += dg[n[i]] + ' ';
                    if ((x - i) % 3 == 0) str += 'Hundred ';
                    sk = 1;
                }
                if ((x - i) % 3 == 1) {
                    if (sk) str += th[(x - i - 1) / 3] + ' ';
                    sk = 0;
                }
            }

            str = str.trim();

            if (x != s.length) {
                var y = s.length;
                var decimalDigits = s.slice(x + 1);
                // if all decimals are zero, omit the minor part
                if (/^0*$/.test(decimalDigits)) {
                    return str || 'Zero';
                }

                var decimalWords = '';
                for (var i = x + 1; i < y; i++) {
                    if (dg[n[i]] != undefined) {
                        decimalWords += dg[n[i]] + ' ';
                    }
                }
                decimalWords = decimalWords.trim();
                if (!decimalWords) {
                    return str || 'Zero';
                }

                return (str + ' and ' + decimalWords).replace(/\s+/g, ' ').trim();
            }

            return str || 'Zero';
        }
        function amount_w() {
            $('#amount_words').val(toWords($('#amount').val()));
        }

    </script>




    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
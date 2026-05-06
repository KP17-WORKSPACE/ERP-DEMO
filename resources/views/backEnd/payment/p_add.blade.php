    <?php try { ?>

     

    <input type="hidden" id="currency1" value="{{ $currency1 }}" />
    <input type="hidden" id="currency2" value="{{ $currency2 }}" />

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-create-form']) }}
    {{-- @endif --}}
    <input type="hidden" id="receipt_process_id" name="process_id" value="{{ Auth::user()->id . date('YmdHis') }}">
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
    <input type="hidden" name="page_id" id="page_id" value="{{ $page_id }}">
    <input type="hidden" name="cheque_id" id="cheque_id" value="0">


    <?php
    //$invno_cash=@App\SysHelper::get_new_maxid_2('sys_payment','cash','id');
    //$invno_bank=@App\SysHelper::get_new_maxid_2('sys_payment','bank','id');
    
    $invno_cash = @App\SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
    $invno_bank = @App\SysHelper::get_new_code_err('sys_payment', 'BP', 'doc_number');
    
    ?>

    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
        <h4 class="purchase-order-content-header-left">
            New ({{ $invno_cash }})
        </h4>
        <div class="purchase-order-content-header-right">
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
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
                <div class="col-2">
                    <label class="form-label">Mode</label>
                    <div class="form-group">
                        <select class="form-control" name="mode" id="mode" required>
                            <option value="1">Cash</option>
                            <option value="2">Bank</option>
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                        <script>
                            // delegated handler: works with dynamic DOM changes
                            $(document).on('change', '#mode', function() {
                                var mode = $(this).val();
                                if (mode == 1) {
                                    $('#payment_mode_cash').prop('required', true);
                                    $('#payment_mode_bank').prop('required', false);
                                    $('#payment_mode_cash').css("display", "block");
                                    $('#payment_mode_bank').css("display", "none");
                                    $('#div_payment_through').css("display", "none");

                                    $('#div_cheque_date').css("display", "none");
                                    $('#div_cheque_number').css("display", "none");
                                    $('#div_payment_days').css("display", "none");
                                    $('#cheque_number').prop('required', false);
                                    $('#payment_days').prop('required', false);
                                    $('#cheque_date').prop('required', false);
                                    $('#addCheque').css('display', 'none');

                                    $('#doc_number').val($('#cash_doc_number').val()).trigger('change');
                                    $('#btn_submit').text('Add Cash Payment');
                                } else {
                                    $('#payment_mode_cash').prop('required', false);
                                    $('#payment_mode_bank').prop('required', true);
                                    $('#payment_mode_cash').css("display", "none");
                                    $('#payment_mode_bank').css("display", "block");
                                    $('#div_payment_through').css("display", "");

                                    $('#div_cheque_date').css("display", "");
                                    $('#div_cheque_number').css("display", "");
                                    $('#div_payment_days').css("display", "");
                                    $('#cheque_number').prop('required', true);
                                    $('#payment_days').prop('required', true);
                                    $('#cheque_date').prop('required', true);
                                    $('#addCheque').css('display', '');

                                    $('#doc_number').val($('#bank_doc_number').val()).trigger('change');
                                    $('#btn_submit').text('Add Bank Payment');
                                }
                            });
                        </script>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Doc Number</label>
                    <div class="form-group">

                        <input type="hidden" id="cash_doc_number" value="{{ $invno_cash }}" />
                        <input type="hidden" id="bank_doc_number" value="{{ $invno_bank }}" />
                        <input class="form-control" type="text" id="doc_number" name="doc_number"
                            value="{{ $invno_cash }}" readonly>
                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Payment Mode</label>
                    <div class="form-group">
                        <select class="form-control" name="payment_mode_cash" id="payment_mode_cash" required>
                            @if (isset($paymentmode_cash))
                                @foreach ($paymentmode_cash as $val)
                                    <option value="{{ @$val->id }}"
                                        @if (isset($editData)) @if (@$editData->payment_mode == @$val->id) selected @endif
                                        @endif>{{ @$val->account_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <select class="form-control" name="payment_mode_bank" id="payment_mode_bank"
                            style="display: none;">
                            @if (isset($paymentmode_bank))
                                @foreach ($paymentmode_bank as $val)
                                    <option value="{{ @$val->id }}"
                                        @if (isset($editData)) @if (@$editData->payment_mode == @$val->id) selected @endif
                                        @endif>{{ @$val->account_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                    </div>
                </div>
                <div class="col-2">
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
                <div class="col-2">
                    <label class="form-label">Currency</label>
                    <div class="form-group">
                        <select class="form-control" name="currency" id="currency">
                            @foreach ($currency as $value)
                                <option value="{{ @$value->id }}" @if ($company->currency_id == $value->id) selected @endif>
                                    {{ @$value->code }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                    </div>
                </div>
                <div class="col-2">
                    <label class="form-label">Created By</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by"
                            value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}"
                            readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                    data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                    aria-selected="true">Extra Fields</button>
            </li>
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                aria-labelledby="extra-fields-tab">
                <div class="row gap-rows">


                    <div class="col-12 mb-2">
                        <div class="row gap-rows">

                            <div class="col-1-5 mb-4" id="div_payment_through" style="display: none;">

                                <label>@lang('Payment Through')<span>*</span></label>

                                <div class="form-group">
                                    <select class="form-control" name="payment_through" id="payment_through">
                                        <option value="3">Cheque</option>
                                        <option value="1">Bank Transfer</option>
                                        {{--  <option value="2">CDC Cheque</option>  --}}
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                </div>

                            </div>
                            <script>
                                // delegated handler — works when element is added/hidden dynamically
                                $(document).on('change', '#payment_through', function() {
                                    var paymentthrough = $(this).val();
                                    if (paymentthrough == 1) {
                                        $('#div_cheque_date').css("display", "none");
                                        $('#div_cheque_number').css("display", "none");
                                        $('#div_payment_days').css("display", "none");
                                        $('#cheque_number').prop('required', false);
                                        $('#payment_days').prop('required', false);
                                        $('#cheque_date').prop('required', false);
                                        $('#addCheque').css('display', 'none');
                                    }
                                    if (paymentthrough == 2 || paymentthrough == 3) {
                                        $('#div_cheque_date').css("display", "");
                                        $('#div_cheque_number').css("display", "");
                                        $('#div_payment_days').css("display", "");
                                        $('#cheque_number').prop('required', true);
                                        $('#payment_days').prop('required', true);
                                        $('#cheque_date').prop('required', true);
                                        $('#addCheque').css('display', '');
                                    }
                                });
                            </script>
                            <div class="col-1-5 mb-4" id="div_payment_days" style="display: none;">
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
                            <div class="col-1-5 mb-4" id="div_cheque_date" style="display: none;">
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
                                                $('#cheque_date').on('change', function() {
                                                    $('#payment_date').val($('#cheque_date').val());
                                                    $('#payment_date').focus();
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 mb-4" id="div_cheque_number" style="display: none;">
                                <div class="input-effect">
                                    <label> @lang('Cheque Number') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_number"
                                        name="cheque_number"
                                        value="{{ isset($editData) ? @$editData->cheque_number : old('cheque_number') }}">
                                </div>
                            </div>
                            <div class="col-1-5 mb-4">
                                <div class="input-effect">
                                    <label> @lang('Payment Date') <span>*</span> </label>
                                    @php
                                        $value = \Carbon\Carbon::now()->format('d/m/Y');
                                    @endphp
                                    <input class="form-control date-picker" type="text" id="payment_date"
                                        name="payment_date" value="{{ $value }}" required>
                                </div>
                            </div>
                            <div class="col-3 mb-4">
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
                            </div>
                            <script>
                                $(document).ready(function() {
                                    generate_narration();
                                });
                                $('#mode').on('change', function(e) {
                                    generate_narration();
                                });
                                $('#payment_mode_bank').on('change', function(e) {
                                    generate_narration();
                                });
                                $('#payment_through').on('change', function(e) {
                                    generate_narration();
                                });

                                function generate_narration() {
                                    var gn_mode = $('#mode').val();
                                    if (gn_mode == 1) {
                                        $('#narration_1').val('Paid Cash');
                                        var n1 = $('#narration_1').val();
                                        var n2 = $('#narration_2').val();
                                        $('#narration').val(n1 + ' ' + n2);
                                    }
                                    if (gn_mode == 2) {
                                        var gn_bank_name = $("#payment_mode_bank option:selected").text();
                                        //var gn_payment_through = $("#payment_through option:selected").text();

                                        $('#narration_1').val('Paid from ' + gn_bank_name);
                                        var n1 = $('#narration_1').val();
                                        var n2 = $('#narration_2').val();

                                        $('#narration').val(n1 + ' ' + n2);
                                    }
                                }

                                function generate_narration_fa(id) {
                                    var gn_account = $("#account_id_" + id + " option:selected").text();
                                    var gn_remarks = $('#remarks_' + id).val();

                                    $('#narration_2').val('to ' + gn_account + ' against ' + gn_remarks);
                                    var n1 = $('#narration_1').val();
                                    var n2 = $('#narration_2').val();
                                    $('#narration').val(n1 + ' ' + n2);
                                }
                            </script>

                            <div class="col-1-5 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Deal ID')<span>*</span></label>
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off"
                                        id="deal_id" value="Without Deal">
                                </div>
                            </div>
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
            <div class="table-container" style="border: solid 1px #d9d9d9;">
                <table class="table table-hover form-item-table" id="myTable">
                    <thead>
                        <tr>
                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer">
                                </div>
                            </th>
                            <th class="resizable text-center" width="450px">@lang('Account Name') <a
                                    class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                    data-bs-target="#addproductModal"></a>
                                <div class="resizer"></div>
                            </th>
                            <th class="resizable text-center" width="150px">@lang('Amount')<div class="resizer">
                                </div>
                            </th>
                            <th class="resizable text-center">@lang('Narration')<div class="resizer"></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="1" /></td>
                            <td class="noborder">
                                <select class="form-control" name="account_id[]">
                                    <option value=""></option>
                                </select>
                            </td>
                            <td>
                                <input class="form-control text-end" type="decimal" name="amount[]"
                                    autocomplete="off" onchange="update_totals()" onblur="formatCurrency(this)">>
                            </td>
                            <td><input type="text" class="form-control" name="remarks[]"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" scope="col">Total</th>
                            <th class="text-end"><label id="lbl_total_amount">0</label></th>
                            <th class="text-end" scope="col"></th>
                        </tr>
                    </tfoot>
                </table>
                <div id="contextMenu">
                    <button type="button" id="addRow">Add Row</button>
                    <button type="button" id="deleteRow">Delete Row</button>
                </div>
            </div>
            {{ Form::close() }}



            <?php if(isset($cheque_detail)) { ?>
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
            <?php } ?>

            <button type="button" data-bs-toggle="modal" data-bs-target="#cr_popup_win" id="addCtrlPaymentAdjest"
                hidden></button>
            <form id="ta">
                <div class="modal side-panel fade" id="cr_popup_win" data-bs-backdrop="false" tabindex="-1"
                    aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="editModalLabel">Bill Wise Selection</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <input type="hidden" id="br_account_id">
                            <input type="hidden" id="br_account_id_amount">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-4 mb-20">
                                        <div class="input-effect">
                                            <label> @lang('Cheque Amount') <span>*</span> </label>
                                            <input class="primary-input form-control" type="text"
                                                id="bi_cheque_amount" name="bi_cheque_amount" value="0">
                                            <span class="focus-border"></span>
                                            <span class="modal_input_validation_2 red_alert"></span>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-20">
                                        <div class="input-effect">
                                            <label> @lang('Amount Adjusted') <span>*</span> </label>
                                            <input class="primary-input form-control" type="text"
                                                id="bi_amount_adjusted" name="bi_amount_adjusted" value="0">
                                            <span class="focus-border"></span>
                                            <span class="modal_input_validation_3 red_alert"></span>

                                            <input type="hidden" id="bi_balance_adjest" value="">

                                        </div>
                                    </div>
                                    <div class="col-4 mb-20">
                                        <div class="input-effect">
                                            <label> @lang('Balance to Adjust') <span>*</span> </label>
                                            <input class="primary-input form-control" type="text"
                                                id="bi_extra_amount" name="bi_extra_amount" value="0">
                                            <div style="display: none;">
                                                <input class="primary-input form-control" type="text"
                                                    id="bi_balance_to_adjust" name="bi_balance_to_adjust"
                                                    value="0">
                                            </div>
                                            <span class="focus-border"></span>
                                            <span class="modal_input_validation_2 red_alert"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="equipment comon-status row mt-40 d-block">
                                            <table class="table table-hover form-item-table" cellspacing="0"
                                                width="100%" id="crListBankBookAdjest">
                                                <thead>
                                                    <tr>
                                                        <th style="width:100px;" class="text-center">@lang('Doc No')</th>
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

    var form_amt = Number($('#bi_cheque_amount').val().replace(/,/g, '')) || 0;
    var bal_amt  = Number($('#bi_balance_' + id).val().replace(/,/g, '')) || 0;
    var bi_amount = Number($('#bi_amount_' + id).val().replace(/,/g, '')) || 0;

    var adjusted_sum = 0;
    $(".tot_amt").each(function () {
        var v = Number($(this).val().replace(/,/g, ''));
        adjusted_sum += isNaN(v) ? 0 : v;
    });

    $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));
    $('#bi_balance_adjest').val(formatAmount(form_amt - adjusted_sum));

    var amt = Number($('#bi_balance_adjest').val().replace(/,/g, '')) || 0;
    var pending = Number($('#bi_balance_to_adjust').val().replace(/,/g, '')) || 0;

    if (amt > 0 && pending > 0) {

        if (amt === bal_amt) {

            $('#bi_amount_' + id).val(formatAmount(amt));

            adjusted_sum += amt;
            $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));
            $('#bi_balance_to_adjust').val(formatAmount(pending - amt));

            $('#bi_extra_amount').val(
                formatAmount(Math.abs(form_amt - adjusted_sum))
            );

            $('#bi_balance_adjest').val(formatAmount(0));

        } else if (amt > bal_amt) {

            $('#bi_amount_' + id).val(formatAmount(bal_amt));

            adjusted_sum += bal_amt;
            $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));
            $('#bi_balance_to_adjust').val(formatAmount(pending - bal_amt));

            $('#bi_extra_amount').val(
                formatAmount(Math.abs(form_amt - adjusted_sum))
            );

            $('#bi_balance_adjest').val(formatAmount(amt - bal_amt));

        } else if (amt < bal_amt) {

            $('#bi_amount_' + id).val(formatAmount(amt));

            adjusted_sum += amt;
            $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));
            $('#bi_balance_to_adjust').val(formatAmount(pending - amt));

            $('#bi_extra_amount').val(
                formatAmount(Math.abs(form_amt - adjusted_sum))
            );

            $('#bi_balance_adjest').val(formatAmount(0));

        } else {

            $('#bi_amount_' + id).val(formatAmount(0));
            $('#bi_balance_adjest').val(formatAmount(0));
        }

        // Footer total
        var num_tot_amt = $('.tot_amt').length;
        var total = 0;

        for (var i = 1; i <= num_tot_amt; i++) {
            var v = Number($('#bi_amount_' + i).val().replace(/,/g, ''));
            total += isNaN(v) ? 0 : v;
        }
        $('#footer_adjustment').text(formatAmount(total));

        // Remarks
        var docs = [];
        for (var i = 1; i <= num_tot_amt; i++) {
            var v = Number($('#bi_amount_' + i).val().replace(/,/g, ''));
            if (v > 0) {
                docs.push($('#bi_doc_no_' + i).val());
            }
        }

        var re_id = $('#narration_row_id').val();
        $('#remarks_' + re_id).val(docs.join(', '));
    }
}
                                    // function get_set_amount(id) {
                                    //     var form_amt = Number($('#bi_cheque_amount').val().replace(/,/g, ''));
                                    //     var bal_amt = Number($('#bi_balance_' + id).val().replace(/,/g, ''));

                                    //     var bi_amount = Number($('#bi_amount_' + id).val());
                                    //     var adjested_sum = 0;

                                    //     $(".tot_amt").each(function() {
                                    //         adjested_sum += +$(this).val();
                                    //     });
                                    //     $('#bi_amount_adjusted').val(Number(adjested_sum));
                                    //     $('#bi_balance_adjest').val(Number(form_amt) - Number(adjested_sum));

                                    //     if ($('#bi_balance_adjest').val() == "") {
                                    //         $('#bi_balance_adjest').val(form_amt);
                                    //     }
                                    //     var amt = Number($('#bi_balance_adjest').val());
                                    //     var pending = Number($('#bi_balance_to_adjust').val());

                                    //     if (amt > 0 && amt != "" && pending > 0) {
                                    //         if (amt == bal_amt) {
                                    //             //alert("1.if(amt == bal_amt)");

                                    //             $('#bi_amount_' + id).val(formatAmount(amt));
                                    //             var adjusted = Number($('#bi_amount_adjusted').val());
                                    //             var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                    //             $('#bi_amount_adjusted').val(formatAmount(adjusted + amt));
                                    //             $('#bi_balance_to_adjust').val(balance_adjust - (adjusted + amt));
                                    //             var extra = Number($('#bi_extra_amount').val());

                                    //             if (form_amt >= (adjusted + amt)) {
                                    //                 $('#bi_extra_amount').val(formatAmount(form_amt - (adjusted + amt)));
                                    //             } else {
                                    //                 $('#bi_extra_amount').val(formatAmount((adjusted + amt) - form_amt));
                                    //             }

                                    //             $('#bi_balance_adjest').val(0);
                                    //         } else if (amt > bal_amt) {
                                    //             //alert("2.else if(amt > bal_amt)");

                                    //             $('#bi_amount_' + id).val(formatAmount(bal_amt));
                                    //             var adjusted = Number($('#bi_amount_adjusted').val());
                                    //             var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                    //             $('#bi_amount_adjusted').val(formatAmount(adjusted + bal_amt));
                                    //             $('#bi_balance_to_adjust').val(balance_adjust - bal_amt);
                                    //             var extra = Number($('#bi_extra_amount').val());

                                    //             if (form_amt >= (adjusted + bal_amt)) {
                                    //                 $('#bi_extra_amount').val(formatAmount(form_amt - (adjusted + bal_amt)));
                                    //             } else {
                                    //                 $('#bi_extra_amount').val(formatAmount((adjusted + bal_amt) - form_amt));
                                    //             }

                                    //             if (amt >= bal_amt) {
                                    //                 $('#bi_balance_adjest').val(amt - bal_amt);
                                    //             } else {
                                    //                 $('#bi_balance_adjest').val(bal_amt - amt);
                                    //             }
                                    //         } else if (amt < bal_amt) {
                                    //             //alert("3.else if(amt < bal_amt)");

                                    //             $('#bi_amount_' + id).val(formatAmount(amt));
                                    //             var adjusted = Number($('#bi_amount_adjusted').val());
                                    //             var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                    //             $('#bi_amount_adjusted').val(adjusted + amt);
                                    //             $('#bi_balance_to_adjust').val(balance_adjust - amt);
                                    //             var extra = Number($('#bi_extra_amount').val());

                                    //             if (form_amt >= (adjusted + amt)) {
                                    //                 $('#bi_extra_amount').val(formatAmount(form_amt - (adjusted + amt)));
                                    //             } else {
                                    //                 $('#bi_extra_amount').val(formatAmount((adjusted + amt) - form_amt));
                                    //             }

                                    //             $('#bi_balance_adjest').val(0);
                                    //         } else {
                                    //             //alert("4.else");

                                    //             $('#bi_amount_' + id).val(0);
                                    //             $('#bi_balance_adjest').val(0);
                                    //         }

                                    //         var num_tot_amt = $('.tot_amt').length;
                                    //         var n = 0;
                                    //         for (i = 1; i <= num_tot_amt; i++) {
                                    //             if ($('#bi_amount_' + i).val() != "") {
                                    //                 n += Number($('#bi_amount_' + i).val());
                                    //             }
                                    //         }
                                    //         $('#footer_adjustment').text(n);

                                    //         var d = '';
                                    //         for (i = 1; i <= num_tot_amt; i++) {
                                    //             if ($('#bi_amount_' + i).val() != "" && $('#bi_amount_' + i).val() != 0) {
                                    //                 if (d == '') {
                                    //                     d = $('#bi_doc_no_' + i).val();
                                    //                 } else {
                                    //                     d += ', ' + $('#bi_doc_no_' + i).val();
                                    //                 }
                                    //             }
                                    //         }
                                    //         var re_id = $('#narration_row_id').val();
                                    //         $('#remarks_' + re_id).val(d);

                                    //     }
                                    // }
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
                $(document).on('keypress', 'input[name="amount[]"]', function(e) {
                    if (e.which === 13) {
                        $("#loading_bg").css("display", "");
                        const currentInput = $(this);
                        const currentRow = currentInput.closest('tr');

                        let br_account_id = currentRow.find('select[name="account_id[]"]').val();
                        const br_amount = currentRow.find('input[name="amount[]"]').val();
                        if (br_account_id !== "" && br_amount !== "") {
                            $('#br_account_id').val(br_account_id);
                            $('#br_account_id_amount').val(br_amount);
                            $('#bi_cheque_amount').val(formatAmount(br_amount)).focus();
                            $('#addCtrlPaymentAdjest').click().prop("disabled", true);
                        } else {
                            alert("Account / Amount Missing");
                        }
                        $("#loading_bg").css("display", "none");
                        return false; // prevent default behavior in this case
                    }
                });

                // Prevent form submission on Enter for all fields EXCEPT amount[]
                $('#payment-create-form').on('keypress', function(e) {
                    if (e.which === 13 && !$(e.target).is('input[name="amount[]"]')) {
                        e.preventDefault();
                        return false;
                    }
                });

                function validateAttachForm() {
                    $("#loading_bg").css("display", "block");
                    var numRows = $('.row_ctrl').length;
                    for (i = 1; i <= numRows; i++) {
                        if ($("#bi_amount_" + i).val() != "" && $("#bi_amount_" + i).val() != 0) {
                            validateBankBookAdjestForm(i);
                        }
                    }
                    alert("Added!!");
                    generate_narration_fa($('#narration_row_id').val());
                    $('#remarks_' + $('#narration_row_id').val()).val($('#narration').val());

                    $("#cr_popup_win").hide();
                    $("#loading_bg").css("display", "none");
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
                        success: function(response) {
                            var response = JSON.parse(response);
                            var len = 0;
                            if (response['data'] == "ERROR") {
                                alert("Error found in something!!");
                            } else {
                                //$("#btn_close2").click();
                                //$("#addCtrlBankBookAdjest").click();
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {}
                    });
                }

                function BankBookAdjestBalance(id) {
                    var bi_total = $('#bi_total_' + id).val();
                    var bi_paid = $('#bi_paid_' + id).val();
                    var tot = (parseFloat(bi_total) - parseFloat(bi_paid)).toFixed(@json(session('logged_session_data.decimal_point')));
                    $('#bi_balance_' + id).val(tot);
                    $('#bi_amount_' + id).val(bi_paid);
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
                            account_id: account_id,
                            entry_date: entry_date,
                            transaction_type: transaction_type,
                            entry_type: entry_type,
                            process_id: process_id,

                        },
                        cache: false,
                        success: function(response) {
                            var response = JSON.parse(response);
                            var len = 0;
                            if (response['data'] == "ERROR") {
                                alert("Error found in something!!");
                            } else {
                                //$("#btn_close2").click();
                                //$("#addCtrlBankBookAdjest").click();
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {}
                    });

                    //preventDefault();
                    $("#loading_bg").css("display", "none");
                }
            </script>

            <script>
                function update_totals() {
                    let total_amount = 0;

                    const decimal_point = @json(session('logged_session_data.decimal_point'));

                    $('#myTable tbody tr').each(function() {
                        const $row = $(this);

                        total_amount += parseFloat($row.find('input[name="amount[]"]').val().replace(/,/g, '')) || 0;
                    });

                    $('#lbl_total_amount').text(formatAmount(total_amount.toFixed(decimal_point)));
                }
            </script>
            <script>
                $(document).on('focus', 'select[name="account_id[]"]', function() {
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
                $(document).ready(function() {
                    function initAccountSelect2(selector) {
                        $(selector).select2({
                            ajax: {
                                url: '{{ route('autocomplete.get_supp_account_list_ajax') }}',
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        search_text: params.term
                                    };
                                },
                                processResults: function(data) {
                                    return {
                                        results: data.map(function(item) {
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
                            minimumInputLength: 2,
                            dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
                        });

                        $(selector).on('select2:select', function(e) {
                            var selectedData = e.params.data;
                            var $row = $(this).closest('tr'); // find the closest row

                            // Set values using "name" attribute selectors inside the same row

                        });


                    }

                    initAccountSelect2('.js-account-select');

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
                window.onload = function() {
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

                        tbody.appendChild(newRow);
                    }
                };
                /*table row fill based on layout height*/
            </script>



            <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

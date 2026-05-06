    <?php try { ?>




    <input type="hidden" id="currency1" value="{{ $currency1 }}" />
    <input type="hidden" id="currency2" value="{{ $currency2 }}" />

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-store-deal', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-create-form']) }}
    {{-- @endif --}}
    <input type="hidden" id="receipt_process_id" name="process_id" value="{{ Auth::user()->id . date('YmdHis') }}">
    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
    <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">

    <input type="hidden" id="payment_id" value="{{ isset($editData) ? @$editData->id : 0 }}">
    <div id="paymentAttachmentHiddenInputs"></div>
    <input type="hidden" name="cheque_id" id="cheque_id" value="0">


    <?php
    //$invno_cash=@App\SysHelper::get_new_maxid_2('sys_payment','cash','id');
    //$invno_bank=@App\SysHelper::get_new_maxid_2('sys_payment','bank','id');
    
    $invno_cash = @App\SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
    $invno_bank = @App\SysHelper::get_new_code_err('sys_payment', 'BP', 'doc_number');
    
    ?>

    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
        <h4 class="purchase-order-content-header-left">
           <span class="font-weight-600" id="doc_number_display_cash">New ({{ $invno_cash }})</span>
            <span class="font-weight-600" id="doc_number_display_bank" style="display: none">New ({{ $invno_bank }})</span>


        </h4>
        <div class="purchase-order-content-header-right">
             <!-- <button type="button" class="btn btn-light add-cheque-btn" onclick="popup_model()" id="add_cheque_btn" style="display: none;">
                <i class="ico icon-outline-banknote text-success"></i> Add Cheque
            </button> -->

            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item paymentAttachmentsMenu" href="#"><i class="ico icon-outline-paperclip text-success" style="font-size:16px;"></i> Attachments</a></li>
                    <li><a class="dropdown-item" href="{{ url('stl') }}"><i class="ico icon-outline-document-text text-success"></i> STL</a></li>
                    <li><a class="dropdown-item" href="{{ url('chequebook') }}"><i class="ico icon-outline-document-text text-success"></i> Cheque Book</a></li>
                    <li><a class="dropdown-item" href="{{ url('payment-cheque-list') }}"><i class="ico icon-outline-document-text text-success"></i> Cheques</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row gap-rows">
                <div class="col">
                    <label class="form-label">Mode</label>
                    <div class="form-group">
                      
                        <select class="form-control" name="mode" id="mode" required>
                            <option value="1" @if(@$supplier_details->cust_suppl->transaction_type == 'Cash') selected @endif>Cash</option>
                            <option value="2" @if(@$supplier_details->cust_suppl->transaction_type == 'Credit') selected @endif>Bank</option>
                        </select>
                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                
                    </div>
                </div>
                   <div class="col mb-4" id="div_payment_through" style="display: none;">

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
                                    var paymentthrough = String($(this).val());

                                    // Bank Transfer -> hide and clear cheque-related fields
                                    if (paymentthrough === '1') {
                                        $('#div_cheque_date, #div_cheque_number, #div_payment_days, #div_cheque_status').hide();
                                        $('#cheque_number, #payment_days, #cheque_date').prop('required', false).val('');
                                        $('#chequebook').val('');
                                        $('#chequebook_label').hide();
                                        $('#addCheque').hide();
                                        $('#add_cheque_btn').hide();
                                        $('#bill_wise_heading').text('@lang("Bank Transfer Amount")');
                                        return;
                                    }

                                    // Cheque / CDC Cheque -> show and require fields
                                    if (paymentthrough === '2' || paymentthrough === '3') {
                                        $('#div_cheque_date, #div_cheque_number, #div_payment_days, #div_cheque_status').show();
                                        $('#cheque_number, #payment_days, #cheque_date').prop('required', true);
                                        $('#addCheque').show();
                                        $('#bill_wise_heading').text('@lang("Cheque Amount")');
                                        $('#add_cheque_btn').show();
                                        var bankSel = document.querySelector('select[name="payment_mode_bank"]');
                                        if (typeof fetchNextAvailableCheque === 'function' && bankSel && bankSel.value) {
                                            fetchNextAvailableCheque(bankSel.value);
                                        }
                                        return;
                                    }

                                    // fallback: hide/clear
                                    $('#div_cheque_date, #div_cheque_number, #div_payment_days, #div_cheque_status').hide();
                                    $('#cheque_number, #payment_days, #cheque_date').prop('required', false).val('');
                                    $('#chequebook').val('');
                                    $('#chequebook_label').hide();
                                    $('#addCheque').hide();
                                });

                                // ensure UI correct on initial load
                                $(document).ready(function() {
                                    $('#payment_through').trigger('change');
                                });
                            </script>
                <div class="col">
                    <label class="form-label">Doc Number</label>
                    <div class="form-group">

                        <input type="hidden" id="cash_doc_number" value="{{ $invno_cash }}" />
                        <input type="hidden" id="bank_doc_number" value="{{ $invno_bank }}" />
                        <input class="form-control" type="text" id="doc_number_cash" name="doc_number"
                            value="{{ $invno_cash }}" readonly>

                              <input class="form-control" style="display: none" type="text" id="doc_number_bank" name="doc_number"
                            value="{{ $invno_bank }}" readonly>
                    </div>
                </div>

                        <script>
                            $(document).ready(function() {
                                 $(document).on('change', '#mode', function() {

                                    var mode = $('#mode').val();
                                    if (mode == 1) {
                                        $('#payment_mode_cash').prop('required', true);
                                        $('#payment_mode_bank').prop('required', false);
                                        $('#payment_mode_cash').css("display", "block");
                                        $('#payment_mode_bank').css("display", "none");
                                        $('#div_payment_through').css("display", "none");

                                    $('#bill_wise_heading').text('@lang("Cash Amount")');


                                        $('#div_cheque_date').css("display", "none");
                                        $('#div_cheque_number').css("display", "none");
                                        $('#div_payment_days').css("display", "none");
                                        $('#cheque_number').prop('required', false);
                                        $('#payment_days').prop('required', false);
                                        $('#cheque_date').prop('required', false);
                                        $('#addCheque').css('display', 'none');
                                        $('#div_cheque_status').css('display', 'none');
                                        $('#chequebook').val('');
                                        $('#chequebook_label').hide();

                                        $('#doc_number_bank').css('display', 'none');
                                        $('#doc_number_cash').css('display', 'block');

                                        $('#doc_number_display_cash').css('display', 'block');
                                        $('#doc_number_display_bank').css('display', 'none');

                                        $('#doc_number').val($('#cash_doc_number').val());
                                        $('#btn_submit').text('Add Cash Payment');
                                    $('#add_cheque_btn').hide();

                                    } else {
                                        $('#payment_mode_cash').prop('required', false);
                                        $('#payment_mode_bank').prop('required', true);
                                        $('#payment_mode_cash').css("display", "none");
                                        $('#payment_mode_bank').css("display", "block");
                                        $('#div_payment_through').css("display", "");
                                        // Let payment_through handler control cheque field visibility
                                        var ptEl = document.getElementById('payment_through');
                                        if (ptEl) ptEl.dispatchEvent(new Event('change', { bubbles: true }));

                                        $('#doc_number_bank').css('display', 'block');
                                        $('#doc_number_cash').css('display', 'none');
                                        $('#doc_number_display_cash').css('display', 'none');
                                        $('#doc_number_display_bank').css('display', 'block');

                                        $('#doc_number').val($('#bank_doc_number').val());
                                        $('#btn_submit').text('Add Bank Payment');
                                    }
                                });
                            });
                        
                              $(document).ready(function() {
                                $('#mode').trigger('change');
                            });
                        </script>

                <div class="col">
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
                <div class="col">
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
                <div class="col">
                    <label class="form-label">Created By</label>
                    <div class="form-group">
                        <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by"
                            value="{{ Auth::user()->full_name }}"
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

                         

                                
                            <div class="col mb-4" id="div_payment_days" style="display: none;">
                                <label>@lang('No of Days')<span>*</span></label>
                                <input class="form-control" type="number" name="payment_days" value="{{ @$supplier_details->cust_suppl->credit_days }}" id="payment_days"
                                    onchange="">

                                    

                            

                            </div>
                            <div class="col mb-4" id="div_cheque_date" style="display: none;">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Cheque Date')</label>
                                           @php
    // Default date is today
    $defaultDate = \Carbon\Carbon::now();

    // If supplier has credit_days, add them
    if (!empty($supplier_details->cust_suppl->credit_days)) {
        $defaultDate->addDays($supplier_details->cust_suppl->credit_days ?? 0);
    }

    // Format as dd/mm/yyyy
    $value = $defaultDate->format('d/m/Y');
@endphp
                                            <input class="form-control date-picker" id="cheque_date" type="text"
                                                name="cheque_date" value="{{ @$value }}">
                                            <script>
                                                document.getElementById('cheque_date').addEventListener('change', function () {
                                                    var pd = document.getElementById('payment_date');
                                                    if (pd) pd.value = this.value;
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4" id="div_cheque_number" style="display: none;">
                                <div class="input-effect">
                                    <label> @lang('Cheque Number') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_number"
                                        name="cheque_number"
                                        placeholder="Auto-assigned"
                                        value="{{ isset($editData) ? @$editData->cheque_number : old('cheque_number') }}">
                                        <small class="text-muted" id="chequebook_label" style="display:none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;"></small>

                                    <input type="hidden" name="chequebook" id="chequebook"
                                        value="{{ isset($editData) ? @$editData->chequebook_id : '' }}">
                                </div>
                            </div>
                            <div class="col mb-4" id="div_cheque_status" style="display: none;">
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
                            <div class="col mb-4">
                                <div class="input-effect">
                                    <label> @lang('Payment Date') <span>*</span> </label>
                                    
                                    <input class="form-control" type="text" id="payment_date"
                                        name="payment_date" value="{{ @$value }}" required>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="input-effect">
                                    <label>@lang('Deal ID')<span>*</span></label>
                                    <input class="form-control" type="text" name="deal_code" autocomplete="off"
                                        id="deal_id" value="{{  @App\SysHelper::get_code_from_dealid($deal_id) }}">
                                </div>
                            </div>
                            <div class="col mb-4">
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

                                // function generate_narration() {
                                //     var gn_mode = $('#mode').val();
                                //     if (gn_mode == 1) {
                                //         $('#narration_1').val('Paid Cash');
                                //         var n1 = $('#narration_1').val();
                                //         var n2 = $('#narration_2').val();
                                //         $('#narration').val(n1 + ' ' + n2);
                                //     }
                                //     if (gn_mode == 2) {
                                //         var gn_bank_name = $("#payment_mode_bank option:selected").text();
                                //         //var gn_payment_through = $("#payment_through option:selected").text();

                                //         $('#narration_1').val('Paid from ' + gn_bank_name);
                                //         var n1 = $('#narration_1').val();
                                //         var n2 = $('#narration_2').val();

                                //         $('#narration').val(n1 + ' ' + n2);
                                //     }
                                // }
                                function generate_narration()
                                {
                                    var gn_mode = $('#mode').val();
                                    if(gn_mode == 1){
                                        $('#narration_1').val('Paid Cash');
                                        var n1 = $('#narration_1').val();
                                        var n2 = $('#narration_2').val();
                                        $('#narration').val(n1+' '+n2);
                                        $('#narration_actual').val(n1+' '+n2);
                                    }
                                    if(gn_mode == 2){
                                        var gn_bank_name = $("#payment_mode_bank option:selected").text();
                                        //var gn_receipt_through = $("#receipt_through option:selected").text();
                                    
                                        
                                        $('#narration_1').val('Paid From '+gn_bank_name);
                                        var n1 = $('#narration_1').val();
                                        var n2 = $('#narration_2').val();

                                        $('#narration').val(n1+' '+n2);
                                        $('#narration_actual').val(n1+' '+n2);
                                    }
                                }

                                // function generate_narration_fa(id) {
                                //     var gn_account = $("#account_id_" + id + " option:selected").text();
                                //     var gn_remarks = $('#remarks_' + id).val();

                                //     $('#narration_2').val('to ' + gn_account + ' against ' + gn_remarks);
                                //     var n1 = $('#narration_1').val();
                                //     var n2 = $('#narration_2').val();
                                //     $('#narration').val(n1 + ' ' + n2);
                                // }

                                function generate_narration_fa() {
                                    var remarksArr = $('input[name="remarks[]"]').map(function () {
                                        var val = $.trim($(this).val());
                                        return val ? val : null;   // ← ignore empty
                                    }).get();

                                    if (remarksArr.length === 0) {
                                        return; // nothing to add
                                    }

                                    var gn_remarks = remarksArr.join(' | ');

                                    var n1 = $('#narration_actual').val();
                                    $('#narration').val(n1 + ' ' + gn_remarks);
                                }
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

                    @php
                        $show_supplier_code = @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'];
                        $amount_po = $dealtrack_po_payments->sum('payment');
                       $poCodes = $dealtrack_po_payments
    ->pluck('po_id')
    ->unique()
    ->map(function ($poId) {
        return App\SysHelper::getPurchaseOrderCode($poId);
    })
    ->filter() // remove nulls if any
    ->values()
    ->toArray();

$poCodeString = '(' . implode(', ', $poCodes) . ')';

$deal_code = @App\SysHelper::get_code_from_dealid($deal_id);


                    @endphp

                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="1" /></td>
                            <td class="noborder">
                                <select class="form-control" name="account_id[]">
                                    <option value="{{ $supplier_details->id }}">{{ $supplier_details->account_name }} @if($show_supplier_code) ({{  $supplier_details->account_code}}) @endif</option>
                                    
                                </select>
                            </td>
                            <td>
                                <input class="form-control text-end" type="decimal" name="amount[]" data-enter-skip
                                    autocomplete="off" onchange="update_totals()" onblur="formatCurrency(this)" value="{{ @App\SysHelper::com_curr_format($amount_po,2,'.',',')  }}">
                            </td>
                            <td><input type="text" class="form-control" name="remarks[]" value="Payment against Deal {{ $deal_code }} for Purchase Orders {{ $poCodeString }}"></td>
                            <input type="hidden" name="supplier_id" value="{{ $supplier_details->id }}">
                            <input type="hidden" name="deal_id" value="{{ $deal_id }}">
                            <input type="hidden" name="dealtrack_id" value="{{ $dealtrack_id }}">
                            

                        </tr>

                     


                         <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]"
                                    value="2" /></td>
                            <td class="noborder">
                                <select class="form-control" name="account_id[]">
                                    <option value=""></option>
                                    
                                </select>
                            </td>
                            <td>
                                <input class="form-control text-end" type="decimal" name="amount[]" data-enter-skip
                                    autocomplete="off" onchange="update_totals()" onblur="formatCurrency(this)">
                            </td>
                            <td><input type="text" class="form-control" name="remarks[]"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" scope="col">Total</th>
                            <th class="text-end"><label id="lbl_total_amount">{{ @App\SysHelper::com_curr_format($amount_po,2,'.',',')  }}</label></th>
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
                                    <div class="col mb-20">
                                        <div class="input-effect">
                                        <label id="bill_wise_heading"> @lang('Cash Amount') <span>*</span> </label>

                                            <input class="primary-input form-control text-end" type="text"
                                                id="bi_cheque_amount" name="bi_cheque_amount" value="0">
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
                                            <input class="primary-input form-control text-end" type="text"
                                                id="bi_extra_amount" name="bi_extra_amount" value="0">
                                            <div style="display: none;">
                                                <input class="primary-input form-control" type="text"
                                                    id="bi_balance_to_adjust" name="bi_balance_to_adjust"
                                                    value="0">
                                            </div>
                                            <span class="focus-border"></span>
                                            <!-- <span class="modal_input_validation_2 red_alert"></span> -->
                                        </div>
                                    </div>
                                     <div class="col mb-20">
                                        <div class="input-effect">
                                            <label>  @lang('Search in table') </label>
                                            <input class="primary-input form-control" type="text" id="tableSearchBill" name="tableSearchBill" value="" >                                       
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
    var form_amt = Number(($('#bi_cheque_amount').val() || '0').replace(/,/g, '')) || 0;

    if (id !== undefined && id !== null && String(id) !== '') {
        var bal_amt = Number(($('#bi_balance_' + id).val() || '0').replace(/,/g, '')) || 0;
        var cur_val = Number(($('#bi_amount_' + id).val() || '0').replace(/,/g, '')) || 0;

        var other_sum = 0;
        $('.tot_amt').each(function () {
            if ($(this).attr('id') !== 'bi_amount_' + id) {
                var v = Number(($(this).val() || '0').replace(/,/g, ''));
                other_sum += isNaN(v) ? 0 : v;
            }
        });

        var cap = Math.min(bal_amt, Math.max(0, form_amt - other_sum));
        if (cur_val > cap) {
            $('#bi_amount_' + id).val(formatAmount(cap));
        }
    }

    var adjusted_sum = 0;
    $('.tot_amt').each(function () {
        var v = Number(($(this).val() || '0').replace(/,/g, ''));
        adjusted_sum += isNaN(v) ? 0 : v;
    });

    $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));
    $('#bi_balance_adjest').val(formatAmount(Math.max(0, form_amt - adjusted_sum)));
    $('#bi_extra_amount').val(formatAmount(Math.max(0, adjusted_sum - form_amt)));
    $('#bi_balance_to_adjust').val(formatAmount(form_amt - adjusted_sum));

    var num_tot_amt = $('.tot_amt').length;
    var total = 0;
    for (var i = 1; i <= num_tot_amt; i++) {
        var v = Number(($('#bi_amount_' + i).val() || '0').replace(/,/g, ''));
        total += isNaN(v) ? 0 : v;
    }
    $('#footer_adjustment').text(formatAmount(total));

    var docs = [];
    for (var i = 1; i <= num_tot_amt; i++) {
        var val = Number(($('#bi_amount_' + i).val() || '0').replace(/,/g, ''));
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

// Auto-fill adjustment field on click ONLY when it is empty (does not fire on erase)
$(document).on('click', '.tot_amt', function () {
    var cur_val = Number(($(this).val() || '0').replace(/,/g, '')) || 0;
    if (cur_val !== 0) { return; }
    var idMatch = $(this).attr('id') ? $(this).attr('id').match(/(\d+)$/) : null;
    if (!idMatch) { return; }
    var idx = idMatch[1];
    var form_amt = Number(($('#bi_cheque_amount').val() || '0').replace(/,/g, '')) || 0;
    var bal_amt = Number(($('#bi_balance_' + idx).val() || '0').replace(/,/g, '')) || 0;
    var other_sum = 0;
    $('.tot_amt').each(function () {
        if ($(this).attr('id') !== 'bi_amount_' + idx) {
            var v = Number(($(this).val() || '0').replace(/,/g, ''));
            other_sum += isNaN(v) ? 0 : v;
        }
    });
    var cap = Math.min(bal_amt, Math.max(0, form_amt - other_sum));
    if (cap > 0) {
        $(this).val(formatAmount(cap));
        get_set_amount(idx);
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
                $(document).on('keypress', 'input[name="amount[]"]', function(e) {
                    if (e.which === 13) {
                        $("#loading_bg").css("display", "");
                        const currentInput = $(this);
                        const currentRow = currentInput.closest('tr');

                        // remember row index for remark updates
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
                $('#payment-create-form').on('keypress', function(e) {
                    if (e.which === 13 && !$(e.target).is('input[name="amount[]"]')) {
                        e.preventDefault();
                        return false;
                    }
                });

                function validateAttachForm() {
                    $("#loading_bg").css("display", "block");
                    var numRows = $('.row_ctrl').length;
                    var natt_txt="";
                    for (i = 1; i <= numRows; i++) {
                        if ($('#bi_amount_' + i).length && $("#bi_amount_" + i).val() != "" && $("#bi_amount_" + i).val() != 0) {
                            // perform validation; abort immediately if invalid
                            var ok = validateBankBookAdjestForm(i);
                            if (ok === false) {
                                // validateBankBookAdjestForm already shows toastr/inline messages
                                $("#loading_bg").css("display", "none");
                                return false;
                            }
                        }
                        var lpo = $('#bi_lpo_no_' + i);
                        var nar = $('#bi_narration_' + i);
                        var invo = $('#bi_doc_no_'+i);
                        var amt = $('#bi_amount_' + i).val();


                        if (lpo.length && nar.length && invo.length && amt && amt != 0) {
                            if(natt_txt==""){
                                natt_txt += invo.val() + " (" + lpo.val() + ") " + nar.val();
                            } else {
                                natt_txt += ", " + invo.val() + " (" + lpo.val() + ") " + nar.val();
                            }
                        }
                    }

                    // collect deal codes only from modal rows where an adjustment amount was entered
                    var deal_codes = [];
                    // start with any value already present so we append rather than overwrite
                    var existing = $('#deal_id').val();
                    if (existing) {
                        existing.split(',').forEach(function(c) {
                            c = $.trim(c);
                            if (c) deal_codes.push(c);
                        });
                    }
                    $('[id^="bi_deal_code_"]').each(function () {
                        var idxMatch = $(this).attr('id').match(/_(\d+)$/);
                        if (!idxMatch) return; // unexpected format
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
                    unique_deal_codes = unique_deal_codes.filter(function(c) {
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
                        $(".modal_input_validation_2").html("<font style='color:red;'>Adjusted amount cannot exceed Cheque amount.</font>");
                        $("span.modal_input_validation_2").addClass("red_alert");
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Adjusted amount cannot exceed Cheque amount.');
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
                    return true;
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
   const SHOW_SUPPLIER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};

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

                        $(selector).on('select2:select', function(e) {
                            var selectedData = e.params.data;
                            var $row = $(this).closest('tr'); // find the closest row

                            // Set values using "name" attribute selectors inside the same row

                             var $amount = $row.find('input[name="amount[]"]');
                            if ($amount.length) {
                                $amount.focus();
                                // move caret to end
                                var el = $amount.get(0);
                                if (el && el.setSelectionRange) {
                                    var len = $amount.val() ? $amount.val().length : 0;
                                    el.setSelectionRange(len, len);
                                }
                            }

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

             


                                     <script>
document.addEventListener('DOMContentLoaded', function() {

    // Get inputs
    const paymentDaysInput = document.getElementById('payment_days');
    const chequeInput = document.getElementById('cheque_date');
    const paymentInput = document.getElementById('payment_date');

    // Get flatpickr instances if they exist
    const chequePicker = chequeInput?._flatpickr || null;
    const paymentPicker = paymentInput?._flatpickr || null;

    // Listen for input changes
    if (paymentDaysInput) {
        paymentDaysInput.addEventListener('input', function() {

            let daysToAdd = parseInt(this.value);
            if (isNaN(daysToAdd) || daysToAdd <= 0) return;

            // Calculate new date
            let currentDate = new Date();
            currentDate.setDate(currentDate.getDate() + daysToAdd);

            // Format as dd/mm/yyyy
            let day   = ("0" + currentDate.getDate()).slice(-2);
            let month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
            let year  = currentDate.getFullYear();
            let formattedDate = `${day}/${month}/${year}`;

            console.log('Formatted date:', formattedDate);

            // Set value safely
            if (chequePicker) {
                chequePicker.setDate(formattedDate, true);
            } else if (chequeInput) {
                chequeInput.value = formattedDate;
            }

            if (paymentPicker) {
                paymentPicker.setDate(formattedDate, true);
            } else if (paymentInput) {
                paymentInput.value = formattedDate;
            }

        });
    }

});
</script>



<script>
function popup_model(){
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
        if (!decimalWords) return str || 'Zero';
        return (str + ' and ' + decimalWords).replace(/\s+/g, ' ').trim();
    }
    return str || 'Zero';
}
function amount_w(){
    $('#amount_words').val(toWords($('#amount').val()));
}
</script>

<div class="modal  fade" data-bs-backdrop="false" id="addModel" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;left:17%;top:10%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Print Cheque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="model_close"></button>
            </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-store','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-store']) }}
            <div class="modal-body">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="cid" id="cid">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name_text" value="" readonly>
                            <input type="hidden"  name="bank_name" id="bank_name" value="">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Cheque Number</label>
                            <input class="form-control" type="text" name="cheque_number" autocomplete="off" id="cheque_number2" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Cheque Date</label>
                            <input class="form-control date-picker" type="text" name="cheque_date" autocomplete="off" id="cheque_date2" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Supplier Name</label>
                                <input type="hidden"  name="supplier_name" id="supplier_name" value="">
                                <input type="text" class="form-control" id="supplier_name_text" value="" readonly>
                                <input type="hidden" name="other_supplier_name" id="other_supplier_name" value="">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input class="form-control" type="text" name="amount" autocomplete="off" id="amount" onchange="amount_w()" value="" required>
                            
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Amount in Words</label>
                            <input class="form-control" type="text" name="amount_words" autocomplete="off" id="amount_words" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Deal ID</label>
                            <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id2" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Reference</label>
                            <input class="form-control" type="text" name="reference" autocomplete="off" id="reference" value="" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="">Attachment</label>
                            <input class="form-control" type="file" name="attachment" autocomplete="off" id="attachment">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" value="pr" name="submit_btn" class="btn btn-light" onclick="close_model()"><span class="ti-check"></span>Save & Print</button>
            <button type="submit" value="sa" name="submit_btn" class="btn btn-light" id="btnSubmit"><span class="ti-check"></span>Save</button>
            </div>
            {{ Form::close() }}
            <script>
                function close_model(){
                    $('#model_close').click();
                }
            </script>
        </div>
    </div>
</div>

    <script>
        // ── Next Available Cheque (auto-assign) ──────────────────
        function fetchNextAvailableCheque(bankId) {
            var label = document.getElementById('chequebook_label');
            if (!bankId) {
                document.getElementById('cheque_number').value = '';
                document.getElementById('chequebook').value = '';
                if (label) label.style.display = 'none';
                return;
            }
            if (label) { label.textContent = 'Loading...'; label.style.display = ''; }
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ url("api/next-available-cheque") }}/' + bankId, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('cheque_number').value = response.cheque_number;
                        document.getElementById('chequebook').value = response.chequebook_id;
                        document.getElementById('cheque_id').value = response.chequebook_id;
                        if (label) {
                            var range = '';
                            if (response.chequebook_start_no && response.chequebook_end_no) {
                                range = ' (' + response.chequebook_start_no + ' - ' + response.chequebook_end_no + ')';
                            }
                            label.textContent = 'Book: ' + response.chequebook_doc + range;
                            label.style.display = '';
                        }
                    } else {
                        document.getElementById('cheque_number').value = '';
                        document.getElementById('chequebook').value = '';
                        if (label) { label.textContent = response.message || 'No cheques available'; label.style.display = ''; }
                    }
                } else {
                    document.getElementById('cheque_number').value = '';
                    if (label) { label.textContent = 'Error loading cheque number'; label.style.display = ''; }
                }
            };
            xhr.onerror = function () {
                document.getElementById('cheque_number').value = '';
                if (label) { label.textContent = 'Error loading cheque number'; label.style.display = ''; }
            };
            xhr.send();
        }

        // ── Find chequebook by cheque number + bank ───────────
        function getChequebookByChequeNumber(bankId, chequeNumber) {
            if (!bankId || !chequeNumber) return;
            var label = document.getElementById('chequebook_label');
            if (label) { label.textContent = 'Looking up...'; label.style.display = ''; }
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ url("api/find-chequebook") }}/' + bankId + '/' + encodeURIComponent(chequeNumber), true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('chequebook').value = response.chequebook_id;
                        document.getElementById('cheque_id').value = response.chequebook_id;
                        if (label) {
                            label.textContent = 'Book: ' + response.chequebook_doc + ' (' + response.chequebook_start_no + ' - ' + response.chequebook_end_no + ')';
                            label.style.display = '';
                        }
                    } else {
                        document.getElementById('chequebook').value = '';
                        document.getElementById('cheque_id').value = 0;
                        if (label) { label.textContent = response.message || 'No matching chequebook found'; label.style.display = ''; }
                    }
                } else {
                    if (label) { label.textContent = 'Cheque lookup failed'; label.style.display = ''; }
                }
            };
            xhr.onerror = function () {
                if (label) { label.textContent = 'Cheque lookup network error'; label.style.display = ''; }
            };
            xhr.send();
        }

        // update chequebook when user manually enters a cheque number
        document.addEventListener('change', function (e) {
            if (!e.target.matches('#cheque_number')) return;
            var bankSel = document.querySelector('select[name="payment_mode_bank"], #payment_mode_bank');
            var bankId = bankSel ? bankSel.value : '';
            var chequeNumber = e.target.value && e.target.value.trim();
            if (bankId && chequeNumber) {
                getChequebookByChequeNumber(bankId, chequeNumber);
            }
        });

        // Bank change — fetch new cheque number whenever bank selection changes
        document.addEventListener('change', function (e) {
            if (!e.target.matches('select[name="payment_mode_bank"], #payment_mode_bank')) return;
            var bank = e.target.value;
            var pt = (document.getElementById('payment_through') || {}).value;
            if (pt === '2' || pt === '3') {
                fetchNextAvailableCheque(bank);
            }
        });

        // ── Init on load ──
        (function init() {
            var bankSel = document.querySelector('select[name="payment_mode_bank"]');
            var ptSel = document.getElementById('payment_through');
            var initialBank = bankSel ? bankSel.value : '';
            var initialPt = ptSel ? ptSel.value : '';
            if (initialBank && (initialPt === '2' || initialPt === '3')) {
                fetchNextAvailableCheque(initialBank);
            }
        })();
    </script>

    <!-- Payment Attachments Modal -->
    <div class="modal fade" id="paymentAttachmentsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="paymentAttachmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentAttachmentsModalLabel">Payment Attachments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row align-items-end" id="attachmentsUploadSection">
                        <div class="col-md-9">
                            <label class="form-label">Upload files</label>
                            <input type="file" id="paymentAttachmentsFiles" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" />
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="uploadPaymentAttachmentsBtn" class="btn btn-light">Upload</button>
                        </div>
                    </div>
                    <div id="paymentAttachmentsMessage" class="mb-2"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File Name</th>
                                    <th>Uploaded On</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="paymentAttachmentsList">
                                <tr>
                                    <td colspan="5" class="text-center">No attachments yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatDMY(dateString) {
            var d = new Date(dateString);
            if (isNaN(d.getTime())) return '';
            var dd = String(d.getDate()).padStart(2, '0');
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var yyyy = d.getFullYear();
            return dd + '/' + mm + '/' + yyyy;
        }

        function renderPaymentAttachments(attachments, readOnly) {
            var $tbody = $('#paymentAttachmentsList').empty();
            if (!attachments || attachments.length === 0) {
                $tbody.html('<tr><td colspan="5" class="text-center">No attachments found.</td></tr>');
                return;
            }
            attachments.forEach(function (att, index) {
                var viewUrl = '{{ url("payment/attachments") }}'.replace('/payment/attachments', '/payment/attachments/' + att.id + '/download');
                var attachedDate = att.created_at ? formatDMY(att.created_at) : '';
                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + $('<div>').text(att.file_name).html() + '</td>' +
                    '<td>' + attachedDate + '</td>' +
                    '<td class="text-center"><div class="d-flex justify-content-center align-items-center gap-1">' +
                    '<a href="' + viewUrl + '" target="_blank" class="btn btn-sm btn-light" title="View">' +
                    '<i class="ico icon-outline-eye" style="font-size:16px;"></i>' +
                    '</a>' +
                    (readOnly ? '' : '<button type="button" class="btn btn-sm btn-light text-danger delete-payment-attachment-btn" data-id="' + att.id + '" title="Delete">' +
                        '<i class="ico icon-outline-trash-bin-trash" style="font-size:16px;"></i>' +
                        '</button>') +
                    '</div></td>' +
                    '</tr>';
                $tbody.append(row);
            });
        }

        function setTempAttachmentHiddenInputs(attachments) {
            var $container = $('#paymentAttachmentHiddenInputs').empty();
            if (!attachments || attachments.length === 0) {
                return;
            }
            attachments.forEach(function (att) {
                if (att.id) {
                    $container.append('<input type="hidden" name="temp_attachment_ids[]" value="' + att.id + '">');
                }
            });
        }

        function fetchAndRenderPaymentAttachments(paymentId, readOnly) {
            var url = '{{ url("payment") }}/' + paymentId + '/attachments';
            $.get(url, function (response) {
                if (response.success) {
                    renderPaymentAttachments(response.attachments, readOnly);
                    if (!readOnly) {
                        setTempAttachmentHiddenInputs(response.attachments);
                    }
                } else {
                    toastr.error('Unable to load attachments.');
                }
            }).fail(function () {
                toastr.error('Unable to fetch attachments.');
            });
        }

        $('#paymentAttachmentsBtn, .paymentAttachmentsMenu').on('click', function (e) {
            if (e) e.preventDefault();
            var paymentId = parseInt($('#payment_id').val() || 0, 10);
            var readOnly = false;
            $('#paymentAttachmentsMessage').html('');
            $('#attachmentsUploadSection').show();
            $('#paymentAttachmentsModal').modal('show');
            fetchAndRenderPaymentAttachments(paymentId, readOnly);
        });

        $('#uploadPaymentAttachmentsBtn').on('click', function () {
            var paymentId = parseInt($('#payment_id').val() || 0, 10);
            var files = $('#paymentAttachmentsFiles')[0].files;
            if (!files.length) {
                toastr.warning('Please choose at least one file.');
                return;
            }

            var formData = new FormData();
            formData.append('sys_payment_id', paymentId);
            for (var i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
            $.ajax({
                url: '{{ url("payment/attachments/upload") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (response) {
                    if (response.success) {
                        toastr.success('Attachments uploaded successfully');
                        $('#paymentAttachmentsFiles').val('');
                        fetchAndRenderPaymentAttachments(paymentId, false);
                    } else {
                        toastr.error(response.message || 'Upload failed.');
                    }
                },
                error: function (xhr) {
                    var err = 'Upload failed.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        err = Object.values(xhr.responseJSON.errors).map(function (v) { return v.join(', '); }).join(' | ');
                    } else if (xhr.responseText) {
                        err = xhr.status + ' ' + xhr.statusText + ': ' + xhr.responseText;
                    } else {
                        err = xhr.status + ' ' + xhr.statusText;
                    }
                    toastr.error(err);
                }
            });
        });

        $(document).on('click', '.delete-payment-attachment-btn', function () {
            var id = $(this).data('id');
            if (!confirm('Delete this attachment?')) return;
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
            $.ajax({
                url: '{{ url("payment/attachments") }}/' + id + '/delete',
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (response) {
                    if (response.success) {
                        toastr.success('Attachment deleted.');
                        fetchAndRenderPaymentAttachments(parseInt($('#payment_id').val() || 0, 10), false);
                    } else {
                        toastr.error('Unable to delete attachment.');
                    }
                },
                error: function () {
                    toastr.error('Unable to delete attachment.');
                }
            });
        });
    </script>

            <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

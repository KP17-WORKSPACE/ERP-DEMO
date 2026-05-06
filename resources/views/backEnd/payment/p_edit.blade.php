    <?php try { ?>

        
        <input type="hidden" id="currency1" value="{{ $currency1 }}" />
        <input type="hidden" id="currency2" value="{{ $currency2 }}" />

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'payment-create-form']) }}
            
            <input type="hidden" id="bankpayment_process_id" name="process_id" value="{{Auth::user()->id . date("YmdHis")}}">
            <input type="hidden" id="payment_id" value="{{ isset($editData) ? $editData->id : 0 }}">
            <input type="hidden" name="cheque_id" id="cheque_id" value="{{ isset($editData) ? $editData->cheque_id : 0 }}">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
            



    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
        <h4 class="purchase-order-content-header-left">
            Edit (<span id="header_doc_number">{{ $editData->doc_number }}</span>)
        </h4>
        <div class="purchase-order-content-header-right">
                <a class="btn btn-light text-dark" href="{{url('payment/' . $editData->id . '/?pr_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>            
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
             <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
               <ul class="dropdown-menu">
                    <li><a class="dropdown-item paymentAttachmentsMenu" href="#"><i class="ico icon-outline-paperclip text-success"></i> Attachments</a></li>
                    @if(!empty($editData->cheque_id))
                        <li><a class="dropdown-item" href="{{ url('payment-cheque-print/'.$editData->cheque_id) }}" target="_blank"><i class="ico icon-outline-printer-2 text-success"></i> Print Cheque</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{url('stl')}}"><i class="ico icon-outline-document-text text-success"></i> STL</a></li>
                    <li><a class="dropdown-item" href="{{url('chequebook')}}"><i class="ico icon-outline-document-text text-success"></i> Cheque Book</a></li>
                    <li><a class="dropdown-item" href="{{url('payment-cheque-list')}}"><i class="ico icon-outline-document-text text-success"></i> Cheques</a></li>
             
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
                                                <input type="hidden" name="actual_mode" id="actual_mode" value="{{ $editData->mode }}">
                                                <select class="form-control" name="mode" id="mode" required>
                                                    <option value="1" @if($editData->mode == 1) selected @endif>Cash</option>
                                                    <option value="2" @if($editData->mode == 2) selected @endif>Bank</option>
                                                </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                                <?php
    $invno_cash = @App\SysHelper::get_new_code('sys_payment', 'CP', 'doc_number');
    $invno_bank = @App\SysHelper::get_new_code('sys_payment', 'BP', 'doc_number');
                                                ?>
                                                <input type="hidden" name="new_invno_cash" id="new_invno_cash" value="{{ $invno_cash }}">
                                                <input type="hidden" name="new_invno_bank" id="new_invno_bank" value="{{ $invno_bank }}">
                                            </div>
                                        </div>
                                        <script>
                                  $(document).ready(function () {

// delegated handler: works if #mode is added/removed dynamically
                                            $(document).on('change', '#mode', function() {
                                                var mode = $(this).val();
                                                if (mode == 1) {
                                                    // Cash
                                                    $('#payment_mode_cash').prop('required', true);
                                                    $('#payment_mode_bank').prop('required', false);
                                                    $('#payment_mode_cash').css("display", "block");
                                                    $('#payment_mode_bank').css("display", "none");
                                                    $('#div_payment_through').css("display", "none");
                                                    $('#doc_number').val($('#cash_doc_number').val());
                                                    $('#btn_submit').text('Update Cash Payment');

                                                    $('#bill_wise_heading').text('@lang("Cash Amount")');

                                                    $('#div_cheque_date').css("display", "none");
                                                    $('#div_cheque_number').css("display", "none");
                                                    $('#div_payment_days').css("display", "none");
                                                    $('#cheque_number').prop('required', false);
                                                    $('#cheque_date').prop('required', false);

                                                } else {
                                                    // Bank
                                                    $('#payment_mode_cash').prop('required', false);
                                                    $('#payment_mode_bank').prop('required', true);
                                                    $('#payment_mode_cash').css("display", "none");
                                                    $('#payment_mode_bank').css("display", "block");
                                                    $('#div_payment_through').css("display", "");
                                                    $('#doc_number').val($('#bank_doc_number').val());
                                                    $('#btn_submit').text('Update Bank Payment');
                                                    $('#add_cheque_btn').show();

                                                    $('#bill_wise_heading').text('@lang("Bank Transfer Amount")');

                                                }

                                                $('#payment_through').change();

                                                if (mode != $('#actual_mode').val()) {
                                                    if (mode == 1) {
                                                        $('#doc_number_cash').val($('#new_invno_cash').val()).show();
                                                        $('#doc_number_bank').hide();
                                                        $('#doc_number').val($('#new_invno_cash').val());
                                                    }
                                                    if (mode == 2) {
                                                        $('#doc_number_bank').val($('#new_invno_bank').val()).show();
                                                        $('#doc_number_cash').hide();
                                                        $('#doc_number').val($('#new_invno_bank').val());
                                                    }
                                                } else {
                                                    if (mode == 1) {
                                                        $('#doc_number_cash').val($('#cash_doc_number').val()).show();
                                                        $('#doc_number_bank').hide();
                                                        $('#doc_number').val($('#cash_doc_number').val());
                                                    }
                                                    if (mode == 2) {
                                                        $('#doc_number_bank').val($('#bank_doc_number').val()).show();
                                                        $('#doc_number_cash').hide();
                                                        $('#doc_number').val($('#bank_doc_number').val());
                                                    }
                                                }

                                                $('#header_doc_number').text($('#doc_number').val());
                                            });

                                            var existingChequeNumber = '{{ isset($editData) ? @$editData->cheque_number : "" }}';
                                            var existingChequebookId = '{{ isset($editData) ? @$editData->chequebook_id : "" }}';
                                            var originalBankId = $('#payment_mode_bank').val();

                                            function fetchNextAvailableCheque(bankId) {
                                                var $label = $('#chequebook_label');

                                                if (!bankId) {
                                                    if (!existingChequeNumber) {
                                                        $('#cheque_number').val('');
                                                    }
                                                    $('#chequebook').val('');
                                                    if ($label) $label.hide();
                                                    return;
                                                }

                                                if (existingChequeNumber && bankId === originalBankId) {
                                                    // on edit page keep DB cheque number until user actively selects new bank
                                                    if ($label && existingChequebookId) {
                                                        // preserve existing label exactly; avoid repeated "Book: " prefix stacking
                                                        $label.show();
                                                    }
                                                    return;
                                                }

                                                if ($label) {
                                                    $label.text('Loading...').show();
                                                }

                                                $.ajax({
                                                    url: '{{ url("api/next-available-cheque") }}/' + bankId,
                                                    type: 'GET',
                                                    dataType: 'json',
                                                    success: function(response) {
                                                        if (response.success) {
                                                            $('#cheque_number').val(response.cheque_number);
                                                            $('#chequebook').val(response.chequebook_id || '');
                                                            $('#cheque_id').val(response.chequebook_id || '0');
                                                            if ($label) {
                                                                var range = '';
                                                                if (response.chequebook_start_no && response.chequebook_end_no) {
                                                                    range = ' (' + response.chequebook_start_no + ' - ' + response.chequebook_end_no + ')';
                                                                }
                                                                $label.text('Book: ' + response.chequebook_doc + range).show();
                                                            }
                                                        } else {
                                                            $('#cheque_number').val('');
                                                            $('#chequebook').val('');
                                                            if ($label) {
                                                                $label.text(response.message || 'No cheques available').show();
                                                            }
                                                        }
                                                        existingChequeNumber = '';
                                                    },
                                                    error: function() {
                                                        $('#cheque_number').val('');
                                                        if ($label) {
                                                            $label.text('Error loading cheque number').show();
                                                        }
                                                    }
                                                });
                                            }

                                            $(document).on('change', '#payment_through', function() {
                                                var paymentthrough = $(this).val();
                                                var curMode = $('#mode').val();

                                                if (paymentthrough == 1 || curMode != 2) {
                                                    $('#div_cheque_date').css('display', 'none');
                                                    $('#div_cheque_number').css('display', 'none');
                                                    $('#div_payment_days').css('display', 'none');
                                                    $('#cheque_number').prop('required', false);
                                                    $('#cheque_date').prop('required', false);
                                                    $('#bill_wise_heading').text('@lang("Bank Transfer Amount")');
                                                    return;
                                                }

                                                if (paymentthrough == 2 || paymentthrough == 3) {
                                                    $('#div_cheque_date').css('display', '');
                                                    $('#div_cheque_number').css('display', '');
                                                    $('#div_payment_days').css('display', '');
                                                    $('#cheque_number').prop('required', true);
                                                    $('#cheque_date').prop('required', true);
                                                    $('#bill_wise_heading').text('@lang("Cheque Amount")');

                                                    var bankId = $('#payment_mode_bank').val();
                                                    if (bankId) {
                                                        fetchNextAvailableCheque(bankId);
                                                    }
                                                    return;
                                                }

                                            });

                                            function fetchChequebookByChequeNumber(bankId, chequeNumber) {
                                                if (!bankId || !chequeNumber) {
                                                    $('#chequebook').val('');
                                                    $('#cheque_id').val('0');
                                                    $('#chequebook_label').text('').hide();
                                                    return;
                                                }

                                                // Show loading state while async lookup runs
                                                $('#chequebook_label').text('Looking up...').show();

                                                $.ajax({
                                                    url: '{{ url("api/find-chequebook") }}/' + bankId + '/' + encodeURIComponent(chequeNumber),
                                                    type: 'GET',
                                                    dataType: 'json',
                                                    success: function(response) {
                                                        if (response.success) {
                                                            $('#chequebook').val(response.chequebook_id);
                                                            $('#cheque_id').val(response.chequebook_id);
                                                            $('#chequebook_label').text('Book: ' + response.chequebook_doc + ' (' + response.chequebook_start_no + ' - ' + response.chequebook_end_no + ')').show();
                                                        } else {
                                                            $('#chequebook').val('');
                                                            $('#cheque_id').val('0');
                                                            $('#chequebook_label').text(response.message || 'No matching chequebook').show();
                                                        }
                                                    },
                                                    error: function() {
                                                        $('#chequebook_label').text('Cheque lookup failed').show();
                                                    }
                                                });
                                            }

                                            $(document).on('change', '#cheque_number', function() {
                                                var chequeNumber = $(this).val().trim();
                                                var bankId = $('#payment_mode_bank').val();
                                                var paymentthrough = $('#payment_through').val();

                                                if (bankId && chequeNumber && $('#mode').val() == 2 && (paymentthrough == 2 || paymentthrough == 3)) {
                                                    fetchChequebookByChequeNumber(bankId, chequeNumber);
                                                }
                                            });

                                            $(document).on('change', '#payment_mode_bank', function() {
                                                var bankId = $(this).val();
                                                var paymentthrough = $('#payment_through').val();
                                                if ($('#mode').val() == 2 && (paymentthrough == 2 || paymentthrough == 3)) {
                                                    if (bankId !== originalBankId) {
                                                        existingChequeNumber = '';
                                                    }
                                                    originalBankId = bankId;
                                                    fetchNextAvailableCheque(bankId);
                                                }
                                            });

                                            // Ensure handlers run on page load
                                            $('#mode').trigger('change');
                                            $('#payment_through').trigger('change');

                                            var initialBankId = $('#payment_mode_bank').val();
                                            if (initialBankId && $('#mode').val() == 2 && ($('#payment_through').val() == 2 || $('#payment_through').val() == 3)) {
                                                fetchNextAvailableCheque(initialBankId);
                                            }

                                        });

                                        </script>

 <div class="col mb-4" id="div_payment_through" @if ($editData->mode == 1) style="display: none;" @endif>
                                <label>@lang('Payment Through')<span>*</span></label>
                                <div class="form-group">
                                <select class="form-control" name="payment_through" id="payment_through">
                                        <option value="1" @if ($editData->payment_through == 1) selected @endif>Bank Transfer</option>
                                        {{--  <option value="2" @if ($editData->payment_through == 2) selected @endif>CDC Cheque</option>  --}}
                                        <option value="3" @if ($editData->payment_through == 3) selected @endif>Cheque</option>
                                </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                </div>
                            </div>
                            <script>
    // delegated handler: works for dynamically inserted #payment_through
    $(document).on('change', '#payment_through', function() {
        var paymentthrough = $(this).val();
        if (paymentthrough == 1) {
            $('#div_cheque_date').css("display", "none");
            $('#div_cheque_number').css("display", "none");
            $('#div_payment_days').css("display", "none");
            $('#cheque_number').prop('required', false);
            $('#cheque_date').prop('required', false);
            $('#bill_wise_heading').text('@lang("Bank Transfer Amount")');

        }
        if (paymentthrough == 2 || paymentthrough == 3) {
            $('#div_cheque_date').css("display", "");
            $('#div_cheque_number').css("display", "");
            $('#div_payment_days').css("display", "");
            $('#cheque_number').prop('required', true);
            $('#cheque_date').prop('required', true);
            $('#bill_wise_heading').text('@lang("Cheque Amount")');

        }
    });

   
                            </script>

                                        <div class="col">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">
                                                <input type="hidden" id="cash_doc_number" value="{{ $editData->doc_number }}" />
                                                <input type="hidden" id="bank_doc_number" value="{{ $editData->doc_number }}" />

                                                <!-- visible inputs (mirror receipt behaviour) -->
                                                <input class="form-control" @if($editData->mode == 1) style="display: block" @else style="display: none" @endif type="text" id="doc_number_cash" name="doc_number" value="{{ $editData->doc_number }}" readonly>
                                                <input class="form-control" @if($editData->mode == 2) style="display: block" @else style="display: none" @endif type="text" id="doc_number_bank" name="doc_number" value="{{ $editData->doc_number }}" readonly>

                                                <!-- hidden JS-friendly field (keeps existing scripts working) -->
                                                <input type="hidden" id="doc_number" value="{{ $editData->doc_number }}" />
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Payment Mode</label>
                                            <div class="form-group">
                                            <select class="form-control" name="payment_mode_cash" id="payment_mode_cash" @if ($editData->mode == 2) style="display: none;" @endif>
                                                    
                                                    @if(isset($paymentmode_cash))
                                                        @foreach ($paymentmode_cash as $val)
                                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->payment_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <select class="form-control" name="payment_mode_bank" id="payment_mode_bank" @if ($editData->mode == 1) style="display: none;" @endif>
                                                    
                                                    @if(isset($paymentmode_bank))
                                                        @foreach ($paymentmode_bank as $val)
                                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->payment_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Doc Date</label>
                                            <div class="form-group">
                                             @php
                                                $value = date('d/m/Y');
                                                if (isset($editData) && !empty($editData->doc_date)) {
                                                    $value = date('d/m/Y', strtotime($editData->doc_date));
                                                } else {
                                                    if (!empty(old('doc_date'))) {
                                                        $value = date('d/m/Y', strtotime(old('doc_date')));
                                                    } else {
                                                        $value = date('d/m/Y');
                                                    }
                                                }
                                            @endphp

                                                <input class="form-control date-picker" id="doc_date" type="text" name="doc_date" value="{{ @$value }}">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group">
                                                <select class="form-control" name="currency" id="currency">
                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($edit) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
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
                                                <input
                                                    class="form-control"
                                                    type="text" name="createdby" autocomplete="off" id="created_by"
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
                                        <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">

   
                <div class="col-12 mb-2">
                    <div class="row gap-rows">
                       

                            
                            
                        <div class="col-1-5 mb-4" id="div_payment_days" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <label>@lang('No of Days')<span>*</span></label>
                                <input class="form-control" type="number" name="payment_days" id="payment_days" value="{{ @$editData->no_days }}" onchange="days_fun()">
                                <script>
                                    function days_fun()
                                    {
                                        var daysToAdd = parseInt($('#payment_days').val());
                                        if (isNaN(daysToAdd) || daysToAdd <= 0) {
                                            alert("Please enter a valid positive number of days.");
                                            return;
                                        }
                                        var currentDate = new Date();
                                        currentDate.setDate(currentDate.getDate() + daysToAdd);
                                        var formattedDate = currentDate.toISOString().split('T')[0];
                                        $('#cheque_date').val(formattedDate);
                                        $('#payment_date').val(formattedDate);
                                    }
                                </script>
                            </div>

                            
                            <div class="col-1-5 mb-4" id="div_cheque_date" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Cheque Date')</label>
                                         @php
                                            $value = date('d/m/Y');
                                            if (isset($editData) && !empty($editData->cheque_date)) {
                                                $value = date('d/m/Y', strtotime($editData->cheque_date));
                                            }
                                        @endphp
                                            <input class="form-control date-picker" id="cheque_date" type="text" name="cheque_date" value="{{ @$value }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-2 mb-4" id="div_cheque_number" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <div class="input-effect">
                                    <label>  @lang('Cheque Number') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_number" name="cheque_number"
                                        placeholder="Auto-assigned"
                                        value="{{ isset($editData) ? @$editData->cheque_number : old('cheque_number') }}">
                                    <small class="text-muted" id="chequebook_label" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">Book : {{ isset($editData) ? @$editData->chequebook->doc_number : '' }} ({{ isset($editData) ? @$editData->chequebook->start_no : '' }} - {{ isset($editData) ? @$editData->chequebook->end_no : '' }})</small>
                                    <input type="hidden" name="chequebook" id="chequebook" value="{{ isset($editData) ? @$editData->chequebook_id : '' }}">
                                </div>
                            </div>

                             <div class="col-2 mb-4" id="div_cheque_status" @if ($editData->payment_through == 1) style="display: none;" @endif>
                                <div class="input-effect">
                                    <label> @lang('Status') <span>*</span> </label>
                                    <select class="form-control js-example-basic-single" name="cheque_status" id="cheque_status">
                           <option value="4" @if ($editData->cheque_status == 4) selected @endif>Issued</option>
                            <option value="2" @if ($editData->cheque_status == 2) selected @endif>Cleared</option>
                            <option value="1" @if ($editData->cheque_status == 1) selected @endif>Cancelled</option>
                            <option value="3" @if ($editData->cheque_status == 3) selected @endif>Missed</option>
                                      
                                    </select>
                                </div>
                            </div>

                          

                            <div class="col-1-5 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Payment Date') <span>*</span> </label>
                                <input class="form-control date-picker" 
                                    type="text" 
                                    id="payment_date" 
                                    name="payment_date" 
                                    value="{{ isset($editData) && !empty($editData->payment_date)
        ? date('d/m/Y', strtotime($editData->payment_date))
        : (old('payment_date') ? date('d/m/Y', strtotime(old('payment_date'))) : '') }}" 
                                    required>

                                </div>
                            </div>
                             <div class="col-1-5 mb-4">
                                <div class="input-effect">
                                    @php
                                        //comma seperated deal ids
                                        $deal_ids = explode(',', $editData->deal_id);
                                    @endphp
                                    
                                    <label>@lang('Deal ID')<span>*</span></label>
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="@foreach ($deal_ids as $deal_id){{ @App\SysHelper::get_code_from_dealid($deal_id) }}@if (!$loop->last), @endif @endforeach ">
                                </div>
                            </div>
                            <div class="col-2 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Remarks') <span></span></label>
                                    <input
                                        class="form-control"
                                        type="text" name="narration" autocomplete="off"
                                        value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                        id="narration">
                                </div>
                            </div>
                           

                                            


                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-container" style="border: solid 1px #d9d9d9;">
                                <table class="table table-hover form-item-table" id="myTable">
                                    <thead>                                                            
                                        <tr>
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="450px">@lang('Account Name') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Amount')<div class="resizer"></div></th>
                                            <th class="resizable text-center">@lang('Narration')<div class="resizer"></div></th>
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
                                        <tr>
                                                <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                                <td class="noborder">
                                                    <select class="form-control" name="account_id[]">
                                                        <option value="{{ @$editDataList[$roid - 1]->account_id }}">{{ @$editDataList[$roid - 1]->accounts->account_name }} 
                                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                                ({{ @$editDataList[$roid - 1]->accounts->supplier_code }})
                                                                
                                                            @endif
                                                            
                                                        </option>
                                                    </select>
                                                </td> 
                                                <td>
                                                    <input class="form-control text-end" type="decimal" name="amount[]"  data-enter-skip autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->debit_amount,2,'.','') }}" onchange="update_totals()" onblur="formatCurrency(this)">
                                                </td>
                                                <td><input type="text" class="form-control" name="remarks[]" value="{{ @$editDataList[$roid - 1]->remarks }}"></td>
                                            </tr>
                                    @endfor
                                    <script>
$(document).ready(function() {
    $('input[name="amount[]"]').each(function() {
        update_totals();
        formatCurrency(this);
    });
});
</script>


                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]">
                                                <option value=""></option>
                                            </select>
                                            </td> 
                                            <td>                                                                    
                                                <input class="form-control text-end" type="decimal" name="amount[]" autocomplete="off" onchange="update_totals()" onblur="formatCurrency(this)">

                                            </td>
                                            <td><input type="text" class="form-control" name="remarks[]"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" scope="col" >Total</th>
                                            <th class="text-end"><label id="lbl_total_amount" >0</label></th>
                                            <th class="text-end" scope="col" ></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>
                            {{ Form::close() }}

                <div class="row mt-40">
                    <div class="col-lg-12 text-left mb-2">
                        <b>Adjusted Items</b>
                            <table class="table table-hover " id="long-list" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;" class="text-center">@lang('#')</th>
                                        <th style="width:100px;" class="text-center">@lang('Deal ID')</th>
                                        <th style="width:100px;" class="text-center">@lang('Doc Number')</th>
                                        <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                        <th style="width:100px;" class="text-center">@lang('LPO NO')</th>
                                        <th style="width:100px;" class="text-center">@lang('Bill NO')</th>
                                        <th style="width:100px;" class="text-end">Total</th>
                                        <th style="width:100px;" class="text-end">Paid</th>
                                        <th style="width:100px;" class="text-end">Balance</th>
                                        <th style="width:100px;" class="text-end">Adjusted</th>
                                        <th style="width:100px;" class="text-end">Unadjusted</th>
                                        <th style="width:100px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @if(count($editDataAdjustments) > 0)
                            @foreach ($editDataAdjustments as $item)
                            @php
                                $pi = @App\SysPurchaseInvoice::where('doc_number', $item->bi_doc_no)->first();
                                $deal_code = @App\SysHelper::get_code_from_dealid(@$pi->deal_id);
                            @endphp
                            
                                <tr>
                                    <td class="text-center">{{ @$loop->iteration }}</td>
                                <td class="text-center"><a href="{{ url('get-url-deal',$deal_code) }}">{{ @$deal_code }}</a></td>
                                    <td class="text-center">{{ @$item->bi_doc_no }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$item->bi_doc_date)) }}</td>
                                    <td class="text-center">{{ @$item->bi_lpo_no }}</td>
                                    <td class="text-center">{{ @$item->bi_bill_number }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_total,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_paid,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_balance_to_adjust,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_amount,2,'.',',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_cheque_amount - @$item->bi_amount_adjusted,2,'.',',') }}</td>
                                    <td class="text-center"><a class="btn-sm btn-light" onclick="return delete_adjestments({{ $item->id }});"><i class="ico ico ico icon-outline-trash-bin-minimalistic text-darkphp -S text-dark" style="font-size: 16px;"></i></a></td>
                                </tr>
                            @endforeach
                        @endif
                                </tbody>
                            </table>
                    </div>
                </div>

                <script>
                    
    function delete_adjestments(id) {
        var action = "{{ URL::to('delete-payment-adjustment-json') }}";

         if (!confirm('Are you sure you want to delete this item?')) {
        return false; // Cancelled
    }
        
        $("#loading_bg").css("display", "block");
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id:id,
                doc_number : $('#doc_number').val(),

            },
            cache: false,
           success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                var decimalPoint = @json(session('logged_session_data.decimal_point'));

                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }

                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var item = dataResult['data'][i];
                        getSelectedRows += "<tr>\
                            <td>" + (i + 1) + "</td>\
                            <td>" + (item.bi_doc_no || '') + "</td>\
                            <td>" + (item.bi_doc_date || '') + "</td>\
                            <td>" + (item.bi_lpo_no || '') + "</td>\
                            <td class='text-end'>" + formatAmount(item.bi_total, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatAmount(item.bi_paid, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatAmount(item.bi_balance_to_adjust, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatAmount(item.bi_amount, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatAmount((item.bi_cheque_amount - item.bi_amount_adjusted), decimalPoint) + "</td>\
                            <td class='text-end'>\
                                <a class='btn-sm btn-danger' onclick='return delete_adjestments(" + item.id + ")' >\
                                    <i class='fa fa-trash' aria-hidden='true'></i>\
                                </a>\
                            </td>\
                        </tr>";
                    }
                        $('#adjustment-table tbody').empty();
                        $("#adjustment-table tbody").append(getSelectedRows); 
                        $('#narration').val('');
                        $('#deal_id').val('');
                        $("input[name='amount[]']").val('');
                        $("input[name='remarks[]']").val('');
                    toastr.success('Adjustments Deleted Successfully');
                    location.reload();
                } else {
                    $('#adjustment-table tbody').empty();
                    location.reload();
                    //alert('Error: Something went wrong!');
                }
                $("#loading_bg").css("display", "none");
                $('#btn_adj_close').click();
            }
        });
    }
    </script>

{{-- Models  --}}
<!-- <a ></a> -->
<button type="button" data-bs-toggle="modal" data-bs-target="#cr_popup_win" id="addCtrlPaymentAdjestEdit" hidden></button>
<div class="modal side-panel fade" id="cr_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Bill Wise Selection</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payables-outstanding-store-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'receipt-create-form']) }}
                    <input type="hidden" id="br_account_id" name="br_account_id">                           
                    <input type="hidden" id="br_account_id_amount" name="br_account_id_amount">
                    <input type="hidden" name="bi_currency2" value="{{ $editData->currency }}" />
                    <input type="hidden" name="doc_number2" value="{{ $editData->doc_number }}" />
                    <input type="hidden" name="transaction_type2" value="@if($editData->mode == 1) cashpayment @else bankpayment @endif" />
					<div class="card-body">
                                
                                <div class="row">
                                    <div class="col mb-20">
                                        <div class="input-effect">
                                            <label id="bill_wise_heading">  @lang('Cheque Amount') <span>*</span> </label>
                                            <input class="primary-input form-control text-end" type="text" id="bi_cheque_amount" name="bi_cheque_amount" value="0" >
                                            <span class="focus-border"></span>
                                            <!-- <span class="modal_input_validation_2 red_alert"></span>                                     -->
                                        </div>
                                    </div>
                                    <div class="col mb-20">
                                        <div class="input-effect">
                                            <label>  @lang('Amount Adjusted') <span>*</span> </label>
                                            <input class="primary-input form-control text-end" type="text" id="bi_amount_adjusted" name="bi_amount_adjusted" value="0" >
                                            <span class="focus-border"></span>
                                            <span class="modal_input_validation_3 red_alert"></span>
                                            
                                            <input type="hidden" id="bi_balance_adjest" value="">

                                        </div>
                                    </div>
                                    <div class="col mb-20">
                                        <div class="input-effect">
                                            <label>  @lang('Balance to Adjust') <span>*</span> </label>
                                            <input class="primary-input form-control text-end" type="text" id="bi_extra_amount" name="bi_extra_amount" value="0" >                                    
                                            <div style="display: none;">
                                            <input class="primary-input form-control text-end" type="text" id="bi_balance_to_adjust" name="bi_balance_to_adjust" value="0" ></div>
                                            <span class="focus-border"></span>
                                            <!-- <span class="modal_input_validation_2 red_alert"></span>                                     -->
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
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table class="table table-hover form-item-table data-table-bill" cellspacing="0" width="100%" id="crListBankBookAdjest">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;" class="text-start">&nbsp; @lang('Deal ID')</th>
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
        var v = Number(($('#bi_amount_' + i).val() || '0').replace(/,/g, ''));
        if (v > 0) {
            docs.push($('#bi_doc_no_' + i).val() || '');
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
                        <button type="submit" class="btn btn-light add-btn ms-2" type="submit" onclick="popup_form_submit()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Save
						</button>
                        <script>
                            function popup_form_submit(){
                                $("#loading_bg").css("display", "block");
                            }
                            // Prevent modal form submit when balance-to-adjust is negative
                            $(document).ready(function() {
                                function isBalanceNegative() {
                                    // check multiple fields (formatted values may contain commas)
                                    var raw = $('#bi_balance_adjest').val() || $('#bi_extra_amount').val() || $('#bi_balance_to_adjust').val() || '0';
                                    var num = parseFloat(String(raw).replace(/,/g, '')) || 0;
                                    return num < 0;
                                }

                                // show inline validation + toastr
                                function showBalanceError() {
                                    $('.modal_input_validation_2').text('Adjustment amount cannot exceed cheque/transfer amount').show();
                                    $('#bi_extra_amount').addClass('is-invalid');
                                    toastr.error('Adjustment amount cannot exceed cheque/transfer amount');
                                }

                                // clear validation
                                function clearBalanceError() {
                                    $('.modal_input_validation_2').hide().text('');
                                    $('#bi_extra_amount').removeClass('is-invalid');
                                }

                                // Modal form (Save inside Bill Wise modal)
                                $('#receipt-create-form').on('submit', function(e) {
                                    if (isBalanceNegative()) {
                                        e.preventDefault();
                                        // hide loading indicator if the button already showed it
                                        $("#loading_bg").css("display", "none");
                                        showBalanceError();
                                        // focus the offending field so user can correct it
                                        $('#bi_extra_amount').focus();
                                        return false;
                                    }

                                    clearBalanceError();
                                    return true;
                                });

                                // Main payment form — block submit if modal balance is negative
                                $('#payment-create-form').on('submit', function(e) {
                                    if (isBalanceNegative()) {
                                        e.preventDefault();
                                        showBalanceError();
                                        // open the modal so user can fix the adjustment
                                        $('#addCtrlPaymentAdjestEdit').prop('disabled', false).click();
                                        $('#cr_popup_win').one('shown.bs.modal', function() { $('#bi_extra_amount').focus(); });
                                        return false;
                                    }
                                    clearBalanceError();
                                    return true;
                                });

                                // keep inline message in sync while user types
                                $(document).on('input change', '#bi_extra_amount, #bi_balance_adjest, #bi_balance_to_adjust', function() {
                                    if (isBalanceNegative()) {
                                        $('.modal_input_validation_2').show();
                                    } else {
                                        clearBalanceError();
                                    }
                                });
                            });                        </script>
					</div>
    {{ Form::close() }}
              	</div>
            </div>
        </div>


<script>
  // Run cr_popup_fun only once (no repeated bindings)
  $(document).on('keypress', 'input[name="amount[]"]', function (e) {
    if (e.which === 13) {
      const currentInput = $(this);
      const currentRow = currentInput.closest('tr');

      let br_account_id = currentRow.find('select[name="account_id[]"]').val();
      if (br_account_id === null || br_account_id === undefined) {
        br_account_id = currentRow.find('input[name="account_id[]"]').val();
        }
      const br_amount = currentRow.find('input[name="amount[]"]').val();

      if (br_account_id !== "" && br_amount !== "") {
        // set values used by the modal
        $('#br_account_id').val(br_account_id);
        $('#br_account_id_amount').val(br_amount);
        $('#bi_cheque_amount').val(formatAmount(br_amount));

        // initialize modal totals immediately (like p_add_2)
        if (typeof get_set_amount === 'function') { get_set_amount(); }

        // show loading indicator (matches p_add_2) and open modal via the hidden opener
        $("#loading_bg").css("display", "");

        // ensure the hidden opener is enabled for programmatic click, then click it
        $('#addCtrlPaymentAdjestEdit').prop('disabled', false).click();

// focus the cheque amount & initialize totals after modal becomes visible
        $('#cr_popup_win').off('shown.bs.modal.focusAmt').on('shown.bs.modal.focusAmt', function () {
            if (typeof get_set_amount === 'function') { get_set_amount(); }
            $('#bi_cheque_amount').focus();
            $("#loading_bg").css("display", "none");
        });
      } else {
        alert("Account / Amount Missing");
      }

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

  // ensure hidden opener is usable again after modal closes
  $('#cr_popup_win').off('hidden.bs.modal.reenableBtn').on('hidden.bs.modal.reenableBtn', function () {
      $('#addCtrlPaymentAdjestEdit').prop('disabled', false);
  });
</script>


<script>
   update_totals();

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
   const SHOW_SUPPLIER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};

$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_supp_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
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

        $(selector).on('select2:select', function (e) {
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
    /*table row fill based on layout height (limit to 70% of viewport)*/
function fillTableTo70Percent() {
    const table = document.getElementById('myTable');
    if (!table) return;
    const tbody = table.querySelector('tbody');

    // If there are no rows, do nothing
    if (!tbody || tbody.rows.length === 0) return;

    const rowHeight = tbody.rows[0].offsetHeight || 30;

    // target area is 80% of the viewport height
    const viewportHeight = window.innerHeight;
    const targetHeight = Math.floor(viewportHeight * 0.8);

    const tableTop = table.getBoundingClientRect().top;
    const availableHeight = Math.max(0, targetHeight - tableTop);

    const existingRows = tbody.rows.length;
    const totalRows = Math.max(existingRows, Math.floor(availableHeight / rowHeight));

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
}

// run on load and on resize (debounced)
window.addEventListener('load', fillTableTo70Percent);
let _resizeTimer;
window.addEventListener('resize', function () {
    clearTimeout(_resizeTimer);
    _resizeTimer = setTimeout(fillTableTo70Percent, 120);
});
/*table row fill based on layout height*/
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
function amount_w(){
    $('#amount_words').val(toWords($('#amount').val()));
}

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

                        <!-- File Input -->
                        <div class="col-md-9">
                            <label class="form-label">Upload files</label>
                            <input type="file" id="paymentAttachmentsFiles" class="form-control" multiple
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" />
                        </div>

                        <!-- Upload Button -->
                        <div class="col-md-3">
                            <button type="button" id="uploadPaymentAttachmentsBtn" class="btn btn-light">
                                Upload
                            </button>
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
                            <tr><td colspan="5" class="text-center">No attachments yet.</td></tr>
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
            var viewUrl = '{{ url("payment/attachments") }}/' + att.id + '/download';
            var attachedDate = att.created_at ? formatDMY(att.created_at) : '';
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + $('<div>').text(att.file_name).html() + '</td>' +
                '<td>' + attachedDate + '</td>' +
              
                '<td class="text-center"><div class="d-flex justify-content-center align-items-center gap-1">' +
                    '<a href="' + viewUrl + '" target="_blank" class="btn btn-sm btn-light" title="View">' +
                        '<i class="ico icon-outline-eye" style="font-size:16px;"></i>' +
                    '</a>' +
                    (readOnly ? '' : '<button type="button" class="btn btn-sm btn-light text-danger delete-payment-attachment-btn" data-id="' + att.id + '" title="Delete"><i class="ico icon-outline-trash-bin-trash" style="font-size:16px;"></i></button>') +
                '</div></td>' +
                '</tr>';
            $tbody.append(row);
        });
    }

    function fetchAndRenderPaymentAttachments(paymentId, readOnly) {
        if (!paymentId || paymentId == 0) {
            renderPaymentAttachments([], readOnly);
            return;
        }
        $.get('{{ url("payment") }}/' + paymentId + '/attachments', function (response) {
            if (response.success) {
                renderPaymentAttachments(response.attachments, readOnly);
            } else {
                $('#paymentAttachmentsMessage').html('<div class="text-danger">Unable to load attachments.</div>');
            }
        }).fail(function () {
            $('#paymentAttachmentsMessage').html('<div class="text-danger">Unable to fetch attachments.</div>');
        });
    }

    $('#paymentAttachmentsBtn, .paymentAttachmentsMenu').on('click', function (e) {
        if (e) e.preventDefault();
        var paymentId = parseInt($('#payment_id').val() || 0, 10);
        var readOnly = false;
        if (!paymentId || paymentId <= 0) {
            $('#paymentAttachmentsMessage').html('<div class="text-warning">Please save the payment first to add attachments.</div>');
            $('#attachmentsUploadSection').hide();
        } else {
            $('#paymentAttachmentsMessage').html('');
            $('#attachmentsUploadSection').show();
        }
        $('#paymentAttachmentsModal').modal('show');
        fetchAndRenderPaymentAttachments(paymentId, readOnly);
    });

    $('#uploadPaymentAttachmentsBtn').on('click', function () {
        var paymentId = parseInt($('#payment_id').val() || 0, 10);
        if (!paymentId || paymentId <= 0) {
            $('#paymentAttachmentsMessage').html('<div class="text-danger">Save Payment first.</div>');
            return;
        }

        var files = $('#paymentAttachmentsFiles')[0].files;
        if (!files.length) {
            $('#paymentAttachmentsMessage').html('<div class="text-warning">Please choose at least one file.</div>');
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
                    toastr.success('Attachments uploaded successfully.');
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
                $('#paymentAttachmentsMessage').html('<div class="text-danger">' + err + '</div>');
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

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
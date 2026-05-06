@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

<div class="content-container col-9">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Cheque
                </h4>
                <div class="purchase-order-content-header-right">
                    <!-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Add Cheque
                    </button> -->
                    <a class="btn btn-light" href="{{url('payment')}}">
                        <i class="ico icon-outline-document-medicine text-success"></i> Payment List
                    </a>
                </div>
            </div>
            @php
            $accounts = @App\SysHelper::get_bank_account();
            $suppliers = @App\SysHelper::get_supplier_list(session('logged_session_data.company_id'));
                
            @endphp
                    <div class="card mb-3" id="card-2">
                    <div class="card-body">
                        <form class="form-horizontal" method="GET" action="{{ url('payment-cheque-list') }}" enctype="multipart/form-data">
                            <div class="row">
                            <div class="col-1-5 mb-20">
                                <div class="input-effect">
                                    <label>@lang('Bank')</label>
                                    <select class="form-control js-example-basic-single" name="bank_name" >
                                        <option value="">All Banks</option>
                                    @foreach ($accounts as $val)
                                            <option value="{{ @$val->id }}" @if(isset($selectedBankId) && $selectedBankId == $val->id) selected @endif>{{ @$val->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-1-5 mb-20">
                                <div class="input-effect">
                                    <label>@lang('Cheque Book')</label>
                                    <select class="form-control js-example-basic-single" name="cheque_id" >
                                        <option value="">All Chequebooks</option>
                                        @foreach ($chequebook as $ch)
                                            <option value="{{ @$ch->id }}" @if(isset($selectedChequeId) && $selectedChequeId == $ch->id) selected @endif>{{ @$ch->doc_number }} ({{ @$ch->start_no }}-{{ @$ch->end_no }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                             <div class="col-1 mb-20">
                                <div class="input-effect">
                                    <label>@lang('From Date')</label>
                                    <input class="primary-input date-picker form-control" id="from_date" type="text" name="from_date" value="{{ isset($from_date) ? $from_date : '' }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-1 mb-20">
                                <div class="input-effect">
                                    <label>@lang('To Date')</label>
                                    <input class="primary-input date-picker form-control" id="to_date" type="text" name="to_date" value="{{ isset($to_date) ? $to_date : '' }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-1-5 mb-2 filter-field">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control js-example-basic-single" name="sort_id" id="sort_id"
                                    onchange="this.form.submit()">
                                    <option value="" {{ empty($selectedSortId) ? 'selected' : '' }}>-Select-</option>
                                  
                                    <option value="1" {{ $selectedSortId == 1 ? 'selected' : '' }}>Today</option>
                                    <option value="2" {{ $selectedSortId == 2 ? 'selected' : '' }}>This Week</option>
                                    <option value="3" {{ $selectedSortId == 3 ? 'selected' : '' }}>Last Week</option>
                                    <option value="4" {{ $selectedSortId == 4 ? 'selected' : '' }}>This Month</option>
                                    <option value="5" {{ $selectedSortId == 5 ? 'selected' : '' }}>Last Month</option>
                                    <option value="6" {{ $selectedSortId == 6 ? 'selected' : '' }}>Last 6 Months</option>
                                    <option value="7" {{ $selectedSortId == 7 ? 'selected' : '' }}>This Year</option>
                                    <option value="8" {{ $selectedSortId == 8 ? 'selected' : '' }}>Last Year</option>
                    
                                </select>
                            </div>

                                <div class="col-2 mb-20">
                                <div class="input-effect">
                                    <label>@lang('Supplier')</label>
                                    <select class="form-control js-example-basic-single" name="supplier_id" id="supplier_id">
                                        <option value="">Select Suppliers</option>
                                    @foreach ($suppliers as $val)
                                            <option value="{{ @$val->id }}" @if(isset($selectedSupplierId) && $selectedSupplierId == $val->id) selected @endif>{{ @$val->account_name }}

                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                {{ $val->account_code }}
                                            @endif
                                            </option>
                                        @endforeach
                                   
                                    </select>
                                </div>
                            </div>




                            <div class="col-1" style="margin-top:1.4rem">
                                <button class="btn btn-light" id="btnSubmit">
                                    <i class="ico icon-outline-minimalistic-magnifer text-success"
                                        style="font-size: 18px;"></i> Filter
                                </button>
                            </div>
                            <div class="col mb-2">
                                <label for="" class="form-check-label">Search in List</label>
                                <input type="text" id="tableSearch" class="form-control mb-2" placeholder="">
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            <div class="card mb-3 card-min-height">
                <div class="card-body">
                    <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                        <div class="row">
                            <div class="col-12 mb-2" >
                                <table class="table table-hover data-table" width="100%" cellspacing="0">
                        <thead>
                            @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                <tr>
                                    <td colspan="6">
                                        @if (session()->has('message-success-delete'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success-delete') }}
                                            </div>
                                        @elseif(session()->has('message-danger-delete'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger-delete') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th> @lang('Cheque Number')</th>
                                <th> @lang('Cheque Book')</th>
                                <th> @lang('Cheque Status')</th>
                                <th class="text-center"> @lang('Doc Number')</th>
                                <th  class="text-center"> @lang('Doc Date')</th>
                                <th style="min-width: 180px;"> @lang('Bank Name')</th>
                                <th  class="text-center"> @lang('Cheque Date')</th>
                                <th style="min-width: 240px;"> @lang('Supplier Name')</th>
                                <th class="text-end"> @lang('Amount')</th>                        
                                <th class="text-center"> @lang('Payment Doc')</th>
                                
                                <th  class="text-center"> @lang('Deal ID')</th>
                                <th> @lang('Created By')</th>
                                <th width="50px" class="text-center"> @lang('Action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if (isset($data))
                            @foreach ($data as $dt)
                            <tr @if($dt->status == 0) class="bg-dark" @endif>
                                  <td>{{ @$dt->cheque_number }}</td>
                                <td>{{ optional(optional($dt->payment)->chequebook)->doc_number ?: optional($dt->cheque)->doc_number ?: '-' }}</td>
                                <td>
                                    @php
                                        $status = data_get($dt, 'payment.cheque_status', $dt->cheque_status);
                                        $statusText = 'Unknown';
                                        $statusClass = 'badge bg-secondary';
                                        if ($status == 4) { $statusText = 'Issued'; $statusClass = 'badge bg-info'; }
                                        elseif ($status == 2) { $statusText = 'Cleared'; $statusClass = 'badge bg-success'; }
                                        elseif ($status == 1) { $statusText = 'Cancelled'; $statusClass = 'badge bg-danger'; }
                                        elseif ($status == 3) { $statusText = 'Missed'; $statusClass = 'badge bg-warning text-dark'; }
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">{{ @$dt->doc_number }}</td>
                                <td class="text-center">{{date('d/m/Y', strtotime(@$dt->doc_date))}}</td>
                                <td>{{ @$dt->bankname->account_name }}</td>
                                <td class="text-center">{{date('d/m/Y', strtotime(@$dt->cheque_date))}}</td>
                                @if (@$dt->supplier_name == 0)
                                <td>{{ @$dt->other_supplier_name }}</td>
                                @else
                                <td>{{ @$dt->suppliername->account_name }}</td>
                                @endif
                                

                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$dt->amount,2,'.',',') }}</td>
                                <td class="text-center"><a href="{{url('get-url-payment/' . @optional(@$dt->payment)->doc_number)}}" target="_blank">{{ optional(@$dt->payment)->doc_number }}</a></td>
                              
                                <td class="text-center"><a href="{{url('crm-deals/' . @$dt->deal_id . '/view')}}" target="_blank">{{ @$dt->deal_code->code }}</a></td>
                                <td>{{ @$dt->createdby->full_name }}</td>
                                <td class="text-center">
                                    <input type="hidden" id="edit_bank_name_{{ $dt->id }}" value="{{ $dt->bank_name }}"/>
                                    <input type="hidden" id="edit_cheque_number_{{ $dt->id }}" value="{{ $dt->cheque_number }}"/>
                                    <input type="hidden" id="edit_cheque_date_{{ $dt->id }}" value="{{ !empty($dt->cheque_date) ? date('d/m/Y', strtotime($dt->cheque_date)) : '' }}"/>
                                    <input type="hidden" id="edit_supplier_name_{{ $dt->id }}" value="{{ $dt->supplier_name }}"/>
                                    <input type="hidden" id="edit_other_supplier_name_{{ $dt->id }}" value="{{ $dt->other_supplier_name }}"/>
                                    <input type="hidden" id="edit_amount_{{ $dt->id }}" value="{{ $dt->amount }}"/>
                                    <input type="hidden" id="edit_amount_words_{{ $dt->id }}" value="{{ $dt->amount_words }}"/>
                                    <input type="hidden" id="edit_deal_id_{{ $dt->id }}" value="{{ @$dt->deal_code->code }}"/>
                                    <input type="hidden" id="edit_attachment_{{ $dt->id }}" value="{{ $dt->attachment }}"/>
                                    <input type="hidden" id="edit_reference_{{ $dt->id }}" value="{{ $dt->reference }}"/>


                                    <div class="d-flex justify-content-ceter align-items-center gap-1">
                                        <a class="btn btn-sm btn-light" href="{{ url('payment-cheque-print/' . $dt->id) }}" target="_blank" title="Print Cheque"><i class="ico icon-outline-printer-2" style="font-size: 16px;"></i></a>
                                        @if(optional($dt->payment)->id)
                                        <button type="button" class="btn btn-sm btn-light cheque-payment-attachments-btn" data-payment-id="{{ $dt->payment->id }}" title="Payment Attachments"><i class="ico icon-outline-paperclip" style="font-size: 16px;"></i></button>
                                        @endif
                                        @if($dt->attachment != "")
                                            <a class="btn btn-sm btn-light" href="{{url('public/uploads/payment_cheque/'.$dt->attachment)}}" target="_blank"><i class="ico icon-bold-download-minimalistic" style="font-size: 16px;" aria-hidden="true"></i></a>
                                        @endif
                                        <!-- <a class="btn btn-sm btn-light" onclick="edit_cheque({{ @$dt->id}})"><i class="ico icon-outline-pen-2" style="font-size: 16px;" aria-hidden="true"></i></a> -->
                                        @if (@$dt->status == 0)
                                            <a class="btn btn-sm btn-light" href="{{url('payment-cheque/'.$dt->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="ico icon-outline-restart text-dark" style="font-size: 16px;"></i></a>
                                        @else
                                            <a class="btn btn-sm btn-light" href="{{url('payment-cheque/'.$dt->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="ico icon-outline-trash-bin-trash" style="font-size: 16px;" aria-hidden="true"></i></a>
                                        @endif
                                    </div>
                                   

                                  
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




                
<script>
    function edit_cheque(id){
        $('#cid').val(id);
        $('#edit_bank_name').val($('#edit_bank_name_'+id).val());
        $('#edit_cheque_number').val($('#edit_cheque_number_'+id).val());
        $('#edit_cheque_date').val($('#edit_cheque_date_'+id).val());
        $('#edit_supplier_name').val($('#edit_supplier_name_'+id).val());
        $('#edit_other_supplier_name').val($('#edit_other_supplier_name_'+id).val());
        $('#edit_amount').val($('#edit_amount_'+id).val());
        $('#edit_amount_words').val($('#edit_amount_words_'+id).val());
        $('#edit_deal_id').val($('#edit_deal_id_'+id).val());
        $('#edit_attachment').val($('#edit_attachment_'+id).val());
        $('#edit_reference').val($('#edit_reference_'+id).val());
        editsuppliername();
        $('#edit_popup').click();
    }

    $(document).ready(function() {
        $('#amount').on('change', function() {
            let inputValue = $(this).val();
    
            // Allow only numbers and a single decimal point
            inputValue = inputValue.replace(/[^0-9.]/g, '');
    
            // Allow only one decimal point
            const parts = inputValue.split('.');
            if (parts.length > 2) {
                inputValue = parts[0] + '.' + parts.slice(1).join('');
            }
    
            // Handle leading decimal point
            if (inputValue.startsWith('.')) {
                inputValue = '0' + inputValue; // Change ".50" to "0.50"
            }
    
            // Convert to float
            const floatValue = parseFloat(inputValue);
            if (!isNaN(floatValue)) {
                // Ensure two decimal places
                const formattedValue = floatValue.toFixed(@json(session('logged_session_data.decimal_point')));
                // Format with commas without the dollar sign
                const finalValue = parseFloat(formattedValue).toLocaleString('en-US');
    
                // Update the input field with the formatted value
                $(this).val(finalValue);
            } else {
                $(this).val(''); // Clear the input if invalid
            }
        });
    });

    $(document).ready(function() {
        $('#edit_amount').on('change', function() {
            let inputValue = $(this).val();
    
            // Allow only numbers and a single decimal point
            inputValue = inputValue.replace(/[^0-9.]/g, '');
    
            // Allow only one decimal point
            const parts = inputValue.split('.');
            if (parts.length > 2) {
                inputValue = parts[0] + '.' + parts.slice(1).join('');
            }
    
            // Handle leading decimal point
            if (inputValue.startsWith('.')) {
                inputValue = '0' + inputValue; // Change ".50" to "0.50"
            }
    
            // Convert to float
            const floatValue = parseFloat(inputValue);
            if (!isNaN(floatValue)) {
                // Ensure two decimal places
                const formattedValue = floatValue.toFixed(@json(session('logged_session_data.decimal_point')));
                // Format with commas without the dollar sign
                const finalValue = parseFloat(formattedValue).toLocaleString('en-US');
    
                // Update the input field with the formatted value
                $(this).val(finalValue);
            } else {
                $(this).val(''); // Clear the input if invalid
            }
        });
    });

    function suppliername(){
        if($('#supplier_name').val()==0){
            $('#other_supplier_name_div').css('display','');
            $('#other_supplier_name').prop("required", true);
        } else {
            $('#other_supplier_name_div').css('display','none');
            $('#other_supplier_name').prop("required", false);
        }
    }

    function editsuppliername(){
        if($('#edit_supplier_name').val()==0){
            $('#edit_other_supplier_name_div').css('display','');
            $('#edit_other_supplier_name').prop("required", true);
        } else {
            $('#edit_other_supplier_name_div').css('display','none');
            $('#edit_other_supplier_name').prop("required", false);
        }
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
function edit_amount_w(){
    $('#edit_amount_words').val(toWords($('#edit_amount').val()));
}

$(document).ready(function() {
    $('#bank_name').on('change', function () {
        var bankId = $(this).val();
        if (!bankId) {
            $('#cheque_id').html('<option value="">Select Cheque Book</option>');
            return;
        }

        $.ajax({
            url: "{{ url('api/chequebooks-by-bank') }}/" + bankId,
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    var options = '';
                    if (response.data.length > 0) {
                        response.data.forEach(function (item) {
                            options += '<option value="' + item.id + '">' + item.doc_number + ' (' + item.start_no +  ' - ' + item.end_no + ')' + '</option>';
                        });
                    } else {
                        options = '<option value="">No cheque books available</option>';
                    }
                    $('#cheque_id').html(options).trigger('change');
                } else {
                    $('#cheque_id').html('<option value="">Error loading cheque books</option>');
                }
            },
            error: function () {
                $('#cheque_id').html('<option value="">Error loading cheque books</option>');
            }
        });
    });

    $('#bank_name').trigger('change');
});
</script>


        <div class="modal side-panel fade" id="addChequeModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: auto;"> 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Add Cheque</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="model_close"></button>
					</div>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-store','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-form']) }}
                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Bank Name</label>
                            <select class="form-control js-example-basic-single" name="bank_name" autocomplete="off" id="bank_name" required>
                                @if (count($bank)>0)
                                    @foreach ($bank as $b)
                                        <option value="{{ $b->id }}">{{ $b->account_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @php
                        @$chequeBooks = @App\Chequebook::where('bank_id', @$bank[0]->id)->whereNull('deleted_at')->get();
                    @endphp
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Cheque Book</label>
                            <select class="form-control js-example-basic-single" name="cheque_id" autocomplete="off" id="cheque_id" required>
                                @if (count($chequeBooks)>0)
                                    @foreach ($chequeBooks as $cheque)
                                        <option value="{{ $cheque->id }}">{{ $cheque->cheque_number }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Cheque Number</label>
                            <input class="form-control" type="text" name="cheque_number" autocomplete="off" id="cheque_number" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Cheque Date</label>
                            <input class="form-control date-picker" type="text" name="cheque_date" autocomplete="off" id="cheque_date" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Supplier Name</label>
                            <select class="form-control js-example-basic-single" name="supplier_name" autocomplete="off" id="supplier_name" onchange="suppliername()" required>
                                <option>Select</option>
                                @if (count($supplier)>0)
                                    @foreach ($supplier as $b)
                                        <option value="{{ $b->id }}">{{ $b->account_name }}</option>
                                    @endforeach
                                @endif
                                <option value="0">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2" id="other_supplier_name_div" style="display: none;">
                        <div class="form-group">
                            <label for="">Other Supplier Name</label>
                            <input class="form-control" type="text" name="other_supplier_name" autocomplete="off" id="other_supplier_name">
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input class="form-control" type="text" name="amount" autocomplete="off" id="amount" onchange="amount_w()" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Amount in Words</label>
                            <input class="form-control" type="text" name="amount_words" autocomplete="off" id="amount_words" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Deal ID</label>
                            <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Reference</label>
                            <input class="form-control" type="text" name="reference" autocomplete="off" id="reference" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Attachment</label>
                            <input class="form-control" type="file" name="attachment" autocomplete="off" id="attachment">
                        </div>
                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
                        <button type="submit" value="pr" name="submit_btn" class="btn btn-light add-btn ms-2" onclick="close_model()"><i class="ico icon-outline-archive-minimalistic text-success"></i>Save & Print</button>
                        <button type="submit" value="py" name="submit_btn" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-archive-minimalistic text-success"></i>Payment</button>
                        <button type="submit" value="jv" name="submit_btn" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-archive-minimalistic text-success"></i>JV</button>
                        <button type="submit" value="sa" name="submit_btn" class="btn btn-light add-btn ms-2" id="btnSubmit"><i class="ico icon-outline-archive-minimalistic text-success"></i>Save</button>
                        <script>
                            function close_model(){
                                $('#model_close').click();
                            }
                        </script>
					</div>
                    {{ Form::close() }}
              	</div>
            </div>
        </div>

<!-- Payment Attachments Modal -->
<div class="modal fade" id="chequePaymentAttachmentsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="chequePaymentAttachmentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chequePaymentAttachmentsLabel">Payment Attachments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-9">
                        <label class="form-label">Upload files</label>
                        <input type="file" id="chequePaymentAttachmentsFiles" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" />
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="uploadChequePaymentAttachmentsBtn" class="btn btn-light">Upload</button>
                    </div>
                </div>
                <div id="chequePaymentAttachmentsMessage" class="mb-2"></div>
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
                        <tbody id="chequePaymentAttachmentsList">
                            <tr><td colspan="4" class="text-center">No attachments yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var currentChequePaymentId = 0;

function formatChequeAttachmentDMY(dateString) {
    var d = new Date(dateString);
    if (isNaN(d.getTime())) return '';
    var dd = String(d.getDate()).padStart(2, '0');
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var yyyy = d.getFullYear();
    return dd + '/' + mm + '/' + yyyy;
}

function renderChequePaymentAttachments(attachments) {
    var $tbody = $('#chequePaymentAttachmentsList').empty();
    if (!attachments || attachments.length === 0) {
        $tbody.html('<tr><td colspan="4" class="text-center">No attachments found.</td></tr>');
        return;
    }
    attachments.forEach(function (att, index) {
        var viewUrl = '{{ url("payment") }}/attachments/' + att.id + '/download';
        var attachedDate = att.created_at ? formatChequeAttachmentDMY(att.created_at) : '';
        var row = '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + $('<div>').text(att.file_name).html() + '</td>' +
            '<td>' + attachedDate + '</td>' +
            '<td class="text-center"><div class="d-flex justify-content-center align-items-center gap-1">' +
                '<a href="' + viewUrl + '" target="_blank" class="btn btn-sm btn-light" title="View">' +
                    '<i class="ico icon-outline-eye" style="font-size:16px;"></i>' +
                '</a>' +
                '<button type="button" class="btn btn-sm btn-light text-danger delete-cheque-payment-attachment-btn" data-id="' + att.id + '" title="Delete">' +
                    '<i class="ico icon-outline-trash-bin-trash" style="font-size:16px;"></i>' +
                '</button>' +
            '</div></td>' +
            '</tr>';
        $tbody.append(row);
    });
}

function fetchAndRenderChequePaymentAttachments(paymentId) {
    $('#chequePaymentAttachmentsList').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
    $.get('{{ url("payment") }}/' + paymentId + '/attachments', function (response) {
        if (response.success) {
            renderChequePaymentAttachments(response.attachments);
        } else {
            toastr.error('Unable to load attachments.');
        }
    }).fail(function () {
        toastr.error('Unable to fetch attachments.');
    });
}

$(document).on('click', '.cheque-payment-attachments-btn', function () {
    currentChequePaymentId = parseInt($(this).data('payment-id') || 0, 10);
    $('#chequePaymentAttachmentsMessage').html('');
    $('#chequePaymentAttachmentsFiles').val('');
    $('#chequePaymentAttachmentsModal').modal('show');
    fetchAndRenderChequePaymentAttachments(currentChequePaymentId);
});

$('#uploadChequePaymentAttachmentsBtn').on('click', function () {
    if (!currentChequePaymentId || currentChequePaymentId <= 0) {
        toastr.error('No payment linked.');
        return;
    }
    var files = $('#chequePaymentAttachmentsFiles')[0].files;
    if (!files.length) {
        toastr.warning('Please choose at least one file.');
        return;
    }
    var formData = new FormData();
    formData.append('sys_payment_id', currentChequePaymentId);
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
                $('#chequePaymentAttachmentsFiles').val('');
                fetchAndRenderChequePaymentAttachments(currentChequePaymentId);
            } else {
                toastr.error(response.message || 'Upload failed.');
            }
        },
        error: function (xhr) {
            var err = 'Upload failed.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                err = Object.values(xhr.responseJSON.errors).map(function (v) { return v.join(', '); }).join(' | ');
            } else if (xhr.responseText) {
                err = xhr.status + ' ' + xhr.statusText;
            }
            toastr.error(err);
        }
    });
});

$(document).on('click', '.delete-cheque-payment-attachment-btn', function () {
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
                fetchAndRenderChequePaymentAttachments(currentChequePaymentId);
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

<button data-bs-toggle="modal" data-bs-target="#editChequeModal" id="edit_popup"></button>
<div class="modal side-panel fade" id="editChequeModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: auto;"> 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Edit Cheque</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="model_close_edit"></button>
					</div>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-update','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-form2']) }}
                        <div class="modal-body">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                        <input type="hidden" name="cid" id="cid">
					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Bank Name</label>
                            <select class="form-control" name="bank_name" autocomplete="off" id="edit_bank_name" required>
                                @if (count($bank)>0)
                                    @foreach ($bank as $b)
                                        <option value="{{ $b->id }}">{{ $b->account_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Cheque Number</label>
                            <input class="form-control" type="text" name="cheque_number" autocomplete="off" id="edit_cheque_number" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Cheque Date</label>
                            <input class="form-control date-picker" type="text" name="cheque_date" autocomplete="off" id="edit_cheque_date" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Supplier Name</label>
                            <select class="form-control" name="supplier_name" autocomplete="off" id="edit_supplier_name" onchange="editsuppliername()" required>
                                <option>Select</option>
                                @if (count($supplier)>0)
                                    @foreach ($supplier as $b)
                                        <option value="{{ $b->id }}">{{ $b->account_name }}</option>
                                    @endforeach
                                @endif
                                <option value="0">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2" id="edit_other_supplier_name_div" style="display: none;">
                        <div class="form-group">
                            <label for="">Other Supplier Name</label>
                            <input class="form-control" type="text" name="other_supplier_name" autocomplete="off" id="edit_other_supplier_name">
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input class="form-control" type="number" step="any" name="amount" autocomplete="off" id="edit_amount" onchange="edit_amount_w()" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Amount in Words</label>
                            <input class="form-control" type="text" name="amount_words" autocomplete="off" id="edit_amount_words" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Deal ID</label>
                            <input class="form-control" type="text" name="deal_id" autocomplete="off" id="edit_deal_id" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Reference</label>
                            <input class="form-control" type="text" name="reference" autocomplete="off" id="edit_reference" required>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group">
                            <label for="">Attachment</label>
                            <input class="form-control" type="hidden" name="edit_attachment" autocomplete="off" id="edit_attachment">
                            <input class="form-control" type="file" name="attachment" autocomplete="off" id="attachment">
                        </div>
                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
                        <button type="submit" value="pr" name="submit_btn" onclick="close_model_edit()" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-archive-minimalistic text-success"></i>Save & Print</button>
                        <button type="submit" value="py" name="submit_btn" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-archive-minimalistic text-success"></i>Payment</button>
                        <button type="submit" value="jv" name="submit_btn" class="btn btn-light add-btn ms-2"><i class="ico icon-outline-archive-minimalistic text-success"></i>JV</button>
                        <button type="submit" value="sa" name="submit_btn" class="btn btn-light add-btn ms-2" id="btnSubmit"><i class="ico icon-outline-archive-minimalistic text-success"></i>Save</button>
					</div>
                    <script>
                        function close_model_edit(){
                            $('#model_close_edit').click();
                        }
                    </script>
                    {{ Form::close() }}
              	</div>
            </div>
        </div>

                

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

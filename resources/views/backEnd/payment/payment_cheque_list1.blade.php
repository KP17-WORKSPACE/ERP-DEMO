@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Cheque</h2>
                <span class="page-label">Home - Cheque</span>
            </div>
            <div>
                <a type="button" data-toggle="modal" data-target="#addModel" class="btn btn-primary"><i class="fa fa-plus"></i> Add Cheque</a>
                <a href="{{ url('payment') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Payment List</a>
            </div>
        </div>

        <input type="hidden" id="currency1" value="{{ $currency1 }}" />
        <input type="hidden" id="currency2" value="{{ $currency2 }}" />

        <div class="card p-4 mb-2">

            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
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
                        <th> @lang('Doc Number')</th>
                        <th> @lang('Doc Date')</th>
                        <th> @lang('Bank Name')</th>
                        <th> @lang('Cheque Date')</th>
                        <th> @lang('Supplier Name')</th>
                        <th class="text-right"> @lang('Amount')</th>                        
                        <th> @lang('Cheque Number')</th>
                        <th> @lang('Deal ID')</th>
                        <th> @lang('Created By')</th>
                        <th width="110px" align="right"> @lang('Action')</th>
                    </tr>
                </thead>

                <tbody>
                    @if (isset($data))
                    @foreach ($data as $dt)
                    <tr @if($dt->status == 0) class="bg-dark" @endif>
                        <td>{{ @$dt->doc_number }}</td>
                        <td>{{date('d/m/Y', strtotime(@$dt->doc_date))}}</td>
                        <td>{{ @$dt->bankname->account_name }}</td>
                        <td>{{date('d/m/Y', strtotime(@$dt->cheque_date))}}</td>
                        @if (@$dt->supplier_name == 0)
                        <td>{{ @$dt->other_supplier_name }}</td>
                        @else
                        <td>{{ @$dt->suppliername->account_name }}</td>
                        @endif
                        

                        <td class="text-right">{{ @App\SysHelper::com_curr_format(@$dt->amount,2,'.',',') }}</td>
                        <td>{{ @$dt->cheque_number }}</td>
                        <td><a href="{{url('crm-deals/' . @$dt->deal_id . '/view')}}" target="_blank">{{ @$dt->deal_code->code }}</a></td>
                        <td>{{ @$dt->createdby->full_name }}</td>
                        <td align="right">
                            <input type="hidden" id="edit_bank_name_{{ $dt->id }}" value="{{ $dt->bank_name }}"/>
                            <input type="hidden" id="edit_cheque_number_{{ $dt->id }}" value="{{ $dt->cheque_number }}"/>
                            <input type="hidden" id="edit_cheque_date_{{ $dt->id }}" value="{{ $dt->cheque_date }}"/>
                            <input type="hidden" id="edit_supplier_name_{{ $dt->id }}" value="{{ $dt->supplier_name }}"/>
                            <input type="hidden" id="edit_other_supplier_name_{{ $dt->id }}" value="{{ $dt->other_supplier_name }}"/>
                            <input type="hidden" id="edit_amount_{{ $dt->id }}" value="{{ $dt->amount }}"/>
                            <input type="hidden" id="edit_amount_words_{{ $dt->id }}" value="{{ $dt->amount_words }}"/>
                            <input type="hidden" id="edit_deal_id_{{ $dt->id }}" value="{{ @$dt->deal_code->code }}"/>
                            <input type="hidden" id="edit_attachment_{{ $dt->id }}" value="{{ $dt->attachment }}"/>
                            <input type="hidden" id="edit_reference_{{ $dt->id }}" value="{{ $dt->reference }}"/>

                            @if($dt->attachment != "")
                            <a class="btn-sm btn-info" href="{{url('public/uploads/payment_cheque/'.$dt->attachment)}}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                            @endif

                            <a class="btn-sm btn-primary" onclick="edit_cheque({{ @$dt->id}})"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            @if (@$dt->status == 0)
                                <a class="btn-sm btn-warning" href="{{url('payment-cheque/'.$dt->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                            @else
                                <a class="btn-sm btn-danger" href="{{url('payment-cheque/'.$dt->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
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
        var curr1 = $('#currency1').val();
        var curr2 = $('#currency2').val();
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
        str += curr1 + ' ';
        if (x != s.length) {
            var y = s.length;
            str += '& ';
            for (var i = x + 1; i < y; i++) str += dg[n[i]] + ' ';
            str += curr2 + ' ';
        }
        return str.replace(/\s+/g, ' ');
    }

function amount_w(){
    $('#amount_words').val(toWords($('#amount').val()));
}
function edit_amount_w(){
    $('#edit_amount_words').val(toWords($('#edit_amount').val()));
}
</script>

<div class="modal fade bd-example-modal-lg" id="addModel" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Payment Voucher</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="model_close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-store','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-form']) }}
            <div class="modal-body">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Bank Name</label>
                            <select class="form-control" name="bank_name" autocomplete="off" id="bank_name" required>
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
                            <input class="form-control" type="text" name="cheque_number" autocomplete="off" id="cheque_number" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Cheque Date</label>
                            <input class="form-control" type="date" name="cheque_date" autocomplete="off" id="cheque_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4" id="other_supplier_name_div" style="display: none;">
                        <div class="form-group">
                            <label for="">Other Supplier Name</label>
                            <input class="form-control" type="text" name="other_supplier_name" autocomplete="off" id="other_supplier_name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input class="form-control" type="text" name="amount" autocomplete="off" id="amount" onchange="amount_w()" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Amount in Words</label>
                            <input class="form-control" type="text" name="amount_words" autocomplete="off" id="amount_words" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Deal ID</label>
                            <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Reference</label>
                            <input class="form-control" type="text" name="reference" autocomplete="off" id="reference" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Attachment</label>
                            <input class="form-control" type="file" name="attachment" autocomplete="off" id="attachment">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="pr" name="submit_btn" class="btn btn-warning" onclick="close_model()"><span class="ti-check"></span>Save & Print</button>
                <button type="submit" value="py" name="submit_btn" class="btn btn-info"><span class="ti-check"></span>Payment</button>
                <button type="submit" value="jv" name="submit_btn" class="btn btn-info"><span class="ti-check"></span>JV</button>
                <button type="submit" value="sa" name="submit_btn" class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>Save</button>
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

<a type="button" data-toggle="modal" data-target="#addModelEdit" id="edit_popup"></a>
<div class="modal fade bd-example-modal-lg" id="addModelEdit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Payment Voucher</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="model_close_edit">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-update','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-form2']) }}
            <div class="modal-body">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="cid" id="cid">
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
                            <input class="form-control" type="date" name="cheque_date" autocomplete="off" id="edit_cheque_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4" id="edit_other_supplier_name_div" style="display: none;">
                        <div class="form-group">
                            <label for="">Other Supplier Name</label>
                            <input class="form-control" type="text" name="other_supplier_name" autocomplete="off" id="edit_other_supplier_name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input class="form-control" type="number" step="any" name="amount" autocomplete="off" id="edit_amount" onchange="edit_amount_w()" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Amount in Words</label>
                            <input class="form-control" type="text" name="amount_words" autocomplete="off" id="edit_amount_words" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Deal ID</label>
                            <input class="form-control" type="text" name="deal_id" autocomplete="off" id="edit_deal_id" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Reference</label>
                            <input class="form-control" type="text" name="reference" autocomplete="off" id="edit_reference" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Attachment</label>
                            <input class="form-control" type="hidden" name="edit_attachment" autocomplete="off" id="edit_attachment">
                            <input class="form-control" type="file" name="attachment" autocomplete="off" id="attachment">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" value="pr" name="submit_btn" class="btn btn-warning" onclick="close_model_edit()"><span class="ti-check"></span>Save & Print</button>
                <button type="submit" value="py" name="submit_btn" class="btn btn-info"><span class="ti-check"></span>Payment</button>
                <button type="submit" value="jv" name="submit_btn" class="btn btn-info"><span class="ti-check"></span>JV</button>
                <button type="submit" value="sa" name="submit_btn" class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>Save</button>
            </div>
            {{ Form::close() }}
            <script>
                function close_model_edit(){
                    $('#model_close_edit').click();
                }
            </script>
        </div>
    </div>
</div>

<?php
/*function getIndianCurrency(float $number, string $r1, string $r2)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . " " .$r2 : '';
    return ($Rupees ? $Rupees . $r1 : ' ') . $paise;
}*/
?>

@endsection
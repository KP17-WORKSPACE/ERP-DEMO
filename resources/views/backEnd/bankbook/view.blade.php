@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <style>
        .border {
            border: solid 1px #e3e6f0;
        }

        .modal.side-panel .modal-dialog .modal-content .modal-footer .btn {
    min-width: 100px;
    justify-content: center;
}
 .btn-light {
    color: var(--color-btn-light);
    border: 1px solid var(--color-btn-light-border);
    background-color: var(--color-btn-light-bg);
}
.btn {
    display: flex;
    align-items: center;
    font-size: 12px;
    padding: 3px 10px;
    gap: 5px;
    border-radius: 8px;
    min-height: 25px;
}

    </style>



    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">

                <div class="top-header sticky-top bg-white"  style="z-index: 1020;">
<div class="purchase-order-content-header ">
                    <h4 class="purchase-order-content-header-left">
                        Bank Book
                    </h4>
                    <div class="purchase-order-content-header-right">

                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('receipt-add/bankbook') }}"><i
                                            class="ico icon-outline-bill-list text-success"></i> Receipts</a></li>
                                <li><a class="dropdown-item" href="{{ url('payment-add/bankbook') }}"><i
                                            class="ico icon-outline-bill-list text-success"></i> Payments</a></li>
                                <li><a class="dropdown-item" href="{{ url('journalvoucher-add/bankbook') }}"><i
                                            class="ico icon-outline-bill-list text-success"></i> Journal Voucher</a></li>
                            </ul>
                        </div>
                        {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                        {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'bankbook', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                        <div class="row">
                            <div class="col-md-3 mb-20">
                                <div class="input-effect">
                                    <label>@lang('Select Bank')</label>
                                    <select class="form-control js-example-basic-single" name="account_id" id="account_id"
                                        required>
                                        @foreach ($accounts as $val)
                                            <option value="{{ @$val->id }}"
                                                @if (isset($account_id)) @if (@$account_id == @$val->id) selected @endif
                                                @endif >{{ @$val->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('From Date')</label>
                                            <input class="form-control date-picker" id="from_date" type="text"
                                                name="from_date"
                                                value="{{ @$from_date ? @App\SysHelper::normalizeToDmy(@$from_date) : '' }}"
                                                autocomplete="off" onchange="set_filter()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('To Date')</label>
                                            <input class="form-control date-picker" id="to_date" type="text"
                                                name="to_date"
                                                value="{{ @$to_date ? @App\SysHelper::normalizeToDmy(@$to_date) : '' }}"
                                                autocomplete="off" onchange="set_filter()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 mb-2">
                                <label for="" class="form-check-label">Filter By</label>
                                <select onchange="this.form.submit()" class="form-control js-example-basic-single" name="filter_by" id="filter_by">
                                    <option value="">-Select-</option>
                                    <option value="this_month" @if (@$filter_by == 'this_month') selected @endif>This Month
                                    </option>
                                    <option value="today" @if (@$filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if (@$filter_by == 'this_week') selected @endif>This Week
                                    </option>
                                    <option value="last_week" @if (@$filter_by == 'last_week') selected @endif>Last Week
                                    </option>
                                    <option value="last_month" @if (@$filter_by == 'last_month') selected @endif>Last Month
                                    </option>
                                    <option value="this_quarter" @if (@$filter_by == 'this_quarter') selected @endif>This
                                        Quarter</option>
                                    <option value="pre_quarter" @if (@$filter_by == 'pre_quarter') selected @endif>Previous
                                        Quarter</option>
                                    <option value="this_year" @if (@$filter_by == 'this_year') selected @endif>This Year
                                    </option>
                                    <option value="last_year" @if (@$filter_by == 'last_year') selected @endif>Last Year
                                    </option>
                                </select>
                            </div>
                            <script>
                                function set_filter() {
                                    if ($('#from_date').val() != "" || $('#to_date').val() != "") {
                                        $('#filter_by').val('')
                                    }
                                }
                            </script>


                            <div class="col-md-1 mb-2">
                                <label for="" class="form-check-label">PDC Filter</label>
                                <select onchange="this.form.submit()" class="form-control js-example-basic-single" name="pdc_filter" id="pdc_filter">
                                    <option value="">-Select-</option>
                                    <option value="with_pdc" @if (@$pdc_filter == 'with_pdc') selected @endif>With PDC
                                    </option>
                                    <option value="without_pdc" @if (@$pdc_filter == 'without_pdc') selected @endif>Without PDC</option>
                                    <option value="hide_pdc" @if (@$pdc_filter == 'hide_pdc') selected @endif>Hide PDC
                                    </option>
                                   
                                  
                                </select>
                            </div>

                            <div class="col-md-1" style="margin-top:1.4rem">
                                <button class="btn btn-light" id="btnSubmit">
                                    <i class="ico icon-outline-minimalistic-magnifer text-success" style="font-size:18px"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Search in List</label>
                                <input type="text" id="tableSearch" class="form-control mb-2" placeholder="">
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>

                </div>

                
                <div class="card mb-3">
                    <div class="card-body p-0">
                        <table id="long-list" class="table table-hover data-table table-fixed-header" style="table-layout: fixed;width:100%">

                            <thead>
                                <tr>
                                    <th class="border text-center" width="9%">Date</th>
                                    <th class="border text-start" width="7%">Doc No</th>
                                    <th class="border text-start" width="20%">Particular</th>
                                    <th class="border text-end" width="7%">Debit</th>
                                    <th class="border text-end" width="7%">Credit</th>
                                    <th class="border text-end" width="7%">Balance</th>
                                    <th class="border text-center">Narration</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $tot = 0;
                                $total = 0;
                                $total_dr = 0;
                                $total_cr = 0;
                                $i = 1; ?>

                                {{-- sort data so same-date receipts (debits) appear before payments (credits) --}}
                                @php
                                    if (count($data) > 1) {
                                        usort($data, function ($a, $b) {
                                            if ($a['transaction_date'] === $b['transaction_date']) {
                                                // receipts have debit_amount > 0
                                                if ($a['debit_amount'] != $b['debit_amount']) {
                                                    return ($b['debit_amount'] <=> $a['debit_amount']);
                                                }
                                            }
                                            return strtotime($a['transaction_date']) - strtotime($b['transaction_date']);
                                        });
                                    }
                                @endphp

                                @if (count($data) > 0)
                                    @foreach ($data as $dt)
                                        @if ($dt != '')
                                            <tr>
                                                <td class=" text-center">
                                                    <input type="hidden" id="receipt_id_{{ $i }}"
                                                        value='{{ $dt['transaction_no'] }}' />
                                                    <input type="hidden" id="receipt_date_{{ $i }}"
                                                        value='{{ $dt['transaction_date'] }}' />

                                                         @if (substr($dt['transaction_no'], 0, 2) != 'JV')
                                                       
                                                    
                                                        <a onclick="date_change({{ $i }},'{{ substr($dt['transaction_no'], 0, 2) }}')" class="text-danger"
                                                        style="cursor: pointer;"><i
                                                            class="ico icon-outline-pen-new-square"
                                                            style="font-size: 16px" aria-hidden="true"></i></a>  &nbsp;&nbsp;

                                                       @endif
                                                    {{ date('d/m/Y', strtotime($dt['transaction_date'])) }}&nbsp;

                                                      

                                                    
                                                </td>
                                                <td class="text-start">
                                                    @if (substr($dt['transaction_no'], 0, 2) == 'JV')
                                                        <a href="{{ url('journalvoucher/' . $dt['transaction_id'] ) }}"
                                                            target="_blank">{{ $dt['transaction_no'] }}</a>
                                                    @elseif(substr($dt['transaction_no'], 0, 2) == 'CR')
                                                        <a href="{{ url('receipt/' . $dt['transaction_id'] ) }}"
                                                            target="_blank">{{ $dt['transaction_no'] }}</a>
                                                    @elseif(substr($dt['transaction_no'], 0, 2) == 'BR')
                                                        <a href="{{ url('receipt/' . $dt['transaction_id'] ) }}"
                                                            target="_blank">{{ $dt['transaction_no'] }}</a>
                                                    @elseif(substr($dt['transaction_no'], 0, 2) == 'CP')
                                                        <a href="{{ url('payment/' . $dt['transaction_id'] ) }}"
                                                            target="_blank">{{ $dt['transaction_no'] }}</a>
                                                    @elseif(substr($dt['transaction_no'], 0, 2) == 'BP')
                                                        <a href="{{ url('payment/' . $dt['transaction_id'] ) }}"
                                                            target="_blank">{{ $dt['transaction_no'] }}</a>
                                                    @else
                                                        {{ $dt['transaction_no'] }}
                                                    @endif
                                                </td>
                                                <td class="">{{ $dt['account_name'] }}</td>
                                                <td class=" text-end ">
                                                    {{ @App\SysHelper::com_curr_format($dt['debit_amount'], 2, '.', ',') }}
                                                    @php $total_dr += $dt["debit_amount"]; @endphp </td>
                                                <td class="text-end">
                                                    {{ @App\SysHelper::com_curr_format($dt['credit_amount'], 2, '.', ',') }}
                                                    @php $total_cr += $dt["credit_amount"]; @endphp </td>
                                                <td class="text-end {{ $tot < 0 ? 'text-danger' : '' }}">
                                                    <?php $tot -= $dt['credit_amount']; ?>
                                                    <?php $tot += $dt['debit_amount']; ?>
                                                    {{ @App\SysHelper::com_curr_format($tot, 2, '.', ',') }}
                                                </td>
                                                <td class="text-start"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt['remarks'] }}</td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endif
                                    @endforeach
                                @endif

                                @if (@$pdc_filter == 'with_pdc')

                                
                                  @foreach ($receipt_pdc_list as $p)
                                        <tr>
                                            <td class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                                            <td class="text-start"> <a href="{{ url('receipt/' . $p->id) }}">{{ $p->doc_number }}</a> </td>
                                            <td class="">{{ $p->account_name }}</td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($p->credit_amount, 2, '.', ',') }}
                                            
                                                @php
                                                     $total_dr += $p->credit_amount;
                                                @endphp
                                            </td>
                               
                                            <td class="text-end">0.00</td>
                                                   <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($p->credit_amount, 2, '.', ',') }}</td>
                                            <td class="text-start">
                                                <div class="d-flex justify-content-start">
                                                    <button class="btn btn-sm btn-light" style="margin-left:29px"
                                                        id="btn_pdc_received_{{ $p->doc_number }}" href="#"
                                                        onclick="receipt_pdc_update('{{ $p->doc_number }}','{{ $p->receipt_date ? \Carbon\Carbon::parse($p->receipt_date)->format('d/m/Y') : '' }}')">Update</button>
                                                </div>

                                            </td>
                                        </tr>
                                  @endforeach

                                   @foreach ($payment_pdc_list as $p)
                                        <tr>
                                            <td class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                                            <td class="text-start"> <a href="{{ url('payment/' . $p->id) }}">{{ $p->doc_number }}</a> </td>
                                            <td class="">{{ $p->account_name }}</td>
                                            <td class="text-end">
                                                0.00</td>
                                                       <td class="text-end">{{ @App\SysHelper::com_curr_format($p->debit_amount, 2, '.', ',') }}</td>
                                                       <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($p->debit_amount, 2, '.', ',') }}
                                            
                                                  @php $total_cr += $p->debit_amount; 
                                            
                                                  @endphp
                                            </td>
                                           
                                          
                                            <td class="text-start">
                                                <div class="d-flex justify-content-start">
                                                    <button class="btn-sm btn btn-light"  style="margin-left:29px"
                                                        id="btn_pdc_payment_{{ $p->doc_number }}"
                                                        onclick="payment_pdc_update('{{ $p->doc_number }}','{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : '' }}')">Update</button>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                @endif


                                

                                <script>
                                    function date_change(id, type) {

                                    if(type == 'CR' || type == 'BR'){
                                        $('#modalChangeDTHeading').text('Change Receipt Date');
                                        

                                    }else if(type == 'CP' || type == 'BP'){
                                        $('#modalChangeDTHeading').text('Change Payment Date');
                                    }else if(type == 'JV'){
                                        $('#modalChangeDTHeading').text('Change Journal Voucher Date');
                                    }

                                     var doc_id = $('#receipt_id_' + id).val();
                                        var doc_date = $('#receipt_date_' + id).val();
                                        $('#receipt_id').val(doc_id);
                                        // $('#receipt_date').val(doc_date);
                                        $('#receipt_date').val(doc_date ? doc_date.split('-').reverse().join('/') : '');
                                        $('#account_id_re').val($('#account_id').val());
                                        $('#from_date_re').val($('#from_date').val());
                                        $('#to_date_re').val($('#to_date').val());
                                        $('#ModalReceiptDate').modal('show');
                                       
                                       
                                    }
                                </script>
                                {{-- <a id="btn_receipt" data-toggle="modal" data-target="#ModalReceiptDate"></a> --}}

                                <tr><td colspan="7" class="text-center"  style="height:19px;"></td></tr>
                            </tbody>
                            <tfoot>
                                   
                                <tr>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th class="text-end">
                                        {{ @App\SysHelper::com_curr_format($total_dr, 2, '.', ',') }}</th>
                                    <th class="text-end">
                                        {{ @App\SysHelper::com_curr_format($total_cr, 2, '.', ',') }}</th>
                                    <th class="text-end">
                                        {{ @App\SysHelper::com_curr_format($total_dr - $total_cr, 2, '.', ',') }}</th>
                                    <th class=""></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- also dont show this when its with pdc -->
                @if (@$pdc_filter != 'hide_pdc' && @$pdc_filter != 'with_pdc')
                  @if (count($receipt_pdc_list) > 0)
                      <div class="row mb-3">
                    <div class="col-md-12">

                            <b>PDC Receipt Register:-</b>
                            <table id="long-list" class="table table-hover data-table table-fixed-header table-fixed-header3"
                                style="table-layout: fixed;width:100%">

                                <thead>
                                    <tr>
                                        <th class="text-center" width="9%">Doc Date</th>
                                        <th class="text-center" width="9%">Receipt No</th>
                                        <th class="" width="24%">Particular</th>
                                        <th class="text-end" width="10%">Amount</th>
                                        <th class="text-center" width="10%">Cheque Date</th>
                                        <th class="" width="10%">Cheque No</th>
                                        <th class="text-center" width="10%">Receipt Date</th>
                                        <th class="" width="10%">Invoice Adjusted</th>
                                        <th class="text-center" width="8%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receipt_pdc_list as $p)
                                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                                            <td class="text-center"> <a href="{{ url('receipt/' . $p->id) }}">{{ $p->doc_number }}</a> </td>
                                            <td class="">{{ $p->account_name }}</td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($p->credit_amount, 2, '.', ',') }}</td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                                            <td class="">{{ $p->cheque_number }}</td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                                            <td class="">{{ $p->bi_doc_no }}</td>

                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <button class="btn btn-sm btn-light" style="margin-left:29px"
                                                        id="btn_pdc_received_{{ $p->doc_number }}" href="#"
                                                        onclick="receipt_pdc_update('{{ $p->doc_number }}','{{ $p->receipt_date ? \Carbon\Carbon::parse($p->receipt_date)->format('d/m/Y') : '' }}')">Update</button>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                <tr><td colspan="9" class="text-center"  style="height:19px;"></td></tr></tfoot>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($receipt_pdc_list->sum('credit_amount'), 2, '.', ',') }}
                                        </th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">

                        @if (count($payment_pdc_list) > 0)
                            <b>PDC Payment Register:-</b>
                            <table id="long-list" class="table table-hover data-table table-fixed-header table-fixed-header2"
                                style="table-layout: fixed;width:100%">

                                <thead>
                                    <tr>
                                        <th class="text-center" width="9%">Doc Date</th>
                                        <th class="text-center" width="9%">Receipt No</th>
                                        <th class="" width="24%">Particular</th>
                                        <th class="text-end" width="10%">Amount</th>
                                        <th class="text-center" width="10%">Cheque Date</th>
                                        <th class="" width="10%">Cheque No</th>
                                        <th class="text-center" width="10%">Payment Date</th>
                                        <th class="" width="10%">Invoice Adjusted</th>
                                        <th class="text-center" width="8%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment_pdc_list as $p)
                                        <tr id="row_pdc_paid_{{ $p->doc_number }}">
                                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                                            <td class="text-center"> <a href="{{ url('payment/' . $p->id) }}">{{ $p->doc_number }}</a> </td>
                                            <td class="">{{ $p->account_name }}</td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($p->debit_amount, 2, '.', ',') }}</td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                                            <td class="">{{ $p->cheque_number }}</td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                                            <td class="">{{ $p->bi_doc_no }}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <button class="btn-sm btn btn-light"  style="margin-left:29px"
                                                        id="btn_pdc_payment_{{ $p->doc_number }}"
                                                        onclick="payment_pdc_update('{{ $p->doc_number }}','{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : '' }}')">Update</button>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                <tr><td colspan="9" class="text-center"  style="height:19px;"></td></tr></tfoot>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format($payment_pdc_list->sum('debit_amount'), 2, '.', ',') }}
                                        </th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                        <th colspan=""></th>
                                    </tr>
                                </tfoot>
                            </table>
                        @endif

                    </div>
                </div>
                    
                @endif
               

            </div>
        </div>
    </div>

    <script>
        function receipt_pdc_update(id, dat) {
            $('#pdc_receipt_doc_no').val(id);
            $('#pdc_receipt_doc_date').val(dat);

            // $('#pdc_receipt_doc_date').val(dat ? dat.split('-')
            //     .reverse().join('/') : '');


            $('#receiptPDCUpdate').click();
        }

        function receipt_pdc_update_save() {




            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('update-receivable-pdc') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    doc_id: $('#pdc_receipt_doc_no').val(),
                    doc_date: $('#pdc_receipt_doc_date').val(),
                    status: $('#pdc_receipt_status').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);

                    console.log(dataResult)

                    if (dataResult['data'] == "SUCCESS") {
                        // alert("Updated Successfully!!");
                        var a = $('#pdc_receipt_doc_no').val();
                        $('#btn_pdc_received_' + a).css("background-color", "#f6c23e");
                        $('#btn_pdc_received_' + a).text("Updated");
                        if ($('#pdc_receipt_status').val() == 2) {
                            $('#row_pdc_received_' + a).css("display", "none");
                        }
                        $('#ModalreceiptPDCUpdate').modal('hide');
                        location.reload();
                    } else {
                        alert("Error!!");
                    }

                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <button id="receiptPDCUpdate" data-bs-toggle="modal" data-bs-target="#ModalreceiptPDCUpdate" hidden></button>
    <!-- Modal Receipt PDC Update -->
    <div class="modal side-panel fade" id="ModalreceiptPDCUpdate" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Receipt PDC Update</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-12">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <input type="hidden" id="pdc_receipt_doc_no">
                                        <label class="form-label">@lang('Receipt Date')<span></span></label>
                                        <input class="form-control date-picker" id="pdc_receipt_doc_date" type="text"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-12">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Status')<span></span></label>
                                        <select class="form-control js-example-basic-single" id="pdc_receipt_status">
                                            <option value="2">Received & Removed</option>
                                            <option value="1">Received</option>
                                            <option value="3">Pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="btnReceiptSubmitPDC"
                        onclick="receipt_pdc_update_save()">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Receipt PDC Update -->




    {{-- <a class="btn-sm btn-danger" data-toggle="modal" data-target="#ModalPaymentPDCUpdate" id="paymentPDCUpdate"
        style="display: none;"></a> --}}
    <!-- Modal Payment PDC Update -->
    {{-- <div class="modal fade" id="ModalPaymentPDCUpdate" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Payment PDC Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-12">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <input type="hidden" id="pdc_payment_doc_no">
                                        <label class="form-label">@lang('Payment Date')<span></span></label>
                                        <input class="form-control" id="pdc_payment_doc_date" type="date" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-12">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Status')<span></span></label>
                                        <select class="form-control" id="pdc_payment_status">
                                            <option value="2">Paid & Removed</option>
                                            <option value="1">Paid</option>
                                            <option value="3">Pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn-small" type="button" id="btnpaymentSubmitPDC_close"
                        data-dismiss="modal">Close</button>
                    <button type="button" class=" btn-small" id="btnpaymentSubmitPDC"
                        onclick="payment_pdc_update_save()">PDC Paid</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal side-panel fade" id="ModalPaymentPDCUpdate" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Payment PDC Update</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-12">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <input type="hidden" id="pdc_payment_doc_no">
                                        <label class="form-label">@lang('Payment Date')<span></span></label>
                                        <input class="form-control date-picker" id="pdc_payment_doc_date" type="text"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-12">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Status')<span></span></label>
                                        <select class="form-control js-example-basic-single" id="pdc_payment_status">
                                            <option value="2">Paid & Removed</option>
                                            <option value="1">Paid</option>
                                            <option value="3">Pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="btnpaymentSubmitPDC"
                        onclick="payment_pdc_update_save()">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> PDC Paid
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Payment PDC Update -->

    <script>
        function payment_pdc_update(id, dat) {
            $('#pdc_payment_doc_no').val(id);
            console.log(dat)
            $('#pdc_payment_doc_date').val(dat);
            $('#ModalPaymentPDCUpdate').modal('show');
        }

        function payment_pdc_update_save() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('update-payable-pdc') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    doc_id: $('#pdc_payment_doc_no').val(),
                    doc_date: $('#pdc_payment_doc_date').val(),
                    status: $('#pdc_payment_status').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);

                    if (dataResult['data'] == "SUCCESS") {
                        // alert("Updated Successfully!!");
                        var a = $('#pdc_payment_doc_no').val();
                        $('#btn_pdc_payment_' + a).css("background-color", "#f6c23e");
                        $('#btn_pdc_payment_' + a).text("Updated");
                        if ($('#pdc_payment_status').val() == 2) {
                            $('#row_pdc_paid_' + a).css("display", "none");
                        }
                        $('#ModalPaymentPDCUpdate').modal('hide');
                        location.reload();
                    } else {
                        alert("Error!!");
                    }

                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>



    </div>

    </div>



    <div class="modal side-panel fade" id="ModalReceiptDate" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="top:30%">
         
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalChangeDTHeading">Change Receipt Date</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt-date-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="receipt_id" id="receipt_id" />
                    <input type="hidden" name="account_id_re" id="account_id_re" />
                    <input type="hidden" name="from_date_re" id="from_date_re" />
                    <input type="hidden" name="to_date_re" id="to_date_re" />
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Receipt Date</label>
                                            <input class="form-control date-picker" type="text" name="receipt_date"
                                                id="receipt_date" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2 d-flex justify-content-center">
                            <i class="ico icon-outline-bookmark-opened text-success title-15"></i> Update
                        </button>
                    </div>
                    {{ Form::close() }}

                </div>
      

        </div>
    </div>



    <!-- Modal Receipt Date Change-->
    {{-- <div class="modal fade" id="ModalReceiptDate" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xs" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Receipt Date</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt-date-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="receipt_id" id="receipt_id" />

                <input type="hidden" name="account_id_re" id="account_id_re" />
                <input type="hidden" name="from_date_re" id="from_date_re" />
                <input type="hidden" name="to_date_re" id="to_date_re" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Receipt Date</label>
                                <input class="form-control" type="date" name="receipt_date" id="receipt_date"
                                    required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div> --}}
    <!-- Modal Support Cmt-->
{{-- 
   <script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
        // Use fixed pixel widths for columns
        // Date(9%), Doc No(7%), Particular(20%), Debit(7%), Credit(7%), Balance(7%), Narration(auto)
        var columnWidthsPx = [100, 85, 240, 85, 85, 85, 350];

        $theadTh.each(function(i) {
            var w = columnWidthsPx[i];
            if (w) {
                $(this).css('width', w + 'px');
                $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
            } else {
                $(this).css('width', 'auto');
                $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', 'auto');
            }
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script> --}}

<script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
         var columnWidths = [9, 7, 20, 7, 7, 7, 43];

        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + '%');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + '%');
        });

        // Apply the same widths to <tfoot>
        $tfootTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + '%');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>

<script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header2');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
         var columnWidths = [9, 8, 20, 10, 10, 8, 10, 10, 23];

        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + '%');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + '%');
        });

        // Apply the same widths to <tfoot>
        $tfootTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + '%');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>

<script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header3');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
         var columnWidths = [9, 8, 20, 10, 10, 8, 10, 10, 23];


        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + '%');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + '%');
        });

        // Apply the same widths to <tfoot>
        $tfootTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + '%');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>

@endsection

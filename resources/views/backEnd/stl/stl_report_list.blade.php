@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        STL Report
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('stl-add') }}">
                            <i class="ico icon-outline-add-square text-success"></i> Add STL
                        </a>
                        <a class="btn btn-light" href="{{ url('stl-supplier-report') }}"> STL Supplier Report</a>
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-report', 'method' => 'POST', 'id' => 'stl-report']) }}
                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-check-label">Bank Name</label>
                                <select class="form-control js-example-basic-single" name="bank" id="bank" required>
                                    <option value="0" @if ($ctrl_bank == 0) selected @endif>Select</option>
                                    @if (count($bank) > 0)
                                        @foreach ($bank as $value)
                                            <option value="{{ @$value->id }}"
                                                @if ($ctrl_bank == $value->id) selected @endif>
                                                {{ @$value->account_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
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
                                <select onchange="this.form.submit()" class="form-control" name="filter_by" id="filter_by">
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

                            <div class="col-md-3 mb-2"><br />
                                <button class="btn btn-light" type="submit">
                                    <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Show
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body p-0">
                    
                       
                            <table id="long-list" class="table table-hover table-fixed-header" style="table-layout: fixed;width:100%">
                                <thead >
                                
                                    
                                    <tr>
                                        <th class="text-center">STL No.</th>
                                        <th class="text-center">Date</th>
                                        <th>Particular</th>
                                        <th style="width: 100px;" class="text-center">Debit</th>
                                        <th style="width: 100px;" class="text-center">Credit</th>
                                        <th style="width: 100px;" class="text-center">Balance</th>
                                        <th style="width: 100px;" class="text-center">Deposit</th>

                                        <th>Ref. No</th>
                                        <th class="text-center">Processing Date</th>
                                        <th class="text-center">Settlement Date</th>
                                        <th style="width: 100px;" class="text-center">Interest</th>
                                        <th style="width: 100px;" class="text-center">Bank Charges</th>
                                        <th style="width: 100px;" class="text-center">Other Charges</th>

                                        <th>PI/ PFI No.</th>
                                        <th>Sup. Bill No.</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width: 100px;">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $amount_aed = 0;
                                    $debit = 0;
                                    $credit = 0;
                                    $balance = 0;
                                    $deposit = 0;
                                    $stl_intrest = 0;
                                    $bank_charges = 0;
                                    $other_charges = 0;
                                    ?>


                                    @if (isset($sortedData))
                                        @php $recordCount = 0; @endphp
                                        @foreach ($sortedData as $value)
                                            @php $recordCount++; @endphp
                                            <tr @if (@$value['status'] == 2) class="bg-dark" @endif>

                                                @if (@$value['type'] == 1)
                                                    <td></td>
                                                    <td class="text-center">
                                                        {{ date('d/m/Y', strtotime(@$value['created_at'])) }}</td>
                                                    <td>
                                                        <div>
                                                            {{ @$value['account_name'] }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ @App\SysHelper::com_curr_format(@$value['stl_limit'], 2, '.', ',') }}
                                                        <?php $amount_aed += $value['stl_limit'];
                                                        $debit += $value['stl_limit']; ?></td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">
                                                        {{ @App\SysHelper::com_curr_format(@$amount_aed, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>

                                                    <td class="text-center">--</td>
                                                    <td class="text-center">--</td>

                                                    <td class="text-center">--</td>

                                                    <td>
                                                        @if (@$value['status'] == 1)
                                                            <span class="text-success">Processed</span>
                                                        @else
                                                            <span class="text-danger">Canceled</span>
                                                        @endif
                                                    </td>
                                                    <td></td>
                                                @endif

                                                @if (@$value['type'] == 2)
                                                    <td class="text-center"><a>{{ @$value['doc_number'] }}</a>

                                                        <input type="hidden" id="doc_number_{{ $value['id'] }}"
                                                            value="{{ @$value['doc_number'] }}" />

                                                        <input type="hidden" id="stl_no_{{ $value['id'] }}"
                                                            value="{{ @$value['doc_number'] }}" />
                                                        <input type="hidden" id="supplier_name_{{ $value['id'] }}"
                                                            value="{{ @$value['vendor_account_name'] }}" />
                                                        <input type="hidden" id="supplier_id_{{ $value['id'] }}"
                                                            value="{{ @$value['vendor'] }}" />
                                                        <input type="hidden" id="set_amount_{{ $value['id'] }}"
                                                            value="" />

                                                        <input type="hidden" id="stl_ref_no_{{ $value['id'] }}"
                                                            value="{{ @$value['stl_ref_no'] }}" />
                                                        <input type="hidden" id="processing_date_{{ $value['id'] }}"
                                                            value="{{ @$value['processing_date'] }}" />
                                                        <input type="hidden" id="settlement_date_{{ $value['id'] }}"
                                                            value="{{ @$value['settlement_date'] }}" />
                                                        <input type="hidden" id="stl_interest_{{ $value['id'] }}"
                                                            value="{{ @$value['stl_interest'] }}" />
                                                        <input type="hidden" id="bank_charges_{{ $value['id'] }}"
                                                            value="{{ @$value['bank_charges'] }}" />
                                                        <input type="hidden" id="other_charges_{{ $value['id'] }}"
                                                            value="{{ @$value['other_charges'] }}" />



                                                    </td>
                                                    <td class="text-center">
                                                        {{ date('d/m/Y', strtotime(@$value['submition_date'])) }}</td>
                                                    <td>
                                                        <div>
                                                            {{ @$value['vendor_account_name'] }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-center">
                                                        {{ @App\SysHelper::com_curr_format(@$value['amount_aed'], 2, '.', ',') }}<?php $amount_aed -= $value['amount_aed'];
                                                        $credit += $value['amount_aed']; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ @App\SysHelper::com_curr_format(@$amount_aed, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ @App\SysHelper::com_curr_format((@$value['amount_aed'] * 20) / 100, 2, '.', ',') }}
                                                        <?php $deposit -= ($value['amount_aed'] * 20) / 100; ?></td>
                                                    <td class="text-end">{{ @$value['stl_ref_no'] }}</td>
                                                    <td class="text-center">
                                                        @if ($value['processing_date'] != null)
                                                            {{ date('d/m/Y', strtotime(@$value['processing_date'])) }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($value['processing_date'] != null)
                                                            {{ date('d/m/Y', strtotime(@$value['settlement_date'])) }}
                                                        @endif
                                                    </td>
                                                    <td class="text-end">{{ @$value['stl_interest'] }} <?php $stl_intrest += $value['stl_interest']; ?>
                                                    </td>
                                                    <td class="text-end">{{ @$value['bank_charges'] }} <?php $bank_charges += $value['bank_charges']; ?>
                                                    </td>
                                                    <td class="text-end">{{ @$value['other_charges'] }}
                                                        <?php $other_charges += $value['other_charges']; ?>
                                                    </td>

                                                    <td>{{ @$value['pi_inv_no'] }}</td>
                                                    <td>{{ @$value['awbno'] }}</td>

                                                    <td>{{ @$value['narration'] }}</td>

                                                    <td>
                                                        @if (@$value['status'] == 1)
                                                            <span class="text-success">Processed</span>
                                                        @else
                                                            <span class="text-danger">Canceled</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <a class="btn btn-sm btn-light" title="Edit"
                                                                onclick="stl_edit({{ $value['id'] }})"><i
                                                                    class="ico icon-outline-pen-2"
                                                                    style="font-size: 16px;"></i></a>
                                                            <a class="btn btn-sm btn-light" title="Payment"
                                                                onclick="stl_payment({{ $value['id'] }})"><i
                                                                    class="ico icon-outline-card" aria-hidden="true"
                                                                    style="font-size: 16px;"></i></a>
                                                            @if (@$value['status'] == 1)
                                                                <a class="btn btn-sm btn-light" title="Delete"
                                                                    href="{{ url('stl/' . $value['id'] . '/delete') }}"
                                                                    onclick="return confirm('Are you sure you want to delete this STL?');"><i
                                                                        class="ico icon-outline-trash-bin-minimalistic"
                                                                        style="font-size: 16px;"></i></a>
                                                            @else
                                                                <a class="btn btn-sm btn-light" title="Process"
                                                                    href="{{ url('stl/' . $value['id'] . '/restore') }}"
                                                                    onclick="return confirm('Are you sure you want to restore this STL?');"><i
                                                                        class="ico icon-bold-restart text-white"
                                                                        style="font-size: 16px;"></i></a>
                                                            @endif
                                                        </div>


                                                    </td>
                                                @endif


                                                @if (@$value['type'] == 3)
                                                    <td></td>
                                                    <td class="text-center">
                                                        {{ date('d/m/Y', strtotime(@$value['payment_req_date'])) }}</td>
                                                    <td>
                                                        <div>
                                                            {{ @$value['payment_supplier_name'] }}
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        {{ @App\SysHelper::com_curr_format(@$value['payment_set_amount'], 2, '.', ',') }}<?php $amount_aed += $value['payment_set_amount'];
                                                        $debit += $value['payment_set_amount']; ?>
                                                    </td>
                                                    <td class="text-center">--</td>
                                                    <td class="text-end">
                                                        {{ @App\SysHelper::com_curr_format(@$amount_aed, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ @App\SysHelper::com_curr_format((@$value['payment_set_amount'] * 20) / 100, 2, '.', ',') }}
                                                        <?php $deposit += ($value['payment_set_amount'] * 20) / 100; ?></td>
                                                    <td class="text-end">{{ @$value['payment_stl_ref_no'] }}</td>
                                                    <td></td>
                                                    <td class="text-center">
                                                        {{ date('d/m/Y', strtotime(@$value['payment_settlement_date'])) }}
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        @if (@$value['status'] == 1)
                                                            <span class="text-success">Paid</span>
                                                        @else
                                                            <span class="text-danger">Canceled</span>
                                                        @endif
                                                    </td>
                                                    <td>

                                                        <input type="hidden" id="edit_doc_number_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_stl_no'] }}" />
                                                        <input type="hidden" id="edit_req_date_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_req_date'] }}" />
                                                        <input type="hidden" id="edit_stl_no_{{ $value['id'] }}"
                                                            value="{{ @$value['stl_id'] }}" />
                                                        <input type="hidden" id="edit_stl_ref_no_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_stl_ref_no'] }}" />
                                                        <input type="hidden" id="edit_supplier_id_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_supplier_id'] }}" />
                                                        <input type="hidden" id="edit_supplier_name_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_supplier_name'] }}" />
                                                        <input type="hidden" id="edit_set_amount_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_set_amount'] }}" />
                                                        <input type="hidden"
                                                            id="edit_settlement_date_{{ $value['id'] }}"
                                                            value="{{ @$value['payment_settlement_date'] }}" />

                                                        <button class="btn-sm btn btn-light" title="Edit"
                                                            onclick="stl_payment_edit({{ $value['id'] }})"><i
                                                                class="ico icon-outline-pen-2"
                                                                style="font-size: 16px;"></i></button>
                                                    </td>
                                                @endif

                                            </tr>
                                        @endforeach
                                        @if($recordCount == 0)
                                            <tr><td colspan="18" class="text-center">No records found</td></tr>
                                        @endif
                                    @else
                                        <tr><td colspan="18" class="text-center">No records found</td></tr>
                                    @endif

                                    <tr><td colspan="18" class="text-center"  style="height:25px;"></td></tr>

                                </tbody>
                                <tfoot>
                                   

                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format(@$debit, 2, '.', ',') }}
                                        </th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format(@$credit, 2, '.', ',') }}
                                        </th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$debit - $credit, 2, '.', ',') }}</th>
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format(@$deposit, 2, '.', ',') }}
                                        </th>

                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$stl_intrest, 2, '.', ',') }}</th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$bank_charges, 2, '.', ',') }}</th>
                                        <th class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$other_charges, 2, '.', ',') }}</th>

                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                       

                        <script>
                            function show_tool_tip(id) {
                                $('#desc_' + id).css('white-space', '');
                            }

                            function hide_tool_tip(id) {
                                $('#desc_' + id).css('white-space', 'nowrap');
                            }
                        </script>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <script>
        function stl_edit(id) {

            $('#edit_stl_id').val(id);
            $('#edit_stl_name').text($('#doc_number_' + id).val());
            $('#stl_ref_no').val($('#stl_ref_no_' + id).val());
            // $('#processing_date').val($('#processing_date_' + id).val());
            $('#processing_date').val($('#processing_date_' + id).val() ? $('#processing_date_' + id).val().split('-')
                .reverse().join('/') : '');

            // $('#settlement_date').val();
            $('#settlement_date').val($('#settlement_date_' + id).val() ? $('#settlement_date_' + id).val().split('-')
                .reverse().join('/') : '');

            $('#stl_interest').val($('#stl_interest_' + id).val());
            $('#bank_charges').val($('#bank_charges_' + id).val());
            $('#other_charges').val($('#other_charges_' + id).val());
            $('#editModal').modal('show');
        }

        function stl_payment(id) {
            $('#payment_stl_id').val(id);
            $('#payment_stl_name').text($('#doc_number_' + id).val());

            $('#payment_stl_no').val($('#stl_no_' + id).val());
            $('#payment_stl_ref_no').val($('#stl_ref_no_' + id).val());
            $('#payment_supplier_id').val($('#supplier_id_' + id).val());
            $('#payment_supplier_name').val($('#supplier_name_' + id).val());
            $('#payment_set_amount').val($('#set_amount_' + id).val());

            $('#paymentModal').modal('show')
        }

        function stl_payment_edit(id) {
            $('#edit_payment_stl_id').val(id);
            $('#edit_payment_stl_name').text($('#edit_doc_number_' + id).val());
            // $('#edit_payment_req_date').text($('#edit_req_date_' + id).val());
             $('#edit_payment_req_date').val($('#edit_req_date_' + id).val() ? $('#edit_req_date_' + id).val().split('-')
                .reverse().join('/') : '');
            $('#edit_payment_stl_no').val($('#edit_doc_number_' + id).val());
            $('#edit_payment_stl_ref_no').val($('#edit_stl_ref_no_' + id).val());
            $('#edit_payment_supplier_id').val($('#edit_supplier_id_' + id).val());
            $('#edit_payment_supplier_name').val($('#edit_supplier_name_' + id).val());
            $('#edit_payment_set_amount').val($('#edit_set_amount_' + id).val());
            // $('#edit_payment_settlement_date').val($('#edit_settlement_date_' + id).val());
             $('#edit_payment_settlement_date').val($('#edit_settlement_date_' + id).val() ? $('#edit_settlement_date_' + id).val().split('-')
                .reverse().join('/') : '');

            $('#paymentEditModal').modal('show')
        }
    </script>
    {{-- STL EDIT MODEL --}}
    {{-- <a data-toggle="modal" id="btn_edit_modal" data-bs-toggle="modal" data-bs-target="#editModal"  data-backdrop="static"
        data-keyboard="false"></a> --}}


    <div class="modal side-panel fade" id="editModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 600px !important;">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-edit-update', 'method' => 'POST', 'id' => 'stl-edit-update']) }}
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit STL - <span
                            style=" font: inherit;color: inherit;font-weight: inherit;" id="edit_stl_name"></span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" id="edit_stl_id" name="edit_stl_id" />

                                <div class="col-lg-12">
                                    <label for="" class="form-label">STL Ref. No</label>

                                    <input class="form-control" type="text" id="stl_ref_no" name="stl_ref_no"
                                        value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Processing Date</label>

                                    <input class="form-control date-picker" type="text" id="processing_date"
                                        name="processing_date" value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Settlement Date</label>

                                    <input class="form-control date-picker" type="text" id="settlement_date"
                                        name="settlement_date" value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">STL Interest</label>

                                    <input class="form-control" type="text" id="stl_interest" name="stl_interest"
                                        value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Bank Charges</label>

                                    <input class="form-control" type="text" id="bank_charges" name="bank_charges"
                                        value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Other Charges</label>

                                    <input class="form-control" type="text" id="other_charges" name="other_charges"
                                        value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="custom_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>

            </div>
            {{ Form::close() }}
        </div>
    </div>

    {{-- <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-edit-update', 'method' => 'POST', 'id' => 'stl-edit-update']) }}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit STL - <label id="edit_stl_name"></label></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_stl_id" name="edit_stl_id" />
                    <div class="row">
                        <div class="col-lg-12 pt-2">
                            STL Ref. No
                            <input class="form-control" type="text" id="stl_ref_no" name="stl_ref_no"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Processing Date
                            <input class="form-control" type="date" id="processing_date" name="processing_date"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Settlement Date
                            <input class="form-control" type="date" id="settlement_date" name="settlement_date"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            STL Interest
                            <input class="form-control" type="text" id="stl_interest" name="stl_interest"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Bank Charges
                            <input class="form-control" type="text" id="bank_charges" name="bank_charges"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Other Charges
                            <input class="form-control" type="text" id="other_charges" name="other_charges"
                                value="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" id="closeimport"
                        data-dismiss="modal">Close</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div> --}}
    {{-- STL EDIT MODEL --}}

    {{-- STL PAYMENT MODEL --}}
    {{-- <a data-toggle="modal" id="btn_payment_modal" data-target="#paymentModal" data-toggle="modal"
        data-backdrop="static" data-keyboard="false"></a> --}}
    {{-- <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-payment-add', 'method' => 'POST', 'id' => 'stl-payment-add']) }}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">STL Payment - <label id="payment_stl_name"></label>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="payment_stl_id" name="payment_stl_id" />
                    <div class="row">
                        <div class="col-lg-12 pt-2">
                            Req. Date
                            <input class="form-control" type="date" id="payment_req_date" name="payment_req_date"
                                value="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            STL No.
                            <input class="form-control" type="text" id="payment_stl_no" name="payment_stl_no"
                                value="" readonly />
                        </div>
                        <div class="col-lg-12 pt-2">
                            STL Ref. No
                            <input class="form-control" type="text" id="payment_stl_ref_no" name="payment_stl_ref_no"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Supplier Name
                            <input class="form-control" type="text" id="payment_supplier_name"
                                name="payment_supplier_name" value="" />
                            <input type="hidden" id="payment_supplier_id" name="payment_supplier_id" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Set. Amount
                            <input class="form-control" type="text" id="payment_set_amount" name="payment_set_amount"
                                value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Settlement Date
                            <input class="form-control" type="date" id="payment_settlement_date"
                                name="payment_settlement_date" value="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Payment</button>
                    <button type="button" class="btn btn-secondary" id="closeimport"
                        data-dismiss="modal">Close</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div> --}}

    <div class="modal side-panel fade" id="paymentModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 600px !important;">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-payment-add', 'method' => 'POST', 'id' => 'stl-payment-add']) }}
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">STL Payment - <span
                            style=" font: inherit;color: inherit;font-weight: inherit;" id="payment_stl_name"></span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <input type="hidden" id="payment_stl_id" name="payment_stl_id" />
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="" class="form-label">Req. Date</label>

                                    <input class="form-control date-picker" type="text" id="payment_req_date"
                                        name="payment_req_date" value="{{ date('d/m/Y') }}" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">STL No.</label>

                                    <input class="form-control" type="text" id="payment_stl_no" name="payment_stl_no"
                                        value="" readonly />
                                </div>
                                <div class="col-lg-12 pt-2">
                                    <label for="" class="form-label">STL Ref. No</label>


                                    <input class="form-control" type="text" id="payment_stl_ref_no"
                                        name="payment_stl_ref_no" value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Supplier Name</label>

                                    <input class="form-control" type="text" id="payment_supplier_name"
                                        name="payment_supplier_name" value="" />
                                    <input type="hidden" id="payment_supplier_id" name="payment_supplier_id" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Set. Amount</label>

                                    <input class="form-control" type="text" id="payment_set_amount"
                                        name="payment_set_amount" value="" />
                                </div>
                                <div class="col-lg-12 pt-2">

                                    <label for="" class="form-label">Settlement Date</label>

                                    <input class="form-control date-picker" type="text" id="payment_settlement_date"
                                        name="payment_settlement_date" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="custom_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Payment
                    </button>
                </div>

            </div>
            {{ Form::close() }}
        </div>
    </div>
    {{-- STL PAYMENT MODEL --}}

    {{-- STL PAYMENT MODEL EDIT --}}
    {{-- <a data-toggle="modal" id="btn_payment_edit_modal" data-target="#paymentEditModal" data-toggle="modal"
        data-backdrop="static" data-keyboard="false"></a> --}}
    {{-- <div class="modal fade" id="paymentEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-payment-update', 'method' => 'POST', 'id' => 'stl-payment-update']) }}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">EDIT STL Payment - <label
                            id="edit_payment_stl_name"></label></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_payment_stl_id" name="payment_stl_id" />
                    <div class="row">
                        <div class="col-lg-12 pt-2">
                            Req. Date
                            <input class="form-control" type="date" id="edit_payment_req_date"
                                name="payment_req_date" value="{{ date('Y-m-d') }}" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            STL No.
                            <input class="form-control" type="text" id="edit_payment_stl_no" name="payment_stl_no"
                                value="" readonly />
                        </div>
                        <div class="col-lg-12 pt-2">
                            STL Ref. No
                            <input class="form-control" type="text" id="edit_payment_stl_ref_no"
                                name="payment_stl_ref_no" value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Supplier Name
                            <input class="form-control" type="text" id="edit_payment_supplier_name"
                                name="payment_supplier_name" value="" />
                            <input type="hidden" id="edit_payment_supplier_id" name="payment_supplier_id" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Set. Amount
                            <input class="form-control" type="text" id="edit_payment_set_amount"
                                name="payment_set_amount" value="" />
                        </div>
                        <div class="col-lg-12 pt-2">
                            Settlement Date
                            <input class="form-control" type="date" id="edit_payment_settlement_date"
                                name="payment_settlement_date" value="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Payment</button>
                    <button type="button" class="btn btn-secondary" id="closeimport"
                        data-dismiss="modal">Close</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div> --}}


    <div class="modal side-panel fade" id="paymentEditModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 600px !important;">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stl-payment-update', 'method' => 'POST', 'id' => 'stl-payment-update']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">EDIT STL Payment - <span
                            style=" font: inherit;color: inherit;font-weight: inherit;" id="edit_payment_stl_name"></span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <input type="hidden" id="edit_payment_stl_id" name="payment_stl_id" />
                            <div class="row">
                                <div class="col-lg-12">
                                   
                                    <label for="" class="form-label"> Req. Date</label>
                                    <input class="form-control date-picker" type="text" id="edit_payment_req_date"
                                        name="payment_req_date" value="}" />
                                </div>
                                <div class="col-lg-12 pt-2">
                                    
                                    <label for="" class="form-label"> STL No.</label>

                                    <input class="form-control" type="text" id="edit_payment_stl_no"
                                        name="payment_stl_no" value="" readonly />
                                </div>
                                <div class="col-lg-12 pt-2">
                                    
                                    <label for="" class="form-label"> STL Ref. No</label>

                                    <input class="form-control" type="text" id="edit_payment_stl_ref_no"
                                        name="payment_stl_ref_no" value="" />
                                </div>
                                <div class="col-lg-12 pt-2">
                                    
                                    <label for="" class="form-label"> Supplier Name</label>

                                    <input class="form-control" type="text" id="edit_payment_supplier_name"
                                        name="payment_supplier_name" value="" />
                                    <input type="hidden" id="edit_payment_supplier_id" name="payment_supplier_id" />
                                </div>
                                <div class="col-lg-12 pt-2">
                                    
                                    <label for="" class="form-label"> Set. Amount</label>

                                    <input class="form-control" type="text" id="edit_payment_set_amount"
                                        name="payment_set_amount" value="" />
                                </div>
                                <div class="col-lg-12 pt-2">
                                    
                                    <label for="" class="form-label">Settlement Date</label>

                                    <input class="form-control date-picker" type="text" id="edit_payment_settlement_date"
                                        name="payment_settlement_date" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="custom_add_btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update Payment
                    </button>
                </div>

            </div>
            {{ Form::close() }}
        </div>
    </div>
 
    {{-- STL PAYMENT MODEL EDIT --}}
<script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
         var columnWidths = ['50', '50', '50', '100','100','100','100','50','50','50','100','100','100','50','50','50','50','100'];

        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
        });

        // Apply the same widths to <tfoot>
        $tfootTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

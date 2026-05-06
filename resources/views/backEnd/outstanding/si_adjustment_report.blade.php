@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>
    
        
<script>
function set_total(id,at){
    $('#sum_'+id).text(at.toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
    $('#collapse'+id).css('display','');
    $('#account_table'+id).css('display','');
}
function formatAmountToNumber(input) {
    if (!input) return 0;

    let inputStr = String(input).replace(/,/g, '').trim();
    let number = parseFloat(inputStr);
    return isNaN(number) ? 0 : number;
}
function set_total_addmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal + additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function set_total_lessmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal - additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function check_total(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    if(currentTotal != additionalAmount){
        $('#sum_' + id).css('color', 'red');
    }
}
</script>

    <div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    SI Adjustment Report
                </h4>
                <div class="purchase-order-content-header-right">
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'si-adjustment-report', 'method' => 'post', 'id' => 'si-adjustment-report']) }}
                        <div class="row">
                            <div class="col-md-5 mb-2">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="form-check-label">
                                            <label>@lang('Customer Name')</label>
                                            <select class="form-control js-account-select" name="customer" id="customer">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="form-check-label">
                                            <label>@lang('From Date')</label>
                                            <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{!empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('d/m/Y') : '' }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="form-check-label">
                                            <label>@lang('To date ')</label>
                                            <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{!empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('d/m/Y') : '' }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if(@$filter_by == "") selected @endif>-Select-</option>
                                    <option value="this_month" @if(@$filter_by == "this_month") selected @endif>This Month</option>
                                    <option value="today" @if(@$filter_by == "today") selected @endif>Today</option>
                                    <option value="this_week" @if(@$filter_by == "this_week") selected @endif>This Week</option>
                                    <option value="last_week" @if(@$filter_by == "last_week") selected @endif>Last Week</option>                                    
                                    <option value="last_month" @if(@$filter_by == "last_month") selected @endif>Last Month</option>
                                    <option value="this_quarter" @if(@$filter_by == "this_quarter") selected @endif>This Quarter</option>
                                    <option value="pre_quarter" @if(@$filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                                    <option value="this_year" @if(@$filter_by == "this_year") selected @endif>This Year</option>
                                    <option value="last_year" @if(@$filter_by == "last_year") selected @endif>Last Year</option>
                                </select>
                            </div>
                            <script>
                                function set_filter(){
                                if($('#from_date').val()!="" || $('#to_date').val() != "")
                                {
                                    $('#filter_by').val('')
                                }
                                }
                            </script>
                            <div class="col-1">&nbsp;<br />
                                <button class="btn btn-light" type="submit">
                                    <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                </button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mb-4">

                        <table class="table table-hover form-item-table" id="dataTable" width="100%" cellspacing="0" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                            @php
                                $grouped = $sales_invoice->groupBy('customer');
                            @endphp
                            @foreach($grouped as $customer_id => $invoices)
                            @php $total_balance=0;
                            $total_invoice_amount = 0;
                            $total_adjustments = 0;
                            $total_balance = 0;
                            $total_total_balance = 0;
                            $total_cheque_amount = 0;
                            @endphp
    

                                <thead>
                                    <tr>
                                        <td style="cursor: pointer; padding: 5px 10px !important;" onclick="show_details({{ $invoices->first()->account_id }})" colspan="17"><h7>{{ $invoices->first()->account_code }} - {{ $invoices->first()->account_name }}</h7> <label style="float: right;" class="main_sum" id="sum_{{ $invoices->first()->account_id }}"></label></td>
                                    </tr>
                                    <tr class="{{ $invoices->first()->account_id }}" style="display: none;">
                                        <th class="border text-center">Doc Number</th>
                                        <th class="border text-center">Doc Date</th>
                                        <th class="border text-center">LPO Number</th>
                                        <th class="border text-center">Deal ID</th>
                                        <th class="border text-end">Amount</th>
                                        <th class="border text-end">Adjustments</th>
                                        <th class="border text-end">Balance</th>
                                        <th class="border text-end">Total Balance</th>

                                        <th class="border text-center">Doc Date</th>
                                        <th class="border text-center">Receipt No</th>
                                        <th class="border text-end">Amount</th>
                                        <th class="border text-center">Cheque Date</th>
                                        <th class="border text-center">Cheque No</th>
                                        <th class="border text-center">Receipt Date</th>


                                        <th class="border text-center">Sales Person</th>
                                        <th class="border text-center">Due Date</th>
                                        <th class="border text-center">Over Due</th>
                                    </tr>
                                </thead>
                                <tbody class="{{ $invoices->first()->account_id }}" style="display: none;">
                                    @foreach($invoices as $invoice)
                                    
                                            <?php $adjustment_data =  $sys_adjustment_list->where('bi_doc_no',$invoice->doc_number);
                                            if(count($adjustment_data)>0){
                                                foreach ($adjustment_data as $item) {
                                                    $adjustments =  $item['total_paid'];
                                                    $balance = max(0, $invoice->amount - $adjustments);
                                                    $r_doc_date = $item['r_doc_date'];
                                                    $bi_doc_numbers = $item['bi_doc_numbers'];
                                                    $cheque_amount = $item['cheque_amount'];
                                                    $r_cheque_date = $item['r_cheque_date'];
                                                    $r_cheque_number = $item['r_cheque_number'];
                                                    $r_doc_date = $item['r_doc_date'];
                                                    $displayDate = '';
                                                    if (!empty($item['r_doc_date'])) {
                                                        $displayDate = $item['r_doc_date'];
                                                    } elseif (!empty($item['j_doc_date'])) {
                                                        $displayDate = $item['j_doc_date'];
                                                    } elseif (!empty($item['s_doc_date'])) {
                                                        $displayDate = $item['s_doc_date'];
                                                    }
                                                }
                                            } else {
                                                $adjustments = 0;
                                                $balance = 0;
                                                $r_doc_date = 0;
                                                $bi_doc_numbers = "";
                                                $cheque_amount = "";
                                                $r_cheque_date = "";
                                                $r_cheque_number = "";
                                                $r_doc_date = "";
                                                $displayDate="";
                                            }
                                            ?>
                                        <tr>
                                            <td class="border text-center"><a href="{{url('get-url-sales-invoice/'.$invoice->doc_number)}}" target="_blank">{{ $invoice->doc_number }}</a></td>
                                            <td class="border text-center">{{ date('d/m/Y', strtotime(@$invoice->doc_date)) }}</td>
                                            <td class="border text-center">{{ $invoice->lpo_number }}</td>
                                            <td class="border text-center"><a href="{{url('get-url-deal-track/'.@$invoice->deal_code->code)}}" target="_blank">{{ @$invoice->deal_code->code }}</a></td>

                                            <td class="border text-end">{{ number_format($invoice->amount, 2) }} <?php $total_invoice_amount += @$invoice->amount; ?></td>
                                            <td class="border text-end">{{ number_format(@$adjustments, 2) }}  <?php $total_adjustments += @$adjustments; ?></td>
                                            <td class="border text-end">{{ number_format(@$balance, 2) }}  <?php $total_balance += @$balance; ?></td>
                                            <td class="border text-end">{{ number_format(@$total_balance, 2) }} <?php $total_total_balance += @$balance; ?></td>
                                            
                                            <td class="border text-center">{{ @$displayDate ? date('d/m/Y', strtotime($displayDate)) : '' }}</td> 
                                            
                                                @if(Illuminate\Support\Str::contains(@$bi_doc_numbers, ['BR', 'CR']))
                                <td class="border text-center">
                                    <a href="{{ url('get-url-receipt/' . @$bi_doc_numbers) }}" target="_blank">{{ @$bi_doc_numbers }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains(@$bi_doc_numbers, ['JV']))
                                <td class="border text-center">
                                    <a href="{{ url('get-url-journalvoucher/' . @$bi_doc_numbers) }}" target="_blank">{{ @$bi_doc_numbers }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains(@$bi_doc_numbers, ['SR']))
                                <td class="border text-center">
                                    <a href="{{ url('get-url-sales-return/' . @$bi_doc_numbers) }}" target="_blank">{{ @$bi_doc_numbers }}</a>
                                </td>
                            @else
                            <td class="border text-center">
                                    {{ @$bi_doc_numbers }}
                                </td>
                            @endif
                                            <td class="border text-end">{{ @$cheque_amount }}<?php /*$total_cheque_amount += @$cheque_amount;*/ ?></td>
                                            <td class="border text-center" class="border">{{ @$r_cheque_date ? date('d/m/Y', strtotime($r_cheque_date)) : '' }}</td> 
                                            <td class="border text-center">{{ @$r_cheque_number }}</td>
                                            <td class="border text-center">{{ @$displayDate ? date('d/m/Y', strtotime($displayDate)) : '' }}</td> 

                                            <td class="border text-center">{{ @$invoice->salesman->full_name }}</td>

                                            <?php $DueData =  App\SysHelper::get_due_date_sales_invoice_report($sys_payment_terms_list,$invoice->payment_terms,$invoice->doc_date,$displayDate);  ?>
                                            @if($displayDate!="")
                                            <td class="border text-center">{{ $DueData[0] }}</td>
                                            <td class="border text-center">{{ $DueData[1] }}</td>
                                            @else
                                            <td class="border"></td>
                                            <td class="border"></td>
                                            @endif
                                        </tr>

                            


                                    @endforeach
                                    <tr>
                                        <th class="border text-end" colspan="4">Total</th>
                                        <th class="border text-end">{{ number_format($total_invoice_amount, 2) }}</th>
                                        <th class="border text-end">{{ number_format($total_adjustments, 2) }}</th>
                                        <th class="border text-end">{{ number_format($total_balance, 2) }}</th>
                                        <th class="border text-end">{{ number_format($total_total_balance, 2) }}</th>
                                        <th class="border" colspan="2"></th>
                                        <th class="border">
                                            {{-- {{ $total_cheque_amount }} --}}
                                        </td>
                                        <th class="border" style="margin-bottom: 40px;" colspan="6"></th>
                                    </tr>
                                    <tr><td colspan="17">

                            <script>
                                set_total({{ $invoices->first()->account_id }},{{ $total_total_balance }});
                            </script>


                                    <?php
                                        $account_id = $invoices->first()->account_id;
                                        $unadj_list = $list_of_unadjusted->where('account_id',$account_id);
                                        $unadj_list_jv_to_jv = $list_of_unadjusted_jv_to_jv->where('account_id',$account_id); ?>
                  
                  @if (count($unadj_list)>0 || count($unadj_list_jv_to_jv)>0)
                  <b>List of Unadjusted balance:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Receipt No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($unadj_list)>0)
                        @foreach ($unadj_list as $p)
                        <tr>
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BR', 'CR']))
                                <td class="border">
                                    <a href="{{ url('get-url-receipt/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['SR']))
                                <td class="border">
                                    <a href="{{ url('get-url-sales-return/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="border">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                        </tr>
                        <script>
                            set_total_lessmore({{ $invoices->first()->account_id }},{{ $p->amount - $p->adj_amount }})
                        </script>
                        @endforeach
                        @endif
                        
                        @if (count($unadj_list_jv_to_jv)>0)
                        @foreach ($unadj_list_jv_to_jv as $p)
                        <tr>
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="border">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->amount2,2,'.',',') }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                        </tr>
                        <script>
                            set_total_lessmore({{ $invoices->first()->account_id }},{{ $p->amount - $p->amount }})
                        </script>
                        @endforeach
                        @endif                       

                    </tbody>
                  </table>
                  @endif

                  <?php $pdc = $list_of_unadjusted_pdc->where('account_id',$account_id); ?>
                  @if (count($pdc)>0)
                  <b>List of Unadjusted PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Receipt No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Receipt Date</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif

                  <?php $pdc = $list_of_adjusted_pdc->where('account_id',$account_id); ?>
                  @if (count($pdc)>0)
                  <b>List of PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Receipt No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Receipt Date</th>
                            <th class="border">Invoice Adjusted</th>
                            <th class="border text-end">Adjusted</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="border">
                                <a style="cursor: pointer;" onclick="row_det_fun('{{ $p->doc_number }}','{{ $p->bi_doc_no }}')">{{ $p->bi_doc_no }}</a>
                            </td>
                            <td class="border text-end">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                            <td class="border">{{ $p->remarks }}</td>
                            
                        </tr>
                            <script>
                                set_total_addmore({{ $invoices->first()->account_id }},{{ $p->adj_amount }})
                            </script>
                        <tr style="display: none;" id="row_det_{{ $p->doc_number }}">
                            <td></td>
                            <td colspan="9">
                                    <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;" id="row_det_table_{{ $p->doc_number }}">
                                        <thead>
                                            <tr>
                                                <th class="border">Doc Date</th>
                                                <th class="border">Doc No</th>
                                                <th class="border">LPO No</th>
                                                <th class="border">Deal ID</th>
                                                <th class="border text-end">Amount</th>
                                                <th class="border text-end">Adjustments</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif    




                                    </td></tr>
                                </tbody>
                        @endforeach
                            
                    </table>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>

    
<script>
    function show_details(account_code) {
        $('.' + account_code).toggle(); // Toggle visibility
    }
</script>

<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
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
            minimumInputLength: 2
        });
    }

    // Initial init
    initAccountSelect2('.js-account-select');

    // Re-initialize on focus (if needed for dynamically added fields)
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // Open dropdown and focus search box on click
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Focus the search input inside the opened Select2 dropdown
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 0);
    });
});
</script>
        
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
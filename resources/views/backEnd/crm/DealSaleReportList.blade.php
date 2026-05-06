@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <style>
        .progress {
            height: 15px;
        }
    </style>

    <script>
        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>
    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0"> Sales Report by User
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">


                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-sales-report-list', 'method' => 'POST', 'id' => 'crm-deals-sales-report']) }}
                        <input type="hidden" name="company_id" id="company_id"
                            value="{{ session('logged_session_data.company_id') }}" />
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if ($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date" value="{{ @App\SysHelper::normalizeToDmy($ctrl_date) }}" required onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" id="date2" type="text" autocomplete="off" name="date2"
                                    value="{{ @App\SysHelper::normalizeToDmy($ctrl_date2) }}" required onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if ($filter_by == '') selected @endif>-Select-
                                    </option>
                                    <option value="this_month" @if ($filter_by == 'this_month') selected @endif>This Month
                                    </option>
                                    <option value="today" @if ($filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if ($filter_by == 'this_week') selected @endif>This Week
                                    </option>
                                    <option value="last_week" @if ($filter_by == 'last_week') selected @endif>Last Week
                                    </option>
                                    <option value="last_month" @if ($filter_by == 'last_month') selected @endif>Last Month
                                    </option>
                                    <option value="this_quarter" @if ($filter_by == 'this_quarter') selected @endif>This
                                        Quarter</option>
                                    <option value="pre_quarter" @if ($filter_by == 'pre_quarter') selected @endif>Previous
                                        Quarter</option>
                                    <option value="this_year" @if ($filter_by == 'this_year') selected @endif>This Year
                                    </option>
                                    <option value="last_year" @if ($filter_by == 'last_year') selected @endif>Last Year
                                    </option>
                                </select>
                            </div>
                            <script>
                                function set_filter() {
                                    if ($('#date').val() != "" || $('#date2').val() != "") {
                                        $('#filter_by').val('')
                                    }
                                }
                            </script>

                            <div class="col-2 mb-2">
                                <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}

                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">



            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width:100px">@lang('Company')</th>
                            <th style="width:70px" class="text-center">@lang('Deal ID')</th>
                            <th style="width:70px" class="text-center">@lang('Invoice')</th>
                            <th style="width:70px" class="text-center">@lang('Date')</th>
                            <th style="width:150px">@lang('Customer')</th>
                            <th style="width:130px">@lang('Deal Name')</th>
                            
                            <th style="display: none;">@lang('Accounts')</th>
                            <th style="display: none;">@lang('Sales')</th>
                            <th style="width:120px" class="text-center">@lang('Purchase')</th>
                            <th style="width:120px" class="text-center">@lang('Invoice')</th>
                            <th style="width:120px" class="text-center">@lang('Delivery')</th>
                            <th style="width:120px" class="text-center">@lang('Receivables')</th>
                            <th style="width:100px" class="text-end">@lang('Invoice Value')</th>
                            <th style="width:100px" class="text-end">@lang('GP')</th>
                            <th style="width:70px" class="text-end">@lang('GP %')</th>
                            <th style="width:70px" class="text-center">@lang('Currency')</th>
                           
                        </tr>
                    </thead>


                    <tbody>
                        <?php $deal_value = 0;
                        $deal_value_total = 0;
                        $deal_profit = 0;
                        $deal_profit_total = 0; ?>
                        @foreach ($deals1 as $value)
                    
                            <tr>
                                <td>
                                    {{ @$value->company->company_name }}
                                </td>
                                <td class="text-center"><a href="{{ url('crm-deal-track-approval-list/' . $value->trackid) }}"
                                        target="_blank">{{ @$value->code }}</a></td>
                                <td class="text-center"><a href="{{ url('get-url-sales-invoice/' . $value->doc_number) }}"
                                        target="_blank">{{ @$value->doc_number }}</a></td>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                <td>
                                    @php
                                        $customer_name = @App\SysChartofAccounts::find($value->customer);
                                    @endphp
                                     {{ @$customer_name->account_name }}
                                </td>
                                <td>{{ @$value->deal_code->deal_name }}</td>
                                {{-- <td><div style="width:100px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->title}}</div></td> --}}
                                <td style="display: none;">
                                    @if ($value->accounts == 1)
                                        <span class="badge bg-success">Accounts Approved</span>
                                    @elseif($value->accounts == 2)
                                        <span class="badge bg-danger">Accounts Rejected</span>
                                    @elseif($value->accounts == 3)
                                        <span class="badge bg-primary">Accounts Pending</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td style="display: none;">
                                    @if ($value->sales == 1)
                                        <span class="badge bg-success">Sales Approved</span>
                                    @elseif($value->sales == 2)
                                        <span class="badge bg-danger">Sales Rejected</span>
                                    @elseif($value->sales == 3)
                                        <span class="badge bg-primary">Sales Pending</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($value->purchease_approval == 0)
                                        <span class="badge bg-info">Not Applicable</span>
                                    @else
                                        @if ($value->purchease == 1)
                                            <span class="badge bg-success">Purchase Approved</span>
                                        @elseif($value->purchease == 2)
                                            <span class="badge bg-danger">Purchase Rejected</span>
                                        @elseif($value->purchease == 3)
                                            <span class="badge bg-primary">Purchase Pending</span>
                                        @elseif($value->purchease == 4)
                                            <span class="badge bg-primary">Partial Delivery</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->invoice_approval == 0)
                                        <span class="badge bg-info">Not Applicable</span>
                                    @else
                                        @if ($value->invoice == 1)
                                            <span class="badge bg-success">Invoice Approved</span>
                                        @elseif($value->invoice == 2)
                                            <span class="badge bg-danger">Invoice Rejected</span>
                                        @elseif($value->invoice == 3)
                                            <span class="badge bg-primary">Invoice Pending</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->delivery_approval == 0)
                                        <span class="badge bg-info">Not Applicable</span>
                                    @else
                                        @if ($value->delivery == 1)
                                            <span class="badge bg-success">Delivery Completed</span>
                                        @elseif($value->delivery == 2)
                                            <span class="badge bg-danger">Delivery Rejected</span>
                                        @elseif($value->delivery == 3)
                                            <span class="badge bg-primary">Out For Delivery</span>
                                        @elseif($value->delivery == 4)
                                            <span class="badge bg-primary">Pending For Delivery</span>
                                        @elseif($value->delivery == 5)
                                            <span class="badge bg-primary">Ready For Delivery</span>
                                        @elseif ($value->delivery == 6)
                                            <span class="badge bg-primary">Partial Delivery</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($value->receivables_approval == 0)
                                        <span class="badge bg-info">Not Applicable</span>
                                    @else
                                        @if ($value->receivables == 1)
                                            <span class="badge bg-success">Payment Received</span>
                                        @elseif($value->receivables == 2)
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($value->receivables == 3)
                                            <span class="badge bg-primary">Payment Pending</span>
                                        @elseif($value->receivables == 4)
                                            <span class="badge bg-dark">Order Cancelled</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    @endif
                                </td>
                                <?php
                                $deal_value = @App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_value);
                                $deal_profit = @App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_profit);
                                $deal_percentage = round(($deal_profit / $deal_value) * 100, 2);
                                $gp = (($value->total_taxableamount - $value->deal_discount) * $deal_percentage) / 100;
                                ?>
                                <td class="text-end">

                                    @if ($value->source == 'Fulfillment')
                                        ({{ @App\SysHelper::com_curr_format(@App\SysHelper::get_aed_amount_new($value->deal_currency, $value->deal_value), 2, '.', ',') }})
                                        &nbsp;&nbsp;&nbsp;
                                    @endif

                                    @php
                                        /*if ($value->is_partial_invoice==1){
                                            $aed=@App\SysHelper::get_deal_value(abs($value->partial_invoice_amount),$value->source,$value->deal_currency,$value->deal_percent,$value->cust_id); 
                                        } else {
                                            $aed=@App\SysHelper::get_deal_value($value->deal_value,$value->source,$value->deal_currency,$value->deal_percent,$value->cust_id); 
                                        }*/
                                        if ($value->is_partial_invoice == 1) {
                                            $aed = @App\SysHelper::get_aed_amount_new(
                                                $value->currency,
                                                $value->partial_invoice_amount,
                                            );
                                        } else {
                                            $aed = @App\SysHelper::get_aed_amount_new(
                                                $value->currency,
                                                $value->total_taxableamount - $value->deal_discount,
                                            );
                                        }
                                    @endphp
                                    {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }} <?php $deal_value_total += $aed; ?>
                                </td>
                                <td class="text-end">
                                    {{ @App\SysHelper::com_curr_format($gp, 2, '.', ',') }}
                                    <?php $deal_profit_total += $gp; ?></td>
                                <td class="text-end">
                                    @if ($value->deal_profit != 0)
                                        {{ round($deal_percentage, 2) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ @$value->currency_name->code }}</td>
                             

                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end">Total: </th>
                        <th class="text-end">{{ @App\SysHelper::com_curr_format($deal_value_total, 2, '.', ',') }}</th>
                        <th class="text-end">{{ @App\SysHelper::com_curr_format($deal_profit_total, 2, '.', ',') }}</th>
                        <th></th>
                        <th class="text-end"></th>
                       
                    </tfoot>



                </table>
            </div>

            {{-- Sales Return Table --}}
            <div class="mt-3">
                <h4 class="mb-3">Sales Returns</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-hover data-table" id="long-list" style="table-layout: fixed;width:100%">
                        <thead>
                            <tr>
                                <th style="width:100px">@lang('Company')</th>
                                <th style="width:70px" class="text-center">@lang('Deal ID')</th>
                                <th style="width:70px" class="text-center">@lang('Doc Number')</th>
                                <th style="width:70px" class="text-center">@lang('Date')</th>
                                <th style="width:150px">@lang('Customer')</th>
                                <th style="width:180px">@lang('Deal Name')</th>
                                <th style="width:80px" class="text-end">@lang('Invoice Value')</th>
                                <th style="width:70px" class="text-end">@lang('GP')</th>
                                <th style="width:70px" class="text-end">@lang('GP %')</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $sr_value_total = 0;
                            $sr_profit_total = 0; 
                            ?>
                            @if(isset($salesReturns) && count($salesReturns) > 0)
                                @foreach ($salesReturns as $sr)
                                    <tr>
                                        <td>{{ @$sr->company->company_name ?? App\SysCompany::find($sr->company_id)->company_name ?? '--' }}</td>
                                        <td class="text-center">
                                            @if($sr->deal_code)
                                                <a href="{{ url('crm-deal-track-approval-list/' . $sr->deal_id) }}" target="_blank">{{ $sr->deal_code }}</a>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('sales-return/' . $sr->id) }}" target="_blank">{{ $sr->doc_number }}</a>
                                        </td>
                                        <td class="text-center">{{ date('d/m/Y', strtotime($sr->doc_date)) }}</td>
                                        <td>{{ @App\SysChartofAccounts::find($sr->customer)->account_name ?? '--' }}</td>
                                        <td>{{ $sr->deal_name ?? '--' }}</td>
                                        
                                        <?php
                                        $deal_value = @App\SysHelper::get_aed_amount_new($sr->deal_currency, $sr->deal_value);
                                        $deal_profit = @App\SysHelper::get_aed_amount_new($sr->deal_currency, $sr->deal_profit);
                                        $deal_percentage = $deal_value > 0 ? round(($deal_profit / $deal_value) * 100, 2) : 0;
                                        
                                        // Use amount from chartofaccounts_transaction (matches salesreturnList function)
                                        $sr_invoice_value = @App\SysHelper::get_aed_amount_new($sr->currency, $sr->amount);
                                        $sr_gp = ($sr_invoice_value * $deal_percentage) / 100;
                                        
                                        $sr_value_total += $sr_invoice_value;
                                        $sr_profit_total += $sr_gp;
                                        ?>
                                        
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($sr_invoice_value, 2, '.', ',') }}
                                        </td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($sr_gp, 2, '.', ',') }}
                                        </td>
                                        <td class="text-end">
                                            @if ($deal_profit != 0)
                                                {{ round($deal_percentage, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center">No sales returns found</td>
                                </tr>
                            @endif
                        </tbody>

                        <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-end">Total: </th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sr_value_total, 2, '.', ',') }}</th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($sr_profit_total, 2, '.', ',') }}</th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </aside>









    <script>
        function view_in_ex_tr(id) {
            var tr_in = $('#in_tr_' + id);
            var tr_ex = $('#ex_tr_' + id);
            if (tr_in.css('display') === 'none') {
                tr_in.css('display', '');
            } else {
                tr_in.css('display', 'none');
            }
            if (tr_ex.css('display') === 'none') {
                tr_ex.css('display', '');
            } else {
                tr_ex.css('display', 'none');
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-down ')
                    .addClass('ico icon-outline-alt-arrow-up');
            });

            $('.collapse').on('hide.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-up ')
                    .addClass('ico icon-outline-alt-arrow-down');
            });
        });
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

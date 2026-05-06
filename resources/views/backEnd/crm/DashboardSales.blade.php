@extends('backEnd.newmasterpage')
@section('mainContent')


<style>
    
        /* ================================
                   Dashboard Grade Styling
                   ================================ */

        /* ================================
               Reusable Max-Height Scrollable
               ================================ */
        .max-height {
            max-height: 300px;
            /* adjust as needed */
            overflow-y: auto;
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: #b0b8c5 #f1f3f9;
            /* thumb + track */
        }

        /* Chrome/Edge Scrollbar */
        .max-height::-webkit-scrollbar {
            width: 6px;
        }

        .max-height::-webkit-scrollbar-track {
            background: #f1f3f9;
            border-radius: 8px;
        }

        .max-height::-webkit-scrollbar-thumb {
            background-color: #b0b8c5;
            border-radius: 8px;
        }


        /* Card Styling */
        .card {
            border: none;

            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out;
        }



        /* Card Header */
        .card-header {
            background-color: white;
            color: #212529 !important;
            border-bottom: none
        }

        .card-header h6 {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .card-fixed-lg {
            height: 325px;
            /* large card */
            overflow-y: auto;
        }

         .card-fixed-sm {
            height: 120px;
            /* large card */
            overflow-y: auto;
        }

        /* Rounded Box Metrics */
        .rounded__box {
            border: 2px solid transparent;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin: 0.5rem;
            background: rgb(222, 235, 225);
            min-width: 140px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .rounded__box:hover {
            background: #eef2fb;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        }

        /* Font Sizes for Metrics */
        .font-card-large {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1b1e34;
        }

        .font-card-medium {
            font-size: 1.1rem;
            font-weight: 600;
            color: #444;
        }

        /* Sales Table */
        .sales_tab {
            font-size: 0.85rem;
            color: #4e5d78;
        }

        .sales_tab thead {
            background: #f1f3f9;
            font-weight: 600;
        }

        .sales_tab td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .sales_tab tbody tr:hover {
            background: #f9fbff;
        }

        /* Table Striping */
        .table-striped2 tbody tr:nth-child(odd) {
            background-color: #f8f9fc;
        }

        /* Links inside Metrics */
        .rounded__box a {
            text-decoration: none;
            color: inherit;
        }

        .rounded__box a:hover {
            color: #0b2262;
        }
    
</style>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">
        <div class="long-list" id="filters-long">
            <div class="d-flex  justify-content-between ">
                <!-- Left: Heading -->
                <h4 class="mb-0">Sales Dashboard</h4>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />



            </div>

        </div>

        <div class="left-nav-list">


            <div class="row mt-3">

                <?php try { ?>
                <div class="col-lg-3 mb-4">
                    <div class="card shadow py-2 card-fixed-sm">
                        <div class="card-header">
                            <h5>Total Target</h5>
                        </div>
                        <div class="p-2">

                            <?php
                            // if($targets->type == 2){
                            //     $target_amount=$target_gp[1];
                            //     $tp = round(str_replace(',','',$target_gp[1]) / $targets->revenue_target_monthly * 100,0);
                            // } else {
                            $target_amount = $target_amt;
                            $tp = round($target_gp, 2);
                            //}
                            
                            $tp_balance = 100 - $tp;
                            
                            ?>

                            <i class="ico icon-bold-calculator-minimalistic ml-3 mr-4" aria-hidden="true"
                                style="color: #3a62d7; font-size: 50px; float: left;"></i> 
                                
                               <h2>{{ @$target_amount }}</h2> 


                            <div class="svg-item">
                                <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                                    <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954"
                                        fill="#fff"></circle>
                                    <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954"
                                        fill="transparent" stroke-width="3.5"></circle>
                                    <circle class="donut-segment donut-segment-2" cx="20" cy="20"
                                        r="15.91549430918954" fill="transparent" stroke-width="3.5"
                                        stroke-dasharray="{{ $tp }} {{ $tp_balance }}" stroke-dashoffset="25">
                                    </circle>
                                    <g class="donut-text donut-text-1">

                                        <text y="50%" transform="translate(0, 2)">
                                            <tspan x="50%" text-anchor="middle" class="donut-percent">
                                                <a
                                                    href="{{ url('crm-deals-sales-report-list/' . $url_array[0] . '/' . $url_array[1] . '/' . $url_array[2] . '/' . $url_array[3] . '') }}">{{ $tp }}%</a>
                                            </tspan>
                                        </text>
                                        <text y="60%" transform="translate(0, 2)">
                                            <tspan x="50%" text-anchor="middle" class="donut-data"></tspan>
                                        </text>
                                    </g>
                                </svg>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <div class="card shadow  py-2 card-fixed-sm">
                        <div class="card-header">
                            <h5>Revenue</h5>
                        </div>
                        <div class="p-2">
                            <i class="ico icon-outline-chart-2 ml-3 mr-4" aria-hidden="true"
                                style="color: #1cc88a; font-size: 40px; float: left;"></i>
                            <h2><a
                                    href="{{ url('crm-deals-sales-report-list/' . $url_array[0] . '/' . $url_array[1] . '/' . $url_array[2] . '/' . $url_array[3] . '') }}">{{ $sales_revenue[0] }}</a>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <div class="card shadow  py-2 card-fixed-sm" >
                        <div class="card-header">
                            <h5>On Process Deal</h5>
                        </div>
                        <div class="p-2">
                            <i class="ico icon-outline-chart-2 ml-3 mr-4" aria-hidden="true"
                                style="color: #1ca2c8; font-size: 40px; float: left;"></i>
                            <h2><a
                                    href="{{ url('crm-deals-onprocess-report-list/' . $url_array[0] . '/' . $url_array[1] . '/' . $url_array[2] . '/' . $url_array[3] . '') }}">{{ $on_process[0] }}</a>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <div class="card shadow  py-2 card-fixed-sm">
                        <div class="card-header">
                            <h5>Forcast</h5>
                        </div>
                        <div class="p-2">
                            <i class="ico icon-outline-cart-plus ml-3 mr-4" aria-hidden="true"
                                style="color: #f6c23e; font-size: 40px; float: left;"></i>
                            <h2><a
                                    href="{{ url('crm-deals-forecast-report-list/' . $url_array[0] . '/' . $url_array[1] . '/' . $url_array[2] . '/' . $url_array[3] . '') }}">{{ $forcast[0] }}</a>
                            </h2>
                        </div>
                    </div>
                </div>
                <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



                <div class="col-lg-4 mb-3">
                    <div class="card p-4 card-fixed-lg" >
                        <h6 class="card-head m-0 mb-2 ">Customer Database</h6>
                        <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                            <tbody>
                                <tr>
                                    <td class="text-start">Total Customers</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('customers?sales=' . $user_id . '') }}">{{ $total_customers }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_customers != 0)
                                            {{ round(($total_customers / $total_customers) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Active Customers</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('customers?sales=' . $user_id . '&status=active') }}">{{ $active_customers }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_customers != 0)
                                            {{ round(($active_customers / $total_customers) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">In-Active Customers</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('customers?sales=' . $user_id . '&status=inactive') }}">{{ $inactive_customers }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_customers != 0)
                                            {{ round(($inactive_customers / $total_customers) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Potential Customers / Lead</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('customers?sales=' . $user_id . '&status=potential') }}">{{ $potential_customers }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_customers != 0)
                                            {{ round(($potential_customers / $total_customers) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Open Customers</td>
                                    <td class="text-end">{{ $open_customers }}</td>
                                    <td class="text-end">0%</td>
                                </tr>
                                <tr style="background: #f3f3f3;">
                                    <th>Active Conversion Rate</th>
                                    <th colspan="2" class="text-end">
                                        @if ($total_customers != 0)
                                            {{ round(($active_customers / $total_customers) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card p-4 card-fixed-lg" >
                        <h6 class="header-title m-0 mb-2">Deals</h6>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => url()->current(), 'method' => 'POST', 'id' => 'crm-dashboard']) }}
                        <table style="float: right;">
                            <tr>
                                {{-- <td><input class="form-control" style="height: 27px;" type="date" id="lead_from_date" name="lead_from_date" /></td>
                    <td><input class="form-control" style="height: 27px;" type="date" id="lead_to_date" name="lead_to_date" /></td> --}}
                                <td><select class="form-control" style="height: 27px;" id="lead_filter_by"
                                        name="lead_filter_by">
                                        <option value="">All</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This Week</option>
                                        <option value="last_week">Last Week</option>
                                    </select></td>
                                <td><input type="hidden" name="show_deals" value="1" /><button type="submit"
                                        class="btn btn-sm btn-success "><i class="ico icon-outline-magnifer" style="font-size:16px"
                                            aria-hidden="true"></i></button></td>
                            </tr>
                        </table>
                        {{ Form::close() }}

                        <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                            <tbody>
                                <tr>
                                    <th></th>
                                    <th class="text-end">No. of Deal</th>
                                    <th class="text-end">Lead %</th>
                                    <th class="text-end">Conversion %</th>
                                </tr>
                                <tr>
                                    <td class="text-start">Total Deal</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '') }}">{{ $total_deal }}</a>
                                    </td>
                                    <td class="text-end">100%</td>
                                    <td class="text-end">100%</td>
                                </tr>
                                <tr>
                                    <td class="text-start">New Deal</td>
                                    <td class="text-end"><a href="#"><a target="_blank"
                                                href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=new') }}">{{ $new_deal }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($new_deal / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($new_deal / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Unqualified Deal</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=unqualified') }}">{{ $unqualified_deal }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($unqualified_deal / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($unqualified_deal / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Qualified Deal</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=qualified') }}">{{ $qualified_deal }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($qualified_deal / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($qualified_deal / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Quotation</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=quote') }}">{{ $total_quote }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($total_quote / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($total_quote / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Win</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=win') }}">{{ $total_win }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($total_win / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_quote != 0)
                                            {{ round(($total_win / $total_quote) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                {{-- <tr>
                                <td>In Process</td><td class="text-end">{{ $total_in_progress }}</td>
                                <td class="text-end">@if ($total_lead != 0){{ round($total_in_progress/$total_lead*100,2) }}% @else 0% @endif</td>
                                <td class="text-end">@if ($total_win != 0){{ round($total_in_progress/$total_win*100,2) }}% @else 0% @endif</td>
                            </tr> --}}
                                <tr>
                                    <td class="text-start">Invoice</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=invoice') }}">{{ $total_invoice }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($total_invoice / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_in_progress != 0)
                                            {{ round(($total_invoice / $total_win) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">Deal Closed</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals/show?sales=' . $user_id . '&com=' . $company_id . '&status=closed') }}">{{ $total_deal_closed }}</a>
                                    </td>
                                    <td class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($total_deal_closed / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($total_invoice != 0)
                                            {{ round(($total_deal_closed / $total_invoice) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr style="background: #f3f3f3;">
                                    <th colspan="2">Closed Deal Rate</th>
                                    <th class="text-end">
                                        @if ($total_deal != 0)
                                            {{ round(($total_deal_closed / $total_deal) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </th>
                                    <th class="text-end">
                                        @if ($total_invoice != 0)
                                            {{ round(($total_deal_closed / $total_invoice) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="card p-4 card-fixed-lg" >
                        <h4 class="header-title m-0 mb-2">Sales</h4>

                        {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-dashboard', 'method' => 'POST', 'id' => 'crm-dashboard']) }} --}}
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => url()->current(), 'method' => 'POST', 'id' => 'crm-dashboard']) }}
                        <table style="float: right;">
                            <tr>
                                {{-- <td><input class="form-control" style="height: 27px;" type="date" id="sales_from_date" name="sales_from_date" /></td>
                    <td><input class="form-control" style="height: 27px;" type="date" id="sales_to_date" name="sales_to_date" /></td> --}}
                                <td><select class="form-control" style="height: 27px;" id="sales_filter_by"
                                        name="sales_filter_by">
                                        <option value="">All</option>
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This Week</option>
                                        <option value="last_week">Last Week</option>
                                    </select></td>
                                <td><input type="hidden" name="show_sales" value="1" /><button type="submit"
                                        class="btn btn-sm btn-success"><i class="ico icon-outline-magnifer"
                                            aria-hidden="true" style="font-size: 16px"></i></button></td>
                            </tr>
                        </table>
                        {{ Form::close() }}

                        <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                            <tbody>
                                <tr>
                                    <th class="text-center">Partculars</th>
                                    <th class="text-center">Revenue</th>
                                    <th class="text-center">On Process</th>
                                    <th class="text-center">Forecast</th>
                                </tr>
                                <script>
                                    function amt_div() {
                                        var dropdown = $('.int_tr');
                                        if (dropdown.css('display') === 'none') {
                                            dropdown.css('display', '');
                                        } else {
                                            dropdown.css('display', 'none');
                                        }
                                    }

                                    function gp_div() {
                                        var dropdown = $('.gp_tr');
                                        if (dropdown.css('display') === 'none') {
                                            dropdown.css('display', '');
                                        } else {
                                            dropdown.css('display', 'none');
                                        }
                                    }
                                </script>
                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="amt_div()">Amount</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals-sales-report-list/' . $user_id . '/' . $company_id . '/2020-01-01/' . date('Y-m-d') . '') }}">{{ @App\SysHelper::com_curr_format($s_amount, 2, '.', ',') }}</a>
                                    </td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals-onprocess-report-list/' . $user_id . '/' . $company_id . '/2020-01-01/' . date('Y-m-d') . '') }}">{{ @App\SysHelper::com_curr_format($s_on_amount, 2, '.', ',') }}</a>
                                    </td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals-forecast-report-list/' . $user_id . '/' . $company_id . '/2020-01-01/' . date('Y-m-d') . '') }}">{{ @App\SysHelper::com_curr_format($s_fo_amount, 2, '.', ',') }}</a>
                                    </td>
                                </tr>
                                <tr class="int_tr" style="display: none;">
                                    <td class="text-start"><i>Internal Amount</i></td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_in_amount, 2, '.', ',') }}
                                    </td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($s_on_in_amount, 2, '.', ',') }}</td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($s_fo_in_amount, 2, '.', ',') }}</td>
                                </tr>
                                <tr class="int_tr" style="display: none;">
                                    <td class="text-start"><i>External Amount</i></td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_ex_amount, 2, '.', ',') }}
                                    </td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($s_on_ex_amount, 2, '.', ',') }}</td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($s_fo_ex_amount, 2, '.', ',') }}</td>
                                </tr>

                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="gp_div()">GP</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_gp, 2, '.', ',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_on_gp, 2, '.', ',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_fo_gp, 2, '.', ',') }}</td>
                                </tr>
                                <tr class="gp_tr" style="display: none;">
                                    <td class="text-start"><i>Internal GP</i></td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_in_gp, 2, '.', ',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_on_in_gp, 2, '.', ',') }}
                                    </td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_fo_in_gp, 2, '.', ',') }}
                                    </td>
                                </tr>
                                <tr class="gp_tr" style="display: none;">
                                    <td class="text-start"><i>External GP</i></td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_ex_gp, 2, '.', ',') }}</td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_on_ex_gp, 2, '.', ',') }}
                                    </td>
                                    <td class="text-end">{{ @App\SysHelper::com_curr_format($s_fo_ex_gp, 2, '.', ',') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-start">GP%</td>
                                    <td class="text-end">{{ round($s_gp_p, 2) }}%</td>
                                    <td class="text-end">{{ round($s_on_gp_p, 2) }}%</td>
                                    <td class="text-end">{{ round($s_fo_gp_p, 2) }}%</td>
                                </tr>
                                <tr>
                                    <td class="text-start">No. of Deal</td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals-sales-report-list/' . $user_id . '/' . $company_id . '/2020-01-01/' . date('Y-m-d') . '') }}">{{ $s_no_deals }}</a>
                                    </td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals-sales-report-list/' . $user_id . '/' . $company_id . '/2020-01-01/' . date('Y-m-d') . '') }}">{{ $s_on_no_deals }}</a>
                                    </td>
                                    <td class="text-end"><a target="_blank"
                                            href="{{ url('crm-deals-sales-report-list/' . $user_id . '/' . $company_id . '/2020-01-01/' . date('Y-m-d') . '') }}">{{ $s_fo_no_deals }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">NC</td>
                                    <td class="text-end">{{ $s_nc }}</td>
                                    <td class="text-end">{{ $s_on_nc }}</td>
                                    <td class="text-end">{{ $s_fo_nc }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">OC</td>
                                    <td class="text-end">{{ $s_oc }}</td>
                                    <td class="text-end">{{ $s_on_oc }}</td>
                                    <td class="text-end">{{ $s_fo_oc }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">IC</td>
                                    <td class="text-end">{{ $s_ic }}</td>
                                    <td class="text-end">{{ $s_on_ic }}</td>
                                    <td class="text-end">{{ $s_fo_ic }}</td>
                                </tr>
                                <tr style="background: #f3f3f3;">
                                    <td class="text-start">NC Ratio</td>
                                    <td class="text-end"><?php try{ ?>
                                        {{ round(($s_nc / ($s_nc + $s_oc + $s_ic)) * 100, 2) }} <?php } catch (\Throwable $th) { ?> 0
                                        <?php } ?> %</td>
                                    <td class="text-end">
                                        <?php try{ ?>{{ round(($s_on_nc / ($s_on_nc + $s_on_oc + $s_on_ic)) * 100, 2) }}
                                        <?php } catch (\Throwable $th) { ?> 0 <?php } ?>%</td>
                                    <td class="text-end">
                                        <?php try{ ?>{{ round(($s_fo_nc / ($s_fo_nc + $s_fo_oc + $s_fo_ic)) * 100, 2) }}
                                        <?php } catch (\Throwable $th) { ?> 0 <?php } ?>%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card p-4 card-fixed-lg">
                        <h4 class="card-head m-0 mb-2">Receivable Outstanding</h4>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => url()->current(), 'method' => 'POST', 'id' => 'crm-dashboard']) }}
                        <table style="float: right;">
                            <tr>
                                <td><input type="hidden" name="show_receivable" value="1" /><button type="submit"
                                        class="btn btn-sm text-success">View
                                    All</button></td>
                            </tr>
                        </table>
                        {{ Form::close() }}
                        <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                            <tbody>
                                <tr>
                                    <th class="text-center">Particulars</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">No. of Invoice</th>
                                    <th class="text-center">%</th>
                                </tr>
                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('total_receivable')">Total Receivable
                                    </td>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('receivable-outstanding?sales=' . $user_id . '&com=' . $company_id . '') }}">{{ $os_det[0] }}</a>
                                    </td>
                                    <td class="text-center">{{ $os_det[1] }}</td>
                                    <td class="text-center">100%</td>
                                </tr>

                                <tr class="os_tr_total_receivable" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $os_det_in[0] }}</td>
                                    <td class="text-center">{{ $os_det_in[1] }}</td>
                                    <td class="text-center">100%</td>
                                </tr>
                                <tr class="os_tr_total_receivable" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $os_det_ex[0] }}</td>
                                    <td class="text-center">{{ $os_det_ex[1] }}</td>
                                    <td class="text-center">100%</td>
                                </tr>

                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('due')">Due</td>
                                    <td class="text-center">{{ $os_det[2] }}</td>
                                    <td class="text-center">{{ $os_det[3] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[0] != 0)
                                            {{ round(($os_det[2] / $os_det[0]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr class="os_tr_due" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $os_det_in[2] }}</td>
                                    <td class="text-center">{{ $os_det_in[3] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[2] != 0)
                                            {{ round(($os_det_in[2] / $os_det[2]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_due" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $os_det_ex[2] }}</td>
                                    <td class="text-center">{{ $os_det_ex[3] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[2] != 0)
                                            {{ round(($os_det_ex[2] / $os_det[2]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>


                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('overdue')">Overdue</td>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('receivable-outstanding?sales=' . $user_id . '&com=' . $company_id . '') }}">{{ $os_det[4] }}</a>
                                    </td>
                                    <td class="text-center">{{ $os_det[5] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[0] != 0)
                                            {{ round(($os_det[4] / $os_det[0]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr class="os_tr_overdue" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $os_det_in[4] }}</td>
                                    <td class="text-center">{{ $os_det_in[5] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[2] != 0)
                                            {{ round(($os_det_in[4] / $os_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_overdue" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $os_det_ex[4] }}</td>
                                    <td class="text-center">{{ $os_det_ex[5] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[2] != 0)
                                            {{ round(($os_det_ex[4] / $os_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('030')" class="text-center">0-30</td>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('receivable-outstanding?sales=' . $user_id . '&com=' . $company_id . '&over=30') }}">{{ $due_by_det[0] }}</a>
                                    </td>
                                    <td class="text-center">{{ $due_by_det[1] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[4] != 0)
                                            {{ round(($due_by_det[0] / $os_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_030" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $due_by_det_in[0] }}</td>
                                    <td class="text-center">{{ $due_by_det_in[1] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[0] != 0)
                                            {{ round(($due_by_det_in[0] / $due_by_det[0]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_030" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $due_by_det_ex[0] }}</td>
                                    <td class="text-center">{{ $due_by_det_ex[1] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[0] != 0)
                                            {{ round(($due_by_det_ex[0] / $due_by_det[0]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('3160')" class="text-center">31-60</td>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('receivable-outstanding?sales=' . $user_id . '&com=' . $company_id . '&over=60') }}">{{ $due_by_det[2] }}</a>
                                    </td>
                                    <td class="text-center">{{ $due_by_det[3] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[4] != 0)
                                            {{ round(($due_by_det[2] / $os_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_3160" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $due_by_det_in[2] }}</td>
                                    <td class="text-center">{{ $due_by_det_in[3] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[2] != 0)
                                            {{ round(($due_by_det_in[2] / $due_by_det[2]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_3160" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $due_by_det_ex[2] }}</td>
                                    <td class="text-center">{{ $due_by_det_ex[3] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[2] != 0)
                                            {{ round(($due_by_det_ex[2] / $due_by_det[2]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('6190')" class="text-center">61-90</td>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('receivable-outstanding?sales=' . $user_id . '&com=' . $company_id . '&over=90') }}">{{ $due_by_det[4] }}</a>
                                    </td>
                                    <td class="text-center">{{ $due_by_det[5] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[4] != 0)
                                            {{ round(($due_by_det[4] / $os_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_6190" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $due_by_det_in[4] }}</td>
                                    <td class="text-center">{{ $due_by_det_in[5] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[4] != 0)
                                            {{ round(($due_by_det_in[4] / $due_by_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_6190" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $due_by_det_ex[4] }}</td>
                                    <td class="text-center">{{ $due_by_det_ex[5] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[4] != 0)
                                            {{ round(($due_by_det_ex[4] / $due_by_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-start" style="cursor: pointer;" onclick="os_div('90')" class="text-center">>90</td>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('receivable-outstanding?sales=' . $user_id . '&com=' . $company_id . '&over=90+') }}">{{ $due_by_det[6] }}</a>
                                    </td>
                                    <td class="text-center">{{ $due_by_det[7] }}</td>
                                    <td class="text-center">
                                        @if ($os_det[4] != 0)
                                            {{ round(($due_by_det[6] / $os_det[4]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_90" style="font-style: italic; display: none;">
                                    <td class="text-end">internal</td>
                                    <td class="text-center">{{ $due_by_det_in[6] }}</td>
                                    <td class="text-center">{{ $due_by_det_in[7] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[6] != 0)
                                            {{ round(($due_by_det_in[6] / $due_by_det[6]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>
                                <tr class="os_tr_90" style="font-style: italic; display: none;">
                                    <td class="text-end">external</td>
                                    <td class="text-center">{{ $due_by_det_ex[6] }}</td>
                                    <td class="text-center">{{ $due_by_det_ex[7] }}</td>
                                    <td class="text-center">
                                        @if ($due_by_det[6] != 0)
                                            {{ round(($due_by_det_ex[6] / $due_by_det[6]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </td>
                                </tr>

                                <tr style="background: #f3f3f3;">
                                    <th colspan="4" class="text-center">Overdue Ratio : @if ($os_det[0] != 0)
                                            {{ round(($os_det[4] / $os_det[0]) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </th>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <script>
                            function os_div(id) {
                                var os = $('.os_tr_' + id);
                                if (os.css('display') === 'none') {
                                    os.css('display', '');
                                } else {
                                    os.css('display', 'none');
                                }
                            }
                        </script>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card p-4 card-fixed-lg">
                        <h4 class="header-title m-0 mb-2">Top Brand</h4>
                        <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                            <tbody>
                                <tr>
                                    <th>Brand</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-end">%</th>
                                </tr>
                                @if (count($topBrands) > 0)
                                    @foreach ($topBrands as $b)
                                        <tr>
                                            <td class="text-start">{{ $b->title }}</td>
                                            <td class="text-center">{{ $b->qty_out }}</td>
                                            <td class="text-end">{{ $b->price_out }}</td>
                                            <td class="text-end">
                                                @if ($topBrands->sum('price_out') > 0)
                                                    {{ round(($b->price_out / $topBrands->sum('price_out')) * 100, 2) }} %
                                                @else
                                                    0 %
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th>Total Sales</th>
                                        <th class="text-center">{{ $topBrands->sum('qty_out') }}</td>
                                        <th class="text-end">{{ $topBrands->sum('price_out') }}</td>
                                        <th class="text-end">
                                            </td>
                                    </tr>
                                @endif


                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- @if (Auth::user()->role_id == 1) --}}
                <div class="col-lg-4 mb-3">
                    <div class="card p-4 card-fixed-lg" >
                        <h4 class="header-title m-0 mb-2">Brand Stock</h4>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => url()->current(), 'method' => 'POST', 'id' => 'crm-dashboard']) }}
                        <table style="float: right;">
                            <tr>
                                <td><input type="hidden" name="show_brand_stock" value="1" /><button
                                        type="submit" class="btn  text-success">View
                                    All</button></td>
                            </tr>
                        </table>
                        {{ Form::close() }}
                        <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                            <tbody>
                                <tr>
                                    <th>Brand</th>
                                    <th class="text-center">Total Qty</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                                @if (count($brand_list_data) > 0)
                                    @foreach ($brand_list_data as $bsd)
                                        <tr>
                                            <td class="text-start">{{ $bsd->title }}</td>
                                            <td class="text-center">{{ $bsd->qty }}</td>
                                            <td class="text-end"><a
                                                    onclick="view_stock({{ $bsd->id }})">{{ $bsd->total }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                <script>
                                    function view_stock(bid) {
                                        $('#a_stock_' + bid).click();
                                    }
                                </script>


                            </tbody>
                        </table>
                    </div>
                </div>

                @if (count($brand_list_data) > 0)
                    @foreach ($brand_list_data as $b)
                        <a data-bs-toggle="modal" data-bs-target="#ModalStock{{ $b->id }}"
                            id="a_stock_{{ $b->id }}"></a>
                        <!-- Modal Cancel-->
                        <div class="modal fade" id="ModalStock{{ $b->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Stock List</h5>
                                        <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                                                        <tr>
                                                            <td>Brand</td>
                                                            <td>Category</td>
                                                            <td>Sub Category</td>
                                                            <td>Part_No</td>
                                                            <td>Qty</td>
                                                            <td>Amount</td>
                                                        </tr>
                                                        <?php
                                                        $dt = $stock_data_list->where('bid', $b->id);
                                                        ?>
                                                        @if (count($dt) > 0)
                                                            @foreach ($dt as $d)
                                                                <tr>
                                                                    <td>{{ $d->bname }}</td>
                                                                    <td>{{ $d->category_name }}</td>
                                                                    <td>{{ $d->sub_category_name }}</td>
                                                                    <td>{{ $d->part_number }}</td>
                                                                    <td>{{ $d->qty_out }}</td>
                                                                    <td>{{ $d->qty_out * $d->price_out }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-light"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- Modal Cancel-->
                    @endforeach
                @endif

                {{-- @endif --}}



                <div class="col-lg-6 mb-3">
                    <div class="card p-4 card-fixed-lg">
                        <div>
                            {{--  <select class="form-control form-card-select float-right" id="filter_date_target" style="width: 300px;">
                            <option value="m">Monthly</option>
                            <option value="pm">Previous Month</option>
                            <option value="d">Day</option>
                            <option value="q">Quarterly</option>
                            <option value="pq">Previous Quarter</option>
                            <option value="y">This Year</option>
                        </select>  --}}
                            <h6 class="card-head mb-3">Sales Target This Month</h6>
                            <hr>
                        </div>
                        @if (count($sales_target) > 0)
                            @foreach ($sales_target as $top)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-1">{{ $top->userid->full_name }}</p>
                                        <?php
                                        
                                        //$total_sales = App\SysHelper::get_total_sales_brand($top->user_id,0,$top->company_id); //get_total_sales_brand($top->user_id,$top->brand);
                                        $total_sales = @App\SysHelper::get_total_revenue_all_by_user($top->userid, $ctrl_date, $ctrl_date2, [$company_id], []);
                                        //$total_sales =$sales_revenue[0];
                                        
                                        $tp = round(($total_sales[0] / $top->revenue_target_monthly) * 100, 0);
                                        $tpcolor = 'bg-danger';
                                        if ($tp < 40) {
                                            $tpcolor = 'bg-danger';
                                        }
                                        if ($tp >= 40 && $tp < 80) {
                                            $tpcolor = 'bg-warning';
                                        }
                                        if ($tp >= 80 && $tp <= 100) {
                                            $tpcolor = 'bg-success';
                                        }
                                        if ($tp > 100) {
                                            $tpcolor = 'bg-purple';
                                        }
                                        ?>
                                        {{-- <p class="mb-1 font-semibold">{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', ',') }}AED / {{ @App\SysHelper::com_curr_format($top->target, 2, '.', ',') }}AED</p> --}}
                                        <p class="mb-1 font-semibold"></p>
                                    </div>
                                        <div class="progress flex-fill" data-bar-color="{{$tpcolor}}" data-percentage="{{$tp}}%"><div class="progress-bar" style="width: {{$tp}}%; background-color: {{$tpcolor}};"></div></div>
                                    
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-md-6 pb-4">
                    <div class="card shadow p-3 card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Order In Process</h4>
                            @if (Auth::user()->id == 27 || Auth::user()->id == 33 || Auth::user()->id == 44)
                                <a href="{{ url('crm-deal-track-list/salesteamorderinprocess') }}"
                                    class=" btn-small">View All</a>
                            @else
                                <a href="{{ url('crm-deal-track-list/salesorderinprocess') }}" class=" btn-small">View
                                    All</a>
                            @endif
                        </div>
                        <div class="card-body pt-0  max-height" >
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($order_in_process) > 0)
                                            @foreach ($order_in_process as $top)
                                                <tr>
                                                    <td><a href="{{ url('crm-deal-track/' . $top->deal_id . '/view') }}"
                                                            title="View Deal Track"
                                                            class="text-dark">{{ $top->code }}</a></td>
                                                    <td class="text-start">
                                                        {{ $top->customername->name }}
                                                    </td>
                                                    <td class="text-start">{{ $top->ownername->full_name }}</td>
                                                    <td class="text-center">{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                                    <td>
                                                        {!! App\SysHelper::get_status_icon('accounts', $top->accounts) !!}
                                                        {!! App\SysHelper::get_status_icon('sales', $top->sales) !!}
                                                        {!! App\SysHelper::get_status_icon('purchease', $top->purchease) !!}
                                                        {!! App\SysHelper::get_status_icon('invoice', $top->invoice) !!}
                                                        {!! App\SysHelper::get_status_icon('delivery', $top->delivery) !!}
                                                        {!! App\SysHelper::get_status_icon('receivables', $top->receivables) !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
                <div class="col-xl-3">
                    <div class="card shadow mb-4 p-4 card-fixed-lg">
                        <div class="card-header p-0">
                            <h6 class="card-head ">Leads</h6>
                        </div>
                        <div class="">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="chart-pie">
                                        <canvas id="myPieChart"></canvas>
                                        <script>
                                            $(document).ready(function() {
                                                var ctx = document.getElementById("myPieChart");
                                                var myPieChart = new Chart(ctx, {
                                                    type: 'doughnut',
                                                    data: {
                                                        labels: ["New", "Qualified", "Unqualified", "Reseller", "Enduser", "E-Commerce",
                                                            "Project"
                                                        ],
                                                        datasets: [{
                                                            data: [{{ $total_leads_new }}, {{ $total_leads_qualified }},
                                                                {{ $total_leads_unqualified }},
                                                                {{ $leads_type->where('isproject', 1)->count() }},
                                                                {{ $leads_type->where('isproject', 2)->count() }},
                                                                {{ $leads_type->where('isproject', 3)->count() }},
                                                                {{ $leads_type->where('isproject', 4)->count() }}
                                                            ],
                                                            backgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf',
                                                                '#4e73df', '#934edf'
                                                            ],
                                                            hoverBackgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df',
                                                                '#704edf', '#4e73df', '#934edf'
                                                            ],
                                                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                                                        }],
                                                    },
                                                    options: {
                                                        maintainAspectRatio: false,
                                                        tooltips: {
                                                            backgroundColor: "rgb(255,255,255)",
                                                            bodyFontColor: "#858796",
                                                            borderColor: '#dddfeb',
                                                            borderWidth: 1,
                                                            xPadding: 15,
                                                            yPadding: 15,
                                                            displayColors: false,
                                                            caretPadding: 10,
                                                        },
                                                        legend: {
                                                            display: false
                                                        },
                                                        cutoutPercentage: 80,
                                                    },
                                                });
                                            });
                                        </script>

                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="mt-4 text-left small">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('new')"><i class="fas fa-circle"
                                                    style="color: #36b9cc;"></i> New</a> <a
                                                href="{{ url('#crm-lead/new') }}"
                                                id="lead_new">{{ $total_leads_new }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('qualified')"><i class="fas fa-circle"
                                                    style="color: #1cc88a;"></i> Qualified</a> <a
                                                href="{{ url('#crm-lead/qualified') }}"
                                                id="lead_qualified">{{ $total_leads_qualified }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('unqualified')"><i
                                                    class="fas fa-circle" style="color: #f1416c;"></i> Unqualified</a> <a
                                                href="{{ url('#crm-lead/unqualified') }}"
                                                id="lead_unqualified">{{ $total_leads_unqualified }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('project')"><i class="fas fa-circle"
                                                    style="color: #4e51df;"></i> Reseller</a> <a
                                                href="{{ url('#crm-lead/project') }}"
                                                id="lead_project">{{ $leads_type->where('isproject', 1)->count() }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('channel')"><i class="fas fa-circle"
                                                    style="color: #704edf;"></i> Enduser</a> <a
                                                href="{{ url('#crm-lead/channel') }}"
                                                id="lead_channel">{{ $leads_type->where('isproject', 2)->count() }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('corporate')"><i class="fas fa-circle"
                                                    style="color: #4e73df;"></i> E-Commerce</a> <a
                                                href="{{ url('#crm-lead/corporate') }}"
                                                id="lead_corporate">{{ $leads_type->where('isproject', 3)->count() }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="lead_click('corporate')"><i class="fas fa-circle"
                                                    style="color: #934edf;"></i> Project</a> <a
                                                href="{{ url('#crm-lead/corporate') }}"
                                                id="lead_corporate">{{ $leads_type->where('isproject', 4)->count() }}</a>
                                        </div> <br /><br /><br /><br />
                                        {{-- <script>
                                        function lead_click(id){
                                            var mo = $("#lead_filter_date").val();
                                            var co = $("#lead_filter_company").val();
                                            var url = $("#base_url").val()+"/crm-lead/"+id+"/"+mo+"/"+co;
                                            window.location.href = url;
                                        }
                                    </script> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="card shadow mb-4 p-4 card-fixed-lg">
                        <div class="card-header p-0">
                            <h6 class="card-head ">Deals</h6>
                        </div>
                        <div class="">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="chart-pie">
                                        <canvas id="myPieChart2"></canvas>
                                        <script>
                                            $(document).ready(function() {
                                                var ctx = document.getElementById("myPieChart2");
                                                var myPieChart2 = new Chart(ctx, {
                                                    type: 'doughnut',
                                                    data: {
                                                        labels: ["Prospecting", "Quote", "Closure", "Won", "Lost", "Reseller", "Enduser",
                                                            "E-Commerece", "Project"
                                                        ],
                                                        datasets: [{
                                                            data: [{{ $total_deals_prospecting }}, {{ $total_deals_quote }},
                                                                {{ $total_deals_closure }}, {{ $total_deals_won }},
                                                                {{ $total_deals_lost }},
                                                                {{ $deals_type->where('isproject', 1)->count() }},
                                                                {{ $deals_type->where('isproject', 2)->count() }},
                                                                {{ $deals_type->where('isproject', 3)->count() }},
                                                                {{ $deals_type->where('isproject', 4)->count() }}
                                                            ],
                                                            backgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c',
                                                                '#4e51df', '#704edf', '#4e73df', '#934edf'
                                                            ],
                                                            hoverBackgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a',
                                                                '#f1416c', '#4e51df', '#704edf', '#4e73df', '#934edf'
                                                            ],
                                                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                                                        }],
                                                    },
                                                    options: {
                                                        maintainAspectRatio: false,
                                                        tooltips: {
                                                            backgroundColor: "rgb(255,255,255)",
                                                            bodyFontColor: "#858796",
                                                            borderColor: '#dddfeb',
                                                            borderWidth: 1,
                                                            xPadding: 15,
                                                            yPadding: 15,
                                                            displayColors: false,
                                                            caretPadding: 10,
                                                        },
                                                        legend: {
                                                            display: false
                                                        },
                                                        cutoutPercentage: 80,
                                                    },
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                    <div class="mt-4 text-left small">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('prospecting')"><i
                                                    class="fas fa-circle" style="color: #f6c23e;"></i> Prospecting</a> <a
                                                href="{{ url('#crm-deal/prospecting') }}"
                                                id="deal_prospecting">{{ $total_deals_prospecting }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('quote')"><i class="fas fa-circle"
                                                    style="color: #1cc2c8;"></i> Quote</a> <a
                                                href="{{ url('#crm-deal/quote') }}"
                                                id="deal_quote">{{ $total_deals_quote }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('closure')"><i class="fas fa-circle"
                                                    style="color: #1ca2c8;"></i> Closure</a> <a
                                                href="{{ url('#crm-deal/closure') }}"
                                                id="deal_closure">{{ $total_deals_closure }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('won')"><i class="fas fa-circle"
                                                    style="color: #1cc88a;"></i> Won</a> <a
                                                href="{{ url('#crm-deal/won') }}"
                                                id="deal_won">{{ $total_deals_won }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('lost')"><i class="fas fa-circle"
                                                    style="color: #f1416c;"></i> Lost</a> <a
                                                href="{{ url('#crm-deal/lost') }}"
                                                id="deal_lost">{{ $total_deals_lost }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('project')"><i class="fas fa-circle"
                                                    style="color: #4e51df;"></i> Reseller</a> <a
                                                href="{{ url('#crm-deal/project') }}"
                                                id="deal_project">{{ $deals_type->where('isproject', 1)->count() }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('channel')"><i class="fas fa-circle"
                                                    style="color: #704edf;"></i> Enduser</a> <a
                                                href="{{ url('#crm-deal/channel') }}"
                                                id="deal_channel">{{ $deals_type->where('isproject', 2)->count() }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('corporate')"><i class="fas fa-circle"
                                                    style="color: #4e73df;"></i> E-Commerece</a> <a
                                                href="{{ url('#crm-deal/corporate') }}"
                                                id="deal_corporate">{{ $deals_type->where('isproject', 3)->count() }}</a>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" onclick="deal_click('corporate')"><i class="fas fa-circle"
                                                    style="color: #934edf;"></i> Project</a> <a
                                                href="{{ url('#crm-deal/corporate') }}"
                                                id="deal_corporate">{{ $deals_type->where('isproject', 4)->count() }}</a>
                                        </div>
                                        {{--  <script>
                                        function deal_click(id){
                                            var mo = $("#deal_filter_date").val();
                                            var co = $("#deal_filter_company").val();
                                            var url = $("#base_url").val()+"/crm-deal/"+id+"/"+mo+"/"+co;
                                            window.location.href = url;
                                        }
                                    </script>  --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow p-3 card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Payment Pending</h4>
                            @if (Auth::user()->id == 27 || Auth::user()->id == 33 || Auth::user()->id == 44)
                                <a href="{{ url('crm-deal-track-list/salesteampendingpayments') }}"
                                    class=" btn-small">View All</a>
                            @else
                                <a href="{{ url('crm-deal-track-list/salespendingpayments') }}" class=" btn-small">View
                                    All</a>
                            @endif
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($pending_payments) > 0)
                                            @foreach ($pending_payments as $top)
                                                <tr>
                                                    <td><a href="{{ url('crm-deal-track/' . $top->deal_id . '/view') }}"
                                                            title="View Deal Track"
                                                            class="text-dark">{{ $top->code }}</a></td>
                                                    <td>
                                                        {{ $top->customername->name }}
                                                    </td>
                                                    <td>{{ $top->ownername->full_name }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                                    <td> <span
                                                            class="btn-badge rejected py-1 px-2">{!! App\SysHelper::get_deal_status_log(
                                                                $top->accounts,
                                                                $top->sales,
                                                                $top->purchease,
                                                                $top->invoice,
                                                                $top->delivery,
                                                                $top->receivables
                                                            ) !!}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
                <div class="col-md-6 mt-0 card-fixed-lg">
                    <div class="card shadow p-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Deals Overdue After Closing Date</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover" style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th>Deal</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Date</th>
                                            <th>Stage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($dealsbyclosedate) > 0)
                                            @foreach ($dealsbyclosedate as $top)
                                                <tr>
                                                    <td><a href="{{ url('crm-deals/' . $top->id . '/view') }}"
                                                            title="View Deal Track"
                                                            class="text-dark">{{ $top->code }}</a></td>
                                                    <td>
                                                       {{ $top->deal_name }}
                                                    </td>
                                                    <td>{{ $top->ownername->full_name }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($top->estimated_close_date)) }}</td>
                                                    <td>
                                                        @if ($top->stage == 1)
                                                            <span class="btn-badge warning py-1 px-2">Prospecting</span>
                                                        @elseif($top->stage == 2)
                                                            <span class="btn-badge warning py-1 px-2">Quote</span>
                                                        @else
                                                            <span class="btn-badge warning py-1 px-2">Closure</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>

            </div>


        </div>
    </aside>




    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


    <script>
        $(document).on("change", "#filter_company", function() {
            var company = $("#filter_company").val();
            var date = $("#filter_date").val();
            get_data(company, date);
        });
        $(document).on("change", "#filter_date", function() {
            var company = $("#filter_company").val();
            var date = $("#filter_date").val();
            get_data(company, date);
        });

        function get_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-dashboard-sales-filter') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company: company,
                    date: date,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        $("#revenue").html(dataResult['data'][0]);
                        $("#forcast").html(dataResult['data'][1]);
                        $("#lost").html(dataResult['data'][0]);
                    } else {
                        $("#revenue").html("0");
                        $("#forcast").html("0");
                        $("#lost").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("change", "#lead_filter_company", function() {
            var company = $("#lead_filter_company").val();
            var date = $("#lead_filter_date").val();
            get_lead_data(company, date);
        });
        $(document).on("change", "#lead_filter_date", function() {
            var company = $("#lead_filter_company").val();
            var date = $("#lead_filter_date").val();
            get_lead_data(company, date);
        });

        function get_lead_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-dashboard-lead-filter') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company: company,
                    date: date,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        $("#lead_new").html(dataResult['data'][0]);
                        $("#lead_qualified").html(dataResult['data'][1]);
                        $("#lead_unqualified").html(dataResult['data'][2]);
                        $("#lead_project").html(dataResult['data'][3]);
                        $("#lead_channel").html(dataResult['data'][4]);
                        $("#lead_corporate").html(dataResult['data'][5]);
                        chart_lead(dataResult['data'][0], dataResult['data'][1], dataResult['data'][2],
                            dataResult['data'][3], dataResult['data'][4], dataResult['data'][5], dataResult[
                                'data'][6]);
                    } else {
                        $("#lead_new").html("0");
                        $("#lead_qualified").html("0");
                        $("#lead_unqualified").html("0");
                        $("#lead_project").html("0");
                        $("#lead_channel").html("0");
                        $("#lead_corporate").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("change", "#deal_filter_company", function() {
            var company = $("#deal_filter_company").val();
            var date = $("#deal_filter_date").val();
            get_deal_data(company, date);
        });
        $(document).on("change", "#deal_filter_date", function() {
            var company = $("#deal_filter_company").val();
            var date = $("#deal_filter_date").val();
            get_deal_data(company, date);
        });

        function get_deal_data(company, date) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-dashboard-deal-filter') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company: company,
                    date: date,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        $("#deal_prospecting").html(dataResult['data'][0]);
                        $("#deal_quote").html(dataResult['data'][1]);
                        $("#deal_closure").html(dataResult['data'][2]);
                        $("#deal_won").html(dataResult['data'][3]);
                        $("#deal_lost").html(dataResult['data'][4]);
                        $("#deal_project").html(dataResult['data'][5]);
                        $("#deal_channel").html(dataResult['data'][6]);
                        $("#deal_corporate").html(dataResult['data'][7]);
                        chart_deal(dataResult['data'][0], dataResult['data'][1], dataResult['data'][2],
                            dataResult['data'][3], dataResult['data'][4], dataResult['data'][5], dataResult[
                                'data'][6], dataResult['data'][7]);
                    } else {
                        $("#deal_prospecting").html("0");
                        $("#deal_quote").html("0");
                        $("#deal_closure").html("0");
                        $("#deal_won").html("0");
                        $("#deal_lost").html("0");
                        $("#deal_project").html("0");
                        $("#deal_channel").html("0");
                        $("#deal_corporate").html("0");
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function chart_lead(a, b, c, d, e, f) {
            var ctx = document.getElementById("myPieChart");
            var myPieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["New", "Qualified", "Unqualified", "Project", "Channel", "Corporate"],
                    datasets: [{
                        data: [a, b, c, d, e, f],
                        backgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
                        hoverBackgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf',
                            '#4e73df'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }

        function chart_deal(a, b, c, d, e, f, g) {
            var ctx = document.getElementById("myPieChart2");
            var myPieChart2 = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Prospecting", "Quote", "Closure", "Won", "Lost", "Project", "Channel", "Corporate"],
                    datasets: [{
                        data: [a, b, c, d, e, f, g],
                        backgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c', '#4e51df',
                            '#704edf', '#4e73df'
                        ],
                        hoverBackgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c',
                            '#4e51df', '#704edf', '#4e73df'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                },
            });
        }
    </script>

    <style>
        .svg-item {
            float: right;
            margin-top: -70px !important;
            width: 100%;
            font-size: 16px;
            margin: 0 auto;
            animation: donutfade 1s;
        }

        @keyframes donutfade {

            /* this applies to the whole svg item wrapper */
            0% {
                opacity: .2;
            }

            100% {
                opacity: 1;
            }
        }

        @media (min-width: 992px) {
            .svg-item {
                width: 20%;
            }
        }

        .donut-ring {
            stroke: #EBEBEB;
        }

        .donut-segment {
            transform-origin: center;
            stroke: #FF6200;
        }

        .donut-segment-2 {
            stroke: #3a62d7;
            animation: donut1 3s;
        }

        .segment-1 {
            fill: #ccc;
        }

        .segment-2 {
            fill: #3a62d7;
        }

        .donut-percent {
            animation: donutfadelong 1s;
        }

        @keyframes donutfadelong {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes donut1 {
            0% {
                stroke-dasharray: 0, 100;
            }

            100% {
                stroke-dasharray: {{ $tp }}, {{ $tp_balance }};
            }
        }

        .donut-text {
            font-family: Arial, Helvetica, sans-serif;
            fill: #FF6200;
        }

        .donut-text-1 {
            fill: #3a62d7;
        }

        .donut-label {
            font-size: 0.28em;
            font-weight: 700;
            line-height: 1;
            fill: #000;
            transform: translateY(0.25em);
        }

        .donut-percent {
            font-size: 0.5em;
            line-height: 1;
            transform: translateY(0.5em);
            font-weight: bold;
        }

        .donut-data {
            font-size: 0.12em;
            line-height: 1;
            transform: translateY(0.5em);
            text-align: center;
            text-anchor: middle;
            color: #666;
            fill: #666;
            animation: donutfadelong 1s;
        }

        /* ---------- */
        /* just for this presentation */
        html {
            text-align: center;
        }

        .svg-item {
            max-width: 40%;
            display: inline-block;
        }
    </style>
@endsection

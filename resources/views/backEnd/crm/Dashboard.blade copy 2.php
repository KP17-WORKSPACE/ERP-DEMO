@extends('backEnd.masterpage')
@section('mainContent')

@php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp


<?php try { ?>
<div class="container-fluid mb-4">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Administrator Dashboard</h2>
            <span class="page-label">Home - Administrator Dashboard</span>
            <input type="hidden" id="base_url" value="{{ url('/') }}" />
        </div>
        <div>
            
        </div>
    </div>
    <div class="row">
{{--  Sales Performance Selecter-----------------------  --}}
        <div class="" style="display: none;">
            <div class="row">
                <div class="col-md-4">
                    <select class="form-control form-card-select" id="filter_date">
                        <option value="m" @if ($_SESSION["page_date_id"]=='m') selected @endif>Monthly</option>
                        <option value="pm" @if ($_SESSION["page_date_id"]=='pm') selected @endif>Previous Month</option>
                        <option value="d" @if ($_SESSION["page_date_id"]=='d') selected @endif>Day</option>
                        <option value="q" @if ($_SESSION["page_date_id"]=='q') selected @endif>Quarterly</option>
                        <option value="pq" @if ($_SESSION["page_date_id"]=='pq') selected @endif>Previous Quarter</option>
                        <option value="y" @if ($_SESSION["page_date_id"]=='y') selected @endif>This Year</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <select class="form-control form-card-select" id="filter_company">
                        <select class="form-control form-card-select" id="main_filter_company">
                            @php $com_list = App\SysHelper::get_company_names(); @endphp
                            @foreach ($com_list as $list)
                                <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                            @endforeach
                        </select>
                    </select>
                </div>
            </div>
        </div>
        
        @if(session('logged_session_data.company_id') == 1)
        <div class="col-lg-8 mb-3">
            <div class="card p-4 max-height">
                <div>
                    <h2 class="page-heading mb-3">Sales Performance</h2>
                    <hr>
                </div>
                <div>
                    <table class="table table-nowrap table-centered mb-0 table-striped2">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Target GP</th>
                                <th>Revenue</th>
                                <th>On Process Deal</th>
                                <th>Forcast</th>
                            </tr>
                        </thead>
                        <tbody>@if(count($performance)>0)
                            @foreach ($performance as $top)
                            <tr>
                                <td><a>{{ $top["company_name"] }}</a></td>
                                <td>{{ $top["target_gp"] }}</td>
                                <td>{{ $top["revenue"][2] }}</td>
                                <td>{{ $top["on_process_deal"] }}</td>
                                <td>{{ $top["forcast"] }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3"></div>

        @else

        <div class="col-lg-3 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h5>Target GP</h5>
                </div>    
                <div class="p-2">
                        <i class="fa fa-calculator ml-3 mr-4" aria-hidden="true" style="color: #3a62d7; font-size: 40px; float: left;"></i>
                        <h2><a href="{{url('crm-deals-sales-report')}}">{{ $target_gp }}</a></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h5>Revenue</h5>
                </div>    
                <div class="p-2">
                        <i class="fa fa-signal ml-3 mr-4" aria-hidden="true" style="color: #1cc88a; font-size: 40px; float: left;"></i>
                        <h2><a href="{{url('crm-deals-sales-report')}}">{{ $sales_revenue }}</a></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h5>On Process Deal</h5>
                </div>
                <div class="p-2">
                    <i class="fa fa-signal ml-3 mr-4" aria-hidden="true" style="color: #1ca2c8; font-size: 40px; float: left;"></i>
                    <h2><a href="{{url('crm-deals-sales-report')}}">{{ $on_process }}</a></h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h5>Forcast</h5>
                </div>
                <div class="p-2">
                    <i class="fa fa-cart-plus ml-3 mr-4" aria-hidden="true" style="color: #f6c23e; font-size: 40px; float: left;"></i>
                    <h2><a href="{{url('crm-deals-sales-report')}}">{{ $forcast }}</a></h2>
                </div>
            </div>
        </div>
        @endif


{{--  Sales Performance Selecter-----------------------  --}}

        
        <div class="mb-4 @if(session('logged_session_data.company_id')==0) col-lg-8 @else col-lg-4 @endif ">
        
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h6 class="card-head ">Sales Performance</h6>
                </div>
                
            @if(session('logged_session_data.company_id')==0)
            <div class="row">
                <div class="col-lg-5">
                    <div class="text-center">
                        <div class="d-inline-block d-flex justify-content-center ">
                            <div class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                                <a href="#" onclick="sales_click('revenue')">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase">Revenue</div>
                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="revenue">{{ $sales[0] }}</div>
                                </a>
                            </div>
                        </div>
                        <div class="d-inline-block d-flex justify-content-center ">
                            <div class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #36b9cc;">
                                <a href="#" onclick="sales_click('forcast')">
                                    <div class="text-xs font-weight-bold text-info text-uppercase">Forcast</div>
                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="forcast">{{ $sales[1] }}</div>
                                </a>
                            </div>
                        </div>
                        Lost : <span class="mb-0 font-weight-bold text-gray-800 font-card-medium" id="lost">{{ $sales[2] }}</span><br /><br />
                    </div>
                </div>
                <div class="col-lg-7" style="padding-right: 30px;">
<style>
    .sales_tab { font-size: 80%; color: #7183b9; }
    .sales_tab td{text-align: right;}
</style>


            <table class="table table-nowrap table-centered mb-0 table-striped2 sales_tab">
                <thead>
                    <tr>
                        <td style="text-align: left;">Company</td>
                        <td>Revenue</td>
                        <td>Forcast</td>
                    </tr>
                </thead>
                <tbody>
                    @php $com_list = App\SysHelper::get_company_names(); @endphp
                    @foreach ($com_list as $list)
                    @php $data = App\SysHelper::get_total_sales_revenue($_SESSION["page_date_id"],$list->id); @endphp
                    <tr><td style="text-align: left;"><i>{{ $list->company_name }}</i></td><td>{{ $data[0] }}</td><td>{{ $data[1] }}</td></tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td>{{ $sales[0] }}</td>
                        <td>{{ $sales[1] }}</td>
                    </tr>
                </tfoot>
            </table>

                </div>
            </div>
            @else
                <div class="text-center">
                    <div class="d-inline-block d-flex justify-content-center ">
                        <div class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                            <a href="#" onclick="sales_click('revenue')">
                                <div class="text-xs font-weight-bold text-primary text-uppercase">Revenue</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="revenue">{{ $sales[0] }}</div>
                            </a>
                        </div>
                    </div>
                    <div class="d-inline-block d-flex justify-content-center ">
                        <div class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #36b9cc;">
                            <a href="#" onclick="sales_click('forcast')">
                                <div class="text-xs font-weight-bold text-info text-uppercase">Forcast</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="forcast">{{ $sales[1] }}</div>
                            </a>
                        </div>
                    </div>
                    Lost : <span class="mb-0 font-weight-bold text-gray-800 font-card-medium" id="lost">{{ $sales[2] }}</span><br /><br />
                </div>
            @endif
                
            </div>
        </div>
        <script>
            function sales_click(id){
                var mo = $("#filter_date").val();
                var co = $("#filter_company").val();
                if(co==0){ return false; }
                var url = $("#base_url").val()+"/crm-deal-sales-performance/"+id+"/"+mo+"/"+co;
                window.location.href = url;
            }
        </script>


        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h6 class="card-head ">Project Performance</h6>
                    <div class="" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="filter_date_project">
                                    <option value="m" @if ($_SESSION["page_date_id"]=='m') selected @endif>Monthly</option>
                            <option value="pm" @if ($_SESSION["page_date_id"]=='pm') selected @endif>Previous Month</option>
                            <option value="d" @if ($_SESSION["page_date_id"]=='d') selected @endif>Day</option>
                            <option value="q" @if ($_SESSION["page_date_id"]=='q') selected @endif>Quarterly</option>
                            <option value="pq" @if ($_SESSION["page_date_id"]=='pq') selected @endif>Previous Quarter</option>
                            <option value="y" @if ($_SESSION["page_date_id"]=='y') selected @endif>This Year</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="filter_company_project">
                                    @php $com_list = App\SysHelper::get_company_names(); @endphp
                                    @foreach ($com_list as $list)
                                        <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                            <a href="#" onclick="project_click('project_revenue')">
                                <div class="text-xs font-weight-bold text-primary text-uppercase">Revenue</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="project_revenue">{{ $project[0] }}</div>
                            </a>
                        </div>
                    </div>

                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #36b9cc;">
                            <a href="#" onclick="project_click('project_forcast')">
                                <div class="text-xs font-weight-bold text-info text-uppercase">Forcast</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="project_forcast">{{ $project[1] }}</div>
                            </a>
                        </div>
                    </div>
                    <script>
                        function project_click(id){
                            var mo = $("#filter_date_project").val();
                            var co = $("#filter_company_project").val();
                            if(co==0){ return false; }
                            var url = $("#base_url").val()+"/crm-deal-project/"+id+"/"+mo+"/"+co;
                            window.location.href = url;
                        }
                    </script>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h6 class="card-head ">Service Performance</h6>
                    <div class="" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="filter_date_service">
                                    <option value="m" @if ($_SESSION["page_date_id"]=='m') selected @endif>Monthly</option>
                            <option value="pm" @if ($_SESSION["page_date_id"]=='pm') selected @endif>Previous Month</option>
                            <option value="d" @if ($_SESSION["page_date_id"]=='d') selected @endif>Day</option>
                            <option value="q" @if ($_SESSION["page_date_id"]=='q') selected @endif>Quarterly</option>
                            <option value="pq" @if ($_SESSION["page_date_id"]=='pq') selected @endif>Previous Quarter</option>
                            <option value="y" @if ($_SESSION["page_date_id"]=='y') selected @endif>This Year</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="filter_company_service">
                                    @php $com_list = App\SysHelper::get_company_names(); @endphp
                            @foreach ($com_list as $list)
                                <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                            @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                            <a href="#" onclick="service_click('service_revenue')">
                                <div class="text-xs font-weight-bold text-primary text-uppercase">Revenue</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="service_revenue">{{ $service[0] }}</div>
                            </a>
                        </div>
                    </div>

                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #36b9cc;">
                            <a href="#" onclick="service_click('service_forcast')">
                                <div class="text-xs font-weight-bold text-info text-uppercase">Forcast</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="service_forcast">{{ $service[1] }}</div>
                            </a>
                        </div>
                    </div>
                    <script>
                        function service_click(id){
                            var mo = $("#filter_date_service").val();
                            var co = $("#filter_company_service").val();
                            if(co==0){ return false; }
                            var url = $("#base_url").val()+"/crm-deal-service/"+id+"/"+mo+"/"+co;
                            window.location.href = url;
                        }
                    </script>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h6 class="card-head ">AMC Performance</h6>
                    <div class="" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="filter_date_amc">
                                    <option value="m" @if ($_SESSION["page_date_id"]=='m') selected @endif>Monthly</option>
                            <option value="pm" @if ($_SESSION["page_date_id"]=='pm') selected @endif>Previous Month</option>
                            <option value="d" @if ($_SESSION["page_date_id"]=='d') selected @endif>Day</option>
                            <option value="q" @if ($_SESSION["page_date_id"]=='q') selected @endif>Quarterly</option>
                            <option value="pq" @if ($_SESSION["page_date_id"]=='pq') selected @endif>Previous Quarter</option>
                            <option value="y" @if ($_SESSION["page_date_id"]=='y') selected @endif>This Year</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="filter_company_amc">
                                    @php $com_list = App\SysHelper::get_company_names(); @endphp
                            @foreach ($com_list as $list)
                                <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                            @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                            <a href="#" onclick="amc_click('amc_revenue')">
                                <div class="text-xs font-weight-bold text-primary text-uppercase">Revenue</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="amc_revenue">{{ $amc[0] }}</div>
                            </a>
                        </div>
                    </div>

                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #36b9cc;">
                            <a href="#" onclick="amc_click('amc_forcast')">
                                <div class="text-xs font-weight-bold text-info text-uppercase">Forcast</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="amc_forcast">{{ $amc[1] }}</div>
                            </a>
                        </div>
                    </div>
                    <script>
                        function amc_click(id){
                            var mo = $("#filter_date_amc").val();
                            var co = $("#filter_company_amc").val();
                            if(co==0){ return false; }
                            var url = $("#base_url").val()+"/crm-deal-amc/"+id+"/"+mo+"/"+co;
                            window.location.href = url;
                        }
                    </script>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card shadow mb-4 p-4">
                <div class="card-header p-0">
                    <h6 class="card-head ">Deals</h6>
                    <div class="" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="deal_filter_date">
                                    <option value="m" @if ($_SESSION["page_date_id"]=='m') selected @endif>Monthly</option>
                            <option value="pm" @if ($_SESSION["page_date_id"]=='pm') selected @endif>Previous Month</option>
                            <option value="d" @if ($_SESSION["page_date_id"]=='d') selected @endif>Day</option>
                            <option value="q" @if ($_SESSION["page_date_id"]=='q') selected @endif>Quarterly</option>
                            <option value="pq" @if ($_SESSION["page_date_id"]=='pq') selected @endif>Previous Quarter</option>
                            <option value="y" @if ($_SESSION["page_date_id"]=='y') selected @endif>This Year</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="deal_filter_company">
                                    @php $com_list = App\SysHelper::get_company_names(); @endphp
                                    @foreach ($com_list as $list)
                                        <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
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
                                            labels: ["Prospecting", "Quote", "Closure", "Won", "Lost", "Project","Channel","Corporate"],
                                            datasets: [{
                                                data: [{{ $total_deals_prospecting }}, {{ $total_deals_quote }}, {{ $total_deals_closure }}, {{ $total_deals_won }}, {{ $total_deals_lost }}, {{ $deals_type_project }}, {{ $deals_type_channel }}, {{ $deals_type_corporate }}],
                                                backgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
                                                hoverBackgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
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
                                    <a href="#" onclick="deal_click('prospecting')"><i class="fas fa-circle" style="color: #f6c23e;"></i> Prospecting</a> <a href="{{ url('crm-deal/prospecting') }}" id="deal_prospecting">{{ $total_deals_prospecting }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('quote')"><i class="fas fa-circle" style="color: #1cc2c8;"></i> Quote</a> <a href="{{ url('crm-deal/quote') }}" id="deal_quote">{{ $total_deals_quote }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('closure')"><i class="fas fa-circle" style="color: #1ca2c8;"></i> Closure</a> <a href="{{ url('crm-deal/closure') }}" id="deal_closure">{{ $total_deals_closure }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('won')"><i class="fas fa-circle" style="color: #1cc88a;"></i> Won</a> <a href="{{ url('crm-deal/won') }}" id="deal_won">{{ $total_deals_won }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('lost')"><i class="fas fa-circle" style="color: #f1416c;"></i> Lost</a> <a href="{{ url('crm-deal/lost') }}" id="deal_lost">{{ $total_deals_lost }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('project')"><i class="fas fa-circle" style="color: #4e51df;"></i> Project</a> <a href="{{ url('crm-deal/project') }}" id="deal_project">{{ $deals_type_project }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('channel')"><i class="fas fa-circle" style="color: #704edf;"></i> Channel</a> <a href="{{ url('crm-deal/channel') }}" id="deal_channel">{{ $deals_type_channel }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('corporate')"><i class="fas fa-circle" style="color: #4e73df;"></i> Corporate</a> <a href="{{ url('crm-deal/corporate') }}" id="deal_corporate">{{ $deals_type_corporate }}</a>
                                </div>
                                <script>
                                    function deal_click(id){
                                        var mo = $("#deal_filter_date").val();
                                        var co = $("#deal_filter_company").val();
                                        if(co==0){ return false; }
                                        var url = $("#base_url").val()+"/crm-deal/"+id+"/"+mo+"/"+co;
                                        window.location.href = url;
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card shadow mb-4 p-4">
                <div class="card-header p-0">
                    <h6 class="card-head ">Leads</h6>
                    <div class="" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="lead_filter_date">
                                    <option value="m" @if ($_SESSION["page_date_id"]=='m') selected @endif>Monthly</option>
                            <option value="pm" @if ($_SESSION["page_date_id"]=='pm') selected @endif>Previous Month</option>
                            <option value="d" @if ($_SESSION["page_date_id"]=='d') selected @endif>Day</option>
                            <option value="q" @if ($_SESSION["page_date_id"]=='q') selected @endif>Quarterly</option>
                            <option value="pq" @if ($_SESSION["page_date_id"]=='pq') selected @endif>Previous Quarter</option>
                            <option value="y" @if ($_SESSION["page_date_id"]=='y') selected @endif>This Year</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="lead_filter_company">
                                    @php $com_list = App\SysHelper::get_company_names(); @endphp
                                    @foreach ($com_list as $list)
                                        <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
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
                                            labels: ["New", "Qualified", "Unqualified", "Project","Channel","Corporate"],
                                            datasets: [{
                                            data: [{{ $total_leads_new }}, {{ $total_leads_qualified }}, {{ $total_leads_unqualified }}, {{ $leads_type_project }}, {{ $leads_type_channel }}, {{ $leads_type_corporate }}],
                                            backgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
                                            hoverBackgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
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
                                    <a href="#" onclick="lead_click('new')"><i class="fas fa-circle" style="color: #36b9cc;"></i> New</a> <a href="{{ url('crm-lead/new') }}" id="lead_new">{{ $total_leads_new }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('qualified')"><i class="fas fa-circle" style="color: #1cc88a;"></i> Qualified</a> <a href="{{ url('crm-lead/qualified') }}" id="lead_qualified">{{ $total_leads_qualified }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('unqualified')"><i class="fas fa-circle" style="color: #f1416c;"></i> Unqualified</a> <a href="{{ url('crm-lead/unqualified') }}" id="lead_unqualified">{{ $total_leads_unqualified }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('project')"><i class="fas fa-circle" style="color: #4e51df;"></i> Project</a> <a href="{{ url('crm-lead/project') }}" id="lead_project">{{ $leads_type_project }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('channel')"><i class="fas fa-circle" style="color: #704edf;"></i> Channel</a> <a href="{{ url('crm-lead/channel') }}" id="lead_channel">{{ $leads_type_channel }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('corporate')"><i class="fas fa-circle" style="color: #4e73df;"></i> Corporate</a> <a href="{{ url('crm-lead/corporate') }}" id="lead_corporate">{{ $leads_type_corporate }}</a>
                                </div>
                                <script>
                                    function lead_click(id){
                                        var mo = $("#lead_filter_date").val();
                                        var co = $("#lead_filter_company").val();
                                        if(co==0){ return false; }
                                        var url = $("#base_url").val()+"/crm-lead/"+id+"/"+mo+"/"+co;
                                        window.location.href = url;
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="card p-4 max-height">
                <div>
                    {{--  <select class="form-control form-card-select float-right" id="filter_date_target" style="width: 300px;">
                        <option value="m">Monthly</option>
                        <option value="pm">Previous Month</option>
                        <option value="d">Day</option>
                        <option value="q">Quarterly</option>
                        <option value="pq">Previous Quarter</option>
                        <option value="y">This Year</option>
                    </select>  --}}
                    <h2 class="page-heading mb-3">Sales Target This Month</h2>
                    <hr>
                </div>
                @if(count($sales_target)>0)
                @foreach ($sales_target as $top)
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-1">{{ $top->userid->full_name }}</p>
                        <?php 
                        
                        if($top->user_id==41){
                            $total_sales = App\SysHelper::get_total_sales_brand_name(1); //get_total_sales_brand($top->user_id,$top->brand);
                        }
                        else{
                            $total_sales = App\SysHelper::get_total_sales_brand($top->user_id,0,$top->company_id); //get_total_sales_brand($top->user_id,$top->brand);
                        }

                        $tp = round($total_sales / $top->target * 100,0);
                        $tpcolor="bg-danger";
                        if($tp<40){$tpcolor="bg-danger";}
                        if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                        if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                        if($tp>100){$tpcolor="bg-purple";}
                        ?>
                        <p class="mb-1 font-semibold">{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', ',') }}AED / {{ @App\SysHelper::com_curr_format($top->target, 2, '.', ',') }}AED</p>
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow p-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title m-0">Payment Pending</h4>
                    <a href="{{url('crm-deal-track-list/pendingpayments')}}" class=" btn-small">View All</a>
                </div>
                <div class="card-body pt-0  max-height">
                    <div class="table-responsive table-bordered">
                        <table class="table table-nowrap table-centered mb-0 table-striped2">
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
                                @if(count($pending_payments)>0)
                                @foreach ($pending_payments as $top)
                                <tr>
                                    <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->deal_id }}</a></td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                    <td>{{ $top->ownername->full_name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                                    <td> <span class="btn-badge rejected py-1 px-2">{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!}</span></td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>

        <div class="col-md-6">
            <div class="card p-3 shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title m-0">Order In Process</h4>
                    <a href="{{url('crm-deal-track-list/orderinprocess')}}" class=" btn-small">View All</a>
                </div>
                <div class="card-body pt-0  max-height">
                    <div class="table-responsive table-bordered">
                        <table class="table table-nowrap table-centered mb-0 table-striped2">
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
                                @if(count($order_in_process)>0)
                                @foreach ($order_in_process as $top)
                                <tr>
                                    <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->deal_id }}</a></td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                    <td>{{ $top->ownername->full_name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                                    <td> <span class="rejected btn-badge py-1 px-2">{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!}</span></td>

                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>

        

        

        
        <div class="col-md-4 mt-3" style="display: none;">

                        <div class="card shadow h-100">
                        <div class="card-header py-4">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-8"><h6 class="card-head ">Ecommerce Sale</h6></div>
                                    <div class="col-md-4">
                                        <select class="form-control form-card-select" id="ecommerce_filter_date">
                                            <option value="m">Monthly</option>
                                            <option value="d">Day</option>
                                            <option value="q">Quarterly</option>
                                            <option value="y">Yearly</option>
                                        </select>
                                        <script>
                                            $(document).on("change", "#ecommerce_filter_date", function () {
                                                var id = $("#ecommerce_filter_date").val();
                                                $("#loading_bg").css("display", "block");
                                                if(id=='m'){
                                                    $("#spanD").css("display", "none");
                                                    $("#spanM").css("display", "block");
                                                    $("#spanY").css("display", "none");
                                                    $("#spanQ").css("display", "none");
                                                }
                                                if(id=='d'){
                                                    $("#spanD").css("display", "block");
                                                    $("#spanM").css("display", "none");
                                                    $("#spanY").css("display", "none");
                                                    $("#spanQ").css("display", "none");
                                                }
                                                if(id=='q'){
                                                    $("#spanD").css("display", "none");
                                                    $("#spanM").css("display", "none");
                                                    $("#spanY").css("display", "none");
                                                    $("#spanQ").css("display", "block");
                                                }
                                                if(id=='y'){
                                                    $("#spanD").css("display", "none");
                                                    $("#spanM").css("display", "none");
                                                    $("#spanY").css("display", "block");
                                                    $("#spanQ").css("display", "none");
                                                }
                                                $("#loading_bg").css("display", "none");
                                            });
                                        </script>
                                    </div>
                                    {{--  <div class="col-md-8">
                                        <select class="form-control form-card-select" id="filter_company">
                                            @php $com_list = App\SysHelper::get_company_names(); @endphp
                            @foreach ($com_list as $list)
                                <option value="{{ $list->id }}" @if (session('logged_session_data.company_id')==$list->id) selected @endif>{{ $list->company_name }}</option>
                            @endforeach
                                        </select>
                                    </div>  --}}
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-4">
                                    <div class="text-center">
                                        <div class="d-inline-block d-flex justify-content-center ">
                                            <div class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">                                        
                                                <?php $abc=App\SysHelper::get_total_ecommerce_sale(); ?>
                                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large">
                                                        <span id="spanD" style="display: none;">{{ $abc[0] }}</span>
                                                        <span id="spanM">{{ $abc[1] }}</span>
                                                        <span id="spanY" style="display: none;">{{ $abc[2] }}</span>
                                                        <span id="spanQ" style="display: none;">{{ $abc[3] }}</span>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        </div>

                        
                    
        </div>

        <div class="col-md-6 mt-4">
            <div class="card p-3 shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title m-0">Payment Reminder</h4>
                    <a href="{{url('crm-deal-track-list/paymentreminder')}}" class=" btn-small">View All</a>
                </div>
                <div class="card-body pt-0  max-height">
                    <div class="table-responsive table-bordered">
                        <table class="table table-nowrap table-centered mb-0 table-striped2">
                            <thead>
                                <tr>
                                    <th>Deal</th>
                                    <th>Company</th>
                                    <th>Owner</th>
                                    <th>Reminder</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($payment_reminder) > 0)
                                @foreach ($payment_reminder as $top)  
                                <tr @if(date('d/m/Y', strtotime($top->reminder_date))==date('d/m/Y')) class="text-danger" @endif >
                                    <td><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}" title="View Deal Track" class="text-dark">{{ $top->deal_id }}</a></td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                    <td>{{ $top->ownername->full_name }}</td>
                                    <td>{{ date('d/m/Y h:i:A', strtotime($top->reminder_date)) }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
        
        <div class="col-md-6 mt-4">
            <div class="card p-3 shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title m-0">Pending Payment (After Reminder)</h4>
                    <a href="{{url('crm-deal-track-list/paymentpendingafterreminder')}}" class=" btn-small">View All</a>
                </div>
                <div class="card-body pt-0  max-height">
                    <div class="table-responsive table-bordered">
                        <table class="table table-nowrap table-centered mb-0 table-striped2">
                            <thead>
                                <tr>
                                    <th>Deal</th>
                                    <th>Company</th>
                                    <th>Owner</th>
                                    <th>Reminder</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($payment_pending) > 0)
                                @foreach ($payment_pending as $top) 
                                <tr>
                                    <td><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}" title="View Deal Track" class="text-dark">{{ $top->deal_id }}</a></td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                    <td>{{ $top->ownername->full_name }}</td>
                                    <td>{{ date('d/m/Y h:i:A', strtotime($top->reminder_date)) }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
        <div class="col-md-6 mt-4">
            <div class="card shadow p-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title m-0">Partial Invoice</h4>
                </div>
                <div class="card-body pt-0  max-height">
                    <div class="table-responsive table-bordered">
                        <table class="table table-nowrap table-centered mb-0 table-striped2">
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
                                @if(count($partial_invoice)>0)
                                @foreach ($partial_invoice as $top)
                                <tr>
                                    <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->deal_id }}</a></td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                    <td>{{ $top->ownername->full_name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                                    <td> <span class="btn-badge warning py-1 px-2">Partial</span></td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
        <div class="col-md-6 mt-4">
            <div class="card shadow p-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="header-title m-0">Deals Overdue After Closing Date</h4>
                </div>
                <div class="card-body pt-0  max-height">
                    <div class="table-responsive table-bordered">
                        <table class="table table-nowrap table-centered mb-0 table-striped2">
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
                                @if(count($dealsbyclosedate)>0)
                                @foreach ($dealsbyclosedate as $top)
                                <tr>
                                    <td><a href="{{url('crm-deals/'.$top->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->id }}</a></td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->deal_name }}<?php } catch (\Exception $e) { }?></div></td>
                                    <td>{{ $top->ownername->full_name }}</td>
                                    <td>{{ date('d/m/Y', strtotime($top->estimated_close_date)) }}</td>
                                    <td>
                                        @if ($top->stage==1)
                                        <span class="btn-badge warning py-1 px-2">Prospecting</span>
                                        @elseif($top->stage==2)
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

        <div class="col-md-12 mt-4">
            <div class="card icon__card__home p-4 ">
                <h2 class="sub-head text-muted">Brand Sales This Month</h2>
                <div class="row">

                    @if(Auth::user()->role_id == 1)
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-allied.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(10) }}</b>
                                <p>Allied Telesis</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-avaya.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(1) }}</b>
                                <p>Avaya</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-cisco.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(4) }}</b>
                                <p>Cisco</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-fortinet.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(12) }}</b>
                                <p>Fortinet</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-huawei.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(39) }}</b>
                                <p>Huawei</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-linksys.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(3) }}</b>
                                <p>Linksys</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-netgear.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(60) }}</b>
                                <p>Netgear</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-sonicwall.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(8) }}</b>
                                <p>Sonicwall</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-ubiquiti.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(46) }}</b>
                                <p>Ubiquiti</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-seceon.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(15) }}</b>
                                <p>Seceon</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-apphaz.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(17) }}</b>
                                <p>Apphaz</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-sisa.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(59) }}</b>
                                <p>SISA</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-securden.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(44) }}</b>
                                <p>Securden</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-xcitium.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(14) }}</b>
                                <p>Xcitium</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-instasafe.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(16) }}</b>
                                <p>Instasafe</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-allied.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name_company(10,3) }}</b>
                                <p>Allied Telesis</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-sonicwall.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name_company(8,3) }}</b>
                                <p>Sonicwall</p>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card icon__card__home p-4 ">
                <h2 class="sub-head text-muted">Aruba Brand Sales This Month</h2>
                <div class="row">                    
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-aruba.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(6) }}</b>
                                <p>Aruba Networks</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-4">
                        <div class="p-4 text-center">
                            <div class="img__wrap">
                                <img src="{{ asset('public/admin-iroid/') }}/img/bl-aruba-in.png" width="70">
                            </div>
                            <div class="txt__wrap">
                                <b>{{ App\SysHelper::get_total_sales_brand_name(9) }}</b>
                                <p>Aruba Instant On</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

</div>
    
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <script>

$(document).on("change", "#main_filter_company", function () {
    var company = $("#main_filter_company").val();
    var date = $("#main_filter_date").val();
    change_all(company,date);
    $("#loading_bg").css("display", "block");
    location.reload();
});

$(document).on("change", "#main_filter_date", function () {
    var company = $("#main_filter_company").val();
    var date = $("#main_filter_date").val();
    change_all(company,date);
    $("#loading_bg").css("display", "block");
    location.reload();
});

function change_all(company,date)
{
    $("#filter_company").val(company);
    $("#filter_date").val(date);
    get_data(company,date);

    $("#lead_filter_company").val(company);
    $("#lead_filter_date").val(date);
    get_lead_data(company,date);
    
    $("#deal_filter_company").val(company);
    $("#deal_filter_date").val(date);
    get_deal_data(company,date);
    
    $("#filter_company_service").val(company);
    $("#filter_date_service").val(date);
    get_service_data(company,date);
    
    $("#filter_company_amc").val(company);
    $("#filter_date_amc").val(date);
    get_amc_data(company,date);
    
    $("#filter_company_project").val(company);
    $("#filter_date_project").val(date);
    get_project_data(company,date);
    
    $("#loading_bg").css("display", "block");
    
}


$(document).on("change", "#filter_company", function () {
    var company = $("#filter_company").val();
    var date = $("#filter_date").val();
    get_data(company,date);
});
$(document).on("change", "#filter_date", function () {
    var company = $("#filter_company").val();
    var date = $("#filter_date").val();
    get_data(company,date);
});
function get_data(company,date) {
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
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                        $("#revenue").html(dataResult['data'][0]);
                        $("#forcast").html(dataResult['data'][1]);
                        $("#lost").html(dataResult['data'][2]);
                }
                else{
                    $("#revenue").html("0");
                    $("#forcast").html("0");
                    $("#lost").html("0");
                }
                $("#loading_bg").css("display", "none");
        }
    });
}

$(document).on("change", "#lead_filter_company", function () {
    var company = $("#lead_filter_company").val();
    var date = $("#lead_filter_date").val();
    get_lead_data(company,date);
});
$(document).on("change", "#lead_filter_date", function () {
    var company = $("#lead_filter_company").val();
    var date = $("#lead_filter_date").val();
    get_lead_data(company,date);
});
function get_lead_data(company,date) {
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
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                    $("#lead_new").html(dataResult['data'][0]);
                    $("#lead_qualified").html(dataResult['data'][1]);
                    $("#lead_unqualified").html(dataResult['data'][2]);
                    $("#lead_project").html(dataResult['data'][3]);
                    $("#lead_channel").html(dataResult['data'][4]);
                    $("#lead_corporate").html(dataResult['data'][5]);
                    chart_lead(dataResult['data'][0],dataResult['data'][1],dataResult['data'][2],dataResult['data'][3],dataResult['data'][4],dataResult['data'][5],dataResult['data'][6]);
                }
                else{
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

$(document).on("change", "#deal_filter_company", function () {
    var company = $("#deal_filter_company").val();
    var date = $("#deal_filter_date").val();
    get_deal_data(company,date);
});
$(document).on("change", "#deal_filter_date", function () {
    var company = $("#deal_filter_company").val();
    var date = $("#deal_filter_date").val();
    get_deal_data(company,date);
});
function get_deal_data(company,date) {
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
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                    $("#deal_prospecting").html(dataResult['data'][0]);
                    $("#deal_quote").html(dataResult['data'][1]);
                    $("#deal_closure").html(dataResult['data'][2]);
                    $("#deal_won").html(dataResult['data'][3]);
                    $("#deal_lost").html(dataResult['data'][4]);
                    $("#deal_project").html(dataResult['data'][5]);
                    $("#deal_channel").html(dataResult['data'][6]);
                    $("#deal_corporate").html(dataResult['data'][7]);
                    chart_deal(dataResult['data'][0],dataResult['data'][1],dataResult['data'][2],dataResult['data'][3],dataResult['data'][4],dataResult['data'][5],dataResult['data'][6],dataResult['data'][7]);
                }
                else{
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
function chart_lead(a,b,c,d,e,f)
{
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["New", "Qualified", "Unqualified", "Project","Channel","Corporate"],
        datasets: [{
        data: [a, b, c, d, e, f],
        backgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
        hoverBackgroundColor: ['#36b9cc', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
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
function chart_deal(a,b,c,d,e,f,g)
{
    var ctx = document.getElementById("myPieChart2");
    var myPieChart2 = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Prospecting", "Quote", "Closure", "Won", "Lost", "Project","Channel","Corporate"],
        datasets: [{
            data: [a,b,c,d,e,f,g],
            backgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
            hoverBackgroundColor: ['#f6c23e', '#1cc2c8', '#1ca2c8', '#1cc88a', '#f1416c', '#4e51df', '#704edf', '#4e73df'],
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


$(document).on("change", "#filter_company_service", function () {
    var company = $("#filter_company_service").val();
    var date = $("#filter_date_service").val();
    get_service_data(company,date);
});
$(document).on("change", "#filter_date_service", function () {
    var company = $("#filter_company_service").val();
    var date = $("#filter_date_service").val();
    get_service_data(company,date);
});
function get_service_data(company,date) {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('crm-dashboard-service-filter') }}";
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
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                        $("#service_revenue").html(dataResult['data'][0]);
                        $("#service_forcast").html(dataResult['data'][1]);
                }
                else{
                    $("#service_revenue").html("0");
                    $("#service_forcast").html("0");                    
                }
                $("#loading_bg").css("display", "none");
        }
    });
}
$(document).on("change", "#filter_company_amc", function () {
    var company = $("#filter_company_amc").val();
    var date = $("#filter_date_amc").val();
    get_amc_data(company,date);
});
$(document).on("change", "#filter_date_amc", function () {
    var company = $("#filter_company_amc").val();
    var date = $("#filter_date_amc").val();
    get_amc_data(company,date);
});
function get_amc_data(company,date) {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('crm-dashboard-amc-filter') }}";
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
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                        $("#amc_revenue").html(dataResult['data'][0]);
                        $("#amc_forcast").html(dataResult['data'][1]);
                }
                else{
                    $("#amc_revenue").html("0");
                    $("#amc_forcast").html("0");                    
                }
                $("#loading_bg").css("display", "none");
        }
    });
}
$(document).on("change", "#filter_company_project", function () {
    var company = $("#filter_company_project").val();
    var date = $("#filter_date_project").val();
    get_project_data(company,date);
});
$(document).on("change", "#filter_date_project", function () {
    var company = $("#filter_company_project").val();
    var date = $("#filter_date_project").val();
    get_project_data(company,date);
});
function get_project_data(company,date) {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('crm-dashboard-project-filter') }}";
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
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                        $("#project_revenue").html(dataResult['data'][0]);
                        $("#project_forcast").html(dataResult['data'][1]);
                }
                else{
                    $("#project_revenue").html("0");
                    $("#project_forcast").html("0");                    
                }
                $("#loading_bg").css("display", "none");
        }
    });
}
    </script>
@endsection
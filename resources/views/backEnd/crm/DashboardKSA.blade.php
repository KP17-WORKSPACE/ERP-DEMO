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
            
            <div class="p-2" style="background: #0b2262; color: #e5e5e5; min-width: 350px; width: 100%; height: auto; margin: -20px 0px 15px 0px;">
                @if (file_exists(@session('logged_session_data.staff_photo')))
                    <img class="rounded float-right" src="{{ file_exists(@session('logged_session_data.staff_photo')) ? asset(session('logged_session_data.staff_photo')) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="" height="85px">
                @else
                    <img class="rounded float-right" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="" height="85px">
                @endif
                <h6 class="font-weight-700">{{ session('logged_session_data.full_name') }}</h6>
                <b>{{ session('logged_session_data.designation_name') }}</b><br /><br />
                <i><i class="fa fa-calendar" aria-hidden="true"></i> {{ date('l, F j, Y') }}</i>
            </div>
            
        </div>
        <div>
            {{--  <button class="btn-topnav" type="button" class="btn btn-primary" data-toggle="modal" data-target="#adddeal"><i class="fa fa-plus"></i> New Leads</button>
            <button class="btn-topnav"><i class="fa fa-eye"></i> View Leads</button>  --}}
            <button class="btn-topnav" onclick="window.location.reload();"><i class=""></i> Refresh</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-4">
                    <h6 class="card-head ">Sales Performance</h6>
                    <div class="">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="filter_date">
                                    <option value="m">Monthly</option>
                                    <option value="pm">Previous Month</option>
                                    <option value="d">Day</option>
                                    <option value="q">Quarterly</option>
                                    <option value="pq">Previous Quarter</option>
                                    <option value="y">This Year</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="filter_company">
                                    <option value="4">Supreme KSA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                            <a href="#" onclick="sales_click('revenue')">
                                <div class="text-xs font-weight-bold text-primary text-uppercase">Revenue</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="revenue">{{ $sales[0] }}</div>
                            </a>
                        </div>
                    </div>

                    <div class="d-inline-block d-flex justify-content-center ">
                        <div
                            class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #36b9cc;">
                            <a href="#" onclick="sales_click('forcast')">
                                <div class="text-xs font-weight-bold text-info text-uppercase">Forcast</div>
                                <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="forcast">{{ $sales[1] }}</div>
                            </a>
                        </div>
                    </div>
                    <script>
                        function sales_click(id){
                            var mo = $("#filter_date").val();
                            var co = $("#filter_company").val();
                            if(co==0){$("#filter_company").focus(); return false;}
                            var url = $("#base_url").val()+"/crm-deal-sales-performance/"+id+"/"+mo+"/"+co;
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
                    <div class="">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="deal_filter_date">
                                    <option value="m">Monthly</option>
                                    <option value="d">Day</option>
                                    <option value="q">Quarterly</option>
                                    <option value="y">Yearly</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="deal_filter_company">
                                    <option value="4">Supreme KSA</option>
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
                                                data: [{{ $total_deals_prospecting }}, {{ $total_deals_quote }}, {{ $total_deals_closure }}, {{ $total_deals_won }}, {{ $total_deals_lost }}, {{ $deals_type->where('isproject',1)->count() }}, {{ $deals_type->where('isproject',2)->count() }}, {{ $deals_type->where('isproject',3)->count() }}],
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
                                    <a href="#" onclick="deal_click('project')"><i class="fas fa-circle" style="color: #4e51df;"></i> Project</a> <a href="{{ url('crm-deal/project') }}" id="deal_project">{{ $deals_type->where('isproject',1)->count() }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('channel')"><i class="fas fa-circle" style="color: #704edf;"></i> Channel</a> <a href="{{ url('crm-deal/channel') }}" id="deal_channel">{{ $deals_type->where('isproject',2)->count() }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="deal_click('corporate')"><i class="fas fa-circle" style="color: #4e73df;"></i> Corporate</a> <a href="{{ url('crm-deal/corporate') }}" id="deal_corporate">{{ $deals_type->where('isproject',3)->count() }}</a>
                                </div>
                                <script>
                                    function deal_click(id){
                                        var mo = $("#deal_filter_date").val();
                                        var co = $("#deal_filter_company").val();
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
                    <div class="">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control form-card-select" id="lead_filter_date">
                                    <option value="m">Monthly</option>
                                    <option value="d">Day</option>
                                    <option value="q">Quarterly</option>
                                    <option value="y">Yearly</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control form-card-select" id="lead_filter_company">
                                    <option value="4">Supreme KSA</option>
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
                                            data: [{{ $total_leads_new }}, {{ $total_leads_qualified }}, {{ $total_leads_unqualified }}, {{ $leads_type->where('isproject',1)->count() }}, {{ $leads_type->where('isproject',2)->count() }}, {{ $leads_type->where('isproject',3)->count() }}],
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
                                    <a href="#" onclick="lead_click('project')"><i class="fas fa-circle" style="color: #4e51df;"></i> Project</a> <a href="{{ url('crm-lead/project') }}" id="lead_project">{{ $leads_type->where('isproject',1)->count() }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('channel')"><i class="fas fa-circle" style="color: #704edf;"></i> Channel</a> <a href="{{ url('crm-lead/channel') }}" id="lead_channel">{{ $leads_type->where('isproject',2)->count() }}</a>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <a href="#" onclick="lead_click('corporate')"><i class="fas fa-circle" style="color: #4e73df;"></i> Corporate</a> <a href="{{ url('crm-lead/corporate') }}" id="lead_corporate">{{ $leads_type->where('isproject',3)->count() }}</a>
                                </div>
                                <script>
                                    function lead_click(id){
                                        var mo = $("#lead_filter_date").val();
                                        var co = $("#lead_filter_company").val();
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
        






    </div>

</div>
    
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <script>

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
                }
                else{
                    $("#revenue").html("0");
                    $("#forcast").html("0");                    
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
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

        <style>
            /* Quote Section */
            .quote-box {
                background: #f8fafc;
                /* light neutral background */
                border-left: 4px solid #36b9cc;
                /* accent bar */
                padding: 1rem 1.25rem;
                margin: 0;
                border-radius: 0.5rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                max-width: 600px;
            }

            .quote-box h2 {
                font-size: 1.1rem;
                font-weight: 500;
                color: #333;
                margin: 0;
                line-height: 1.6;
                font-style: italic;
            }

            .quote-box i {
                font-size: 1.2rem;
                color: #36b9cc;
                margin-right: 6px;
            }
        </style>

        <div class="long-list" id="filters-long">
            <div class="d-flex  justify-content-between ">
                <!-- Left: Heading -->
                <h4 class="mb-0">Administrator Dashboard</h4>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />

               
            </div>

        </div>

        <div class="left-nav-list">

            <div class="row mt-3">
            <?php /*
            <div class="col-lg-3 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-4">
                        <h5>Target GP</h5>
                    </div>
                    <div class="p-2">
                        <i class="fa fa-calculator ml-3 mr-4" aria-hidden="true" style="color: #3a62d7; font-size: 50px; float: left;"></i>
                        @if(@$targets !=0)
                        <?php @$tp = round(str_replace(',','',@$target_gp) / @$targets * 100,0);
                        @$tp_balance = 100-$tp;
                        
                        ?>
                        <style>
                            .svg-item { float: right; margin-top: -80px !important;
                                width: 100%;
                                font-size: 16px;
                                margin: 0 auto;
                                animation: donutfade 1s;
                            }
                            
                            @keyframes donutfade {
                                0% {
                                    opacity: .2;
                                }
                                100% {
                                    opacity: 1;
                                }
                            }
                            
                            @media (min-width: 992px) {
                                .svg-item {
                                    width: 80%;
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
                            
                            .segment-1{fill:#ccc;}
                            .segment-2{fill:#3a62d7;}
                            
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
                                color:#666;
                                fill: #666;
                                animation: donutfadelong 1s;
                            }
                            html { text-align:center; }
                            .svg-item {
                              max-width:40%;
                              display:inline-block;
                            }
                        </style>
                        <div class="svg-item">
                            <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                              <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                              <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                              <circle class="donut-segment donut-segment-2" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="{{ @$tp }} {{ @$tp_balance }}" stroke-dashoffset="25"></circle>
                              <g class="donut-text donut-text-1">
                          
                                <text y="50%" transform="translate(0, 2)">
                                  <tspan x="50%" text-anchor="middle" class="donut-percent"><a href="{{url('crm-deals-sales-report')}}">{{ @$tp }}%</a></tspan>   
                                </text>
                                <text y="60%" transform="translate(0, 2)">
                                  <tspan x="50%" text-anchor="middle" class="donut-data"></tspan>   
                                </text>
                              </g>
                            </svg>
                          </div>

                        @endif
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
                            <h2><a href="{{url('crm-deals-sales-report')}}">{{ @$sales_revenue }}</a></h2>
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
                        <h2><a href="{{url('crm-deals-sales-report')}}">{{ @$on_process }}</a></h2>
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
                        <h2><a href="{{url('crm-deals-sales-report')}}">{{ @$forcast }}</a></h2>
                    </div>
                </div>
            </div> */ ?>
            <div class="col-md-6 pb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Pending Approval</h4>
                        <a href="{{url('crm-deal-track-list/0')}}" class=" btn-small">View All</a>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"  style="table-layout: fixed;width:100%" id="long-list">
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
                                    @if(count($pending_approval)>0)
                                    @foreach ($pending_approval as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td>{{ $top->customername->name }}</div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td>{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!}
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
            <div class="col-md-6 pb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Deals Approved</h4>
                        <a href="{{url('crm-deal-track-list/salesapprovedlist')}}" class=" btn-small">View All</a>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"  style="table-layout: fixed;width:100%" id="long-list">
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
                                    @if(count($approved_list)>0)
                                    @foreach ($approved_list as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td>{{ $top->customername->name }}</div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td>{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!}
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
            <div class="col-md-6 pb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Order In Process</h4>
                        <a href="{{url('crm-deal-track-list/orderinprocess')}}" class=" btn-small">View All</a>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"  style="table-layout: fixed;width:100%" id="long-list">
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
                                        <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td>{{ $top->customername->name }}</div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td>{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!}
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
            <div class="col-md-6 mt-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Deals Overdue After Closing Date</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"  style="table-layout: fixed;width:100%" id="long-list">
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
                                        <td><a href="{{url('crm-deals/'.$top->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->code }}</a></td>
                                        <td>{{ $top->deal_name }}</div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->estimated_close_date)) }}</td>
                                        <td>
                                            @if ($top->stage==1)
                                            <span class="badge bg-warning py-1 px-2">Prospecting</span>
                                            @elseif($top->stage==2)
                                            <span class="badge bg-warning py-1 px-2">Quote</span>
                                            @else
                                            <span class="badge bg-warning py-1 px-2">Closure</span>
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
    </script>

@endsection

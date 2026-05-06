@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>
    <div class="container-fluid mb-4">

        <div class="d-sm-flex justify-content-between">
            <div class="shadow" style="background: #36b9cc4a; color: #000; padding: 20px; margin-bottom: 20px; width: 100%;">
                    <h2 class="page-heading m-0" style="font-size: 25px; font-weight: normal;"><span class=""><i class="fa fa-quote-left" aria-hidden="true"></i><br />
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 19px; font-weight:normal; font-style: italic;">{{ App\SysHelper::get_quote_text() }}</span></h2>
                    <input type="hidden" id="base_url" value="{{ url('/') }}" />
            </div>
            <div>
                @if($d_full_name == "")
                <div class="p-2" style="background: #0b2262; color: #e5e5e5; min-width: 350px; width: 100%; height: auto; margin: 0px 0px 15px 0px;">
                    @if (file_exists(@session('logged_session_data.staff_photo')))
                        <img class="rounded float-right" src="{{ file_exists(@session('logged_session_data.staff_photo')) ? asset(session('logged_session_data.staff_photo')) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="" height="85px">
                    @else
                        <img class="rounded float-right" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="" height="85px">
                    @endif
                    <h6 class="font-weight-700">{{ session('logged_session_data.full_name') }}</h6>
                    <b>{{ session('logged_session_data.designation_name') }}</b><br /><br />
                    <i><i class="fa fa-calendar" aria-hidden="true"></i> {{ date('l, F j, Y') }}</i>
                </div>
                @else
                <div class="p-2" style="background: #0b2262; color: #e5e5e5; min-width: 350px; width: 100%; height: auto; margin: 0px 0px 15px 0px;">
                    @if (file_exists(@session('logged_session_data.staff_photo')))
                        <img class="rounded float-right" src="{{ file_exists($d_staff_photo) ? asset($d_staff_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="" height="85px">
                    @else
                        <img class="rounded float-right" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="" height="85px">
                    @endif
                    <h6 class="font-weight-700">{{ $d_full_name }}</h6>
                    <b>{{ $d_designation }}</b><br /><br />
                    <i><i class="fa fa-calendar" aria-hidden="true"></i> {{ date('l, F j, Y') }}</i>
                </div>
                @endif
            </div>
        </div>

        <div class="row">
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
                                    @if(count($pending_approval)>0)
                                    @foreach ($pending_approval as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
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
                                    @if(count($approved_list)>0)
                                    @foreach ($approved_list as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
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
                                        <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
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
                                        <td><a href="{{url('crm-deals/'.$top->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->code }}</a></td>
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
    </script>
@endsection
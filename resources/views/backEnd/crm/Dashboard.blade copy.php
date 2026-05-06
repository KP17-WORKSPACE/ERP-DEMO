@extends('backEnd.master')
@section('mainContent')

@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    
    if(isset($generalSetting->logo)){ @$logo = @$generalSetting->logo; }
    else{ $logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
@endphp
    

<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Admin Dashboard')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-leads') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New Leads</a>
            <a href="{{ url('crm-leads/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View Leads</a>
            <a href="{{ url('crm-deals') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New Deals</a>
            <a href="{{ url('crm-deals/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View Deals</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>
<div class="col-lg-12 text-right">
    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
        @if (session()->has('message-success'))
            <p class="text-success">
                {{ session()->get('message-success') }}
            </p>
        @elseif(session()->has('message-danger'))
            <p class="text-danger">
                {{ session()->get('message-danger') }}
            </p>
        @endif
    @endif
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<?php try{ ?>
    
    <section class="admin-visitor-area ml-3">
        <div class="row">
            
            <div class="col-lg-4">
                <div class="white-box leadbox pt-3" style="height: 300px;">
                    <select class="float-right dynamicstxt w-25" id="filter_company">
                        <option value="1">Syscom Distributions LLC</option>
                        <option value="3">Magnus Infotech Trading LLC</option>
                        <option value="4">Supreme KSA</option>
                        <option value="6">Supreme System Distributors SPC</option>
                        <option value="7">Syscom Distributions LLC - Abu Dhabi</option>
                    </select>
                    <select class="float-right dynamicstxt w-25" id="filter_date">
                        <option value="d">Day</option>
                        <option value="m">Monthly</option>
                        <option value="q">Quarterly</option>
                        <option value="y">Yearly</option>
                    </select>
                    <h6>Sales Performance</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />

                    <div id="spanMonth" style="padding-top: 60px; text-align: center; font-size: 20px; line-height: 40px;">
                        Revenue : <span id="revenue">{{ App\SysHelper::get_revenue_D() }}</span>
                        <br />
                        Forcast : <span id="forcast">{{ App\SysHelper::get_forcast_D() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="white-box leadbox pt-3" style="height: 300px;">
                    <h6>Leads By Month</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />
                    
                        <div class="row">
                        <div class="col-lg-8">                        
                        <canvas id="pieChart" style="width:100%;"></canvas>                        
                        </div>                        
                        <div class="col-lg-4"><br />
                        <ul class="chart-legend clearfix">
                        <li><a href="{{ url('crm-lead/new') }}"><i class="far fa-circle text-warning"></i> <span class="text-bold"> {{ $total_leads_new }}</span> New</a></li>
                        <li><a href="{{ url('crm-lead/qualified') }}"><i class="far fa-circle text-success"></i> <span class="text-bold"> {{ $total_leads_qualified }}</span> Qualified</a></li>
                        <li><a href="{{ url('crm-lead/unqualified') }}"><i class="far fa-circle text-danger"></i> <span class="text-bold"> {{ $total_leads_unqualified }}</span> Unqualified</a></li>
                        
                        <li><a href="{{ url('crm-lead/project') }}"><i class="far fa-circle text-purple"></i> <span class="text-bold"> {{ $leads_type->where('isproject',1)->count() }}</span> Project</a></li>
                        <li><a href="{{ url('crm-lead/channel') }}"><i class="far fa-circle text-purple"></i> <span class="text-bold"> {{ $leads_type->where('isproject',2)->count() }}</span> Channel</a></li>
                        <li><a href="{{ url('crm-lead/corporate') }}"><i class="far fa-circle text-purple"></i> <span class="text-bold"> {{ $leads_type->where('isproject',3)->count() }}</span> Corporate</a></li>
                        </ul>
                        </div>
                        
                        </div>

                    <script>
                        var xValues = ["New", "Qualified", "Unqualified"];
                        var yValues = [{{ $total_leads_new }}, {{ $total_leads_qualified }}, {{ $total_leads_unqualified }}];
                        var barColors = ["#ffc107","#28a745","#dc3545"];
                        
                        new Chart("pieChart", {
                            type: "doughnut",
                            data: {
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                            },
                            options: {
                            title: {
                                display: true,
                                text: ""
                            }
                            }
                        });
                    </script>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="white-box leadbox pt-3" style="height: 300px;">
                    <h6>Deals By Month</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />

                    <div class="row">
                        <div class="col-lg-8">                        
                            <canvas id="myChart" style="width:100%;"></canvas>                      
                        </div>                        
                        <div class="col-lg-4">
                        <ul class="chart-legend clearfix">
                        <li><a href="{{ url('crm-deal/prospecting') }}"><i class="far fa-circle text-warning"></i> <span class="text-bold"> {{ $total_deals_prospecting }}</span> Prospecting</a></li>
                        <li><a href="{{ url('crm-deal/quote') }}"><i class="far fa-circle text-info"></i> <span class="text-bold"> {{ $total_deals_quote }}</span> Quote</a></li>
                        <li><a href="{{ url('crm-deal/closure') }}"><i class="far fa-circle text-primary"></i> <span class="text-bold"> {{ $total_deals_closure }}</span> Closure</a></li>
                        <li><a href="{{ url('crm-deal/won') }}"><i class="far fa-circle text-success"></i> <span class="text-bold"> {{ $total_deals_won }}</span> Won</a></li>
                        <li><a href="{{ url('crm-deal/lost') }}"><i class="far fa-circle text-danger"></i> <span class="text-bold"> {{ $total_deals_lost }}</span> Lost</a></li>

                        <li><a href="{{ url('crm-deal/project') }}"><i class="far fa-circle text-purple"></i> <span class="text-bold"> {{ $deals_type->where('isproject',1)->count() }}</span> Project</a></li>
                        <li><a href="{{ url('crm-deal/channel') }}"><i class="far fa-circle text-purple"></i> <span class="text-bold"> {{ $deals_type->where('isproject',2)->count() }}</span> Channel</a></li>
                        <li><a href="{{ url('crm-deal/corporate') }}"><i class="far fa-circle text-purple"></i> <span class="text-bold"> {{ $deals_type->where('isproject',3)->count() }}</span> Corporate</a></li>

                        {{--  0-Deal, 1-Project, 2-Channel, 3-Corporate  --}}
                        
                        </ul>
                        </div>
                        
                        </div>
                    <script>
                        var xValues = ["Prospecting", "Quote", "Closure", "Won", "Lost"];
                        var yValues = [{{ $total_deals_prospecting }}, {{ $total_deals_quote }}, {{ $total_deals_closure }}, {{ $total_deals_won }}, {{ $total_deals_lost }}];
                        var barColors = ["#ffc107", "#17a2b8","#007bff","#28a745","#dc3545"];
                        
                        new Chart("myChart", {
                            type: "doughnut",
                            data: {
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                            },
                            options: {
                            title: {
                                display: true,
                                text: ""
                            }
                            }
                        });
                    </script>
                </div>
            </div>

            
            
        </div>

        <br />

        <div class="row">
            
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 300px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-approval-list')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Payment Pending</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                            <td>Status</td>
                        </tr>
                    @if(count($pending_payments)>0)
                    @foreach ($pending_payments as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                        <td class="text-xs">                            
                            {!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!} </td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track">{{ $top->deal_name }}</a> - <span class="text-xs">{{ $top->cust_name }}</span> 
                    <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a><hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>                    
                </div>
            </div>
        
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 300px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-approval-list')}}" class="btn btn-info btn-xs text-white p-0 mr-2 float-right">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Order In Process</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                            <td>Status</td>
                        </tr>
                        @if(count($order_in_process)>0)
                    @foreach ($order_in_process as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                        <td class="text-xs">{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!} </td>
                    </tr>
                    @endforeach
                    @endif
                </table>

                    {{--  Total <span class="text-danger text-lg">{{ $total_deals }}</span> - Deals<br />
                    <span class="text-danger text-lg">{{ $total_deals_prospecting }}</span> - Deals Prospecting<br />
                    <span class="text-danger text-lg">{{ $total_deals_quote }}</span> - Deals Quote<br />
                    <span class="text-danger text-lg">{{ $total_deals_closure }}</span> - Deals Closure<br />
                    <span class="text-danger text-lg">{{ $total_deals_won }}</span> - Deals Won<br />
                    <span class="text-danger text-lg">{{ $total_deals_lost }}</span> - Deals Lost  --}}
                </div>
            </div>
            
        </div>
        <br />

        <div class="row">
            
            <div class="col-lg-6">
                <div class="white-box leadbox pt-3" style="height: 350px; overflow-y: scroll;">
                    <h6>Target This Month</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />
                    @if(count($sales_target)>0)
                    @foreach ($sales_target as $top)
                    <div class="progress-group">
                        <span class="progress-text">{{ $top->userid->full_name }}</span>
                        <?php $total_sales = App\SysHelper::get_total_sales_brand($top->user_id,$top->brand);
                        $tp = round($total_sales / $top->target * 100,0);
                        $tpcolor="bg-danger";
                        if($tp<40){$tpcolor="bg-danger";}
                        if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                        if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                        if($tp>100){$tpcolor="bg-purple";}
                        ?>
                        <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', ',') }}AED</b> / {{ @App\SysHelper::com_curr_format($top->target, 2, '.', ',') }}AED</span>
                        <div class="progress sm">
                        <div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div>
                        </div>
                    </div>
                    <hr class="mt-2 mb-2"/>
                    @endforeach
                    @endif
                </div>
            </div>
            <div class="col-lg-4">
                <div class="white-box leadbox pt-3" style="height: 350px; overflow-y: scroll;">
                    <h6>Brand Sales This Month</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />
                    <div class="progress-group">
                        <span class="progress-text">Allied Telesis</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(10) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Aruba Networks</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(6) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Avaya</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(1) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Cisco</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(4) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Fortinet</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(12) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    {{--  <div class="progress-group">
                        <span class="progress-text">Holowits</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(38) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>  --}}
                    <div class="progress-group">
                        <span class="progress-text">Huawei</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(39) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Linksys</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(3) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Netgear</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(60) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    {{--  <div class="progress-group">
                        <span class="progress-text">Snom</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(32) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>  --}}
                    <div class="progress-group">
                        <span class="progress-text">Sonicwall</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(8) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Ubiquiti Networks</span>
                        <span class="progress-number float-right"><b>{{ App\SysHelper::get_total_sales_brand_name(46) }} AED</b></span>
                        <hr class="mt-2 mb-2"/>
                    </div>
                </div>
            </div>

        </div>

        <br />
        <div class="row">
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{ url('crm-deal-track-approval-list') }}"
                        class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Payment Reminder</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Reminder</td>
                        </tr>
                        @if (count($payment_reminder) > 0)
                            @foreach ($payment_reminder as $top)                            
                                <tr @if(date('d/m/Y', strtotime($top->reminder_date))==date('d/m/Y')) class="bg-danger" @endif >
                                    <td class="text-xs"><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}"
                                            title="View Deal Track" class="text-xs text-dark">{{ $top->deal_id }}</a></td>
                                    <td class="text-xs">
                                        <?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                                    <td class="text-xs">{{ $top->ownername->full_name }}</td>
                                    <td class="text-xs">{{ date('d/m/Y h:i:A', strtotime($top->reminder_date)) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{ url('crm-deal-track-approval-list') }}"
                        class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Pending Payment</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Reminder</td>
                        </tr>
                        @if (count($payment_pending) > 0)
                            @foreach ($payment_pending as $top)
                                <tr>
                                    <td class="text-xs"><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}"
                                            title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                                    <td class="text-xs">
                                        <?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                                    <td class="text-xs">{{ $top->ownername->full_name }}</td>
                                    <td class="text-xs">{{ date('d/m/Y h:i:A', strtotime($top->reminder_date)) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>


    </section>
<?php } catch (\Exception $e) { ?>
    {{ $e }}
<?php } ?>
@endsection

@section('script')
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
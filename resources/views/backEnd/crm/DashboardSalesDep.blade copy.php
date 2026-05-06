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
            <h1>@lang('Sales Dept Dashboard')</h1>
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

    <section class="admin-visitor-area ml-3">
        <div class="row">
            {{--  <div class="col-lg-4">
                <div class="white-box leadbox pt-3">
                    <h6>Qualified Leads By Month</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

                    <canvas id="myChart2" style="width:100%;"></canvas>

                    <script>
                        var xValues = ["New", "Qualified", "Unqualified"];
                        var yValues = [{{ $total_leads_new }}, {{ $total_leads_qualified }}, {{ $total_leads_unqualified }}];
                        var barColors = ["#f56954","#00a65a","#d2d6de"];
                        
                        new Chart("myChart2", {
                            type: "pie",
                            data: {
                            labels: xValues,
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
                <div class="white-box leadbox pt-3">
                    <h6>Deals By Month</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />

                    <canvas id="myChart" style="width:100%;"></canvas>

                    <script>
                        var xValues = ["Prospecting", "Quote", "Closure", "Won", "Lost"];
                        var yValues = [{{ $total_deals_prospecting }}, {{ $total_deals_quote }}, {{ $total_deals_closure }}, {{ $total_deals_won }}, {{ $total_deals_lost }}];
                        var barColors = ["#f56954", "#00c0ef","#3c8dbc","#00a65a","#d2d6de"];
                        
                        new Chart("myChart", {
                            type: "doughnut",
                            data: {
                            labels: xValues,
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
            </div>  --}}

            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-approval-list')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Pending Approval</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                            <td>Status</td>
                        </tr>
                    @if(count($pending_approval)>0)
                    @foreach ($pending_approval as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                        <td class="text-xs">{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!} </td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track">{{ $top->deal_name }}</a> - <span class="text-xs">{{ $top->cust_name }}</span> <span class="text-xs"> {{ date('d/m/Y', strtotime($top->created_at)) }}</span>
                    <a href="{{url('crm-deal-track-approval/'.$top->id.'')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a>
                    <hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="white-box leadbox pt-3" style="height: 350px;">
                    <h6>Sales this Month</h6>

                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
                    <hr style="margin: 0px -15px 5px -15px;" />
                            {{--  {{ @App\SysHelper::com_curr_format(($total_revenue_won), 2, '.', ',') }}AED, Revenue<br /><br />
                            {{ @App\SysHelper::com_curr_format(($total_revenue_quote), 2, '.', ',') }}AED, Forcast  --}}                    
                    <canvas id="BarChart" style="width:100%;max-width:600px"></canvas>
<script>
var xValues = ["Forcast", "Revenue"];
var yValues = [{{ $total_revenue_quote }}, {{ $total_revenue_won }}];
var barColors = ["red", "green"];

new Chart("BarChart", {
  type: "bar",
  data: {
    labels: xValues,
    datasets: [{
      backgroundColor: barColors,
      data: yValues
    }]
  },
  options: {
    legend: {display: false},
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
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-approval-list')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Deals Approved</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                            <td>Status</td>
                        </tr>
                    @if(count($approved_list)>0)
                    @foreach ($approved_list as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                        <td class="text-xs">{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!} </td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track">{{ $top->deal_name }}</a> - <span class="text-xs">{{ $top->cust_name }}</span> <span class="text-xs"> {{ date('d/m/Y', strtotime($top->created_at)) }}</span>
                    <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a>  --}}
                    @endforeach
                    @endif
                </table>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-approval-list')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
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
                    {{--  <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track">{{ $top->deal_name }}</a> - <span class="text-xs">{{ $top->cust_name }}</span> <span class="text-xs"> {{ date('d/m/Y', strtotime($top->created_at)) }}</span>
                    <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a>
                    <hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>
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
                    <?php $team_total_sales=0; $team_target=0; ?>
                    @foreach ($sales_target as $top)
                    <div class="progress-group">
                        <span class="progress-text">{{ $top->userid->full_name }}</span>
                        <?php $total_sales = App\SysHelper::get_total_sales_brand($top->user_id,$top->brand);
                        $tp = round($total_sales / $top->target * 100,0);
                        $team_total_sales += $total_sales;
                        $team_target += $top->target;
                        $tpcolor="bg-danger";
                        if($tp<40){$tpcolor="bg-danger";}
                        if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                        if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                        if($tp>100){$tpcolor="bg-purple";}
                        ?>
                        <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $top->target }}AED</span>
                        <div class="progress sm">
                        <div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div>
                        </div>
                    </div>
                    <hr class="mt-2 mb-2"/>
                    @endforeach
                    <?php $ttp = round($team_total_sales / $team_target * 100,0);
                    $ttpcolor="bg-danger";
                    if($ttp<40){$ttpcolor="bg-danger";}
                    if($ttp>=40 && $ttp<80){$ttpcolor="bg-warning";}
                    if($ttp>=80 && $ttp<=100){$ttpcolor="bg-success";}
                    if($ttp>100){$ttpcolor="bg-purple";}
                    ?>
                    <div class="progress-group">
                        <span class="progress-text">Team Target</span>
                        <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($team_total_sales, 2, '.', '') }}AED</b> / {{ $team_target }}AED</span>
                        <div class="progress sm">
                        <div class="progress-bar {{ $ttpcolor }}" style="width:{{ $ttp }}%">{{ $ttp }}%</div>
                        </div>
                    </div>
                    @endif
                    <?php
            $useridarray=[26,48,23,53,22,25,41,45,34];
            if(in_array(Auth::user()->id, $useridarray)) { ?>

                        <?php if(Auth::user()->id==26){ //Naeem
                            $arrID=[46,4,6];
                            $arrBR=['Ubiquiti','Cisco','Aruba'];
                            $arrTO=[3675000,612500,612500];
                            for($i=0; $i < count($arrID); $i++){
                            $total_sales = App\SysHelper::get_total_sales_brand(26,$arrID[$i]);
                            $tp = round($total_sales / $arrTO[$i] * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}?>
                            <span class="progress-text">{{ $arrBR[$i] }}</span>
                            <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $arrTO[$i] }}AED</span>
                            <div class="progress sm"><div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div></div>
                            <hr class="mt-2 mb-2"/>
                        <?php } } ?>

                        <?php if(Auth::user()->id==48){ //Raheem
                            $arrID=[10,39,60];
                            $arrBR=['Allied Telesis','Huawei','Netgear'];
                            $arrTO=[153125,306250,153125];
                            for($i=0; $i < count($arrID); $i++){
                            $total_sales = App\SysHelper::get_total_sales_brand(48,$arrID[$i]);
                            $tp = round($total_sales / $arrTO[$i] * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}?>
                            <span class="progress-text">{{ $arrBR[$i] }}</span>
                            <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $arrTO[$i] }}AED</span>
                            <div class="progress sm"><div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div></div>
                            <hr class="mt-2 mb-2"/>
                        <?php } } ?>

                        <?php if(Auth::user()->id==23 || Auth::user()->id==53 || Auth::user()->id==22){ //Shoji Saher Sarath
                            $arrID=[12,8,3];
                            $arrBR=['Fortinet','Sonicwall','Linksys'];
                            $arrTO=[398125,306250,153125];
                            for($i=0; $i < count($arrID); $i++){
                            $total_sales = App\SysHelper::get_total_sales_brand(23,$arrID[$i]);
                            $tp = round($total_sales / $arrTO[$i] * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}?>
                            <span class="progress-text">{{ $arrBR[$i] }}</span>
                            <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $arrTO[$i] }}AED</span>
                            <div class="progress sm"><div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div></div>
                            <hr class="mt-2 mb-2"/>
                        <?php } } ?>

                        <?php if(Auth::user()->id==25 || Auth::user()->id==41){ //Imran Roveena
                            $arrID=[1];
                            $arrBR=['Avaya'];
                            $arrTO=[735000];
                            for($i=0; $i < count($arrID); $i++){
                            $total_sales = App\SysHelper::get_total_sales_brand(25,$arrID[$i]);
                            $tp = round($total_sales / $arrTO[$i] * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}?>
                            <span class="progress-text">{{ $arrBR[$i] }}</span>
                            <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $arrTO[$i] }}AED</span>
                            <div class="progress sm"><div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div></div>
                            <hr class="mt-2 mb-2"/>
                        <?php } } ?>

                        <?php if(Auth::user()->id==45){ //Suman
                            $arrID=[15,17,59,44,14,16];
                            $arrBR=['Seceon','Apphaz','SISA','Securden','Xcitium','Instasafe'];
                            $arrTO=[257250,55125,55125,73500,147000,73500];
                            for($i=0; $i < count($arrID); $i++){
                            $total_sales = App\SysHelper::get_total_sales_brand(45,$arrID[$i]);
                            $tp = round($total_sales / $arrTO[$i] * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}?>
                            <span class="progress-text">{{ $arrBR[$i] }}</span>
                            <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $arrTO[$i] }}AED</span>
                            <div class="progress sm"><div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div></div>
                            <hr class="mt-2 mb-2"/>
                        <?php } } ?>

                        <?php if(Auth::user()->id==34){ //Stephen
                            $arrID=[15,17,44,14,16];
                            $arrBR=['Seceon','Apphaz','Securden','Xcitium','Instasafe'];
                            $arrTO=[551250,110250,91875,91875,91875];
                            for($i=0; $i < count($arrID); $i++){
                            $total_sales = App\SysHelper::get_total_sales_brand(34,$arrID[$i]);
                            $tp = round($total_sales / $arrTO[$i] * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}?>
                            <span class="progress-text">{{ $arrBR[$i] }}</span>
                            <span class="progress-number float-right"><b>{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', '') }}AED</b> / {{ $arrTO[$i] }}AED</span>
                            <div class="progress sm"><div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div></div>
                            <hr class="mt-2 mb-2"/>
                        <?php } } ?>
                        
            <?php } ?>
                </div>
            </div>


            

        </div>


    </section>

@endsection

@section('script')
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});

$(document).on("click", "#btn_add_company", function () {
    
    $("#btn_add_company").css("display", "none");

    var company_name_add = $("#company_name_add").val();
    var cust_name_add = $("#cust_name_add").val();
    var cust_no_add = $("#cust_no_add").val();
    var cust_email_add = $("#cust_email_add").val();
    var cust_address_add = $("#cust_address_add").val();
    
    var action = "{{ URL::to('crm-leads-addcustomername') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            company_name_add: company_name_add,
            cust_name_add: cust_name_add,
            cust_no_add: cust_no_add,
            cust_email_add: cust_email_add,
            cust_address_add: cust_address_add,
        },
        cache: false,
        success: function(dataResult) {
            //alert(dataResult);
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            if(dataResult['data']=="ERROR")
            {
                alert("Error found in something!!");
                $("#btn_add_company").css("display", "block");
            }
            else if(dataResult['data']=="ERROR2")
            {
                alert("Company Name already exists!!");
                $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                $("#btn_add_company").css("display", "block");
            }
            else{
                if(dataResult['data'] != null){
                len = dataResult['data'].length;
                }
                if(len > 0){
                    
                    //$('#company_name').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].id;
                        var name = dataResult['data'][i].name;
                        var name2 = dataResult['data'][i].code;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#company_name").append(option);
                    }
                    alert('Company Name Added Successfully!!');
                    $('#btn_close2').click();
                    $("#btn_add_company").css("display", "block");
                }
            }
          }
    });
});

$(document).on("change", "#company_name", function () {
    var id = $("#company_name").val();
    get_cust_name(id);
});

function get_cust_name(id) {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('crm-leads-customername') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
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
                    for(var i=0; i<len; i++){
                        $("#cust_name").val(dataResult['data'][i].contcat_person);
                        $("#cust_no").val(dataResult['data'][i].mobile);
                        $("#cust_email").val(dataResult['data'][i].email);
                        $("#address").val(dataResult['data'][i].address);
                    }                        
                }
                else{
                    $("#cust_name").val("");
                    $("#cust_no").val("");
                    $("#cust_email").val("");
                }
                $("#loading_bg").css("display", "none");
        }
    });
}


    </script>
@endsection
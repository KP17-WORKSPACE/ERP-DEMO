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
            <h1>@lang('Technical Dashboard')</h1>
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

<?php try{ ?>

    <section class="admin-visitor-area ml-3">
        <div class="row">
            @if(Auth::user()->id==33)
            <div class="col-lg-4">
                <div class="white-box leadbox pt-3" style="height: 350px;">
                    <h6>Sales Performance</h6>
                    <hr style="margin: 0px -15px 5px -15px;" />

                    <div id="spanMonth" style="padding-top: 60px; text-align: center; font-size: 20px; line-height: 40px;">
                        Revenue : <span id="revenue">{{ $total_revenue }}</span>
                        <br />
                        Forcast : <span id="forcast">{{ $total_forcast }}</span>

                    </div>
                </div>
            </div>
            @endif
            <div class="col-lg-8">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <h6 class="pl-2">Collaboration Requests</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                            <td>Collaboration</td>
                        </tr>                        
                        @foreach ($collaboration as $val)
                        <tr>
                            <td class="text-xs"><a href="{{url('crm-deal-track/'.$val->id.'/view')}}" title="View Deal Track" class="text-xs">{{ $val->id }}</a></td>
                            <td class="text-xs"><?php try {?>{{ $val->customername->name }}<?php } catch (\Exception $e) { }?></td>
                            <td class="text-xs">{{ $val->ownername->full_name }}</td>
                            <td class="text-xs">{{ date('d/m/Y', strtotime($val->created_at)) }}</td>
                            <td class="text-xs">{{ $val->full_name }}</td>                            
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        @if(Auth::user()->id==33)
        <br />
        <div class="row">
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <h6 class="pl-2">Order In Process</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                            <td>Status</td>
                        </tr>
                    @foreach ($order_in_process as $top)
                    
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                        <td class="text-xs">
@if ($top->receivables==1)
Receivables <span class="text-success"><i class="fa fa-check" aria-hidden="true" title="Approved"></i></span>
@elseif ($top->receivables==2)
Receivables <span class="text-danger"><i class="fa fa-times" aria-hidden="true" title="Rejected"></i></span>
@elseif ($top->receivables==3)
Receivables <span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true" title="Pending"></i></span>
@elseif ($top->delivery==1)
Delivery <span class="text-success"><i class="fa fa-check" aria-hidden="true" title="Approved"></i></span>
@elseif ($top->delivery==2)
Delivery <span class="text-danger"><i class="fa fa-times" aria-hidden="true" title="Rejected"></i></span>
@elseif ($top->delivery==3)
Delivery <span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true" title="Pending"></i></span>
@elseif ($top->invoice==1)
Invoice <span class="text-success"><i class="fa fa-check" aria-hidden="true" title="Approved"></i></span>
@elseif ($top->invoice==2)
Invoice <span class="text-danger"><i class="fa fa-times" aria-hidden="true" title="Rejected"></i></span>
@elseif ($top->invoice==3)
Invoice <span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true" title="Pending"></i></span>
@elseif ($top->purchease==1)
Purchease <span class="text-success"><i class="fa fa-check" aria-hidden="true" title="Approved"></i></span>
@elseif ($top->purchease==2)
Purchease <span class="text-danger"><i class="fa fa-times" aria-hidden="true" title="Rejected"></i></span>
@elseif ($top->purchease==3)
Purchease <span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true" title="Pending"></i></span>
@elseif ($top->sales==1)
Sales <span class="text-success"><i class="fa fa-check" aria-hidden="true" title="Approved"></i></span>
@elseif ($top->sales==2)
Sales <span class="text-danger"><i class="fa fa-times" aria-hidden="true" title="Rejected"></i></span>
@elseif ($top->sales==3)
Sales <span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true" title="Pending"></i></span>
@elseif ($top->accounts==1)
Accounts <span class="text-success"><i class="fa fa-check" aria-hidden="true" title="Approved"></i></span>
@elseif ($top->accounts==2)
Accounts <span class="text-danger"><i class="fa fa-times" aria-hidden="true" title="Rejected"></i></span>
@elseif ($top->accounts==3)
Accounts <span class="text-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true" title="Pending"></i></span>
@else
Waiting For Approval
@endif
</td>
                    </tr>
                    @endforeach
                </table>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="white-box leadbox text-xs pt-3"  style="height: 350px; overflow-y: scroll;">
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
                        <span class="progress-number float-right"><b>{{ $total_sales }}AED</b> / {{ $top->target }}AED</span>
                        <div class="progress sm">
                        <div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div>
                        </div>
                    </div>
                    <hr class="mt-2 mb-2"/>
                    @endforeach
                    
                    @if(Auth::user()->id==27 || Auth::user()->id==33 || Auth::user()->id==44)
                    <?php $ttp = round($team_total_sales / $team_target * 100,0);
                    $ttpcolor="bg-danger";
                    if($ttp<40){$ttpcolor="bg-danger";}
                    if($ttp>=40 && $ttp<80){$ttpcolor="bg-warning";}
                    if($ttp>=80 && $ttp<=100){$ttpcolor="bg-success";}
                    if($ttp>100){$ttpcolor="bg-purple";}
                    ?>
                    <div class="progress-group">
                        <span class="progress-text">Team Target</span>
                        <span class="progress-number float-right"><b>{{ $team_total_sales }}AED</b> / {{ $team_target }}AED</span>
                        <div class="progress sm">
                        <div class="progress-bar {{ $ttpcolor }}" style="width:{{ $ttp }}%">{{ $ttp }}%</div>
                        </div>
                    </div>
                    @endif
                    @endif                    
                </div>
            </div>            
        </div>
        @endif

    </section>
<?php 
}catch (\Exception $e) {
    ?> {{ $e }} <?php 
}
?>
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
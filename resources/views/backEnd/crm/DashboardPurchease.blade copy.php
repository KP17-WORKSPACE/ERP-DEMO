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
            <h1>@lang('Purchase Dashboard')</h1>
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
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-list/0')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Pending for Approval</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                        </tr>
                        @if(count($purchease_pending)>0)
                    @foreach ($purchease_pending as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track">{{ $top->deal_name }}</a> <span class="text-xs"> {{ date('d/m/Y', strtotime($top->created_at)) }}</span>
                        <a href="{{url('crm-deal-track-approval/'.$top->id.'')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a><hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-list/3')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Under Purchase</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                        </tr>
                        @if(count($under_purchase)>0)
                    @foreach ($under_purchase as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track">{{ $top->deal_name }}</a>
                    <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a><hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>
                </div>
            </div>
        </div>
        <br />
        <div class="row">
            
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-list/4')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Partial Delivery</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                        </tr>
                        @if(count($partial_delivery)>0)
                    @foreach ($partial_delivery as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track">{{ $top->deal_name }}</a>
                    <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a><hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="white-box leadbox text-sm pt-3 pl-0 pr-0 pb-0" style="height: 350px; overflow-y: scroll;">
                    <a href="{{url('crm-deal-track-list/1')}}" class="btn btn-info btn-xs text-xs text-white float-right p-0 mr-2">&nbspView All&nbsp</a>
                    <h6 class="pl-2">Purchase Completed</h6>
                    <table id="table_custom" class="display school-table text-xs" cellspacing="0" width="100%">
                        <tr>
                            <td>Deal ID</td>
                            <td>Company</td>
                            <td>Owner</td>
                            <td>Date</td>
                        </tr>
                        @if(count($purchase_completed)>0)
                    @foreach ($purchase_completed as $top)
                    <tr>
                        <td class="text-xs"><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-xs">{{ $top->deal_id }}</a></td>
                        <td class="text-xs"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></td>
                        <td class="text-xs">{{ $top->ownername->full_name }}</td>
                        <td class="text-xs">{{ date('d/m/Y', strtotime($top->created_at)) }}</td>
                    </tr>
                    {{--  <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track">{{ $top->deal_name }}</a>
                    <a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}"><i class="fa fa-info-circle text-xs text-info" aria-hidden="true"></i></a><hr class="mt-2 mb-2"/>  --}}
                    @endforeach
                    @endif
                </table>
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
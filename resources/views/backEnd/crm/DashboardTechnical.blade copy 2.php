@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>

    @if (session('logged_session_data.company_id') == 13 && date('Y-m-d') == '2023-10-02')

    @if (session('sess_pops') != 1)
    <script>            
        $(document).ready(function(){
            $("#btn_model").click();
        }); 
    </script>
    @endif
        
    @endif    
    <?php session(['sess_pops' => 1]); ?>

    <!-- Button trigger modal -->
    <a data-toggle="modal" data-target="#exampleModalCenter" id="btn_model"></a>
    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="background: #ffffff95;">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: #4e73de;">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle" style="color: #ffffff;">Hi {{ session('logged_session_data.full_name') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="color: #ffffff; font-size: 18px;">
            Welcome to Syscom IT Solutions LLC!
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>


    <div class="container-fluid mb-4">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Technical Dashboard</h2>
                <span class="page-label">Home - Technical Dashboard</span>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />
            </div>
            <div>
                {{--  <button class="btn-topnav" type="button" class="btn btn-primary" data-toggle="modal" data-target="#adddeal"><i class="fa fa-plus"></i> New Leads</button>
                <button class="btn-topnav"><i class="fa fa-eye"></i> View Leads</button>  --}}
                <a class="btn btn-danger" href="{{url('crm-deal-support-list')}}"><i class=""></i> Support Desk</a>
                <button class="btn-topnav" onclick="window.location.reload();"><i class=""></i> Refresh</button>
            </div>
        </div>
        <div class="row">
            {{--  Mohamed Wafir  --}}
            @if(Auth::user()->id==98)
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-4">
                        <h6 class="card-head ">Sales Performance</h6>
                        <div class="">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control form-card-select" id="filter_date">
                                        <option value="q">Quarterly</option>
                                        <option value="y">Yearly</option>
                                        <option value="m">Monthly</option>
                                        <option value="d">Day</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control form-card-select" id="filter_company">
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

                        Lost : <span class="mb-0 font-weight-bold text-gray-800 font-card-medium" id="lost">{{ $sales[2] }}</span><br /><br />
    
                        <script>
                            function sales_click(id){
                                var mo = $("#filter_date").val();
                                var co = $("#filter_company").val();
                                var url = $("#base_url").val()+"/crm-deal-sales-performance/"+id+"/"+mo+"/"+co;
                                window.location.href = url;
                            }
                        </script>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-2 mb-4">
                <div class="card icon__card__home shadow h-100">
                    <div class="card-header py-4">
                <h6 class="card-head">Brand Sales This Month</h6>
                    </div>
                <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
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
                </div>
            </div>
        </div>


            @endif
            {{--  Jacob George --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-4">
                        <h6 class="card-head ">Gross Profit</h6>
                        <div class="">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control form-card-select" id="filter_date_gp">
                                        <option value="q">Quarterly</option>
                                        <option value="y">Yearly</option>
                                        <option value="m">Monthly</option>
                                        <option value="d">Day</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control form-card-select" id="filter_company_gp">
                                        <option value="13">Syscom IT Solutions LLC</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="text-center">
                        <div class="d-inline-block d-flex justify-content-center ">
                            <div
                                class="text-center mb-2 rounded__box d-flex align-items-center justify-content-center" style="border-color: #4e73df;">
                                <a href="#" onclick="gp_click('service_revenue')">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase">Gross Profit</div>
                                    <div class="mb-0 font-weight-bold text-gray-800 font-card-large" id="service_revenue">{{ $gp[0] }}</div>
                                </a>
                            </div>
                        </div>
    
                        <script>
                            function gp_click(id){
                                var mo = $("#filter_date_gp").val();
                                var co = $("#filter_company_gp").val();
                                var url = $("#base_url").val()+"/crm-deal-gp/"+id+"/"+mo+"/"+co;
                                window.location.href = url;
                            }
                        </script>
                    </div>
                </div>
            </div>
            @if(Auth::user()->id==33)
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-4">
                        <h6 class="card-head ">Service Performance</h6>
                        <div class="">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control form-card-select" id="filter_date_service">
                                        <option value="q">Quarterly</option>
                                        <option value="y">Yearly</option>
                                        <option value="m">Monthly</option>
                                        <option value="d">Day</option>
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
                        <div class="">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control form-card-select" id="filter_date_amc">
                                        <option value="q">Quarterly</option>
                                        <option value="y">Yearly</option>
                                        <option value="m">Monthly</option>
                                        <option value="d">Day</option>
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
                                var url = $("#base_url").val()+"/crm-deal-amc/"+id+"/"+mo+"/"+co;
                                window.location.href = url;
                            }
                        </script>
                    </div>
                </div>
            </div>


            @endif

            <div class="col-lg-8 mt-3">
                <div class="card p-4 max-height">
                    <div>
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
                                $total_sales = App\SysHelper::get_total_sales_brand_3months_profit($top->user_id,0,$top->company_id); //get_total_sales_brand($top->user_id,$top->brand);
                            }
    
                            $tp = round($total_sales / ($top->target*3) * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}
                            ?>
                            <p class="mb-1 font-semibold">{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', ',') }}AED / {{ @App\SysHelper::com_curr_format(($top->target*3), 2, '.', ',') }}AED</p>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6 pb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Service Requests</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Deal/Service Name</th>
                                        <th>Owner</th>
                                        <th>Date</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($new_service)>0)
                                    @foreach ($new_service as $val)
                                    <tr>
                                        <td><a href="{{url('crm-deal-service/'.$val->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $val->id }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>
                                            @if ($val->deal_id==0)
                                            {{ $val->subject }}
                                            @else
                                            {{ $val->deal_name }}
                                            @endif
                                            <?php } catch (\Exception $e) { }?></div></td>
                                        <td>
                                            @if ($val->deal_id==0)
                                            {{ $val->createdby->full_name }}
                                            @else
                                            {{ $val->ownername->full_name }}
                                            @endif
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($val->created_at)) }}</td>
                                        <td><a href="{{url('crm-deal-service/'.$val->id.'/view')}}" title="View Deal Track" class="text-dark">View</a></td>
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
                        <h4 class="header-title m-0">Collaboration Requests</h4>
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
                                        <th>Collaboration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($collaboration)>0)
                                    @foreach ($collaboration as $val)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track/'.$val->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $val->id }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $val->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $val->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($val->created_at)) }}</td>
                                        <td>{{ $val->full_name }}
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
                        <h4 class="header-title m-0">Support Desk</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>Task ID</th>
                                        <th>Site Name</th>
                                        <th>Sales</th>
                                        <th>Date</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($new_support)>0)
                                    @foreach ($new_support as $val)
                                    <tr>
                                        <td><a href="{{url('crm-deal-support/'.$val->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $val->id }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{ $val->site_name }}</div></td>
                                        <td>
                                            {{ $val->salesperson->full_name }}
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($val->support_date)) }}</td>
                                        <td><a href="{{url('crm-deal-support/'.$val->id.'/view')}}" title="View Task" class="text-dark">View</a></td>
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
                        <h4 class="header-title m-0">Service On Process</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Deal/Service Name</th>
                                        <th>Owner</th>
                                        <th>Updated On</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($new_service_pending)>0)
                                    @foreach ($new_service_pending as $val)
                                    <tr>
                                        <td><a href="{{url('crm-deal-service/'.$val->id.'/view')}}" title="View Deal Track" class="text-dark">{{ $val->deal_id }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>
                                            @if ($val->deal_id==0)
                                            {{ $val->subject }}
                                            @else
                                            {{ $val->deal_name }}
                                            @endif
                                            <?php } catch (\Exception $e) { }?></div></td>
                                        <td>
                                            @if ($val->deal_id==0)
                                            {{ $val->createdby->full_name }}
                                            @else
                                            {{ $val->ownername->full_name }}
                                            @endif
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($val->created_at)) }}</td>
                                        <td><a href="{{url('crm-deal-service/'.$val->id.'/view')}}" title="View Deal Track" class="text-dark">View</a></td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
    
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>

            @if(Auth::user()->id==98)
            <div class="col-md-6 pb-4">
                <div class="card p-4 max-height">
                    <div>
                        <h2 class="page-heading mb-3">Sales Target</h2>
                        <hr>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-1">Mohamed Wafir</p>
                            <?php

                            $total_sales = App\SysHelper::get_total_sales_brand_name(1); //get_total_sales_brand($top->user_id,$top->brand);

                            $tp = round($total_sales / 500000 * 100,0);
                            $tpcolor="bg-danger";
                            if($tp<40){$tpcolor="bg-danger";}
                            if($tp>=40 && $tp<80){$tpcolor="bg-warning";}
                            if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                            if($tp>100){$tpcolor="bg-purple";}
                            ?>
                            <p class="mb-1 font-semibold">{{ @App\SysHelper::com_curr_format($total_sales, 2, '.', ',') }}AED / {{ @App\SysHelper::com_curr_format(500000, 2, '.', ',') }}AED</p>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar {{ $tpcolor }}" style="width:{{ $tp }}%">{{ $tp }}%</div>
                        </div>
                    </div>

                </div>
            </div>
            @endif
            
            @if(Auth::user()->id==33)
            <div class="col-md-6 pb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">AMC Expiring This Month</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>Deal</th>
                                        <th>Company</th>
                                        <th>Owner</th>
                                        <th>Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($amc_exp_m)>0)
                                    @foreach ($amc_exp_m as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deals/'.$top->id.'/view')}}" title="View Deal" class="text-dark">{{ $top->id }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->to_date)) }}</td>
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
                        <h4 class="header-title m-0">AMC Summary</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>Deal</th>
                                        <th>Company</th>
                                        <th>Owner</th>
                                        <th>Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($amc_exp)>0)
                                    @foreach ($amc_exp as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deals/'.$top->id.'/view')}}" title="View Deal" class="text-dark">{{ $top->id }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>@if (@$top->to_date != null) {{ date('d/m/Y', strtotime($top->to_date)) }} @endif</td>
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
            @endif
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
                            $("#lost").html(dataResult['data'][0]);
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
</script>

@endsection
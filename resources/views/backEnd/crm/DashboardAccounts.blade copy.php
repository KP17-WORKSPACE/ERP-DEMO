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
            <div class="col-md-6 mb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Deal Approval Pending</h4>
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
                                    @if(count($pending_deals)>0)
                                    @foreach ($pending_deals as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
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

            <div class="col-md-6 mb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">New Payment Pending</h4>
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
                                        <td><a href="{{url('crm-deal-track/'.$top->deal_id.'/view')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
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

            <div class="col-md-6 mb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Payment Reminder</h4>
                        <a href="{{url('crm-deal-track-list/paymentreminder')}}" class=" btn-small">View All</a>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <td>Deal</td>
                                        <td>Company</td>
                                        <td>Owner</td>
                                        <td>Reminder</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($payment_reminder)>0)
                                    @foreach ($payment_reminder as $top)
                                    <tr @if(date('d/m/Y', strtotime($top->reminder_date))==date('d/m/Y')) class="text-danger" @endif >
                                        <td><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
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
            <div class="col-md-6 mb-4">
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
                                        <td><a href="{{ url('crm-deal-track-approval/' . $top->id . '') }}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
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
            <div class="col-lg-6 mb-4" style="display: none;">
                <div class="card p-4 max-height">
                    <div>
                        <h2 class="page-heading mb-3">Target this Month</h2>
                        <hr>
                    </div>
                    @if(count($sales_target)>0)
                    @foreach ($sales_target as $top)
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-1">{{ $top->userid->full_name }}</p>
                            <?php $total_sales = App\SysHelper::get_total_sales_brand($top->user_id,$top->brand,$top->company_id);
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
            <div class="col-lg-6 mb-4" style="display: none;">
                <div class="card p-4 max-height">
                    <div>
                        <h2 class="page-heading mb-3">Target this Quarter</h2>
                        <hr>
                    </div>
                    @if(count($sales_target)>0)
                    @foreach ($sales_target as $top)
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-1">{{ $top->userid->full_name }}</p>
                            <?php $total_sales = App\SysHelper::get_total_sales_brand_3months($top->user_id,$top->brand);
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

    </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    
@endsection
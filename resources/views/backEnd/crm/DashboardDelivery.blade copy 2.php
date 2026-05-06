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
                        <h4 class="header-title m-0">Do Pending</h4>
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
                                    @if(count($do_pending)>0)
                                    @foreach ($do_pending as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td> <span class="btn-badge warning py-1 px-2">New</span></td>
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
                        <h4 class="header-title m-0">Pending For Delivery</h4>
                        {{--  <a href="{{url('crm-deal-track-list/doonprocess')}}" class=" btn-small">View All</a>  --}}
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
                                    @if(count($pending_for_delivery)>0)
                                    @foreach ($pending_for_delivery as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td> 
                                            @if($top->delivery==3)
                                            <span class="btn-badge success py-1 px-2">Out For Delivery</span>
                                            @endif                    
                                            @if($top->delivery==4)
                                            <span class="btn-badge warning py-1 px-2">Pending For Delivery</span>
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

            <div class="col-md-6 mb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Out For Delivery</h4>
                        {{--  <a href="{{url('crm-deal-track-list/doonprocess')}}" class=" btn-small">View All</a>  --}}
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
                                    @if(count($out_for_delivery)>0)
                                    @foreach ($out_for_delivery as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td> 
                                            @if($top->delivery==3)
                                            <span class="btn-badge success py-1 px-2">Out For Delivery</span>
                                            @endif                    
                                            @if($top->delivery==4)
                                            <span class="btn-badge warning py-1 px-2">Pending For Delivery</span>
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

            <div class="col-md-6 mb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Ready For Delivery</h4>
                        {{--  <a href="{{url('crm-deal-track-list/doonprocess')}}" class=" btn-small">View All</a>  --}}
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
                                    @if(count($ready_for_delivery)>0)
                                    @foreach ($ready_for_delivery as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td> 
                                            @if($top->delivery==3)
                                            <span class="btn-badge success py-1 px-2">Out For Delivery</span>
                                            @endif                    
                                            @if($top->delivery==4)
                                            <span class="btn-badge warning py-1 px-2">Pending For Delivery</span>
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
            <div class="col-md-6 mb-4" style="display: none;">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">GRN Pending for Approval</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>Deal</th>
                                        <th>Supplier Name</th>
                                        <th>LPO No</th>
                                        <th>Customer</th>
                                        <th>Owner</th>
                                        <th>Delivery Date</th>
                                        <th>GRN NO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($grn_pending)>0)
                                    @foreach ($grn_pending as $top)
                                    <tr>
                                        <td>{{ $top->deal_id }}</td>
                                        <td>{{ $top->supplier_name }}</td>
                                        <td>{{ $top->lpo_no }}</td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->delivery_date)) }}</td>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->trackid.'')}}" title="View Deal Track" class="btn btn-success p-0 pr-1 pl-1">Update</a></td>
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
                        <h4 class="header-title m-0">Partial Delivery</h4>
                        <a href="{{url('crm-deal-track-list/partialdelivery')}}" class=" btn-small">View All</a>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>Deal</th>
                                        <th>Company</th>
                                        <th>Remarks</th>
                                        <th>Owner</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($partial_delivery)>0)
                                    @foreach ($partial_delivery as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->remarks }}</td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
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
@endsection
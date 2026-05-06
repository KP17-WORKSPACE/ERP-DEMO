@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="container-fluid mb-4">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Sales Dashboard</h2>
                <span class="page-label">Home - Sales Dashboard</span>
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
            <div class="col-md-6 pb-4">
                <div class="card shadow p-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Order In Process</h4>
                        <a href="{{url('crm-deal-track-approval-list')}}" class=" btn-small">View All</a>
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
        </div>
    </div>

    
@endsection
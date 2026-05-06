@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
<?php try { ?>

    <style>
        @media screen and (max-width: 480px) {
            .mobhd {
              display: none;
            }
          }
    </style>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Deals Return List</h2>
            <span class="page-label">Home - Deals Return List</span>
        </div>
        <div>
            {{--  <a href="{{ url('crm-deals') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Deal</a>
            
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>

            
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>  --}}
            
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" onclick="sort(1)">Today</a>
                <a class="dropdown-item" href="#" onclick="sort(2)">This Week</a>
                <a class="dropdown-item" href="#" onclick="sort(3)">Last Week</a>
                <a class="dropdown-item" href="#" onclick="sort(4)">This Month</a>
                <a class="dropdown-item" href="#" onclick="sort(5)">Last Month</a>
                <a class="dropdown-item" href="#" onclick="sort(6)">Last 6 Month</a>
                <a class="dropdown-item" href="#" onclick="sort(7)">This Year</a>
                <a class="dropdown-item" href="#" onclick="sort(8)">Last Year</a>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/show', 'method' => 'POST', 'id' => 'crm-deals-search']) }}
                    <input type="hidden" name="sort_id" id="sort_id" value="1" />
                    <button type="submit" id="btn_sort" style="display: none;"></button>
                {{ Form::close() }}
            </div>
            <script>
                function sort(id) {
                    $("#sort_id").val(id);
                    $("#btn_sort").click();
                }
            </script>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-list', 'method' => 'POST', 'id' => 'crm-deals-search']) }}
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id }}">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">Company Name</label>
                    <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                        <option value="">-Select-</option>
                        @foreach ($vendors as $value)
                        <option value="{{ @$value->id }}" @if($ctrl_company_id ==$value->id) selected @endif>{{ @$value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">Status</label>
                    <select class="form-control js-example-basic-single" name="status_id" id="status_id">
                        <option value="10" @if($ctrl_status_id == "10") selected @endif>-Select-</option>

@if(session('logged_session_data.designation_id')==8 || Auth::user()->role_id==1)
//Account Status
<option value="A1">Accounts Approved</option>
<option value="A2">Accounts Rejected</option>
<option value="A3">Accounts Pending</option>
@endif

@if(session('logged_session_data.designation_id')==27 || Auth::user()->role_id==1)
//Sales Status
<option value="S1">Sales Approved</option>
<option value="S2">Sales Rejected</option>
<option value="S3">Sales Pending</option>
@endif

@if(session('logged_session_data.designation_id')==20 || Auth::user()->role_id==1)
//Purchase Status
<option value="P1">Purchase Approved</option>
<option value="P2">Purchase Rejected</option>
<option value="P3">Purchase Pending</option>
<option value="P4">Purchase Partial Delivery</option>
@endif

@if(session('logged_session_data.designation_id')==35 || Auth::user()->role_id==1)
//Invoice Status
<option value="I1">Invoice Approved</option>
<option value="I2">Invoice Rejected</option>
<option value="I3">Invoice Pending</option>
@endif

@if(session('logged_session_data.designation_id')==34 || Auth::user()->role_id==1)
//Delivery Status
<option value="D1">Delivery Completed</option>
<option value="D2">Delivery Rejected</option>
<option value="D3">Out For Delivery</option>
<option value="D4">Pending For Delivery</option>
<option value="D5">Ready For Delivery</option>
@endif

@if(session('logged_session_data.designation_id')==2 || Auth::user()->role_id==1)
//Receivables Status
<option value="R1">Payment Received</option>
<option value="R2">Receivables Rejected</option>
<option value="R3">Payment Pending</option>
<option value="R4">Order Cancelled</option>
@endif
    
                    </select>
                </div>
                
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">Date</label>
                    <input class="form-control" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}">
                </div>
                @if(Auth::user()->role_id == 1)
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">Salesman</label>
                    <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                        <option value="">-Select-</option>
                        @foreach ($staff as $value)
                        <option value="{{ @$value->user_id }}" @if($ctrl_owner_id ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                </div>
            </div>
        {{ Form::close() }}
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                        <tr>
                            <td colspan="7">
                                @if(session()->has('message-success'))
                                <div class="alert alert-success">
                                    {{ session()->get('message-success') }}
                                </div>
                                @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message-danger') }}
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        
                        <tr>
                            <th>@lang('Deal')</th>
                            @if(session('logged_session_data.designation_id')==35)
                                <th class="mobhd">@lang('Invoice No')</th>
                            @else
                                <th class="mobhd">@lang('Deal Name')</th>
                            @endif
                            <th class="mobhd">@lang('Return Date')</th>
                            <th>@lang('Status')</th>
                            <th></th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $count =1; @endphp
                        @foreach($dealtrack as $value)
                        <tr>
                            <td>{{@$value->deal_id}}</td>
                            <td class="mobhd">
                                <div style="width:170px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    @if(session('logged_session_data.designation_id')==35)
                                    {{ $value->invoice_no }}
                                    @if($value->invoice_no =="") {{@$value->dealid->deal_name}} @endif
                                    @else
                                    {{@$value->dealid->deal_name}}
                                    @endif</div>
                            </td>
                            <td class="mobhd">@if($value->ret_date != '1970-01-01') {{date('d-M-Y', strtotime(@$value->created_at))}} @endif</td>
                            <td>

@if($value->collection==1)
<span class="success btn-badge py-1 px-2">Collection Approved</span>
@elseif($value->collection==2)
<span class="danger btn-badge py-1 px-2">Collection Rejected</span>
@else
<span class="dark btn-badge py-1 px-2">Collection Pending</span>
@endif

@if($value->return==1)
<span class="success btn-badge py-1 px-2">Sales Approved</span>
@elseif($value->return==2)
<span class="danger btn-badge py-1 px-2">Sales Rejected</span>
@else
<span class="dark btn-badge py-1 px-2">Sales Pending</span>
@endif

@if($value->payable==1)
<span class="success btn-badge py-1 px-2">Payable Approved</span>
@elseif($value->payable==2)
<span class="danger btn-badge py-1 px-2">Payable Rejected</span>
@else
<span class="dark btn-badge py-1 px-2">Payable Pending</span>
@endif
                            </td>
                            
                            <td class="text-right">
                                <a class="btn-sm btn-info" href="{{url('crm-deal-return/'.$value->id.'/view')}}"><i class="fa fa-eye mobhd" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                  
                @endforeach
    
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
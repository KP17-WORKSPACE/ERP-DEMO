@extends('backEnd.newmasterpage')
@section('mainContent')

<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
<?php try { ?>

    
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Deals Approval List
                </h4>
                <div class="purchase-order-content-header-right">
                    {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-list', 'method' => 'get', 'id' => 'crm-deals-search']) }}
            <div class="row">
                <div class="col-md-1 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Company Name</label>
                    <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                        <option value="">-Select-</option>
                        @foreach ($vendors as $value)
                        <option value="{{ @$value->id }}" @if($ctrl_company_id ==$value->id) selected @endif>{{ @$value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Status</label>
                    <select class="form-control js-example-basic-single" name="status_id" id="status_id">
                        <option value="10" @if($ctrl_status_id == "10") selected @endif>-Select-</option>

@if(session('logged_session_data.designation_id')==8 || Auth::user()->role_id==1 || Auth::user()->id==56)
//Account Status
<option value="A1">Accounts Approved</option>
<option value="A2">Accounts Rejected</option>
<option value="A3">Accounts Pending</option>
@endif

@if(session('logged_session_data.designation_id')==27 || Auth::user()->role_id==1 || Auth::user()->id==56)
//Sales Status
<option value="S1">Sales Approved</option>
<option value="S2">Sales Rejected</option>
<option value="S3">Sales Pending</option>
@endif

@if(session('logged_session_data.designation_id')==20 || Auth::user()->role_id==1 || Auth::user()->id==56)
//Purchase Status
<option value="P1">Purchase Approved</option>
<option value="P2">Purchase Rejected</option>
<option value="P3">Purchase Pending</option>
<option value="P4">Purchase Partial Delivery</option>
@endif

@if(session('logged_session_data.designation_id')==35 || Auth::user()->role_id==1 || Auth::user()->id==56 || Auth::user()->id==49 || Auth::user()->id==51)
//Invoice Status
<option value="I1">Invoice Approved</option>
<option value="I2">Invoice Rejected</option>
<option value="I3">Invoice Pending</option>
@endif

@if(session('logged_session_data.designation_id')==34 || Auth::user()->role_id==1 || Auth::user()->id==56 || Auth::user()->id==49 || Auth::user()->id==51)
//Delivery Status
<option value="D1">Delivery Completed</option>
<option value="D2">Delivery Rejected</option>
<option value="D3">Out For Delivery</option>
<option value="D4">Pending For Delivery</option>
<option value="D5">Ready For Delivery</option>
@endif

@if(session('logged_session_data.designation_id')==2 || Auth::user()->role_id==1 || Auth::user()->id==49 || Auth::user()->id==51)
//Receivables Status
<option value="R1">Payment Received</option>
<option value="R2">Receivables Rejected</option>
<option value="R3">Payment Pending</option>
<option value="R4">Order Cancelled</option>
@endif
    
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Date</label>
                    <input class="form-control" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}">
                </div>
                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 27 || Auth::user()->role_id == 2)
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Salesman</label>
                    <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                        <option value="">-Select-</option>
                        @foreach ($staff as $value)
                        <option value="{{ @$value->user_id }}" @if($ctrl_owner_id ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Partial Delivery</label>
                    <select class="form-control js-example-basic-single" name="partial_delivery" id="partial_delivery">
                        <option value="">-Select-</option>
                        <option value="1">Partial Delivery</option>
                    </select>
                </div>
                <div class="col-1"><br/>
						<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
                                    <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Search
						</button>
                </div>
            </div>
        {{ Form::close() }}
                </div>
            </div>
            

            
            <div class="card mb-3">
                <div class="card-body">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
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
                            <th style="width: 50px;">@lang('Deal')</th>
                            @if(session('logged_session_data.company_id') == 1)
                            <th style="width: 200px;">@lang('Company')</th>
                            @endif
                            <th class="mobhd">@lang('Deal Name')</th>
                            <th class="mobhd">@lang('Customer')</th>
                            <th class="mobhd">@lang('Salesman')</th>
                            <th class="mobhd">@lang('Delivery_Date')</th>
                            <th class="mobhd">@lang('Payment_Terms')</th>
                            <th>@lang('Status')</th>
                            <th class="text-right">@lang('Value')</th>
                            <th></th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $count =1; @endphp
                        @foreach($dealtrack as $value)
                        <tr>
                            <td><a href="{{url('crm-deal-track-approval/'.$value->id)}}">{{@$value->deal_code->code}}</a></td>
                            @if(session('logged_session_data.company_id') == 1)
                            <td>{{ $value->companyname->company_name }}</td>
                            @endif
                            <td class="mobhd">
                                <div style="width:170px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    {{@$value->dealid->deal_name}}
                                </div>
                            </td>
                            <td class="mobhd"><div style="width:170px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></td>
                            <td class="mobhd">{{@$value->ownername->full_name}}</td>
                            <td class="mobhd">@if(date('d/m/Y', strtotime(@$value->delivery_date)) != '01/01/1970') {{date('d/m/Y', strtotime(@$value->delivery_date))}} @endif</td>
                            <td class="mobhd"><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->paymentterms->title}}</div></td>
                            <td>
                                @if(Auth::user()->role_id==1 || Auth::user()->role_id==2 || Auth::user()->id==21)

@if($value->receivables==1)
<span class="text-success py-1 px-2">Payment Received</span>
@elseif($value->receivables==2)
<span class="text-danger py-1 px-2">Rejected</span>
@elseif($value->receivables==3)
<span class="text-primary py-1 px-2">Payment Pending</span>
@elseif($value->receivables==4)
<span class="text-dark py-1 px-2">Order Cancelled</span>

@elseif($value->delivery==1)
<span class="text-success py-1 px-2">Delivery Completed</span>
@elseif($value->delivery==2)
<span class="text-danger py-1 px-2">Delivery Rejected</span>
@elseif($value->delivery==3)
<span class="text-primary py-1 px-2">Out For Delivery</span>
@elseif($value->delivery==4)
<span class="text-primary py-1 px-2">Pending For Delivery</span>
@elseif($value->delivery==5)
<span class="text-primary py-1 px-2">Ready For Delivery</span>

@elseif($value->invoice==1)
<span class="text-success py-1 px-2">Invoice Approved</span>
@elseif($value->invoice==2)
<span class="text-danger py-1 px-2">Invoice Disapproved</span>
@elseif($value->invoice==3)
<span class="text-primary py-1 px-2">Invoice Pending</span>

@elseif($value->purchease==1)
<span class="text-success py-1 px-2">Purchase Approved</span>
@elseif($value->purchease==2)
<span class="text-danger py-1 px-2">Purchase Disapproved</span>
@elseif($value->purchease==3)
<span class="text-primary py-1 px-2">Purchase Pending</span>
@elseif($value->purchease==4)
<span class="text-primary py-1 px-2">Partial Delivery</span>

@elseif($value->sales==1)
<span class="text-success py-1 px-2">Sales Approved</span>
@elseif($value->sales==2)
<span class="text-danger py-1 px-2">Sales Disapproved</span>
@elseif($value->sales==3)
<span class="text-primary py-1 px-2">Sales Pending</span>

@elseif($value->accounts==1)
<span class="text-success py-1 px-2">Accounts Approved</span>
@elseif($value->accounts==2)
<span class="text-danger py-1 px-2">Accounts Disapproved</span>
@elseif($value->accounts==3)
<span class="text-primary py-1 px-2">Accounts Pending</span>

@else
<span class="text-warning py-1 px-2">New</span>
@endif

                                        {{--  accounts  --}}
                                        @elseif(App\SysHelper::account_approval_access())
                                            @if($value->accounts==1)
                                            <span class="success btn-badge py-1 px-2">Approved</span>
                                            @elseif($value->accounts==2)
                                            <span class="danger btn-badge py-1 px-2">Disapproved</span>
                                            @elseif($value->accounts==3)
                                            <span class="primary btn-badge py-1 px-2">Pending</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">New</span>
                                            @endif
                                        {{--  sales  --}}
                                        @elseif(App\SysHelper::sales_approval_access())
                                            @if($value->sales==1)
                                            <span class="success btn-badge py-1 px-2">Approved</span>
                                            @elseif($value->sales==2)
                                            <span class="danger btn-badge py-1 px-2">Disapproved</span>
                                            @elseif($value->sales==3)
                                            <span class="primary btn-badge py-1 px-2">Pending</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">New</span>
                                            @endif
                                        {{--  purchease  --}}
                                        @elseif(App\SysHelper::purchase_approval_access())
                                            @if($value->purchease==1)
                                            <span class="success btn-badge py-1 px-2">Approved</span>
                                            @elseif($value->purchease==2)
                                            <span class="danger btn-badge py-1 px-2">Disapproved</span>
                                            @elseif($value->purchease==3)
                                            <span class="primary btn-badge py-1 px-2">Pending</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">New</span>
                                            @endif
                                        {{--  invoice  --}}
                                        @elseif(App\SysHelper::invoice_approval_access())
                                            @if($value->invoice=="1")
                                            <span class="success btn-badge py-1 px-2">Approved</span>
                                            @elseif($value->invoice=="2")
                                            <span class="danger btn-badge py-1 px-2">Disapproved</span>
                                            @elseif($value->invoice=="3")
                                            <span class="primary btn-badge py-1 px-2">Pending</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">New</span>
                                            @endif
                                        {{--  delivery  --}}
                                        @elseif(App\SysHelper::delivery_approval_access())
                                            @if($value->delivery==1)
                                            <span class="success btn-badge py-1 px-2">Delivery Completed</span>
                                            @elseif($value->delivery==2)
                                            <span class="danger btn-badge py-1 px-2">Rejected</span>
                                            @elseif($value->delivery==3)
                                            <span class="primary btn-badge py-1 px-2">Out For Delivery</span>
                                            @elseif($value->delivery==4)
                                            <span class="primary btn-badge py-1 px-2">Pending For Delivery</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">New</span>
                                            @endif
                                        {{--  receivables  --}}
                                        @elseif(App\SysHelper::receivables_approval_access())
                                            @if($value->receivables==1)
                                            <span class="success btn-badge py-1 px-2">Payment Received</span>
                                            @elseif($value->receivables==2)
                                            <span class="danger btn-badge py-1 px-2">Rejected</span>
                                            @elseif($value->receivables==3)
                                            <span class="primary btn-badge py-1 px-2">Payment Pending</span>
                                            @elseif($value->receivables==4)
                                            <span class="dark btn-badge py-1 px-2">Order Cancelled</span>
                                            @else
                                            <span class="warning btn-badge py-1 px-2">New</span>
                                            @endif
                                        @endif
                            </td>
                            
                            
                            <td class="text-right">
                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }}
                            </td>
                            <td class="text-right">
                                <a class="btn-sm btn-success text-white" href="{{url('crm-deal-track-approval/'.$value->id)}}">View</a>
                            </td>
                        </tr>
                  
                @endforeach
    
                    </tbody>
                    <footer>
                        <tr>
                            <td colspan="9">
                            </td>
                        </tr>
                    </footer>
                </table>
                </div>
            </div>


        </div>
    </div>
</div>


    <style>
        @media screen and (max-width: 480px) {
            .mobhd {
              display: none;
            }
          }
    </style>

<div class="container-fluid" style="display: none;">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Deals Approval List</h2>
            <span class="page-label">Home - Deals Approval List</span>
        </div>
        <div>            
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>

            {{--  <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filter By {{ $filter_by }}
            </button>  --}}
            
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
            
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" onclick="sort(1)">Today</a>
                <a class="dropdown-item" href="#" onclick="sort(2)">This Week</a>
                <a class="dropdown-item" href="#" onclick="sort(3)">Last Week</a>
                <a class="dropdown-item" href="#" onclick="sort(4)">This Month</a>
                <a class="dropdown-item" href="#" onclick="sort(5)">Last Month</a>
                <a class="dropdown-item" href="#" onclick="sort(6)">Last 6 Month</a>
                <a class="dropdown-item" href="#" onclick="sort(7)">This Year</a>
                <a class="dropdown-item" href="#" onclick="sort(8)">Last Year</a>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/show', 'method' => 'get', 'id' => 'crm-deals-search']) }}
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
            
        
        </div>
    </div>



    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                
                

                
            </div>
        </div>
    </div>

    
{{-- <script>
    $('#dataTable').dataTable({
        aLengthMenu: [
            [10, 25, 50, 200,"All"]
        ],
      });
</script> --}}

</div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
@extends('backEnd.master')
@section('mainContent')

<?php  
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = $generalSetting->currency_symbol;

    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
?>

<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Deals Approval List')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-deal-track-approval-list') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>

<section class="admin-visitor-area ml-2 mr-2">
    <div class="container-fluid p-0">       

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-list', 'method' => 'POST', 'id' => 'crm-deals-search']) }}
        <div class="row white-box leadbox ml-0 mr-0 mb-2">
            
            <div class="col-lg-2">
                Deal ID
                <input class="primary-input dynamicstxt_s w-100" id="deal_id" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id }}">
            </div>
            <div class="col-lg-2">
                Company Name
                <select class="w-100 dynamicstxt_s bb w-100 form-control niceSelect" name="company_id" id="company_id">
                    <option value="">-Select-</option>
                    @foreach ($vendors as $value)
                    <option value="{{ @$value->id }}" @if($ctrl_company_id ==$value->id) selected @endif>{{ @$value->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-lg-2">
                Status
                <select class="w-100 dynamicstxt_s bb w-100 form-control niceSelect" name="status_id" id="status_id">
                    <option value="10" @if($ctrl_status_id == "10") selected @endif>-Select-</option>
                    @if(session('logged_session_data.designation_id')==34)
                    <option value="0" @if($ctrl_status_id == 0) selected @endif>New</option>
                    <option value="1" @if($ctrl_status_id == 1) selected @endif>Delivery Completed</option>
                    <option value="2" @if($ctrl_status_id == 2) selected @endif>Rejected</option>
                    <option value="3" @if($ctrl_status_id == 3) selected @endif>Out For Delivery</option>
                    <option value="4" @if($ctrl_status_id == 4) selected @endif>Pending For Delivery</option>
                    @elseif(session('logged_session_data.designation_id')==2)
                    <option value="0" @if($ctrl_status_id == 0) selected @endif>New</option>
                    <option value="1" @if($ctrl_status_id == 1) selected @endif>Payment Received</option>
                    <option value="2" @if($ctrl_status_id == 2) selected @endif>Rejected</option>
                    <option value="3" @if($ctrl_status_id == 3) selected @endif>Payment Pending</option>
                    @else
                    <option value="0" @if($ctrl_status_id == 0) selected @endif>New</option>
                    <option value="1" @if($ctrl_status_id == 1) selected @endif>Approved</option>
                    <option value="2" @if($ctrl_status_id == 2) selected @endif>Rejected</option>
                    <option value="3" @if($ctrl_status_id == 3) selected @endif>Pending</option>
                    @endif

                </select>
            </div>
            <div class="col-lg-2">
                Date
                <input class="primary-input dynamicstxt_s w-100 date" id="date" type="text" autocomplete="off" name="date" value="{{ $ctrl_date }}" readonly>
            </div>
            @if(Auth::user()->role_id == 1)
            <div class="col-lg-2">
                Salesman
                <select class="w-100 dynamicstxt_s bb w-100 form-control niceSelect" name="owner_id" id="owner_id">
                    <option value="">-Select-</option>
                    @foreach ($staff as $value)
                    <option value="{{ @$value->user_id }}" @if($ctrl_owner_id ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-lg-2"><br />
                <button type="submit" class="btn btn-sm btn-dark pt-1 pb-1 pl-3 pr-3" id="btnSubmit">Filter</button>
            </div>
        </div>
        {{ Form::close() }}


                <div class="row">
                    <div class="col-lg-12">
                        
                        <table id="table_custom" class="display school-table" cellspacing="0" width="100%">

                            <thead>
                               @if(session()->has('message-success') != "" ||
                                session()->get('message-danger') != "")
                                <tr>
                                    <td colspan="11">
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
                                    <th style="width: 70px;">@lang('Deal ID')</th>
                                    @if(session('logged_session_data.designation_id')==35)
                                    <th>@lang('Invoice No')</th>
                                    @else
                                    <th style="width: 200px;">@lang('Deal Name')</th>
                                    @endif
                                    <th style="width: 200px;">@lang('Customer Name')</th>
                                    <th style="width: 150px;">@lang('Salesman')</th>
                                    <th style="width: 150px;">@lang('Delivery Date')</th>
                                    <th style="width: 150px;">@lang('Payment Terms')</th>
                                    {{--  <th style="width: 100px;">@lang('Documents')</th>  --}}
                                    <th style="width: 250px;">@lang('Status')</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count =1; @endphp
                                @foreach($dealtrack as $value)
                                <tr style="line-height: 35px;">
                                    <td><a class="btn-info text-white btn-xs">{{@$value->deal_id}}</a></td>
                                    <td><a class="text-dark"><div style="width:200px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        @if(session('logged_session_data.designation_id')==35)
                                        {{ $value->invoice_no }}
                                        @if($value->invoice_no =="") {{@$value->dealid->deal_name}} @endif
                                        @else
                                        {{@$value->dealid->deal_name}}
                                        @endif</div>
                                    </a></td>
                                    <td><a class="text-dark"><div style="width:200px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></a></td>
                                    <td><a class="text-dark">{{@$value->ownername->full_name}}</a></td>
                                    <td>@if($value->delivery_date != '1970-01-01') {{date('d-M-Y', strtotime(@$value->delivery_date))}} @endif</td>
                                    <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->paymentterms->title}}</div></td>
                                    {{--  <td>
                                        @if($value->lpo != "")<a class="btn-primary btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $value->lpo }}" title="LPO Download" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a> @endif
                                        @if($value->cheque_copy != "")<a class="btn-primary btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $value->cheque_copy }}" title="Cheque Copy Download" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>@endif
                                        @if($value->purchease_quote != "")<a class="btn-primary btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $value->purchease_quote }}" title="Purchase Quote Download" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>  @endif
                                    </td>  --}}
                                    <td>
                                        @if(Auth::user()->role_id==1 || Auth::user()->id==56)

@if($value->receivables==1)
<span class="btn-xs btn-success text-white pl-2 pr-2">Payment Received</span>
@elseif($value->receivables==2)
<span class="btn-xs btn-danger text-white pl-2 pr-2">Rejected</span>
@elseif($value->receivables==3)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Payment Pending</span>
@elseif($value->receivables==4)
<span class="btn-xs btn-dark text-white pl-2 pr-2">Order Cancelled</span>
@elseif($value->delivery==1)
<span class="btn-xs btn-success text-white pl-2 pr-2">Delivery Completed</span>
@elseif($value->delivery==2)
<span class="btn-xs btn-danger text-white pl-2 pr-2">Delivery Rejected</span>
@elseif($value->delivery==3)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Out For Delivery</span>
@elseif($value->delivery==4)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Pending For Delivery</span>
@elseif($value->invoice==1)
<span class="btn-xs btn-success text-white pl-2 pr-2">Invoice Approved</span>
@elseif($value->invoice==2)
<span class="btn-xs btn-danger text-white pl-2 pr-2">Invoice Disapproved</span>
@elseif($value->invoice==3)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Invoice Pending</span>
@elseif($value->purchease==1)
<span class="btn-xs btn-success text-white pl-2 pr-2">Purchase Approved</span>
@elseif($value->purchease==2)
<span class="btn-xs btn-danger text-white pl-2 pr-2">Purchase Disapproved</span>
@elseif($value->purchease==3)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Purchase Pending</span>
@elseif($value->sales==1)
<span class="btn-xs btn-success text-white pl-2 pr-2">Sales Approved</span>
@elseif($value->sales==2)
<span class="btn-xs btn-danger text-white pl-2 pr-2">Sales Disapproved</span>
@elseif($value->sales==3)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Sales Pending</span>
@elseif($value->accounts==1)
<span class="btn-xs btn-success text-white pl-2 pr-2">Accounts Approved</span>
@elseif($value->accounts==2)
<span class="btn-xs btn-danger text-white pl-2 pr-2">Accounts Disapproved</span>
@elseif($value->accounts==3)
<span class="btn-xs btn-primary text-white pl-2 pr-2">Accounts Pending</span>
@else
<span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
@endif

                                        @endif
                                        {{--  //accounts  --}}
                                        @if(session('logged_session_data.designation_id')==8)
                                            @if($value->accounts==1)
                                            <span class="btn-xs btn-success text-white pl-2 pr-2">Approved</span>
                                            @elseif($value->accounts==2)
                                            <span class="btn-xs btn-danger text-white pl-2 pr-2">Disapproved</span>
                                            @elseif($value->accounts==3)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Pending</span>
                                            @else
                                            <span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
                                            @endif
                                        @endif
                                        {{--  //sales  --}}
                                        @if(session('logged_session_data.designation_id')==27)
                                            @if($value->sales==1)
                                            <span class="btn-xs btn-success text-white pl-2 pr-2">Approved</span>
                                            @elseif($value->sales==2)
                                            <span class="btn-xs btn-danger text-white pl-2 pr-2">Disapproved</span>
                                            @elseif($value->sales==3)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Pending</span>
                                            @else
                                            <span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
                                            @endif
                                        @endif
                                        {{--  //purchease  --}}
                                        @if(session('logged_session_data.designation_id')==20)
                                            @if($value->purchease==1)
                                            <span class="btn-xs btn-success text-white pl-2 pr-2">Approved</span>
                                            @elseif($value->purchease==2)
                                            <span class="btn-xs btn-danger text-white pl-2 pr-2">Disapproved</span>
                                            @elseif($value->purchease==3)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Pending</span>
                                            @else
                                            <span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
                                            @endif
                                        @endif
                                        {{--  //invoice  --}}
                                        @if(session('logged_session_data.designation_id')==35)
                                            @if($value->invoice==1)
                                            <span class="btn-xs btn-success text-white pl-2 pr-2">Approved</span>
                                            @elseif($value->invoice==2)
                                            <span class="btn-xs btn-danger text-white pl-2 pr-2">Disapproved</span>
                                            @elseif($value->invoice==3)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Pending</span>
                                            @else
                                            <span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
                                            @endif
                                        @endif
                                        {{--  //delivery  --}}
                                        @if(session('logged_session_data.designation_id')==34)
                                            @if($value->delivery==1)
                                            <span class="btn-xs btn-success text-white pl-2 pr-2">Delivery Completed</span>
                                            @elseif($value->delivery==2)
                                            <span class="btn-xs btn-danger text-white pl-2 pr-2">Rejected</span>
                                            @elseif($value->delivery==3)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Out For Delivery</span>
                                            @elseif($value->delivery==4)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Pending For Delivery</span>
                                            @else
                                            <span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
                                            @endif
                                        @endif 
                                        {{--  //receivables  --}}
                                        @if(session('logged_session_data.designation_id')==2)
                                            @if($value->receivables==1)
                                            <span class="btn-xs btn-success text-white pl-2 pr-2">Payment Received</span>
                                            @elseif($value->receivables==2)
                                            <span class="btn-xs btn-danger text-white pl-2 pr-2">Rejected</span>
                                            @elseif($value->receivables==3)
                                            <span class="btn-xs btn-primary text-white pl-2 pr-2">Payment Pending</span>
                                            @else
                                            <span class="btn-xs btn-warning text-white pl-2 pr-2">New</span>
                                            @endif
                                        @endif                                        

                                        {{--  @if(Auth::user()->role_id == 1)
                                        <a class="btn btn-xs btn-danger text-white pl-2 pr-2" href="{{url('crm-deals/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif  --}}

                                    </td>
                                    <td>
                                        <a class="btn btn-xs btn-info text-white" href="{{url('crm-deal-track-approval/'.$value->id)}}"><i class="fa fa-eye" aria-hidden="true"></i> View </a>
                                    </td>
                                </tr>
                                  <div class="modal fade admin-query" id="deletequotations{{@$value->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') @lang('lang.quotations')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg"
                                                                data-dismiss="modal">@lang('lang.cancel')
                                                        </button>

                                                        <a href="{{url('quotations/delete', [$value->id])}}"
                                                           class="primary-btn fix-gr-bg">@lang('lang.delete')</a>

                                                    </div>
                                                    
                                                     
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>
</section>
@endsection
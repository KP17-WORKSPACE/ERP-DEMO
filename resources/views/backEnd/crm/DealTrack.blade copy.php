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
            <h1>Deal {{ $edit->id }}</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-deals/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
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

    <section class="admin-visitor-area">
        <div class="row">
            <div class="col-lg-4 pl-3 pt-2 text-dark">
                <div class="leadbox">
                <h5 class="mt-2 text-dark">Deal Info:- {!! App\SysHelper::deal_type($edit->isproject) !!}</h5>
                <h5 class="mt-2 text-dark">{{ $edit->deal_name }}</h5>
                Deal Value : 
                    <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">
                    {{ $edit->deal_value }}</span>
                    @if($edit->estimated_close_date !="")
                    | Close Date : 
                        <span class="pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">
                        {{ date('m/d/Y', strtotime($edit->estimated_close_date)) }}</span>
                    @endif
                    <br />
                    Stage : 
                        @if($edit->stage==1) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Prospecting</span> @endif
                        @if($edit->stage==2) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Quote</span> @endif
                        @if($edit->stage==3) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Closure</span> @endif
                        @if($edit->stage==4) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Won</span>@endif
                        @if($edit->stage==5) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Lost</span> @endif
                        
                </div>
            </div>
            <div class="col-lg-4 pl-2 pt-2 text-dark">
                <div class="leadbox">
                <h5 class="mt-2 text-dark">Owner Info:-</h5>
                        <span class="bg-gray text-xs pl-2 pr-2 pb-0 pt-0" style="border-radius: 5px;">
                        {{ $edit->ownername->first_name }}
                        {{ $edit->ownername->middle_name }} {{ $edit->ownername->last_name }}</span> | Added On : <b>{{ date('d/m/Y H:i:s', strtotime(@$edit->created_at)) }}</b>
                         | Source : <b>{{ $edit->source }} @if ($edit->source_o != "") - {{ $edit->source_o }} @endif</b>
                        <br />Mob : {{ $edit->ownername->mobile }} | Email : {{ $edit->ownername->email }}
                        @if($edit->doc != "")<br />Attachment : <a href="{{asset('public/uploads/crm_lead_doc/')}}/{{ $edit->doc }}" target="_blank">View & Download</a> <br /> @endif
                        

                        @if($edit->lead_id !="")
                        <br />Converted From : <b>Lead {{ @$edit->lead_id }}</b>
                        @endif
                </div>
            </div>
            <div class="col-lg-4 pl-2 pt-2 text-dark">
                <div class="leadbox">
                <h5 class="mt-2 text-dark">Customer Info:-</h5>
                <span class="text-md text-dark">{{ $edit->customername->name }}</span>
                <div class="txtlbl">{{ $edit->customername->address }}<br />
                    <b>Contact :</b> {{ $edit->cust_name }} | <b>M :</b> {{ $edit->cust_no }} | <b>E :</b> {{ $edit->cust_email }}</div>
                </div>
            </div>
            <div class="col-lg-12">
                <hr />
            </div>
        </div>


        @if(isset($dealtrack))
        <div class="white-box leadbox mr-3 ml-2 border-danger">
            <h5>Deal Approval Status</h5>
            <div class="row">
                <div class="col-lg-2 mb-1 text-bold">
                    Accounts :-
                </div>
                <div class="col-lg-2 mb-1 text-bold">
                    Sales Manager :-
                </div>
                <div class="col-lg-2 mb-1 text-bold">
                    Purchase :-
                </div>
                <div class="col-lg-2 mb-1 text-bold">
                    Invoice :-
                </div>
                <div class="col-lg-2 mb-1 text-bold">
                    Delivery :-
                </div>
                <div class="col-lg-2 mb-1 text-bold">
                    Receivables :-
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 mb-10">
                    @if ($dealtrack->accounts==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
                    @elseif ($dealtrack->accounts==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($dealtrack->accounts==3)
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
                    @else
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                </div>
                <div class="col-lg-2 mb-10">
                    @if ($dealtrack->sales==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
                    @elseif ($dealtrack->sales==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($dealtrack->sales==3)
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
                    @else
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                </div>
                <div class="col-lg-2 mb-10">
                    @if ($dealtrack->purchease==1)                    
                        @if ($dealtrack->purchease==1 && count($purchease)==0)
                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Not Applicable</div>
                        @else
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
                        @endif
                    @elseif ($dealtrack->purchease==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($dealtrack->purchease==3)
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
                    @elseif ($dealtrack->purchease==4)
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Partial Delivery</div>
                    @else
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                </div>
                <div class="col-lg-2 mb-10">
                    @if ($dealtrack->invoice==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
                    @elseif ($dealtrack->invoice==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($dealtrack->invoice==3)
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
                    @else
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                </div>
                <div class="col-lg-2 mb-10">
                    @if ($dealtrack->delivery==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Delivery Completed</div>
                    @elseif ($dealtrack->delivery==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($dealtrack->delivery==3)
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Out For Delivery</div>
                    @elseif ($dealtrack->delivery==5)
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Ready For Delivery</div>
                    @elseif ($dealtrack->delivery==4)
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending For Delivery</div>
                    @else
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                </div>
                <div class="col-lg-2 mb-10">
                    @if ($dealtrack->receivables==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Payment Received</div>
                    @elseif ($dealtrack->receivables==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($dealtrack->receivables==3)
                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Payment Pending</div>
                    @elseif ($dealtrack->receivables==4)
                        <div class="progress-bar bg-dark" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Order Cancelled</div>
                    @else
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 mb-1">
                    @if(count($accounts)>0)
    @foreach ($accounts as $val)
    <b>Customer Status</b> : @if($val->customer_status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->customer_status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Credit Limit</b> : @if($val->credit_limit == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->credit_limit == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Payment Terms</b> : @if($val->payment_terms == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_terms == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Overdue Payment</b> : @if($val->pending_payment == 1) No <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->pending_payment == 2) Yes <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
                </div>
                <div class="col-lg-2 mb-1">
                    @if(count($sales)>0)
    @foreach ($sales as $val)

    <b>Margin</b> : @if($val->margin == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->margin == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Stock</b> : @if($val->stock == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->stock == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Purchase Quote</b> : @if($val->purcease_quote == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->purcease_quote == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
                </div>
                <div class="col-lg-2 mb-1">
                    @if(count($purchease)>0)
                    @foreach ($purchease as $val)
                    <b>Purchase Quote</b> : @if($val->purchease_quote == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->purchease_quote == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
                    <br />
                    <b>3Quote Qequest</b> : @if($val->three_quote_request == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->three_quote_request == 3) Not Required <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->three_quote_request == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
                    <br />
                    <b>Purchase Status</b> : @if($val->validation == 1) Purchase Completed <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->validation == 3) Under Purchase <i class="fa fa-clock text-warning" aria-hidden="true"></i>@elseif($val->validation == 4) Partial Delivery <i class="fa fa-check text-success" aria-hidden="true"></i> @elseif($val->validation == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
                    <br />
                    
                    @if($val->validation == 3)
                        @if($val->delivery_date != "" && $val->delivery_date != "1970-01-01")<b>Expected Delivery</b> : {{ date('d/m/Y', strtotime($val->delivery_date)) }}<br />@endif
                    @endif
                    

                    <b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
                    <br />
                    @if(session('logged_session_data.designation_id')==20 || Auth::user()->role_id == 1)
                    @if($val->fileone != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->fileone }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 1</a>&nbsp;@endif
                    @if($val->filetwo != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->filetwo }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 2</a>&nbsp;@endif
                    @if($val->filethree != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->filethree }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 3</a>@endif
                    @endif
                    <br />
                    <b>Remarks</b> : {!! $val->remarks !!}
                    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
                    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
                    @endforeach
                    @endif
                </div>
                <div class="col-lg-2 mb-1">
                    @if(count($invoice)>0)
    @foreach ($invoice as $val)
    <b>Delivery Advice</b> : @if($val->delivery_advice == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->delivery_advice == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Validation</b> : @if($val->validation == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->validation == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Hold</b> : @if($val->hold == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->hold == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Print</b> : @if($val->print == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->print == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->invoice_no != "")<b>Invoice No</b> : {{ $val->invoice_no }}@endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
                </div>
                <div class="col-lg-2 mb-1">
                    @if(count($delivery)>0)
    @foreach ($delivery as $val)
    <b>DO Status</b> : @if($val->do_status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->do_status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->do_no != "")<b>Do No</b> : {{ $val->do_no }}<br />@endif
    
    @if($val->print_invoice_no != "")<b>Print Invoice No</b> : {{ $val->print_invoice_no }}<br />@endif

    @if($val->cheque_collection != "")<b>Cheque Collection</b> : @if($val->cheque_collection == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->cheque_collection == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />@endif
    
    @if($val->cheque_collection_file != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->cheque_collection_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a><br />@endif
    
    <b>Delivery Status</b> : @if($val->delivery_status == 1) Delivery Completed <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->delivery_status == 2) Pending For Delivery <i class="fa fa-times text-danger" aria-hidden="true"></i> @elseif($val->delivery_status == 4) Ready For Delivery <i class="fa fa-clock text-info" aria-hidden="true"></i> @else Out For Delivery <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->deliver_by != "")<b>Deliver By</b> : {{ $val->deliver_by }}
    @if($val->driver != ""), {{ $val->driver }}<br />@endif @endif
    
    @if($val->cash_collected != "")<b>Cash Collected</b> : {{ $val->cash_collected }}<br />@endif
    
    @if($val->id_no != "")<b>ID No</b> : {{ $val->id_no }}<br />@endif
    @if($val->contact_no != "")<b>Contact No</b> : {{ $val->contact_no }}<br />@endif
    @if($val->awb_no != "")<b>AWB No</b> : {{ $val->awb_no }}<br />@endif
    @if($val->attach_file != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->attach_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Attachment</a><br />@endif
  
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
                </div>
                <div class="col-lg-2 mb-1">
                    @if(count($receivables)>0)
    @foreach ($receivables as $val)
@if($val->payment_collection == 3)
<b>Credit Note No : {{ $val->credit_note }}</b>
@else
    <b>Payment Collection</b> : @if($val->payment_collection == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_collection == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @elseif($val->payment_collection == 3) Order Cancelled <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Payment Status</b> : @if($val->payment_status == 1) Payment Received <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_status == 2) Pending <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->amount != "")<b>Amount</b> : {{ $val->amount }}<br />@endif
    @if($val->amount2 != "")<b>Amount</b> : {{ $val->amount2 }}<br />@endif
    @if($val->amount3 != "")<b>Amount</b> : {{ $val->amount3 }}<br />@endif
    <b>Payment Mode</b> :    
@if($val->paymenttype == 1) Cash @endif
@if($val->paymenttype == 2) Cheque @endif
@if($val->paymenttype == 3) Bank Transfer @endif
@if($val->paymenttype == 4) Open Credit @endif
@if($val->paymenttype == 5) Credit Card @endif
@if($val->paymenttype == 6) Bank TT @endif
    <br />
    @if($val->cash_date != "" && $val->cash_date != "1970-01-01")<b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date)) }}<br />@endif
    @if($val->cash_date2 != "" && $val->cash_date2 != "1970-01-01")<b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date2)) }}<br />@endif
    @if($val->cash_date3 != "" && $val->cash_date3 != "1970-01-01")<b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date3)) }}<br />@endif

    @if($val->cheque_no != "")<b>Cheque No</b> : {{ $val->cheque_no }}<br />@endif
    @if($val->cheque_date != "1970-01-01" && $val->cheque_date != "")<b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date)) }}<br />@endif
    @if($val->cheque_no2 != "")<b>Cheque No</b> : {{ $val->cheque_no2 }}<br />@endif
    @if($val->cheque_date2 != "1970-01-01" && $val->cheque_date2 != "")<b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date2)) }}<br />@endif
    @if($val->cheque_no3 != "")<b>Cheque No</b> : {{ $val->cheque_no3 }}<br />@endif
    @if($val->cheque_date3 != "1970-01-01" && $val->cheque_date3 != "")<b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date3)) }}<br />@endif

    @if($val->cheque_copy != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->cheque_copy }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a><br />@endif
    
    @if($val->bank_name != "")<b>Bank Name</b> : {{ $val->bank_name }}<br />@endif
    @if($val->deposit_date != "" && $val->deposit_date != "1970-01-01")<b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->deposit_date)) }}<br />@endif
    @if($val->deposit_date2 != "" && $val->deposit_date2 != "1970-01-01")<b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->deposit_date2)) }}<br />@endif
    
    @if($val->open_credit_date != "" && $val->open_credit_date != "1970-01-01")<b>Open Credit</b> : {{ date('d/m/Y', strtotime($val->open_credit_date)) }}<br />@endif
    
    @if($val->credit_card_type != "")<b>Credit Card</b> : {{ $val->credit_card_type }}<br />@endif
    @if($val->payment_date != "" && $val->payment_date != "1970-01-01")<b>Payment Date</b> : {{ date('d/m/Y', strtotime($val->payment_date)) }}<br />@endif
    @if($val->credit_card_deposit_date != "" && $val->credit_card_deposit_date != "1970-01-01")<b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->credit_card_deposit_date)) }}<br />@endif
    
    @if($val->banktt_date != "" && $val->banktt_date != "1970-01-01")<b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date)) }}<br />@endif
    @if($val->banktt_date2 != "" && $val->banktt_date2 != "1970-01-01")<b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date2)) }}<br />@endif
    @if($val->banktt_date3 != "" && $val->banktt_date3 != "1970-01-01")<b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date3)) }}<br />@endif
    @if($val->banktt_copy != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->banktt_copy }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> BankTT Copy</a><br />@endif
@endif
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
                </div>
            </div>
        </div><br />
        @if($dealtrack->accounts==2 || $dealtrack->sales==2 || $dealtrack->purchease==2 || $dealtrack->purchease==4 || $dealtrack->invoice==2 || $dealtrack->delivery==2  || $dealtrack->receivables==2)
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>Re Submit For Approval</h5>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
            <div class="row">
            <div class="col-lg-2 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label>
                            <input class="primary-input dynamicstxt_s w-100 date" id="delivery_date" type="text" autocomplete="off" required name="delivery_date" value="">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Payment Terms<span></span></label>
                    <select class="w-100 dynamicstxt_s bb w-100 form-control" name="payment_terms" id="payment_terms_re" required>
                        <option value="">-Select-</option>
                    @foreach ($paymentterms as $key => $value)
                        <option value="{{ @$value->id }}"
                            @if (isset($editData)) @if (@$editData->payment_terms == @$value->id) selected @endif
                        @else
                            {{ old('payment_terms') == @$value->id ? 'selected' : '' }}
                            @endif
                            >{{ @$value->title }}</option>
                    @endforeach                                                    
                    </select>
                    <script>
                        $('#payment_terms_re').on('change', function(e) {
                            if ($('#payment_terms_re').val() == 20 || $('#payment_terms_re').val() == 21) {
                                $('#payment_mode_sec_re_div').css("display", "none");
                                //$('#payment_mode_sec_re').prop('required', true);
                            } else {
                                $('#payment_mode_sec_re_div').css("display", "none");
                                //$('#payment_mode_sec_re').prop('required', false);
                            }
                        });
                    </script>
                </div>
            </div>
            
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Payment Mode<span></span></label>
                    <select class="w-100 dynamicstxt_s bb w-100 form-control" name="payment_mode" id="payment_mode" required>
                        <option value="">-Select-</option>
                        <option value="1">Cash</option>
                        <option value="2">Cheque</option>
                        <option value="3">Bank Transfer</option>
                        <option value="4">Open Credit</option>
                        <option value="5">Credit Card</option>
                        <option value="6">Bank TT</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 mb-10" id="payment_mode_sec_re_div" style="display: none;">
                <div class="input-effect">
                    <label class="txtlbl">Payment Mode<span></span></label>
                    <select class="w-100 dynamicstxt_s bb w-100 form-control" name="payment_mode_sec" id="payment_mode_sec_re" >
                        <option value="">-Select-</option>
                        <option value="1">Cash</option>
                        <option value="2">Cheque</option>
                        <option value="3">Bank Transfer</option>
                        <option value="4">Open Credit</option>
                        <option value="5">Credit Card</option>
                        <option value="6">Bank TT</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Purchase Required<span></span></label><br />
                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="purchease_required">
                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Partial Delivery<span></span></label><br />
                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault1" name="partial_delivery">
                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault1">Yes, Partial Delivery</label>
                </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('LPO')<span></span></label><br />
                    <div class="form-group files">
                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="lpo[]">
                      </div>
                </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Cheque/TT Copy')<span></span></label><br />

                    <div class="form-group files">
                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="cheque_copy[]">
                      </div>
                </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Purchase Quote')<span></span></label><br />

                    <div class="form-group files">
                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="purchease_quote[]">
                      </div>
                </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mb-10"><br /><br /><br /><br />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}"/>
                <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                    <span class="ti-check"></span>
                        @lang('Re Submit For Approval')
                </button>
            </div>
            {{--  <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('LPO')<span></span></label><br />

                    <label class="filebutton">
                        <span><input type="file" class="dynamicstxt_s w-100" name="lpo" id="lpo"><i class="fa fa-paperclip" aria-hidden="true"></i></span>
                    </label><span id="file-lpo"></span>
                    <script>
                        $("#lpo").change(function(){
                            $("#file-lpo").text(this.files[0].name);
                            });
                    </script>
                        <style>
                            label.filebutton {
                                width:auto; height:auto; padding: 7px 15px; border-radius: 10px;
                                cursor: pointer; overflow:hidden; position:relative; background-color:#ccc;
                            }                                                        
                            label span input {
                                z-index: 999; line-height: 0; font-size: 50px; position: absolute; top: -2px; left: -700px; opacity: 0;
                                filter: alpha(opacity = 0); -ms-filter: "alpha(opacity=0)"; cursor: pointer; _cursor: hand; margin: 0; padding:0;
                            }
                        </style>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Cheque/TT Copy')<span></span></label><br />
                    <label class="filebutton">
                        <span><input type="file" class="dynamicstxt_s w-100" name="cheque_copy" id="cheque_copy"><i class="fa fa-paperclip" aria-hidden="true"></i></span>
                    </label><span id="file-cheque_copy"></span>
                    <script>
                        $("#cheque_copy").change(function(){
                            $("#file-cheque_copy").text(this.files[0].name);
                            });
                    </script>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Purchase Quote')<span></span></label><br />

                    <label class="filebutton">
                        <span><input type="file" class="dynamicstxt_s w-100" name="purchease_quote" id="purchease_quote"><i class="fa fa-paperclip" aria-hidden="true"></i></span>
                    </label><span id="file-purchease_quote"></span>
                    <script>
                        $("#purchease_quote").change(function(){
                            $("#file-purchease_quote").text(this.files[0].name);
                            });
                    </script>
                </div>
            </div>  --}}
        </div>
        {{ Form::close() }}
        </div><br />
        @endif



        @else        
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>Submit For Approval</h5>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
            <div class="row">
            <div class="col-lg-2 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label>
                            <input class="primary-input dynamicstxt_s w-100 date" id="delivery_date" type="text" autocomplete="off" required name="delivery_date" value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Payment Terms<span></span></label>
                    <select class="w-100 dynamicstxt_s bb w-100 form-control" name="payment_terms" id="payment_terms" required>
                        <option value="">-Select-</option>
                    @foreach ($paymentterms as $key => $value)
                        <option value="{{ @$value->id }}"
                            @if (isset($editData)) @if (@$editData->payment_terms == @$value->id) selected @endif
                        @else
                            {{ old('payment_terms') == @$value->id ? 'selected' : '' }}
                            @endif
                            >{{ @$value->title }}</option>
                    @endforeach                                                    
                    </select>
                    <script>
                        $('#payment_terms').on('change', function(e) {
                            if ($('#payment_terms').val() == 20 || $('#payment_terms').val() == 21) {
                                $('#payment_mode_sec_div').css("display", "none");
                                //$('#payment_mode_sec').prop('required', true);
                            } else {
                                $('#payment_mode_sec_div').css("display", "none");
                                //$('#payment_mode_sec').prop('required', false);
                            }
                        });
                    </script>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Payment Mode<span></span></label>
                    <select class="w-100 dynamicstxt_s bb w-100 form-control" name="payment_mode" id="payment_mode" required>
                        <option value="">-Select-</option>
                        <option value="1">Cash</option>
                        <option value="2">Cheque</option>
                        <option value="3">Bank Transfer</option>
                        <option value="4">Open Credit</option>
                        <option value="5">Credit Card</option>
                        <option value="6">Bank TT</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 mb-10" id="payment_mode_sec_div" style="display: none;">
                <div class="input-effect">
                    <label class="txtlbl">Payment Mode<span></span></label>
                    <select class="w-100 dynamicstxt_s bb w-100 form-control" name="payment_mode_sec" id="payment_mode_sec" >
                        <option value="">-Select-</option>
                        <option value="1">Cash</option>
                        <option value="2">Cheque</option>
                        <option value="3">Bank Transfer</option>
                        <option value="4">Open Credit</option>
                        <option value="5">Credit Card</option>
                        <option value="6">Bank TT</option>
                    </select>
                </div>
            </div>
            
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Purchase Required<span></span></label><br />
                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="purchease_required">
                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label>
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Partial Delivery<span></span></label><br />
                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault1" name="partial_delivery">
                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault1">Yes, Partial Delivery</label>
                </div>
            </div>

            <div class="col-lg-4 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('LPO')<span></span></label><br />
                    <div class="form-group files">
                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="lpo[]">
                      </div>
                </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Cheque/TT Copy')<span></span></label><br />

                    <div class="form-group files">
                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="cheque_copy[]">
                      </div>
                </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Purchase Quote')<span></span></label><br />

                    <div class="form-group files">
                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="purchease_quote[]">
                      </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mb-10"><br /><br /><br /><br />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}"/>
                <button type="submit" class="btn btn-sm btn-info pl-3 pr-3" id="btnSubmit">
                    <span class="ti-check"></span>
                        @lang('Submit For Approval')
                </button>
            </div>
        </div>
        {{ Form::close() }}
        </div><br />
        @endif


            <div class="row">
                <div class="col-lg-8">
                    <div class="white-box leadbox ml-2 p-0">
                        @if (count($quoteitems)>0)
                        <h6 class="mb-2 mt-2 ml-2">Quote Items</h6>
                        <table id="table_custom" class="display school-table" cellspacing="0" width="100%">
                            <tr>
                            <td>Part Number</td>
                            <td>Description</td>
                            <td>Qty</td>
                            <td align="right">Price</td>
                            <td align="right">Discount</td>
                            <td align="right">Total</td>
                            </tr>
                        <?php $t_qty=0; $t_price=0; $t_discount=0; $t_net_amount=0;?>
                        <div class=" mb-1" style="max-height: 400px; overflow-x:hidden; overflow-y: scroll;">
                            @foreach ($quoteitems as $Item)
                            <tbody>
                                <tr>
                                    <td><?php try{ ?><span class="text-info">{{ $Item->productname->part_number }}</span><?php }catch (\Exception $e){} ?></td>
                                    <td><span class="text-dark">{!! nl2br($Item->description) !!}</span></td>
                                    <td><span class="text-success">{{ $Item->qty }}</span></td>
                                    <td><span class="text-danger float-right">{{ $Item->price }}</span></td>
                                    <td><span class="text-success float-right">{{ $Item->discount }}</span></td>
                                    <td><span class="text-success float-right">{{ @App\SysHelper::com_curr_format((($Item->price * $Item->qty)-($Item->discount * $Item->qty)), 2, '.', ',')}}</span></td>
                                </tr>
                            </tbody>
                            <?php $t_qty += $Item->qty;
                            $t_price += $Item->price * $Item->qty;
                            $t_discount += $Item->discount * $Item->qty;
                            $t_net_amount += ($Item->price * $Item->qty) - ($Item->discount * $Item->qty);
                        ?>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>{{ $t_qty }}
                                    <?php $t_discount += $edit->deal_discount?></td>
                                <td class="text-bold" align="right">{{ @App\SysHelper::com_curr_format($t_price, 2, '.', '') }}</td>
                                <td class="text-bold" align="right">{{ @App\SysHelper::com_curr_format($t_discount, 2, '.', '') }}</td>
                                <td class="text-bold" align="right">
                                    <?php $vat = (($t_price * $quoteitems[0]->company->net_vat/100) - ($t_discount * $quoteitems[0]->company->net_vat/100)); ?>
                                    {{ @App\SysHelper::com_curr_format($vat, 2, '.', '') }} VAT<br />
                                    {{@App\SysHelper::com_curr_format($t_price - $t_discount + $vat, 2, '.', '') }} {{ $Item->currency->code }}
                                </td>
                            </tr>
                        </table>

                            <a class="btn btn-danger btn-sm text-white mt-1" href="{{url('crm-quote/'.$edit->id.'/download')}}"> <i class="fa fa-download" aria-hidden="true"></i> Download Quotation</a>

                        @else
                                    
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="white-box leadbox" style="display: none;">
                        <div class="add-visitor">
                            <div class="row">
                                {{--  <div class="col-lg-12">
                                    <label class="txtlbl">Deal Name : </label>
                                        {{ $edit->deal_name }}
                                </div>
                                <div class="col-lg-12">
                                    <label class="txtlbl">Company Name : </label>
                                        {{ $edit->customername->name }}
                                </div>  --}}
                                <div class="col-lg-12">
                                    <div class="input-effect">
                                        <label class="txtlbl">Contact Person : </label>
                                        {{ $edit->cust_name }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <label class="txtlbl">Mobile : </label>
                                        {{ $edit->cust_no }}
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                        <label class="txtlbl">Email : </label>
                                        {{ $edit->cust_email }}
                                    </div>
                                </div>
                                {{--  <div class="col-lg-12">
                                    <div class="input-effect">
                                        <label class="txtlbl">Deal Value : </label>
                                            {{ $edit->deal_value }}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-effect">
                                        <label class="txtlbl">Source : </label>
                                            {{ $edit->source }} @if ($edit->source_o != "") - {{ $edit->source_o }} @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-effect">
                                        <label class="txtlbl">Tags : </label>
                                        {{ $edit->tags }}
                                    </div>
                                </div>  --}}
                            </div>
                        </div>
                    </div>

                    @if(isset($comments))
                    <div class="white-box leadbox ml-2" style="background: #ffffff;">
                    
                        @if($edit->note != "")Note : {!! nl2br($edit->note) !!}@endif

                    @foreach ($comments as $cmts)
                        {!! $cmts->comments !!}
                        <div class="text-right"><span class="text-dark">
                        {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}</span>
                         , On {{date('d/m/Y', strtotime($cmts->created_at))}}&nbsp;</div>
                        <hr class="mt-2 mb-2"/>
                    @endforeach
                    </div>
                    @endif

                    @if($edit->tags != "")<br />
                    <div class="white-box leadbox ml-2" style="background: #ffffff;">
                    Tags : <b>{{ $edit->tags }}</b>
                    </div>
                    @endif

                    <br />
                    <div class="white-box leadbox ml-2">
                        <div class="add-visitor">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h6 class="mb-2 mt-2">Delivery Location/Address</h6>
                                    <hr />
                                    @if(isset($addressbook))
                                        <span class="text-sm text-dark">Company : {{ $addressbook->customername->name }}</span>
                                        <div class="text-sm">Address : {{ $addressbook->address }}<br />
                                        Contact Person : {{ $addressbook->contact_person }}<br />
                                        Contact Number :</b> {{ $addressbook->contact_number }} | Email : {{ $addressbook->contact_email }}</div>
                                    @else
                                        <span class="text-sm text-dark">Company : {{ $edit->customername->name }}</span>
                                        <div class="text-sm">Address : {{ $edit->customername->address }}<br />
                                        Contact Person : {{ $edit->cust_name }}<br />
                                        Contact Number :</b> {{ $edit->cust_no }} | Email : {{ $edit->cust_email }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                

            </div>
        </div>
    </section>
<style>
    .files input {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 30px 0px 55px 35%;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }
    .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
     }
    .files{ position:relative}
    .files:after {  pointer-events: none;
        position: absolute;
        top: 60px;
        left: 0;
        width: 50px;
        right: 0;
        height: 25px;
        content: "";
        /*background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);*/
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
    }
    .color input{ background-color:#f1f1f1;}
    .files:before {
        position: absolute;
        bottom: 10px;
        left: 0;  pointer-events: none;
        width: 100%;
        right: 0;
        height: 25px;
        content: " or drag it here. ";
        display: block;
        margin: 0 auto;
        color: #2ea591;
        font-weight: 600;
        text-transform: capitalize;
        text-align: center;
    }
</style>


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

function change_stage(id) {
    $("#loading_bg").css("display", "block");
    var stage = $("#edit_stage").val();

    if (stage == "" || stage <= 0) {
        alert("Please Choose Stage");
        $("#edit_stage").focus();
        $("#loading_bg").css("display", "none");
        return false;
    }
    $("#btn_edit_stage").attr('disabled', true);

    var action = "{{ URL::to('crm-deals-update-stage') }}";
    $.ajax({
        url: action,
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            stage: stage,
        },
        cache: false,
        success: function(dataResult) {
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            if (dataResult['data'] == "ERROR") {
                alert("Error found in something!!");
            } else {
                //$("#loading_bg").css("display", "none");
                //alert("Renewed! Please update and continue");
                location.reload(true);
            }
        }
    });
}

    </script>
@endsection
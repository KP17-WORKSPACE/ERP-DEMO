@extends('backEnd.masterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    @php
        function showPicName($data)
        {
            $name = explode('/', $data);
            return $name[4];
        }
        function showJoiningLetter($data)
        {
            $name = explode('/', $data);
            return $name[3];
        }
        function showResume($data)
        {
            $name = explode('/', $data);
            return $name[3];
        }
        function showOtherDocument($data)
        {
            $name = explode('/', $data);
            return $name[3];
        }
        
    @endphp

    <?php try { ?>

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Customer Details</h2>
                <span class="page-label">Home - Customer Details</span>
            </div>
            <div>
                <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Customer</a>
                @if(Auth::user()->role_id==1 || Auth::user()->role_id ==2)
                <a href="{{ url('customers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer List</a>
                @endif
                <a href="{{ url('customer-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Profile</button></a>
                <a href="{{ url('customer-import') }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Import</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Info</h2>
                        
                    <p class="mb-1 text-muted"><span class="font-semibold">Customer Type :</span> <span
                        class="f-14 text-dark font-weight-semibold">
                        @if (@$custDetails->account_type == 1) Reseller @endif
                        @if (@$custDetails->account_type == 2) Enduser @endif
                        @if (@$custDetails->account_type == 3) Ecommerce @endif
                    </span></p>
                    <span class="badge badge-danger text-left"
                        @if (@$custDetails->type == 1) style="background: #228c22;" @endif
                        @if (@$custDetails->type == 2) style="background: #FFA500;" @endif
                        @if (@$custDetails->type == 3) style="background: #FF0000;" @endif
                        @if (@$custDetails->type == 4) style="background: #000000;" @endif >
                        @if (isset($custDetails))
                            {{ @$custDetails->name }}
                        @endif
                    </span>
                    <p class="mb-1 text-muted"><span class="font-semibold">Display Name :</span> <span
                            class="f-14 text-dark font-weight-semibold">
                            {{ @$custDetails->customer_name_display }}
                        </span></p>

                    <p class="mb-1 text-muted"><span class="font-semibold">Customer Code :</span> <span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->code }}
                            @endif
                        </span></p>
                        
                    <p class="mb-1 text-muted"><span class="font-semibold">Primary Contact :</span> <span
                        class="f-14 text-dark font-weight-semibold">
                            {{ @$custDetails->customer_salutation }} {{ @$custDetails->first_name }} {{ @$custDetails->last_name }}
                    </span></p>

                        
                    <p class="mb-1 text-muted"><span class="font-semibold">Designation : </span><span>
                            @if (isset($custDetails))
                                {{ @$custDetails->designation }}
                            @endif
                        </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Contact Number: </span><span
                                class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->contcat_number }}
                                @endif
                            </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Mobile: </span><span
                                class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->mobile }}
                                @endif
                            </span></p>

                    <p class="mb-1 text-muted"><span class="font-semibold">Mail : </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->email }}
                            @endif
                        </span></p>
                </div>
            </div>
            <div class="col-lg-4 mb-3" style="display: none;">
                <div class="p-4 card h-100">
                    <h2 class="head">Billing Address</h2>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address2 }}
                            @endif
                        </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Mobile: </span><span
                                class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->mobile }}
                                @endif
                            </span></p>
                    <hr />
                    <h2 class="head">Shipping Address</h2>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address2 }}
                            @endif
                        </span></p>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">VAT & Payment Info</h2>
                    <div class="card-body p-0">
                        <div class="row">
                            <label class="col-lg-4 text-muted">Sales Person</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: <span class="text-info">
                                    {{--  {{ $custDetails->salesperson->full_name }}  --}}
                                    @if(count($editAssign)>0)
                                    @foreach ($editAssign as $e)
                                        {{ $e->full_name }}, 
                                    @endforeach
                                    @endif
                                </span>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">Transaction Type</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->transaction_type }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">Credit Limit</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->credit_limit }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">Credit Days </label>
                            <div class="col-lg-8 d-flex align-items-center">
                                <span class="font-weight-bold text-gray-800 me-2">: @if (isset($custDetails))
                                        {{ @$custDetails->credit_days }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @if (isset($custDetails) && !empty(@$custDetails->payment_terms))
                            <div class="row">
                                <label class="col-lg-4 text-muted">Payment Terms </label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <span class="font-weight-bold text-gray-800 me-2">: @if (isset($custDetails))
                                            {{ @$custDetails->paymentterms->title }} {{ @$custDetails->payment_terms_txt }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if (isset($custDetails) && !empty(@$custDetails->vat_country))
                            <div class="row">
                                <label class="col-lg-4 text-muted">VAT Country</label>
                                <div class="col-lg-8">
                                    <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                            {{ @$custDetails->vatcountry->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if (isset($custDetails) && !empty(@$custDetails->vat_state))
                            <div class="row">
                                <label class="col-lg-4 text-muted">VAT State</label>
                                <div class="col-lg-8">
                                    <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                            {{ @$custDetails->vatstate->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <label class="col-lg-4 text-muted">VAT Percentage</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->vat_percentage }}% @if($custDetails->vat_is_fixed==1) <span class="btn btn-warning m-0 p-0">&nbsp;Fixed&nbsp;</span>@endif
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">VAT Number</label>
                            <div class="col-lg-8">
                                <a href="#" class="font-weight-bold text-gray-800 text-hover-primary">: @if (isset($custDetails))
                                        {{ @$custDetails->vat_number }}
                                    @endif
                                </a>
                            </div>
                        </div>
                        @if (isset($custDetails) && !empty(@$custDetails->customer_type))
                            <div class="row">
                                <label class="col-lg-4 text-muted">Customer Type</label>
                                <div class="col-lg-8">
                                    <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                            {{ @$custDetails->customertype->title }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if (isset($custDetails) && !empty(@$custDetails->sale_type))
                        <div class="row">
                            <label class="col-lg-4 text-muted">Sale Type</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->saletype->title }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-3"><br />

{{--  tabs  --}}
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="true">Address</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contactperson" role="tab" aria-controls="contactperson" aria-selected="true">Contact Person</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a></li>
  </ul>
{{--  tabs  --}}

<div class="tab-content" style="min-height: 100px;">
    <div class="tab-pane active pt-2" id="address" role="tabpanel" aria-labelledby="address-tab">

        <div class="row">
            <div class="col-md-12">
                @if (count($custAddress)>0)
                <div class="row">
                    @foreach ($custAddress as $data)
                        <div class="col-md-4">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                
                                @if($data->set_default==1 && $data->is_shipping==0)<tr><td colspan="2"><b>Billing Address</b></td></tr>@endif
                                @if($data->is_shipping==1)<tr><td colspan="2"><b>Shipping Address</b></td></tr>@endif
                                
                                <tr><td>Country</td><td>{{ $data->countryname["name"] }}</td></tr>
                                <tr><td>Address 1</td><td>{{ $data->address }}</td></tr>
                                <tr><td>Address 2</td><td>{{ $data->address2 }}</td></tr>
                                <tr><td>City</td><td>{{ $data->city }}</td></tr>
                                @if ($data->state!=0)
                                <tr><td>State</td><td>{{ $data->statename["name"] }}</td></tr>
                                @endif
                                <tr><td>Post Box</td><td>{{ $data->zip_code }}</td></tr>
                            </table>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    {{--  Address  --}}
    
    {{--  Contact  --}}
    <div class="tab-pane pt-2" id="contactperson" role="tabpanel" aria-labelledby="contactperson-tab">        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped" id="pi-ret-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>@lang('Salutation')</th>
                            <th>@lang('First Name')</th>
                            <th>@lang('Last Name')</th>
                            <th>@lang('Email Address')</th>
                            <th>@lang('Work Phone')</th>
                            <th>@lang('Mobile')</th>
                            <th>@lang('Designation')</th>
                            <th>@lang('Department')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($custContact)>0)
                        @foreach ($custContact as $data)                            
                        <tr>
                            <td>{{ $data->salutation }}</td>
                            <td>{{ $data->first_name }}</td>
                            <td>{{ $data->last_name }}</td>
                            <td>{{ $data->email_address }}</td>
                            <td>{{ $data->work_phone }}</td>
                            <td>{{ $data->mobile }}</td>
                            <td>{{ $data->designation }}</td>
                            <td>{{ $data->department }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
    {{--  Contact  --}}

    {{--  Document  --}}
    <div class="tab-pane pt-2" id="documents" role="tabpanel" aria-labelledby="documents">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    @if (count($custDoc)>0)
                        @foreach ($custDoc as $doc)
                        <tr>
                            <td>{{ $doc->doc_name }}</td>
                            <td>
                                @if($doc->doc_name == "Trade License/Commercial Registration")
                                {{date('d/m/Y', strtotime(@$doc->doc_exp_date))}}
                                @endif
                            </td>
                            <td><a class="btn-sm btn-primary" href="{{asset('public/uploads/cust-suppl/')}}/{{ $doc->doc_file }}" target="_blank">Download</a></td>
                        </tr>  
                        @endforeach                        
                    @endif
                </table>
            </div>
        </div>
    </div>
    {{--  Document  --}}


</div>

            </div>

            
            <div class="col-lg-12 mb-3"><br />
                <style>
                    .card-header {
                        background-color: #b8caff;
                        color: #000000;
                        margin-right: 5px;
                    }
                    .nav-tabs .active{background-color: #4e73df; color: #ffffff;}
                    .tab-pane {
                        background: #ffffff;                        
                    }
                    .nav-tabs{border: none !important;}
                    .card-body{margin-top:10px; }
                    
                </style>

                <ul class="nav nav-tabs">
                    <li><a class="card-header active" data-toggle="tab" href="#tab1">Deals In Progress</a></li>
                    <li><a class="card-header" data-toggle="tab" href="#tab2">Invoice Completed</a></li>
                    <li><a class="card-header" data-toggle="tab" href="#tab3">Payment Pending</a></li>
                    <li><a class="card-header" data-toggle="tab" href="#tab4">Completed Orders</a></li>
                    <li><a class="card-header" data-toggle="tab" href="#tab5">AMC</a></li>
                    <li><a class="card-header" data-toggle="tab" href="#tab6">Project Service</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane fade in active show">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                                <thead>                                
                                    <tr>
                                        <th>@lang('Deal')</th>
                                        <th>@lang('Deal Name')</th>
                                        <th>@lang('Stage')</th>
                                        <th>@lang('Ownership')</th>
                                        <th>@lang('Updated On')</th>
                                        <th class="text-right">@lang('Deal Value')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Clossing Date')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>                    
                                    @php $count =1; $total_deal=0; $total_amount=0; @endphp
                                    @foreach($pending as $value)
                                    @php $total_deal += 1; @endphp
                                    @if((@$value->estimated_close_date <=  Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage ==2 || $value->stage ==3))
                                        <tr style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr>
                                    @endif
                                        <td><a href="{{ url('get-url-deal-track/'.$value->code) }}" target="_blank">{{@$value->code}}</a></td>
                                        <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></td>
                                        <td>
                                            @if($value->stage==1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                                            @if($value->stage==2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                                            @if($value->stage==3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                                            @if($value->stage==4) 
                                            <?php
                                            $data = App\SysHelper::deal_track_status3($value->receivables,$value->delivery,$value->invoice,$value->purchease,$value->sales,$value->accounts);
                                            ?>
                                            {!! $data !!}
                                            @endif
                                            @if($value->stage==5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                                            @if($value->stage==6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif
                                        </td>
                                        <td>{{@$value->ownername->full_name}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->updated_at))}}</td>
                                        <td class="text-right">
                                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp AED
                                        </td>
                                        <td>{{date('d-M-Y', strtotime(@$value->created_at))}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->estimated_close_date))}}</td>
                                        <td>
                                            <a class="btn-sm btn-info" target="_blank" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>              
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>{{ $total_deal }}</th>
                                        <th></th><th></th><th></th><th></th>
                                        <th class="text-right pr-1">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}} AED</th>
                                        <th></th><th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="tab2" class="tab-pane fade">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                                <thead>                                
                                    <tr>
                                        <th>@lang('Deal')</th>
                                        <th>@lang('Deal Name')</th>
                                        <th>@lang('Stage')</th>
                                        <th>@lang('Ownership')</th>
                                        <th>@lang('Updated On')</th>
                                        <th class="text-right">@lang('Deal Value')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Clossing Date')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>                    
                                    @php $count =1; $total_deal=0; $total_amount=0; @endphp
                                    @foreach($invoiced as $value)
                                    @php $total_deal += 1; @endphp
                                    @if((@$value->estimated_close_date <=  Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage ==2 || $value->stage ==3))
                                        <tr style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr>
                                    @endif
                                    <td><a href="{{ url('get-url-deal-track/'.$value->code) }}" target="_blank">{{@$value->code}}</a></td>
                                        <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></td>
                                        <td>
                                            @if($value->stage==1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                                            @if($value->stage==2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                                            @if($value->stage==3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                                            @if($value->stage==4) 
                                            <?php
                                            $data = App\SysHelper::deal_track_status3($value->receivables,$value->delivery,$value->invoice,$value->purchease,$value->sales,$value->accounts);
                                            ?>
                                            {!! $data !!}
                                            @endif
                                            @if($value->stage==5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                                            @if($value->stage==6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif
                                        </td>
                                        <td>{{@$value->ownername->full_name}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->updated_at))}}</td>
                                        <td class="text-right">
                                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp AED
                                        </td>
                                        <td>{{date('d-M-Y', strtotime(@$value->created_at))}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->estimated_close_date))}}</td>
                                        <td>
                                            <a class="btn-sm btn-info" target="_blank" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>              
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>{{ $total_deal }}</th>
                                        <th></th><th></th><th></th><th></th>
                                        <th class="text-right pr-1">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}} AED</th>
                                        <th></th><th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="tab3" class="tab-pane fade">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                                <thead>                                
                                    <tr>
                                        <th>@lang('Deal')</th>
                                        <th>@lang('Deal Name')</th>
                                        <th>@lang('Stage')</th>
                                        <th>@lang('Ownership')</th>
                                        <th>@lang('Updated On')</th>
                                        <th class="text-right">@lang('Deal Value')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Clossing Date')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>                    
                                    @php $count =1; $total_deal=0; $total_amount=0; @endphp
                                    @foreach($delivery as $value)
                                    @php $total_deal += 1; @endphp
                                    @if((@$value->estimated_close_date <=  Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage ==2 || $value->stage ==3))
                                        <tr style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr>
                                    @endif
                                    <td><a href="{{ url('get-url-deal-track/'.$value->code) }}" target="_blank">{{@$value->code}}</a></td>
                                        <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></td>
                                        <td>
                                            @if($value->stage==1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                                            @if($value->stage==2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                                            @if($value->stage==3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                                            @if($value->stage==4) 
                                            <?php
                                            $data = App\SysHelper::deal_track_status3($value->receivables,$value->delivery,$value->invoice,$value->purchease,$value->sales,$value->accounts);
                                            ?>
                                            {!! $data !!}
                                            @endif
                                            @if($value->stage==5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                                            @if($value->stage==6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif
                                        </td>
                                        <td>{{@$value->ownername->full_name}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->updated_at))}}</td>
                                        <td class="text-right">

                                            <?php $vat =@App\SysHelper::get_deal_vat_amount($value->id, $value->quote_id); ?>

                                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value+$vat); @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp AED
                                        </td>
                                        <td>{{date('d-M-Y', strtotime(@$value->created_at))}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->estimated_close_date))}}</td>
                                        <td>
                                            <a class="btn-sm btn-info" target="_blank" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>              
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>{{ $total_deal }}</th>
                                        <th></th><th></th><th></th><th></th>
                                        <th class="text-right pr-1">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}} AED</th>
                                        <th></th><th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="tab4" class="tab-pane fade">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                                <thead>                                
                                    <tr>
                                        <th>@lang('Deal')</th>
                                        <th>@lang('Deal Name')</th>
                                        <th>@lang('Stage')</th>
                                        <th>@lang('Ownership')</th>
                                        <th>@lang('Updated On')</th>
                                        <th class="text-right">@lang('Deal Value')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Clossing Date')</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>                    
                                    @php $count =1; $total_deal=0; $total_amount=0; @endphp
                                    @foreach($receivables as $value)
                                    @php $total_deal += 1; @endphp
                                    @if((@$value->estimated_close_date <=  Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage ==2 || $value->stage ==3))
                                        <tr style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr>
                                    @endif
                                    <td><a href="{{ url('get-url-deal-track/'.$value->code) }}" target="_blank">{{@$value->code}}</a></td>
                                        <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></td>
                                        <td>
                                            @if($value->stage==1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                                            @if($value->stage==2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                                            @if($value->stage==3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                                            @if($value->stage==4) 
                                            <?php
                                            $data = App\SysHelper::deal_track_status3($value->receivables,$value->delivery,$value->invoice,$value->purchease,$value->sales,$value->accounts);
                                            ?>
                                            {!! $data !!}
                                            @endif
                                            @if($value->stage==5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                                            @if($value->stage==6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif
                                        </td>
                                        <td>{{@$value->ownername->full_name}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->updated_at))}}</td>
                                        <td class="text-right">
                                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp AED
                                        </td>
                                        <td>{{date('d-M-Y', strtotime(@$value->created_at))}}</td>
                                        <td>{{date('d-M-Y', strtotime(@$value->estimated_close_date))}}</td>
                                        <td>
                                            <a class="btn-sm btn-info" target="_blank" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>              
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>{{ $total_deal }}</th>
                                        <th></th><th></th><th></th><th></th>
                                        <th class="text-right pr-1">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}} AED</th>
                                        <th></th><th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="tab5" class="tab-pane fade">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                                    <tr>
                                        <td colspan="6">
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
                                        <th>@lang('Sr No')</th>
                                        <th>@lang('Deal ID')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Customer Name')</th>
                                        <th>@lang('Contact Person')</th>
                                        <th>@lang('Mobile No')</th>
                                        <th>@lang('Start Date')</th>
                                        <th>@lang('End Date')</th>
                                        <th>@lang('Invoicing')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Sales Person')</th>
                                        <th>@lang('Description')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if(count($amcdata)>0)
                                    @foreach($amcdata as $value)
                                    <tr @if(@$value->is_delete == 1) class="bg-dark" @endif>
                                        <td>{{@$value->id}}</td>
                                        <td><a href="{{ url('get-url-deal-track/'.$value->deal_code->code) }}" target="_blank">{{@$value->deal_code->code}}</a></td>
                                        <td>{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                        <td>{{@$value->custname->name}}</td>
                                        <td>{{@$value->contact_person}}</td>
                                        <td>{{@$value->mobile_no}}</td>
                                        <td>{{date('d/m/Y', strtotime(@$value->start_date))}}</td>
                                        <td>{{date('d/m/Y', strtotime(@$value->end_date))}}</td>
                                        <td>{{@$value->invoice}}</td>
                                        <td>{{@$value->amount}}</td>
                                        <td>{{@$value->salesperson->full_name}}</td>
                                        <td>{{@$value->description}}</td>
                                    </tr>                                        
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab6" class="tab-pane fade">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                                    <tr>
                                        <td colspan="6">
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
                                        <th>@lang('PS ID')</th>
                                        <th>@lang('Deal No')</th>
                                        <th>@lang('Date ')</th>
                                        <th>@lang('Customer Name')</th>
                                        <th>@lang('Contact Person')</th>
                                        <th>@lang('Mobile No')</th>
                                        <th>@lang('Location of Work')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Sales Person')</th>
                                        <th>@lang('Description')</th>
                                    </tr>
                                </thead>
        
                                <tbody>
                                    @if(count($support)>0)        
                                    @foreach($support as $value)
                                    <tr>
                                        <td>{{@$value->id}}</td>
                                        <!-- <td>
                                                <a class="text-dark" href="{{url('crm-deals/'.$value->id.'/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></a>
                                            </td> -->
                                        <td><a href="{{ url('get-url-deal-track/'.$value->deal_code->code) }}" target="_blank">{{@$value->deal_code->code}}</a></td>
                                        <td>{{date('d-M-Y', strtotime(@$value->date))}}</td>
                                        <td>{{@$value->custname->name}} <input type="hidden" id="list_custname_{{ $value->id }}" value="{{@$value->custname->name}}" /></td>
                                        <td>{{@$value->contact_person}} <input type="hidden" id="list_contact_person_{{ $value->id }}" value="{{@$value->contact_person}}" /></td>
                                        <td>{{@$value->mobile}} <input type="hidden" id="list_mobile_{{ $value->id }}" value="{{@$value->mobile}}" /></td>
                                        <td>{{@$value->location_of_work}} <input type="hidden" id="list_location_of_work_{{ $value->id }}" value="{{@$value->location_of_work}}" /></td>
                                        <td>{{@$value->amount}}</td>
                                        <td>{{@$value->ownername->full_name}}</td>
                                        <td>{{ @$value->deal_description }}</td>
                                    </tr>        
                                    @endforeach
                                    @endif        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>

        <div class="card p-4 mb-3" style="display: none;">
            <div class="d-flex">
                <div class="profile__img mr-4">
                    @if (file_exists(@$custDetails->staff_photo))
                        <img src="{{ asset($custDetails->staff_photo) }}" alt="">
                    @else
                        <img src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="">
                    @endif
                </div>
                <div class="text__wrap pt-2">
                    <h4 class="font-weight-bold">
                        {{--  1-Green, 2-Orange, 3-Red, 4-Black  --}}
                        <span class="badge badge-danger"
                            @if (@$custDetails->type == 1) style="background: #228c22;" @endif
                            @if (@$custDetails->type == 2) style="background: #FFA500;" @endif
                            @if (@$custDetails->type == 3) style="background: #FF0000;" @endif
                            @if (@$custDetails->type == 4) style="background: #000000;" @endif>
                            @if (isset($custDetails))
                                {{ @$custDetails->name }}
                            @endif
                        </span>

                    </h4>
                    <p class="mb-1 text-muted">Customer Code : <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->code }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted">Contact Person : <span class="badge badge-danger">
                            @if (isset($custDetails))
                                {{ @$custDetails->contcat_person }}
                            @endif
                        </span></p>
                    <div class="d-sm-flex">
                        <p class="mb-1 pr-3 text-muted">Contact Number: <span class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->contcat_number }}
                                @endif
                            </span>, </p>
                        <p class="mb-1 text-muted">Mobile: <span class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->mobile }}
                                @endif
                            </span></p>
                    </div>

                    <p class="mb-1 text-muted">Mail : <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->email }}
                            @endif
                        </span></p>

                    <p class="mb-1 text-muted">Address: <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted">Address 2: <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address2 }}
                            @endif
                        </span></p>
                </div>
            </div>
        </div>

    </div>


    {{--  <section class="sms-breadcrumb mb-20 white-box top-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" style="text-align: right;">
                    <div class="top-2-text top-2-text-last"><span>{{ $custDetails->salesperson->full_name }}</span><br />Created By</div>
                    <div class="top-2-text"><b>Sundry Debtors <input type="hidden" value="2" name="sundry_creditors">
                        </b><br />Customer Type</div>
                    <div class="top-2-text">
                        <b>{{ $custDetails->code }}</b><br />Customer
                        Code</div>
                </div>
            </div>
        </div>
    </section>  --}}

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

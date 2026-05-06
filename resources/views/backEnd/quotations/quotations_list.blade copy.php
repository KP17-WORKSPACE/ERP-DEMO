@extends('backEnd.masterpage')
@section('mainContent')

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Quotations</h2>
                <span class="page-label">Home - Quotations</span>
            </div>
            <div>
                {{--  <a href="{{ url('crm-deals') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Quotation</a>  --}}
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
                                <th>@lang('Doc Date')</th>
                                <th>@lang('QTN No')</th>
                                <th>@lang('Deal Number')</th>
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Salesman Name')</th>
                                <th class="text-right">@lang('Amount')</th>
                                <th></th>
                            </tr>
                        </thead>
        
                        <tbody>
                            
                            @php $count =1; $total_deal=0; $total_amount=0; @endphp
                            @foreach($quotations as $value)
                            @php $total_deal += 1; @endphp
                            <tr>
                                <td>{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                <td><a class="text-dark" href="{{url('crm-quote/'.$value->id.'/download/'.$value->quote_id)}}">{{ @$value->deal_code->code }}</a></td>
                                <td><a class="text-dark" href="{{url('crm-deals/'.$value->id.'/view')}}">{{ @$value->deal_code->code }}</a></td>
                                <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></td>
                                
                                <td>{{@$value->ownername->full_name}}</td>
                                <td class="text-right text-primary">{{@App\SysHelper::currancy_format_deal($value->deal_value,$value->company_id)}}
                                    @php $total_amount += $value->deal_value; @endphp
                                </td>
                                <td>
                                    <a class="btn-sm btn-primary" href="{{url('crm-quote/'.$value->id.'/download/'.$value->quote_id)}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    <a class="btn-sm btn-info" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                      
                    @endforeach
        
                        </tbody>
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-right pr-1">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}}</th>
                                <th></th>
                            </tr>                            
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        
        </div>

    {{--  <section class="sms-breadcrumb mb-20 white-box">
        <div class="container-fluid">
            <div class="row" style="float: left;">
                <h1>@lang('Quotations')</h1>
            </div>
            <div class="row" style="float: right;">
                <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home"
                        aria-hidden="true"></i> Home</a>
                <a href="{{ url('quotations/create') }}" class="top-btn-r"><i class="far fa fa-plus"
                        aria-hidden="true"></i> New</a>
                <a href="{{ url('quotations') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i>
                    View</a>
                <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh"
                        aria-hidden="true"></i> Refresh</a>
            </div>
        </div>
    </section>  --}}
@endsection

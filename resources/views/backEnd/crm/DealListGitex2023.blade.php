@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Deal List Gitex 2023</h2>
            <span class="page-label">Home - Deal List Gitex 2023</span>
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
                        <th>@lang('Deal Name')</th>
                        <th>@lang('Company')</th>
                        <th>@lang('Stage')</th>
                        <th>@lang('Ownership')</th>
                        <th class="text-right">@lang('Deal Value')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Clossing Date')</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    
                    @php $count =1; $total_deal=0; $total_amount=0; @endphp
                    @foreach($deals as $value)
                    @php $total_deal += 1; @endphp

                    @if((@$value->estimated_close_date <=  Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage ==2 || $value->stage ==3))
                        <tr style="background-color:#ffebeb !important; color:#ff0000;">
                    @else
                        <tr>
                    @endif
                        <td><a class="text-dark" href="{{url('crm-deals/'.$value->id.'/view')}}">{{@$value->id}}</a></td>
                        <td><a class="text-dark" href="{{url('crm-deals/'.$value->id.'/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></a></td>
                        <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></td>
                        <td>
                            @if($value->stage==1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                            @if($value->stage==2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                            @if($value->stage==3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                            @if($value->stage==4) 
                            <?php
                            $data = App\SysHelper::deal_track_status($value->id);
                            $color = "danger";
                            if($data=="Pending"){
                                $color = "warning";
                            } else if($data=="completed"){
                                $color = "primary";                                            
                            } else if($data=="OnProcess"){
                                $color = "info";                                            
                            } else{
                                $color = "danger";
                            }
                            ?>
                            @if($data!="completed")
                            <span class="primary btn-badge py-1 px-2">Won</span>@endif

                            @if(App\SysHelper::set_track($value->id)==1)
                                <a class="{{ $color }} btn-badge py-1 px-2" href="{{url('crm-deal-track/'.$value->id.'/view')}}" title="Click to Fullfill">
                                @if($data=="Fulfill")<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>@endif {{ $data }} </a>
                            @endif
                                
                            @endif
                            @if($value->stage==5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                            @if($value->stage==6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif
                        </td>
                        <td>{{@$value->ownername->full_name}}</td>
                        <td class="text-right">
                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                            @php $total_amount += $aed; @endphp AED
                        </td>
                        <td>{{date('d-M-Y', strtotime(@$value->created_at))}}</td>
                        <td>{{date('d-M-Y', strtotime(@$value->estimated_close_date))}}</td>
                        <td>

                            <a class="btn-sm btn-info" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a class="btn-sm btn-primary" href="{{url('crm-deals/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            
                            @if(Auth::user()->role_id == 1)
                            <a class="btn-sm btn-danger" href="{{url('crm-deals/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @endif
                        </td>
                    </tr>
              
            @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ $total_deal }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-right pr-1">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}} AED</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php /*
                    <tr>
                        <th colspan="8" style="text-align: center;">
                            {{ $deals->appends(request()->query())->links() }}
                    </tr>
                    <style>
                        .dataTables_length{display: none;}
                        .dataTables_paginate{display: none;}
                    </style>
                    */ ?>
                </tfoot>
            </table>
        </div>
    </div>
</div>

</div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
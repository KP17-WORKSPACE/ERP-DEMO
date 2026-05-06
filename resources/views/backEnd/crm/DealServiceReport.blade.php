@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">@if($type=='service')Service @else AMC @endif Report @if ($id=="amc_revenue" || $id=="service_revenue")
                (Revenue)
            @else
                (Forcast)
            @endif</h2>
            <span class="page-label">Home - @if($type=='service')Service @else AMC @endif Report</span>
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
                                            <th>@lang('Deal Name')</th>
                                            <th>Company</th>
                                            <th>Owner</th>
                                            <th>Closing Date</th>
                                            <th class="text-right">@lang('Deal Value')</th>
                                            <th class="text-right">Deal Detail</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $deal_value1=0; $deal_count=0; ?>
                                        @if(count($data)>0)
                                        @foreach($data as $value)
                                <tr>
                                    <td><a class="text-dark"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></a></td>
                                    <td><a class="text-dark"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></a></td>
                                    <td><a class="text-dark"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->ownername->full_name}}</div></a></td>
                                    <td><a class="text-dark">{{date('d-M-Y', strtotime(@$value->estimated_close_date))}}</a></td>

                                    <td class="text-right">
                                        
                                        @php $deal_value= ($value->qty*$value->price) - ($value->qty*$value->discount); @endphp

                                        @if($id=='service_revenue')
                                        
                                        @if($value->source=="Fulfillment")
                                            @php $tot = App\SysHelper::get_aed_amount($value->deal_currency,($deal_value*20/100)); @endphp
                                        @elseif(in_array($value->cust_id, [2568,4258,4382,5322,7347,8144,8145,8146,3711,4089,8142]))
                                            @php $tot = App\SysHelper::get_aed_amount($value->deal_currency,($deal_value*20/100)); @endphp
                                        @elseif(in_array($value->cust_id, [8866]))
                                            @php $tot = App\SysHelper::get_aed_amount($value->deal_currency,($deal_value*30/100)); @endphp
                                        @else
                                            @php $tot = App\SysHelper::get_aed_amount($value->deal_currency,$deal_value); @endphp
                                        @endif
                                        
                                        @else
                                        @php $tot = App\SysHelper::get_aed_amount($value->deal_currency,(@$value->qty * @$value->price) - (@$value->discount * @$value->qty)); @endphp
                                        @endif
                                        {{ @App\SysHelper::com_curr_format($tot, 2, '.', ',') }}
                                        @php $deal_value1 += $tot @endphp
                                    </td>
                                    <td class="text-right">
                                            <a target="_blank" href="{{url('crm-deals/'.$value->deal_id.'/view')}}" class="btn btn-info pt-0 pb-0">View</a>
                                    </td>
                                                                        
                                    
                                </tr>
                                  
                                @endforeach
                                @endif
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_value1, 2, '.', ',') }}</th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
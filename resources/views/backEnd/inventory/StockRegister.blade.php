@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Stock Register</h2>
            <span class="page-label">Home - Stock Register</span>
        </div>
        <div>
            <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>
            {{--  <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>  --}}
        </div>
    </div>
    <div class="card p-4 mb-2">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-register', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <div class="row">
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('To Date')</label>
                                <input class="form-control" id="to_date" type="date" name="to_date" value="{{ @$to_date }}" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 mt-4">
                    <div class="input-effect" id="sectionSubgroupDiv">
                        <button class="btn btn-primary">
                            <span class="ti-search"></span>
                            @lang('Search')
                        </button>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>                        
                        <tr>
                            <th>@lang('Part Number')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Brand')</th>
                            <th>@lang('Bal Qty')</th>
                            <th class="text-right">@lang('Avg Rate')</th>
                            <th class="text-right">@lang('Amount')</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $count =1; $total_qty=0; $total_price=0; $total_value=0; $total_amount=0; @endphp
                                @foreach($stocklist as $value)
                                <tr>
                                    <td>{{@$value->part_number}}</td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{$value->description}}</div></td>
                                    <td>{{$value->brand}}</td>
                                    <td>{{$value->qty_bal}}</td>
                                    <td class="text-right">{{number_format($value->avg_price, 2, '.', ',')}}</td>
                                    <td class="text-right">{{number_format(($value->avg_price * $value->qty_bal), 2, '.', ',')}}</td>
                                    @php
                                    $total_qty += $value->qty_bal;
                                    $total_price += $value->avg_price;
                                    $total_amount += ($value->avg_price * $value->qty_bal);
                                    @endphp
                                </tr>
                                @endforeach                            
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ $total_qty }}</th>
                            <th class="text-right"></th>
                            <th class="text-right">{{ number_format($total_amount, 2, '.', ',') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


@endsection
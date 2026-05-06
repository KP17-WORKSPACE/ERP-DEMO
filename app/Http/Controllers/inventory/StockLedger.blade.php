@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Stock Ledger</h2>
            <span class="page-label">Home - Stock Ledger</span>
        </div>
        <div>
            {{--  <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>  --}}
            {{--  <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>  --}}
        </div>
    </div>
    <div class="card p-4 mb-2">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-ledger', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <div class="row">
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('From Date')</label>
                                <input class="form-control" id="from_date" type="date" name="from_date" value="{{ @$from_date }}" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <th>@lang('Doc Date')</th>
                            <th>@lang('Doc No')</th>
                            <th>@lang('Ref Doc No')</th>
                            <th>@lang('Account Name')</th>
                            <th>@lang('Part No')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('In Qty')</th>
                            <th>@lang('In Rate')</th>
                            <th>@lang('Out Qty')</th>
                            <th>@lang('Out Rate')</th>
                            <th>@lang('Bal Qty')</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $count =1; $total_qty_in=0; $total_price_in=0; $total_qty_out=0; $total_price_out=0; $total_value=0; @endphp
                                @foreach($stocklist as $value)
                                <tr>
                                    <td>{{@$value->doc_date}}</td>
                                    <td>{{@$value->doc_number}}</td>
                                    <td>{{@$value->refno}}</td>
                                    <td>{{@$value->accountname->account_name}}</td>
                                    <td>{{@$value->productdet->part_number}}</td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{$value->productdet->description}}</div></td>
                                    <td>{{$value->qty_in}}</td>
                                    <td>{{$value->price_in}}</td>
                                    <td>{{$value->qty_out}}</td>
                                    <td>{{$value->price_out}}</td>
                                    <td>{{$value->bal_qty}}</td>
                                    @php
                                    $total_qty_in += $value->qty_in;
                                    $total_price_in += $value->price_in;
                                    $total_qty_out += $value->qty_out;
                                    $total_price_out += $value->price_out;
                                    $total_value += $value->price_in*$value->qty_in;
                                    @endphp
                                </tr>
                                @endforeach                            
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('Doc Date')</th>
                            <th>@lang('Doc No')</th>
                            <th>@lang('Ref Doc No')</th>
                            <th>@lang('Account Name')</th>
                            <th>@lang('Part No')</th>
                            <th>@lang('Description')</th>
                            <th>{{ $total_qty_in }}</th>
                            <th>{{ $total_price_in }}</th>
                            <th>{{ $total_qty_out }}</th>
                            <th>{{ $total_price_out }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


@endsection
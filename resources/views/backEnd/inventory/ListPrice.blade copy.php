@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">List Price</h2>
            <span class="page-label">Home - List Price</span>
        </div>
        <div>
            <a href="{{ url('stock-register') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Stock Register</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">

        
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'list-price', 'method' => 'POST', 'id' => 'stock-register-search']) }}
            <div class="row">
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Part Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="part_number" value="{{ $r_part_number }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Brand</label>
                    <select class="form-control js-example-basic-single" name="brand">
                        <option value="">-Select-</option>
                        @foreach ($brand as $value)
                        <option value="{{ @$value->id }}" @if($r_brand == $value->id) selected @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Category</label>
                    <select class="form-control js-example-basic-single" name="category">
                        <option value="">-Select-</option>
                        @foreach ($category as $value)
                        <option value="{{ @$value->id }}" @if($r_category == $value->id) selected @endif>{{ @$value->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Sub Category</label>
                    <select class="form-control js-example-basic-single" name="sub_category">
                        <option value="">-Select-</option>
                        @foreach ($sub_category as $value)
                        <option value="{{ @$value->id }}" @if($r_sub_category == $value->id) selected @endif>{{ @$value->sub_category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Qty</label>
                    <select class="form-control js-example-basic-single" name="qty">
                        <option value="">-Select-</option>
                        <option value="positive" @if($r_qty == "positive") selected @endif>Positive</option>
                        <option value="negative" @if($r_qty == "negative") selected @endif>Negative</option>
                        <option value="zero" @if($r_qty == "zero") selected @endif>Zero</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">&nbsp;<br />
                    <button type="submit" class="btn btn-primary mt-1" id="btnSubmit">Filter</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <div class="card p-4 mb-2">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'list-price', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                            <th>@lang('Category')</th>
                            <th>@lang('Bal Qty')</th>
                            @if(Auth::user()->role_id != 5)
                                <th class="text-right">@lang('Avg Price')</th>
                                <th class="text-right">@lang('Last Purchase Price')</th>
                                <th class="text-right">@lang('List Price')</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        
                        @php $count =1; $total_qty=0; $total_price=0; $total_value=0; $total_amount=0; $total_lp=0; @endphp

                            <?php 
                            if($r_qty == "zero") { $stocklist2 = $stocklist->where('balance_qty',0); }
                            else if($r_qty == "positive") { $stocklist2 = $stocklist->where('balance_qty','>',0); }
                            else if($r_qty == "negative") { $stocklist2 = $stocklist->where('balance_qty','<',0); }
                            else { $stocklist2 = $stocklist; }
                            ?>

                                @foreach($stocklist2 as $value)                                
                                <tr>
                                    <td>
                                    <a href="{{ url('stock-ledger/'.$value->part_number) }}" target="_blank">
                                        {{@$value->part_number}}</a>
                                    </td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{$value->description}}</div></td>
                                    <td>{{$value->brand}}</td>
                                    <td>{{$value->categoryname}} - {{$value->subcategoryname}}</td>

                                    @php
                                    $balance_qty = $value->balance_qty;
                                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');
                                    @endphp
                                    
                                    <td>{{$balance_qty}}</td>

                                    @if (Auth::user()->role_id != 5)
                                    <td class="text-right">{{@App\SysHelper::com_curr_format($value->avg_price, 2, '.', ',')}}</td>
                                    
                                    <td class="text-right">{{ @App\SysHelper::com_curr_format($value->lp_price, 2, '.', ',') }}</td>
                                    <td class="text-right">

                                        @if($value->avg_price > $value->lp_price)
                                            {{ @App\SysHelper::com_curr_format($value->avg_price*103/100, 2, '.', ',') }}
                                            @php $total_lp += $value->avg_price*103/100; @endphp
                                        @else
                                            {{ @App\SysHelper::com_curr_format($value->lp_price*103/100, 2, '.', ',') }}
                                            @php $total_lp += $value->lp_price*103/100; @endphp
                                        @endif

                                    </td>

                                    @endif

                                    @php
                                    $total_qty += $balance_qty;
                                    $total_price += $value->avg_price;
                                    $total_amount += $value->lp_price;
                                    @endphp
                                </tr>
                                @endforeach                            
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ $total_qty }}</th>
                            @if (Auth::user()->role_id != 5)
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_price, 2, '.', ',') }}</th>
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</th>
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_lp, 2, '.', ',') }}</th>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


@endsection
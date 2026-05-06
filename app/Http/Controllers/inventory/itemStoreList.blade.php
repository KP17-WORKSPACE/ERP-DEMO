@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Opening Stock</h2>
            <span class="page-label">Home - Opening Stock</span>
        </div>
        <div>
            <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>            
            {{--  <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>  --}}
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>                        
                        <tr>
                            <th>@lang('lang.sl') </th>
                            <th>@lang('Doc No')</th>
                            <th>@lang('Doc Date')</th>
                            <th>@lang('Bill Date')</th>
                            <th>@lang('Narration')</th>
                            <th>@lang('lang.action')</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $count =1; @endphp
                                @foreach($ios as $value)
                                <tr>
                                    <td >{{@$count++}}</td>
                                    <td >{{@$value->doc_number}}</td>
                                    <td >{{date('jS M, Y', strtotime(@$value->doc_date))}}</td>
                                    <td >{{date('jS M, Y', strtotime(@$value->bill_date))}}</td>
                                    <td >{{@$value->narration}}</td>
                                    <td >
                                        <a class="btn-sm btn-primary" href="{{url('item-store/'.$value->id.'/edit')}}">View & Edit</a>
                                        <a class="btn-sm btn-danger" href="{{url('item-store/'.$value->doc_number.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                    </td>
                                </tr>
                                @endforeach                            
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
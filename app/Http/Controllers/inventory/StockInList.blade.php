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
<style>
    .dt-buttons{display: none !important;}
    table.dataTable{padding: 0px !important;}
</style>

<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Stock In List')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home" aria-hidden="true"></i> Home</a>
            <a href="{{ url('stock-in') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('stock-in/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>

<section class="admin-visitor-area ml-2 mr-2">
    <div class="container-fluid p-0">
                
        <div class="row">

            <div class="col-lg-12">

                <div class="row">
                    <div class="col-lg-12">
                        
                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">

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
                                    <th>@lang('lang.sl') </th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Part Number')</th>
                                    <th>@lang('Qty')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('INV')</th>
                                    <th>@lang('DO')</th>
                                    <th>@lang('Document')</th>
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count =1; @endphp
                                @foreach($ios as $value)
                                <tr>
                                    <td>{{@$count++}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td>{{@$value->part_number}}</td>
                                    <td>{{@$value->qty}}</td>
                                    <td>{{@$value->customername->name}}</td>
                                    <td>{{@$value->inv}}</td>
                                    <td>{{@$value->do}}</td>
                                    <td>
                                        @if($value->file!="")
                                            <a target="_blank" href="../public/uploads/stock_in_out_file/{{@$value->file}}">Download Doc</a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"> 
                                                @if(in_array(359, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" href="{{url('stock-in/'.$value->id.'/edit')}}">@lang('View & Edit')</a>
                                                @endif
                                                
                                                {{-- @if(in_array(358, @$module_links) || Auth::user()->role_id == 1)
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deletequotations{{@$value->id}}"  href="#">@lang('lang.delete') </a>
                                                @endif --}}

                                            </div>
                                        </div>
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
        </div>
    </div>
</section>
@endsection
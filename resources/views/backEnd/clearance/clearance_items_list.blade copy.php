@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp
    <?php try { ?>
	
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Customs Clearance</h2>
            <span class="page-label">Home - Customs Clearance</span>
        </div>
        <div>
            <a href="{{ url('clearance/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
            <a href="{{ url('clearance') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'clearance', 'method' => 'get', 'id' => 'clearance-search']) }}
            <div class="row">

                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Documents Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="documents_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Invoice No</label>
                    <input class="form-control" type="text" autocomplete="off" name="invoice_no" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Invoice Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="invoice_date" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Bill To</label>
                    <input class="form-control" type="text" autocomplete="off" name="bill_to" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Ship To</label>
                    <input class="form-control" type="text" autocomplete="off" name="ship_to" value="">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning mr-2" id="btnSubmit">Clear</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
		
            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">

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
                        <th>@lang('Doc No')</th>
                        <th>@lang('Invoice No')</th>
                        <th>@lang('Invoice Date')</th>
                        <th>@lang('Deal Id')</th>
                        <th>@lang('Bill To')</th>
                        <th>@lang('Ship To')</th>
                        <th>@lang('Customer Bill Type')</th>
                        <th style="width: 140px;">@lang('lang.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @php $count =1; @endphp
                    @foreach($clearance as $value)
                    <tr>
                        <td>{{@$count++}}</td>
                        <td><a href="{{url('get-url-clearance/'.$value->doc_no)}}" target="_blank">{{@$value->doc_no}}</a></td>
                        <td><a href="{{url('get-url-sales-invoice/'.$value->invoice_no)}}" target="_blank">{{@$value->invoice_no}}</a></td>
                        <td>{{date('d-m-Y', strtotime(@$value->invoice_date))}}</td>
                        <?php $deal_code = @App\SysHelper::get_code_from_dealid($value->deal_id); ?>
                        <td><a href="{{url('get-url-deal/'.$deal_code)}}" target="_blank">{{$deal_code}}</a></td>
                        <td>{{@$value->bill_to}}</td>
                        <td>{{@$value->ship_to}}</td>
                        <td>{{@$value->customer_bill_type}}</td>
                        <td>
                            <a class="p-0 pl-2 pr-2 btn btn-info btn-xs text-white" title="Download PDF" href="{{url('clearance/'.$value->id.'/download')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                            <a class="p-0 pl-2 pr-2 btn btn-success btn-xs text-white" title="Preview" href="{{url('clearance/'.$value->id.'/preview')}}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            <a class="p-0 pl-2 pr-2 btn btn-danger btn-xs text-white" title="View & Edit" href="{{url('clearance/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
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
                <footer>
                    <tr>
                        <td colspan="6">
                            {{ $clearance->appends(request()->input())->links() }}
                        </td>
                    </tr>
                </footer>
            </table>
		
        </div>
    </div>
</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<style>
    .dt-buttons{display: none !important;}
    table.dataTable{padding: 0px !important;}
</style>

@endsection
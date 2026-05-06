@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Pre-Sales Support List</h2>
            <span class="page-label">Home - Pre-Sales Support List</span>
        </div>
        <div>
            <a href="#" type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalService"><i class="fa fa-plus"></i> New Pre-Sales Support</a>
        </div>
        <div style="display: none;">
            <a href="{{ url('crm-leads') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Lead</a>            
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filter By 
            </button>
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" onclick="sort(1)">Today</a>
                <a class="dropdown-item" href="#" onclick="sort(2)">This Week</a>
                <a class="dropdown-item" href="#" onclick="sort(3)">Last Week</a>
                <a class="dropdown-item" href="#" onclick="sort(4)">This Month</a>
                <a class="dropdown-item" href="#" onclick="sort(5)">Last Month</a>
                <a class="dropdown-item" href="#" onclick="sort(6)">Last 6 Month</a>
                <a class="dropdown-item" href="#" onclick="sort(7)">This Year</a>
                <a class="dropdown-item" href="#" onclick="sort(8)">Last Year</a>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/show', 'method' => 'POST', 'id' => 'crm-leads-search']) }}
                    <input type="hidden" name="sort_id" id="sort_id" value="1" />
                    <button type="submit" id="btn_sort" style="display: none;"></button>
                {{ Form::close() }}
            </div>
            <script>
                function sort(id) {
                    $("#sort_id").val(id);
                    $("#btn_sort").click();
                }
            </script>
            
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
                                            <th>@lang('ID')</th>
                                            <th>@lang('Deal ID')</th>
                                            <th>@lang('Deal Name')</th>
                                            @if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20)
                                            <th>@lang('Deal Value')</th>
                                            @endif
                                            <th>@lang('Status')</th>
                                            <th>@lang('Ownership')</th>
                                            <th>@lang('Date')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                        @foreach($service as $value)
                                <tr>
                                    <td>{{@$value->id}}</td>
                                    <td>{{@$value->deal_id}}</td>
                                    <td>
                                        <a class="text-dark" href="{{url('crm-deal-service/'.$value->id.'/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                            @if($value->deal_id==0)
                                                {{@$value->subject}}
                                            @else
                                                {{@$value->deal_name}}
                                            @endif                                            
                                            </div></a>

                                    </td>
                                    @if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20)
                                    <td>
                                        @if($value->deal_id==0)
                                        
                                        @else
                                        {{ App\SysHelper::currancy_format_deal($value->deal_value,$value->company_id) }}
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        @if($value->status==1) <span class="warning btn-badge py-1 px-2">New</span>@endif
                                        @if($value->status==2) <span class="info btn-badge py-1 px-2">Open</span> @endif
                                        @if($value->status==3) <span class="success btn-badge py-1 px-2">Close</span> @endif
                                        @if($value->status==4) <span class="rejected btn-badge py-1 px-2">Cancel</span> @endif
                                    </td>
                                    <td>
                                        @if($value->deal_id==0)
                                        {{@$value->createdby->full_name}}
                                        @else
                                        {{@$value->ownername->full_name}}
                                        @endif
                                    </td>
                                    <td>{{date('d-M-Y', strtotime(@$value->created_at))}}</td>
                                    <td>

                                        <a class="btn-sm btn-info" href="{{url('crm-deal-service/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        {{--  <a class="btn-sm btn-primary" href="{{url('crm-deal-service/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>  --}}

                                        @if(Auth::user()->role_id == 1)
                                        <a class="btn-sm btn-danger" href="{{url('crm-deal-service/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                  
                                @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<!-- Modal Service-->
    <div class="modal fade" id="ModalService" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Support</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Subject</label>
                                <input type="text" class="form-control" name="subject" id="subject" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="service_deal_id" id="service_deal_id" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="comments" id="comments" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" id="user_id" multiple>
                                    @foreach ($support_person as $value)
                                        <option value="{{ @$value->user_id }}" >{{ @$value->full_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Pre-Sales Support</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Service-->


{{--  <section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Lead List')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-leads') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('crm-leads/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>  --}}

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
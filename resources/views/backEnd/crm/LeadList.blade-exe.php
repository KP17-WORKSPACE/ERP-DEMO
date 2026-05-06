@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Lead List</h2>
            <span class="page-label">Home - Lead List</span>
        </div>
        <div>
            <table>
                <tr>
                    <td>
            
                        {{--  <a href="{{ url('crm-leads') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Lead</a>  --}}
                        <a type="button" data-toggle="modal" data-target="#addlead" class="btn btn-info"><i class="fa fa-plus"></i> New Lead</a>

            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filter By {{ $filter_by }}
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
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/show', 'method' => 'get', 'id' => 'crm-leads-search']) }}
                    <input type="hidden" name="sort_id" id="sort_id" value="1" />
                    <button type="submit" id="btn_sort" style="display: none;"></button>
                {{ Form::close() }}
            </div>
            <script>
                function sort(id) {
                    $("#sort_id").val(id);
                    $("#btn_sort").click();
                }
                function company() {
                    $("#btn_company").click();
                }
            </script></td>
        </tr>
    </table>
            
        </div>
    </div>
    <div class="collapse" id="collapseExample">
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/show', 'method' => 'get', 'id' => 'crm-leads-search']) }}
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Lead Id</label>
                                <input class="form-control" id="lead_id" type="text" autocomplete="off" name="lead_id" value="{{ $ctrl_lead_id }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Company Name</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                    <option value="{{ @$value->id }}" @if($ctrl_cust_id ==$value->id) selected @endif>{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 35)
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}" @if($ctrl_owner ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if(Auth::user()->role_id == 13) {{--  KSA Sales Department Head  --}}
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}" @if($ctrl_owner ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Brand</label>
                                <select class="form-control js-example-basic-single" name="brand_id" id="brand_id">
                                    <option value="">-Select-</option>
                                    @foreach ($brand as $value)
                                    <option value="{{ @$value->title }}" @if($ctrl_brand ==$value->title) selected @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Form Date</label>
                                <input class="form-control datepicker" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">To Date</label>
                                <input class="form-control" id="date2" type="date" autocomplete="off" name="date2" value="{{ $ctrl_date2 }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Type</label>
                                <select class="form-control" name="isproject_id" id="isproject_id">
                                    <option value="">-Select-</option>
                                    <option value="1" @if(@$ctrl_isproject == "1") selected @endif >Project</option>
                                    <option value="2" @if(@$ctrl_isproject == "2") selected @endif >Channel</option>
                                    <option value="3" @if(@$ctrl_isproject == "3") selected @endif >Corporate</option>
                                    <option value="0" @if(@$ctrl_isproject == "0") selected @endif >Lead</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Status</label>
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="" @if($ctrl_status == "") selected @endif >-Select-</option>
                                    <option value="1" @if($ctrl_status == 1) selected @endif >New</option>
                                    <option value="2" @if($ctrl_status == 2) selected @endif >Qualified</option>
                                    <option value="3" @if($ctrl_status == 3) selected @endif >Unqualified</option>
                                    <option value="4" @if($ctrl_status == 4) selected @endif >Converted</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="" class="form-check-label">Source</label>
                                <select class="form-control" name="source_id" id="source_id">
                                    <option value="">-Select-</option>
                                    <option value="Gitex 2023" @if($ctrl_source =="Gitex 2023") selected @endif>Gitex 2023</option>
                                    <option value="Gitex" @if($ctrl_source =="Gitex") selected @endif>Gitex</option>
                                    <option value="Chat" @if($ctrl_source =="Chat") selected @endif>Chat</option>
                                    <option value="Call" @if($ctrl_source =="Call") selected @endif>Call</option>
                                    <option value="Mail" @if($ctrl_source =="Mail") selected @endif>Mail</option>
                                    <option value="Ecommerce" @if($ctrl_source =="Ecommerce") selected @endif>Ecommerce</option>
                                    <option value="Other" @if($ctrl_source =="Other") selected @endif>Other</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                    </div>
    </div>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                @if (count($leads)>0)
                                <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
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
                                            <th>@lang('Lead Number')</th>
                                            <th>@lang('Deal ID')</th>
                                            <th>@lang('Company')</th>
                                            <th>@lang('Lead Refrence')</th>
                                            <th>@lang('Brand')</th>
                                            <th>@lang('Sales Person')</th>
                                            <th>@lang('Stage')</th>
                                            <th>@lang('Source')</th>
                                            <th>@lang('Date')</th>
                                            <th>@lang('Updated On')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                        @foreach($leads as $value)
                                <tr>
                                    <td><a href="{{url('crm-leads/'.$value->id.'/view')}}">{{@$value->lead_code->code}}</a></td>
                                    <td>
                                        <?php try{ ?>                                        
                                        {{ $value->lead_deal_code->code }}
                                        <?php }catch (\Exception $e) { ?> -- <?php } ?>                                    
                                    </td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></td>
                                    <td><a class="text-dark" href="{{url('crm-leads/'.$value->id.'/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->lead_name}}</div></a></td>
                                    <td><div style="width:50px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->tags}}</div></td>
                                    <td>{{@$value->ownername->full_name}}</td>
                                    <td>
                                        @if($value->status==1)
                                            <span class="text-info">New</span>
                                        @endif
                                        @if($value->status==2)
                                            <span class="text-primary">Qualified</span>
                                        @endif
                                        @if($value->status==3)
                                            <span class="text-danger">Unqualified</span>
                                        @endif
                                        @if($value->status==0)
                                            <span class="text-success">Converted</span>
                                            <?php $d = $deal_det->where('id',$value->deal_id)->first(); ?>
                                            
                                            @if($d->stage==1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                                            @if($d->stage==2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                                            @if($d->stage==3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                                            @if($d->stage==4) 
                                            <?php
                                            $data = App\SysHelper::deal_track_status($d->id);
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
                                                    <span class="primary btn-badge py-1 px-2">Won</span>
                                                    <span class="primary btn-badge py-1 px-2">{{ $data }}</span>
                                                @else
                                                    <span class="primary btn-badge py-1 px-2">{{ $data }}</span>
                                                @endif                                
                                            @endif
                                            @if($d->stage==5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                                            @if($d->stage==6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif




                                        @endif
                                    </td>
                                    <td>
                                        {{ $value->source }}
                                    </td>
                                    <td>{{date('d/m/Y', strtotime(@$value->created_at))}}</td>
                                    <td>{{date('d/m/Y', strtotime(@$value->updated_at))}}</td>
                                    <td class="text-right">

                                        <a class="btn-sm btn-info" href="{{url('crm-leads/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-primary" href="{{url('crm-leads/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                        
                                        @if(Auth::user()->role_id == 1)
                                        <a class="btn-sm btn-danger" href="{{url('crm-leads/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                  
                                @endforeach
                                    </tbody>
                                    <?php try{ ?>
                                    <footer>
                                        <tr>
                                            <td colspan="11">
                                                {{ $leads->appends(request()->input())->links() }}
                                            </td>
                                        </tr>
                                    </footer>
                                    <?php }catch (\Exception $e) { } ?>

                                    <?php /*
                                <tfoot>
                                    <tr>
                                        <th colspan="8" style="text-align: center;">
                                            {{ $leads->links() }}</th>
                                    </tr>
                                </tfoot>
                                <style>
                                    .dataTables_length{display: none;}
                                    .dataTables_paginate{display: none;}
                                </style>
                                */ ?>

                                </table>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

<? /*lead from start
----------------------------------------------------------------------- */ ?>

<style>
    .modal-dialog {
        right:0px;
        position: fixed;
        z-index: 9999;
    }
    </style>
<div class="modal fade bd-example-modal-lg" id="addlead" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Lead</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>                    
                @if (isset($edit))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-leads-form']) }}
                @else
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                @endif
                <div class="modal-body">
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Lead Refrence</label>
                                {{--  <select class="form-control js-example-basic-single" name="lead_name" id="lead_name">
                                    <option value="" >Select</option>
                                    @foreach ($product as $value)
                                    <option value="{{ @$value->part_number }}" {{ isset($edit) ? (!empty($edit->lead_name) ? (@$edit->lead_name == @$value->part_number ? 'selected' : '') : '') : '' }}>{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>  --}}
                                <input class="form-control" type="text" name="lead_name" autocomplete="off" id="lead_name" value="{{ isset($edit) ? (!empty(@$edit->lead_name) ? @$edit->lead_name : old('lead_name')) : old('lead_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Customer</label>
                                <a style="float: right; cursor: pointer;" class="text-primary" data-toggle="modal" data-target="#addcompany"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Company</a>
                                <select class="form-control js-example-basic-single" name="company_name" id="company_name" required>
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->company_name) ? (@$edit->company_name == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Contact Person Name</label>
                                <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Designation</label>
                                <input class="form-control" type="text" name="cust_designation" autocomplete="off" id="cust_designation" value="{{ isset($edit) ? (!empty(@$edit->cust_designation) ? @$edit->cust_designation : old('cust_designation')) : old('cust_designation') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Mobile</label>
                                <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Address</label>
                                <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Brand</label>
                                <select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple>
                                    @foreach ($brand as $value)
                                    <option value="{{ @$value->title }}"
                                        @if(isset($edit))
                                            @if(!empty($edit->tags))
                                                @if(str_contains($edit->tags, $value->title)) selected @endif
                                            @endif
                                        @endif >{{ @$value->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                    <option value="">-Select-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Source</label>
                                <select class="form-control" name="source" id="source">
                                    <option value="">-Select-</option>
                                    <option value="Chat" @if(@$edit->source == "Chat") selected @endif >Chat</option>
                                    <option value="Call" @if(@$edit->source == "Call") selected @endif >Call</option>
                                    <option value="Mail" @if(@$edit->source == "Mail") selected @endif  @if(!isset($edit)) selected @endif>Mail</option>
                                    <option value="Website" @if(@$edit->source == "Website") selected @endif >Website</option>
                                    <option value="Gitex 2023" @if(@$edit->source == "Gitex 2023") selected @endif >Gitex 2023</option>
                                    <option value="Gitex" @if(@$edit->source == "Gitex") selected @endif >Gitex</option>
                                    <option value="Ecommerce" @if(@$edit->source == "Ecommerce") selected @endif >Ecommerce</option>
                                    <option value="Other" @if(@$edit->source == "Other") selected @endif >Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="sourcediv" style="display: none;">
                            <div class="form-group">
                                <label for="">Other Source</label>
                                <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o" value="{{ isset($edit) ? (!empty(@$edit->source_o) ? @$edit->source_o : old('source_o')) : old('source_o') }}" style="display: none;" placeholder="Source">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Created By</label>
                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Date</label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($edit) && !empty($edit->date) ){
                                    $value = date('Y-m-d', strtotime(@$edit->date)); }                                        
                                @endphp
                                <input class="form-control" id="date" type="date" autocomplete="off" name="date" value="{{ @$value }}" data-date-format="mm/dd/yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Lead Type</label>
                                <select class="form-control" name="isproject" id="isproject">
                                    <option value="4" @if(@$edit->isproject == "4") selected @endif >Project</option>
                                    <option value="1" @if(@$edit->isproject == "1") selected @endif >Reseller</option>
                                    <option value="2" @if(@$edit->isproject == "2") selected @endif >Enduser</option>
                                    <option value="3" @if(@$edit->isproject == "3") selected @endif >E-Commerce</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1" @if(@$edit->status == 1) selected @endif >New</option>
                                    <option value="2" @if(@$edit->status == 2) selected @endif >Qualified</option>
                                    <option value="3" @if(@$edit->status == 3) selected @endif >Unqualified</option>
                                </select>
                                <textarea class="form-control" name="lost_comments" rows="4" style="display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                <script>
                                    $('#status').on('change', function(e) {
                                        if ($('#status').val() == 3) {
                                            $('#lost_comments').css("display", "block");
                                            $('#lost_comments').prop('required', true);
                                        } else {
                                            $('#lost_comments').css("display", "none");
                                            $('#lost_comments').prop('required', false);
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Attach</label>
                                <input type="file" class="form-control" name="doc[]" id="doc" multiple="multiple">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Notes</label>
                                <textarea class="form-control" name="note" rows="3" autocomplete="off" id="note">@if(isset($edit)) {{$edit->note}} @endif</textarea>
                            </div>
                        </div>
                        @if (session('logged_session_data.company_id')==1)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Company</label>
                                <select class="form-control" name="company" id="company" required>
                                    <option value="">Select</option>
                                    @foreach ($company as $value)
                                    <option value="{{ @$value->id }}">{{ @$value->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="company" id="company" value="{{ session('logged_session_data.company_id') }}" />
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>
                        @if (isset($edit)) @lang('Update & View')
                        @else @lang('Save & View')
                        @endif @lang('Lead')
                    </button>
                    <a href="{{ url('crm-leads/show') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Close</a> 
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <? /*lead from end
----------------------------------------------------------------------- */ ?>
<div class="modal fade" id="addcompany" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Company</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer Type</label>
                                <select class="form-control js-example-basic-single" id="account_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1">Reseller</option>
                                    <option value="2">Enduser</option>
                                    <option value="3">Ecommerce</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <input class="form-control" type="text" aria-describedby="" autocomplete="off" id="company_name_add" required>
                                <div id="company_name_add_list">
                                </div>                            
                                <script>
                                    $(document).ready(function(){
                                    
                                     $('#company_name_add').keyup(function(){ 
                                            var query = $(this).val();
                                            if(query != '')
                                            {
                                             var _token = $('input[name="_token"]').val();
                                             $.ajax({
                                              url:"{{ route('autocomplete.customer_name') }}",
                                              method:"POST",
                                              data:{query:query, _token:_token},
                                              success:function(data){
                                               $('#company_name_add_list').fadeIn();  
                                                        $('#company_name_add_list').html(data);
                                              }
                                             });
                                            }
                                        });
                                    
                                        $(document).on('click', 'li', function(){  
                                            $('#company_name_add').val($(this).text());  
                                            $('#company_name_add_list').fadeOut();  
                                        });  
                                    
                                    });
                                    </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person Name</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_name_add" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Designation</label>
                                <select class="form-control js-example-basic-single" name="designation_add" id="designation_add" required>
                                    <option value="">--Designation--</option>
                                    @if (count($designation)>0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_no_add" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_email_add" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Country</label>
                                <select class="form-control js-example-basic-single" name="country_ship" id="country_ship">
                                    <option value="">-Select-</option>
                                    @foreach ($country as $value)
                                    <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Payment Terms</label>
                                <select class="form-control js-example-basic-single" id="payment_terms" required>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}" @if ($value->id==3) selected @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" id="cust_sales_person" required>
                                    @foreach ($sales_person as $value)
                                        <option value="{{ $value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address 1</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address 2</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add2" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">City</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_city" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">State</label>                                
                                <div id="sectionStateDiv_ship">
                                    <select class="form-control" name="state_ship" id="state_ship">
                                        <option data-display="" value=""></option>
                                        <?php try { ?>
                                        @if (isset($editData) && $editData->vat_state != '')
                                            <option data-display="{{ $editData->vatstate->name }}"
                                                value="{{ $editData->vat_state }}" selected>
                                                {{ $editData->vatstate->name }}</option>
                                        @endif
                                        <?php }catch (\Exception $e) {   } ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">PO Box</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_pobox" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="btn_close2" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="btn_add_company" type="button" >Save & Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
            else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
        });
        
        $(document).on("change", "#source", function () {
        if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
        else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
        });

        $(document).on("change", "#company_name", function () {
            var id = $("#company_name").val();
            get_cust_name(id);
            get_sales_person(id);
        });
        
        function get_cust_name(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-leads-customername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                
                                var name = dataResult['data'][i].customer_salutation +' '+ dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                var address = dataResult['data'][i].address +', '+dataResult['data'][i].address2 +', '+dataResult['data'][i].city +', '+dataResult['data'][i].statename +', '+dataResult['data'][i].name;
                                $("#cust_name").val(name.replace('null ','').replace('null',''));
                                $("#cust_no").val(dataResult['data'][i].mobile);
                                $("#cust_email").val(dataResult['data'][i].email);
                                $("#address").val(address);
                                $("#cust_designation").val(dataResult['data'][i].designation);

                                //1.Reseller
                                if(dataResult['data'][i].account_type == 1){
                                    $("#isproject").val(1);
                                }//2.Enduser
                                if(dataResult['data'][i].account_type == 2){
                                    $("#isproject").val(2);
                                }//3.Ecommerce
                                if(dataResult['data'][i].account_type == 3){
                                    $("#isproject").val(3);
                                }
                                
                            }                        
                        }
                        else{
                            $("#cust_name").val();
                            $("#cust_no").val();
                            $("#cust_email").val();
                            $("#address").val();
                            $("#cust_designation").val();
                            $("#isproject").val();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_sales_person(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-salesperson-list') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            $('#owner').find('option').not(':first').remove();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var name = dataResult['data'][i].full_name;
                                var option = "<option value='"+id+"'>"+name+"</option>";
                                $("#owner").append(option);
                            }
                        }
                        else{
                            $('#owner').find('option').not(':first').remove();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("click", "#btn_add_company", function () {
        
            //$("#btn_add_company").css("display", "none");
        
            var company_name_add = $("#company_name_add").val();
            var cust_name_add = $("#cust_name_add").val();
            var designation_add = $("#designation_add").val();
            var cust_no_add = $("#cust_no_add").val();
            var cust_email_add = $("#cust_email_add").val();
            var cust_address_add = $("#cust_address_add").val();
            var cust_address_add2 = $("#cust_address_add2").val();
            var country_add = $("#country_ship").val();
            
            var cust_city = $("#cust_city").val();
            var state_ship = $("#state_ship").val();
            var cust_pobox = $("#cust_pobox").val();
            var sales_person = $("#cust_sales_person").val();
            var payment_terms = $("#payment_terms").val();
            var account_type = $("#account_type").val();
            var company_id = $("#company").val();
        
            var action = "{{ URL::to('add-customer-detail-popup') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company_name_add: company_name_add,
                    cust_name_add: cust_name_add,
                    designation_add: designation_add,
                    cust_no_add: cust_no_add,
                    cust_email_add: cust_email_add,
                    cust_address_add: cust_address_add,
                    cust_address_add2: cust_address_add2,
                    vat_country: country_add,
                    city: cust_city,
                    vat_state: state_ship,
                    zip_code: cust_pobox,
                    sales_person: sales_person,
                    payment_terms: payment_terms,
                    account_type: account_type,
                    company_id: company_id,
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if(dataResult['data']=="ERROR")
                    {
                        alert("Error found in something!!");
                        $("#btn_add_company").css("display", "block");
                    }
                    else if(dataResult['data']=="ERROR2")
                    {
                        alert("Company Name already exists!! Please Contact Support");
                        $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                        $("#btn_add_company").css("display", "block");
                    }
                    else{
                        if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                        }
                        if(len > 0){
                            
                            $('#company_name').find('option').not(':first').remove();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var name = dataResult['data'][i].name;
                                var name2 = dataResult['data'][i].code;
                                var option = "<option value='"+id+"'>"+name+"</option>";
                                $("#company_name").append(option);
                            }
                            alert('Company Name Added Successfully!!');
                            $('#btn_close2').click();
                            $("#btn_add_company").css("display", "block");
                            //location.reload();
                            //$("#company_name").change();
                        }
                    }
                  }
            });
        });

        {{--  $(document).on("change", "#lead_name", function () {
            $("#loading_bg").css("display", "block");
            var id = $("#lead_name").val();
            var action = "{{ URL::to('get-lead-name-to-brand') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                var title = dataResult['data'][i].title;
                                $("#tags").val(title);
                                $('#select2-tags-container').html("&nbsp;&nbsp;" + title);
                                
                            }
                        }
                        else{

                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        });  --}}
    </script>

<? /*lead from
----------------------------------------------------------------------- */ ?>



<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


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
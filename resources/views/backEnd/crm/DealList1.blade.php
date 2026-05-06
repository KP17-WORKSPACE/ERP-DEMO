@extends('backEnd.newmasterpage')
@section('mainContent')

    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Deal List</h2>
                <span class="page-label">Home - Deal List</span>
            </div>
            <div>
                <table>
                    <tr>
                        <td>
                @if($filter_by == "Expired")
                    <a href="#" onclick="sort(11)" type="button" class="btn btn-success">Latest Deals</a>
                @else
                    <a href="#" onclick="sort(12)" type="button" class="btn btn-danger">Expired Deals</a>
                @endif

                {{--  <a href="{{ url('crm-deals') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Deal</a>  --}}
                <a type="button" data-toggle="modal" data-target="#adddeal" class="btn btn-info" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> New Deal</a>

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
                    <a class="dropdown-item" href="#" onclick="sort(9)">By Deal Value</a>
                    <a class="dropdown-item" href="#" onclick="sort(10)">By Date</a>

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/show', 'method' => 'get', 'id' => 'crm-deals-search']) }}
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
                </script>
                    </td>
                </tr>
            </table>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">
                <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">            
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/show', 'method' => 'get', 'id' => 'crm-deals-search']) }}
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>                    
                        <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Company Name</label>
                        <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                            <option value="">-Select-</option>
                            @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_cust_id == $value->id) selected @endif>{{ @$value->code }} - {{ @$value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 35)
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            @foreach ($staff as $value)
                            <option value="{{ @$value->user_id }}" @if($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
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
                            <option value="{{ @$value->user_id }}" @if($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(Auth::user()->id == 33)
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            <option value="33" @if($ctrl_owner == 33) selected @endif>Jacob George</option>
                            <option value="31" @if($ctrl_owner == 31) selected @endif>Sheikh Nadeem Akthar</option>
                            <option value="59" @if($ctrl_owner == 59) selected @endif>Trison Thomas</option>
                        </select>
                    </div>
                    @endif

                    @if(Auth::user()->id == 27)
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            <option value="27" @if($ctrl_owner == 27) selected @endif>Monica</option>
                            <option value="28" @if($ctrl_owner == 28) selected @endif>Archana Revi</option>
                            <option value="30" @if($ctrl_owner == 30) selected @endif>Faizaan Aslam Shaikh</option>
                            <option value="54" @if($ctrl_owner == 54) selected @endif>Satyabhan Sikarwar</option>
                        </select>
                    </div>
                    @endif

                    @if(Auth::user()->id == 44)
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            <option value="44" @if($ctrl_owner == 44) selected @endif>Rajiv R</option>
                            <option value="32" @if($ctrl_owner == 32) selected @endif>Irshaad Aklekar</option>
                            <option value="34" @if($ctrl_owner == 34) selected @endif>Stephen F Mendonsa</option>
                            <option value="45" @if($ctrl_owner == 79) selected @endif>Shamshad Ahmed</option>
                        </select>
                    </div>
                    @endif

                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Brand</label>
                        <select class="form-control js-example-basic-single" name="brand_id" id="brand_id">
                            <option value="">-Select-</option>
                            @foreach ($brand as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_brand == $value->id) selected @endif>{{ @$value->title }}</option>
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
                            <option value="4" @if(@$ctrl_isproject == "4") selected @endif >Ecommerce</option>
                            <option value="0" @if(@$ctrl_isproject == "0") selected @endif >Lead</option>
                            <option value="5" @if(@$ctrl_isproject == "5") selected @endif >Marketing</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Status</label>
                        <select class="form-control" name="stage_id" id="stage_id">
                            <option value="">-Select-</option>
                            <option value="1" @if($ctrl_stage == 1) selected @endif>Prospecting</option>
                            <option value="2" @if($ctrl_stage == 2) selected @endif>Quote</option>
                            <option value="3" @if($ctrl_stage == 3) selected @endif>Closure</option>
                            <option value="4" @if($ctrl_stage == 4) selected @endif>Won</option>
                            <option value="5" @if($ctrl_stage == 5) selected @endif>Lost</option>
                            <option value="6" @if($ctrl_stage == 6) selected @endif>completed</option>
                            <option value="7" @if($ctrl_stage == 7) selected @endif>On Process</option>
                            <option value="8" @if($ctrl_stage == 8) selected @endif>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Source</label>
                        <select class="form-control" name="source_id" id="source_id">
                            <option value="">-Select-</option>
                            <option value="Gitex 2023" @if($ctrl_source == "Gitex 2023") selected @endif>Gitex 2023</option>
                            <option value="Gitex" @if($ctrl_source == "Gitex") selected @endif>Gitex</option>
                            <option value="Chat" @if($ctrl_source == "Chat") selected @endif>Chat</option>
                            <option value="Call" @if($ctrl_source == "Call") selected @endif>Call</option>
                            <option value="Mail" @if($ctrl_source == "Mail") selected @endif>Mail</option>
                            <option value="Fulfillment" @if($ctrl_source == "Fulfillment") selected @endif >Fulfillment</option>
                            <option value="Ecommerce" @if($ctrl_source == "Ecommerce") selected @endif >Ecommerce</option>
                            <option value="Other" @if($ctrl_source == "Other") selected @endif>Other</option>
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

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/show', 'method' => 'post', 'id' => 'crm-deals-show']) }}
                <button class="btn-sm btn-primary" type="submit" style="float: left; left: 20px; position: absolute; z-index: 999;">View All</button>
                {{ Form::close() }}

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
                            <th style="width: 55px;">@lang('Deal No')</th>
                            @if(session('logged_session_data.company_id') == 1)
                            <th style="width: 200px;">@lang('Company')</th>
                            @endif
                            <th style="width: 150px;">@lang('Deal Name')</th>
                            <th>@lang('Customer')</th>
                            <th style="width: 120px;">@lang('Created By')</th>
                            <th style="width: 120px;">@lang('Stage')</th>
                            <th style="width: 120px; padding-right: 30px;" class="text-right">@lang('Deal Value')</th>
                            <th style="width: 120px; padding-right: 30px;" class="text-right">@lang('Deal Profit')</th>
                            <th style="width: 70px;">@lang('Date')</th>
                            <th style="width: 75px;">@lang('Updated On')</th>
                            <th style="width: 80px;">@lang('Closing Date')</th>
                            <th style="width: 170px;">@lang('Actions')</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $count = 1;
        $total_deal = 0;
        $total_amount = 0;
        $deal_currency = "AED"; @endphp
                        @foreach($deals as $value)
                                    @php $total_deal += 1; @endphp

                                    @if((@$value->estimated_close_date <= Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}" style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}">
                                    @endif
                                        <td><a href="{{url('crm-deals/' . $value->id . '/view')}}">{{@$value->deal_code->code }}</a></td>
                                        @if(session('logged_session_data.company_id') == 1)
                                        <td>{{ $value->companyname->company_name }}</td>
                                        @endif
                                        <td><a class="text-dark" href="{{url('crm-deals/' . $value->id . '/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->deal_name}}</div></a></td>
                                        <td><div style="width:240px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{ $value->customername->code }} - {{@$value->customername->name}}</div></td>
                                        <td>{{@$value->ownername->first_name}}</td>
                                        <td>
                                            @if($value->stage == 1) <span class="warning btn-badge py-1 px-2">Prospecting</span> @endif
                                            @if($value->stage == 2) <span class="success btn-badge py-1 px-2">Quote</span> @endif
                                            @if($value->stage == 3) <span class="info btn-badge py-1 px-2">Closure</span> @endif
                                            @if($value->stage == 4) 
                                            <?php
                            $data = App\SysHelper::deal_track_status($value->id);
                            $color = "danger";
                            if ($data == "Pending") {
                                $color = "warning";
                            } else if ($data == "completed") {
                                $color = "primary";
                            } else if ($data == "OnProcess") {
                                $color = "info";
                            } else {
                                $color = "danger";
                            }
                                            ?>
                                            @if($data != "completed")
                                            <span class="primary btn-badge py-1 px-2">Won</span>@endif

                                            @if(App\SysHelper::set_track($value->id) == 1)
                                                <a class="{{ $color }} btn-badge py-1 px-2" href="{{url('crm-deal-track/' . $value->id . '/view')}}" title="Click to Fullfill">
                                                @if($data == "Fulfill")<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>@endif {{ $data }} </a>
                                            @endif

                                            @endif
                                            @if($value->stage == 5) <span class="danger btn-badge py-1 px-2">Lost</span> @endif
                                            @if($value->stage == 6) <span class="dark btn-badge py-1 px-2">Cancelled</span> @endif
                                        </td>
                                        <td class="text-right" style="padding-right: 30px;">
                                            @php $aed = $value->deal_value; @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp {{ $value->dealcurrency->code }} <?php        $deal_currency = $value->dealcurrency->code; ?>
                                        </td>
                                        <td class="text-right" style="padding-right: 30px;">{{@App\SysHelper::currancy_format_deal($value->deal_profit,$value->company_id)}} {{ $value->dealcurrency->code }}
                                        </td>
                                        <td>{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                        <td>{{date('d/m/Y h:i A', strtotime(@$value->updated_at))}}</td>
                                        <td>{{date('d/m/Y', strtotime(@$value->estimated_close_date))}}</td>
                                        <td>

                                            <a class="btn-sm btn-primary open-comments-modal" style="cursor: pointer;"
                                                                data-deal-id="{{ $value->id }}"><i class="fa fa-comments"
                                                                    aria-hidden="true"></i></a>
                                            <a class="btn-sm btn-info" href="{{url('crm-deals/' . $value->id . '/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                            <a class="btn-sm btn-primary" href="{{url('crm-deals/' . $value->id . '/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                            @if(Auth::user()->role_id == 1)
                                                @if ($value->deleted_at)
                                                    <button data-id="{{ $value->id }}" data-toggle="modal" data-target="#restoreModal" type="button"
                                                        class="btn-sm btn-success open-restore-modal" title="Restore">
                                                        <i class="fa fa-undo"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn-sm btn-danger open-delete-modal" data-id="{{ $value->id }}" data-toggle="modal"
                                                        data-target="#deleteModal">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>

                                                @endif

                                            @endif
                                        </td>
                                    </tr>

                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>{{ $total_deal }}</th>
                            @if(session('logged_session_data.company_id') == 1)
                            <th></th>
                            @endif
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-right pr-4">{{@App\SysHelper::currancy_format_deal($total_amount,$value->company_id)}} {{ $deal_currency }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <?php    /*
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
                    <?php    try { ?>
                    <footer>
                        <tr>
                            <td colspan="10">
                                {{ $deals->appends(request()->input())->links() }}
                            </td>
                        </tr>
                    </footer>
                    <?php    } catch (\Exception $e) {
        } ?>

                </table>
            </div>
        </div>
    </div>

    </div>


    <?    /*deal from
       ----------------------------------------------------------------------- */ ?>

    <style>
    .right-aligned{
        right:0px;
        position: fixed;
        z-index: 9999;
    }
    </style>

    <div class="modal fade bd-example-modal-lg" id="adddeal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog right-aligned modal-lg" role="document" style="min-width:50% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Deal</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    @if (isset($edit))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-deals-form']) }}
                    @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deals-form']) }}
                    @endif
                    <div class="modal-body">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                        <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Deal Name</label>
                                    <input class="form-control" type="text" name="deal_name" autocomplete="off" id="deal_name" value="{{ isset($edit) ? (!empty(@$edit->deal_name) ? @$edit->deal_name : old('deal_name')) : old('deal_name') }}" required>

                                
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Company</label>
                                    <a style="float: right; cursor: pointer;" class="text-primary" data-toggle="modal" data-target="#addcompany"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Company</a>
                                    <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required>
                                            <option value="">-Select-</option>
                                            @foreach ($vendors as $value)
                                            <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->cust_id) ? (@$edit->cust_id == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->customer_name_display }}
                                            </option>
                                            @endforeach
                                        </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Contact Person Name</label>
                                    <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Designation</label>
                                    <input class="form-control" type="text" name="designation" autocomplete="off" id="designation" value="{{ isset($edit) ? (!empty(@$edit->designation) ? @$edit->designation : old('designation')) : old('designation') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Mobile</label>
                                    <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
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
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="">Value</label>
                                    <input class="form-control" type="number" step="any" name="deal_value" autocomplete="off" id="deal_value" value="{{ isset($edit) ? (!empty(@$edit->deal_value) ? @App\SysHelper::currancy_format_deal_no($edit->deal_value, $edit->company_id) : old('deal_value')) : old('deal_value') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Est. Closing Date *</label>
                                    @php
        $value = date('m-d-Y');
        if (isset($edit) && $edit->estimated_close_date != "1970-01-01") {
            @$value =
                date('Y-m-d', strtotime(@$edit->estimated_close_date));
        } else {
            if (!empty(old('estimated_close_date'))) {
                @$value = old('estimated_close_date');
            } else {

            }
        }
                                    @endphp
                                    <input class="form-control" id="estimated_close_date" type="date" autocomplete="off" name="estimated_close_date" value="{{ @$value }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Date</label>
                                    @php
        $value = date('Y-m-d');
        if (isset($edit) && !empty($edit->date)) {
            $value = date('Y-m-d', strtotime(@$edit->date));
        }                                        
                                    @endphp
                                    <input class="form-control" id="date" type="date" name="date" value="{{ $value }}">
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="">Stage<span></span></label>
                                    <select class="form-control" name="stage" id="stage">
                                        <option value="1" @if(@$edit->stage == 1) selected @endif >Prospecting</option>
                                        <option value="2" @if(@$edit->stage == 2) selected @endif >Quote</option>
                                        <option value="3" @if(@$edit->stage == 3) selected @endif >Closure</option>
                                        <option value="4" @if(@$edit->stage == 4) selected @endif >Won</option>
                                        <option value="5" @if(@$edit->stage == 5) selected @endif >Lost</option>
                                    </select>
                                    <textarea class="primary-input dynamicstxt_s w-100 form-control" name="lost_comments" rows="4" style="height: 50px !important; display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                    <script>
                                        $('#stage').on('change', function(e) {
                                            if ($('#stage').val() == 5) {
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
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="">Source</label>
                                    <select class="form-control" name="source" id="source">
                                        <option value="">-Select-</option>
                                        <option value="Chat" @if(@$edit->source == "Chat") selected @endif >Chat</option>
                                        <option value="Call" @if(@$edit->source == "Call") selected @endif >Call</option>
                                        <option value="Mail" @if(@$edit->source == "Mail") selected @endif @if(!isset($edit)) selected @endif>Mail</option>
                                        <option value="Website" @if(@$edit->source == "Website") selected @endif >Website</option>
                                        <option value="Gitex 2023" @if(@$edit->source == "Gitex 2023") selected @endif >Gitex 2023</option>
                                        <option value="Gitex" @if(@$edit->source == "Gitex") selected @endif >Gitex</option>
                                        <option value="Fulfillment" @if(@$edit->source == "Fulfillment") selected @endif >Fulfillment</option>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Created By</label>
                                    <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Deal Type<span></span></label>
                                    <select class="form-control" name="isproject" id="isproject">
                                        {{--  <option value="4" @if(@$edit->isproject == "4") selected @endif >Project</option>  --}}
                                        <option value="1" @if(@$edit->isproject == "1") selected @endif >Reseller</option>
                                        <option value="2" @if(@$edit->isproject == "2") selected @endif >Enduser</option>
                                        <option value="3" @if(@$edit->isproject == "3") selected @endif >E-Commerece</option>
                                        <option value="5" @if(@$edit->isproject == "5") selected @endif >Marketing</option>
                                    </select>
                                    <script>
                                        $('#isproject').on('change', function(e) {
                                            if ($('#isproject').val() == 4) {
                                                $('#is_professional_service').prop( "checked", true );
                                            } else {
                                                $('#is_professional_service').prop( "checked", false );
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="1" @if(@$edit->status == 1) selected @endif >New</option>
                                        <option value="2" @if(@$edit->status == 2) selected @endif >Qualified</option>
                                        <option value="3" @if(@$edit->status == 3) selected @endif >Unqualified </option>
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
                            <div class="col-md-4">
                                <div class="form-group files">
                                    <label for="">Project Service</label>
                                    <div class="form-control">
                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="is_professional_service" name="is_professional_service" checked>
                                    <label class="form-check-label ml-4 mt-1" for="is_professional_service">Yes, Project Service</label></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group files">
                                    <label for="">Attach</label>
                                    <input type="file" class="form-control" name="doc" id="doc">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Notes</label>
                                    <textarea class="form-control" name="note" rows="3" autocomplete="off" id="note">@if(isset($edit)) {{$edit->note}} @endif</textarea>
                                </div>
                            </div>
                            @if (session('logged_session_data.company_id') == 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Company</label>
                                    <select class="form-control" name="company" id="company" required>
                                        <option value="">Select</option>
                                        @foreach ($company as $value)
                                        <option value="{{ @$value->id }}" @if(session('logged_session_data.company_id') == @$value->id) selected @endif>{{ @$value->company_name }}</option>
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
                         @if (isset($edit))
                            <?php        $editcheck = App\SysHelper::deal_edit_disable($edit->id); ?>
                                @if($editcheck == 1)
                                    <span class="text-danger">Edit Disabled! This Deal is on Process.</span>
                                @else
                                    <button type="submit" value="3" class="btn btn-primary" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>Update & View Deal</button>
                                @endif
                            @else
                                <button type="submit" value="2" class="btn btn-info" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>Save & Generate Quote</button>
                                <button type="submit" value="1" class="btn btn-primary" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>Save & View Deal</button>                          
                            @endif
                            &nbsp;&nbsp;
                            <a href="{{ url('crm-deals/show') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Close</a>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="modal fade" id="addcompany" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog right-aligned modal-lg" role="document">
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
                                        <option value="1" selected>Reseller</option>
                                        <option value="2">Enduser</option>
                                        <option value="3">Ecommerce</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Company Name</label>
                                    <input class="form-control text-uppercase" type="text" aria-describedby="" autocomplete="off" id="company_name_add" required>
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
                                        @if (count($designation) > 0)
                                            @foreach ($designation as $val)
                                                <option value="{{ $val->title }}" {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}>{{ $val->title }}</option>
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
                                        <option value="{{ @$value->id }}" {{ trim(strtolower($value->name)) == 'united arab emirates' ? 'selected' : '' }}>{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Payment Terms</label>
                                    <select class="form-control js-example-basic-single" id="payment_terms" required>
                                        @foreach ($paymentterms as $key => $value)
                                            <option value="{{ @$value->id }}" @if ($value->id == 3) selected @endif>{{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" id="cust_sales_person" required>
                                        <option value="">-Select-</option>

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
                                            <?php    try { ?>
                                            @if (isset($editData) && $editData->vat_state != '')
                                                <option data-display="{{ $editData->vatstate->name }}"
                                                    value="{{ $editData->vat_state }}" selected>
                                                    {{ $editData->vatstate->name }}</option>
                                            @endif
                                            <?php    } catch (\Exception $e) {
        } ?>
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
     <div class="modal fade" id="commentsModal" tabindex="-1" role="dialog" aria-labelledby="commentsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deal Comments</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped" id="commentsTable">
                            <thead>
                                <tr>
                                      <th width="50%">Comment</th>
                                    <th width="20%">Person</th>
                                    <th width="10%">Attachment</th>
                                    <th width="20%">Date</th>
                                </tr>
                            </thead>
                            <tbody id="commentsModalBody">
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No comments found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


           <!-- Delete Reason Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="" id="deleteForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white" id="deleteModalLabel">Delete Deal</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Please provide a reason for deleting this deal:</p>
                        <textarea name="delete_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="" id="restoreForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white" id="restoreModalLabel">Restore Deal</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Please provide a reason for restoring this deal:</p>
                        <textarea name="restore_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Confirm Restore</button>
                    </div>
                </div>
            </form>
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

            $(document).on("change", "#cust_id", function () {
                var id = $("#cust_id").val();
                var user = $("#user_id").val();
                get_cust_name(id);
                get_sales_person(id,user);
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
                                    $("#designation").val(dataResult['data'][i].designation);
                                    $("#cust_no").val(dataResult['data'][i].mobile);
                                    $("#cust_email").val(dataResult['data'][i].email);
                                    $("#address").val(address);
                                    //1.Reseller
                                    if(dataResult['data'][i].account_type == 1){
                                        $("#isproject").val(1);
                                        $('#is_professional_service').prop( "checked", false );
                                    }//2.Enduser
                                    if(dataResult['data'][i].account_type == 2){
                                        $("#isproject").val(2);
                                        $('#is_professional_service').prop( "checked", false );
                                    }//3.Ecommerce
                                    if(dataResult['data'][i].account_type == 3){
                                        $("#isproject").val(3);
                                        $('#is_professional_service').prop( "checked", false );
                                    }
                                }
                            }
                            else{
                                $("#cust_name").val();
                                $("#designation").val();
                                $("#cust_no").val();
                                $("#cust_email").val();
                                $("#address").val();
                                $("#isproject").val();
                            }
                            $("#loading_bg").css("display", "none");
                    }
                });
            }
            function get_sales_person(id,user) {
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
                                $('#owner').find('option').remove();
                                for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var name = dataResult['data'][i].full_name;
                                    var sele='';
                                    if(user == id) { sele='selected'; }
                                    var option = "<option value='"+id+"' "+sele+">"+name+"</option>";
                                    $("#owner").append(option);
                                }
                            }
                            else{
                                $('#owner').find('option').remove();
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

                                $('#cust_id').find('option').not(':first').remove();
                               var newCompanyId = dataResult['new_company_id'];

                                for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var name = dataResult['data'][i].name;
                                    var name2 = dataResult['data'][i].code;
                                    var option = "<option value='"+id+"'>"+name+"</option>";
                                    $("#cust_id").append(option);
                                }
                                 if (newCompanyId) {
                                    $("#cust_id").val(newCompanyId).trigger('change');
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

            {{--  $(document).on("change", "#deal_name", function () {
                $("#loading_bg").css("display", "block");
                var id = $("#deal_name").val();
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
            $(document).ready(function() {
                // Trigger change event only if a country is selected by default
                if ($('#country_ship').val() !== '') {
                    $('#country_ship').trigger('change');
                }
            });

             $(document).ready(function() {
                $('.open-comments-modal').click(function() {
                $("#loading_bg").css("display", "block");


                    var leadId = $(this).data('deal-id');
                    var $body = $('#commentsModalBody');
                    $body.html('<tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>');

                    $.ajax({
                        url: '/crm-deals/comments/' + leadId,
                        method: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            $body.empty();
                            if (res.data && res.data.length > 0) {
                                $.each(res.data, function(i, comment) {
                                    var row = `
                                        <tr>
                                            <td>${comment.comments}</td>
                                            <td>${comment.createdby.first_name || '-'} ${comment.createdby.last_name || '-'}</td>
                                            <td>
                                           ${comment.commentsdoc ? ` <a class="text-info p-0"
                                                    href="{{asset('public/uploads/crm_deal_doc/')}}/${ comment.commentsdoc }"
                                                    target="_blank"><i class="fa fa-paperclip"
                                                        aria-hidden="true"></i>&nbsp;&nbsp;View File</a>` : '' }

                                            </td>
                                            <td>${formatDateTime(comment.created_at)}</td>
                                        </tr>`;
                                    $body.append(row);
                                });
                            } else {
                                $body.html(
                                    '<tr><td colspan="3" class="text-center text-muted">No comments found</td></tr>'
                                    );
                            }
                $("#loading_bg").css("display", "none");

                            $('#commentsModal').modal('show');
                        },
                        error: function() {
                            $body.html(
                                '<tr><td colspan="3" class="text-danger text-center">Error loading comments</td></tr>'
                                );
                        }
                    });



                });

            });

            function formatDateTime(datetime) {
                var date = new Date(datetime);
                return date.toLocaleString('en-IN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }


            $(document).on('click', '.open-delete-modal', function () {
            var leadId = $(this).data('id');
            var actionUrl = "{{ url('crm-deals') }}/" + leadId + "/delete";
            $('#deleteForm').attr('action', actionUrl);
        });

        $(document).on('click', '.open-restore-modal', function () {
            var leadId = $(this).data('id');
            var actionUrl = "{{ url('crm-deals') }}/" + leadId + "/restore";
            $('#restoreForm').attr('action', actionUrl);
        });

        </script>

    <?    /*deal from
       ----------------------------------------------------------------------- */ ?>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
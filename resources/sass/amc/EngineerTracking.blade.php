@extends('backEnd.masterpage')
@section('mainContent')
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Engineer Tracking</h2>
            <span class="page-label">Home - Engineer Tracking</span>
        </div>
        <div>

             {{-- <a class="btn btn-info" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddNewAMC"
                    data-backdrop="static" data-keyboard="false">Add New AMC</a> --}}

            <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddNewAMCService"
                    data-backdrop="static" data-keyboard="false">Add AMC Request</a>

            <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddProfessionalServicesRequest"
                        data-backdrop="static" data-keyboard="false">Add Project Request</a>
                    
            <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalSupport"
                        data-backdrop="static" data-keyboard="false">Add Pre-Sales Request</a>
            {{--  <a class="btn btn-info" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddNewAMC" data-backdrop="static" data-keyboard="false">Add New AMC</a>
            <a class="btn btn-info" href="{{ url('crm-amc-service-request-list') }}">AMC Service Request List</a>
            --}}
            <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-engineer-tracking', 'method' => 'POST', 'id' => 'engineer-tracking']) }}
        <div class="row">
            <div class="col-md-1 mb-2">
                <label for="" class="form-check-label">Track ID</label>
                <input class="form-control" id="search_track_id" type="text" autocomplete="off" name="search_track_id" value="">
            </div>
            <div class="col-md-1 mb-2">
                <label for="" class="form-check-label">Deal ID</label>
                <input class="form-control" id="search_deal_id" type="text" autocomplete="off" name="search_deal_id" value="">
            </div>
            <div class="col-md-2 mb-2">
                <label for="" class="form-check-label">Customer Name</label>
                <select class="form-control js-example-basic-single" name="search_customer_name" id="search_customer_name">
                    <option value="">-Select-</option>
                    @foreach ($customer as $value)
                    <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label for="" class="form-check-label">Engineer</label>
                <select class="form-control" name="search_engineer" id="search_engineer">
                    <option value="">Select</option>
                    @if(count($salesperson)>0)
                        @foreach ($salesperson as $list)
                            <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label for="" class="form-check-label">Service Date From</label>
                <input class="form-control" id="search_from_date" type="date" autocomplete="off" name="search_from_date" value="">
            </div>
            <div class="col-md-2 mb-2">
                <label for="" class="form-check-label">Service Date To</label>
                <input class="form-control" id="search_to_date" type="date" autocomplete="off" name="search_to_date" value="">
            </div>
            <div class="col-md-1 mb-2">
                <label for="" class="form-check-label">Status</label>
                <select class="form-control" name="search_status" id="search_status">
                    <option value="">All</option>
                    <option value="1">Pending</option>
                    <option value="2">Completed</option>
                </select>
            </div>
            <div class="col-md-1 mb-2">
                <label for="" class="form-check-label">&nbsp;</label><br />
                <button type="submit" class="btn btn-primary" id="btnSubmit">Search</button>
            </div>
        </div>
        {{ Form::close() }}
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
                                            <th width="70px">@lang('Track Id')</th>
                                            <th width="50px">@lang('Deal ID')</th>
                                            <th width="70px">@lang('Date')</th>
                                            <th>@lang('Customer Name')</th>
                                            <th width="200px">@lang('Engineer')</th>
                                            <th width="80px">@lang('Service Date')</th>
                                            <th width="65px">@lang('Time From')</th>
                                            <th width="65px">@lang('Time To')</th>
                                            <th width="58px" class="text-right pr-2">@lang('No. of Hrs')</th>
                                            <th width="100px">Status</th>
                                            <th width="120px">Stage</th>
                                            <th width="70px">Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data))
                                        @foreach($data as $dt)                                        
                                        <tr>
                                            <td>{{ $dt->doc_number }}</td>
                                            <td>{{ $dt->deal_code }}</a></td>
                                            <td>{{ date('d/m/Y', strtotime($dt->date)) }}</td>
                                            <td>{{ $dt->cust_name }}</td>                                            

                                            @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32 )
                                            <td>{{ Auth::user()->full_name }}</td>
                                            @else
                                            <?php
                                            $engineername="";
                                            if($dt->comment_by != null){
                                                $s = $staff->where('user_id',$dt->comment_by)->pluck('full_name');
                                                $engineername=$s[0];
                                            }
                                            else if($dt->service_engineer !=""){
                                                $st = explode(',', $dt->service_engineer);
                                                if(count($st)>0){
                                                    foreach($st as $u){
                                                        $s = $staff->where('user_id',$u)->pluck('full_name');
                                                        if($engineername==""){
                                                            $engineername .= $s[0];
                                                        } else { $engineername .= ", ".$s[0]; }
                                                        
                                                    }
                                                }
                                            }
                                            ?>
                                            <td>{{ $engineername }}</td>
                                            @endif

                                            <td>@if($dt->work_date != null) {{date('d/m/Y', strtotime($dt->work_date ))}} @endif</td>
                                            <td>@if($dt->work_time_from != null) {{date('h:i A', strtotime($dt->work_time_from ))}} @endif</td>
                                            <td>@if($dt->work_time_to != null) {{date('h:i A', strtotime($dt->work_time_to ))}} @endif</td>
                                            <td align="right" class="pr-2">@if($dt->tim != null) @if($dt->tim < 60) {{ $dt->tim }} Min @else {{ @App\SysHelper::com_curr_format($dt->tim/60,2,':','')  }} Hrs @endif @endif</td>
                                            <td>
                                                {{--  @if($dt->type=="AMC")
                                                    {!! App\SysHelper::get_amc_status(@$dt->status) !!}
                                                @endif
                                                
                                                @if($dt->type=="PS")
                                                {!! App\SysHelper::get_ps_status(@$dt->status) !!}
                                                @endif
                                                
                                                @if($dt->type=="PRESALES")
                                                {!! App\SysHelper::get_pre_sales_status_engineer_page(@$dt->status) !!}
                                                @endif  --}}
                                                @if(@$dt->status==1)
                                                    <span class="text-warning">Pending</span>
                                                @elseif(@$dt->status==2)
                                                    <span class="text-success">Completed</span>
                                                @else
                                                
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <?php
                                                $deal_stage="";
                                                $track = $deal_track->where('deal_id',$dt->deal_id);
                                                if(count($track)>0){
                                                    foreach($track as $tr){
                                                        $deal_stage= $deal_stage= @App\SysHelper::deal_track_status3($tr->receivables,$tr->delivery,$tr->invoice,$tr->purchease,$tr->sales,$tr->accounts);
                                                    }
                                                } else {
                                                    $dl = $deals->where('id',$dt->deal_id);
                                                    if(count($dl)>0){
                                                        foreach($dl as $d){
                                                            $deal_stage= @App\SysHelper::deal_stage($d->stage);
                                                        }
                                                    }
                                                }
                                                ?>
                                                {!! $deal_stage !!}
                                                </td>

                                            <td>
                                                @if($dt->type=="AMC")
                                                <a class="text-primary" style="cursor: pointer;" data-toggle="modal" data-target="#amccomments_{{ $dt->id }}" data-backdrop="static" data-keyboard="false">View</a>
                                                @endif
                                                
                                                @if($dt->type=="PS")
                                                <a class="text-primary" style="cursor: pointer;" data-toggle="modal" data-target="#pscomments_{{ $dt->id }}" data-backdrop="static" data-keyboard="false">View</a>
                                                @endif
                                                
                                                @if($dt->type=="PRESALES")
                                                <a class="text-primary" style="cursor: pointer;" data-toggle="modal" data-target="#presalescomments_{{ $dt->id }}" data-backdrop="static" data-keyboard="false">View</a>
                                                @endif
                                            </td>



                                            
                                            
                                            
                                            
                                        </tr>                                        
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if (count($amc_list)>0)
                    @foreach ($amc_list as $amc)

                <div class="modal fade" id="amccomments_{{ $amc->id }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">AMC - Scope of Work & Comments</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        @php
                                            $amc_comments_data = $amc_comments->where('amc_id',$amc->id);
                                            $sw = $amc_work->where('amc_id',$amc->id);
                                        @endphp

                                        @if (count($sw)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="100%">Scope of Work</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sw as $w)
                                                <tr>
                                                    <td>{{ $w->work }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif

                                        @if (count($amc_comments_data)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="70%">Comment</th>
                                                    <th width="10%">Status</th>
                                                    <th width="20%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($amc_comments_data as $cmts)
                                                <tr>
                                                    <td>{{ $cmts->comments }}<br />
                                                        On {{ date('d/m/Y', strtotime($cmts->work_date)) }} 
                                                        From {{ date('h:i A', strtotime($cmts->work_time_from)) }} 
                                                        To {{ date('h:i A', strtotime($cmts->work_time_to)) }}
                                                    </td>
                                                    <td>
                                                        @if ($cmts->status == 1)
                                                            Pending
                                                        @else
                                                            Completed
                                                        @endif                                                    
                                                    </td>
                                                    <td>{{ $cmts->engineerid->full_name }}<br />{{ $cmts->created_at }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif                                 
                                    </div>           
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                    @endforeach                    
                @endif

                @if (count($ps_list)>0)
                    @foreach ($ps_list as $ps)

                <div class="modal fade" id="pscomments_{{ $ps->id }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Project Service - Scope of Work & Comments</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        @php
                                            $ps_comments_data = $ps_comments->where('ps_id',$ps->id);
                                            $sw = $ps_work->where('service_id',$ps->id);
                                        @endphp


                                        @if (count($sw)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="100%">Scope of Work</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sw as $w)
                                                <tr>
                                                    <td>{{ $w->work }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif

                                        @if (count($ps_comments_data)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="70%">Comment</th>
                                                    <th width="10%">Status</th>
                                                    <th width="20%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ps_comments_data as $cmts)
                                                <tr>
                                                    <td>{{ $cmts->comments }}<br />
                                                        On {{ date('d/m/Y', strtotime($cmts->work_date)) }} 
                                                        From {{ date('h:i A', strtotime($cmts->work_time_from)) }} 
                                                        To {{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                    <td>
                                                        @if ($cmts->status == 1)
                                                            Pending
                                                        @else
                                                            Completed
                                                        @endif                                                    
                                                    </td>
                                                    <td>{{ $cmts->engineerid->full_name }}<br />{{ $cmts->created_at }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif                                        
                                    </div>           
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                    @endforeach                    
                @endif

                @if (count($presales_list)>0)
                    @foreach ($presales_list as $presales)

                <div class="modal fade" id="presalescomments_{{ $presales->id }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Presales - Scope of Work & Comments</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        @php
                                            $presales_comments_data = $presales_comments->where('support_id',$presales->id);
                                            $sw = $presales_work->where('support_id',$presales->id);
                                        @endphp

                                        @if (count($sw)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="100%">Scope of Work</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sw as $w)
                                                <tr>
                                                    <td>{{ $w->work }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif

                                        @if (count($presales_comments_data)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="70%">Comment</th>
                                                    <th width="10%">Status</th>
                                                    <th width="20%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($presales_comments_data as $cmts)
                                                <tr>
                                                    <td>{{ $cmts->comments }}<br />
                                                        On {{ date('d/m/Y', strtotime($cmts->work_date)) }} 
                                                        From {{ date('h:i A', strtotime($cmts->work_time_from)) }} 
                                                        To {{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                    <td>
                                                        @if ($cmts->status == 1)
                                                            Pending
                                                        @else
                                                            Completed
                                                        @endif                                                    
                                                    </td>
                                                    <td>{{ $cmts->engineerid->full_name }}<br />{{ $cmts->created_at }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif                                        
                                    </div>           
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                    @endforeach                    
                @endif

                


       <!-- Modal Support-->
    <div class="modal fade" id="ModalAddNewAMCService" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New AMC Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amcid" id="amcid" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="deal_id" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control js-example-basic-single" name="cust_name" id="cust_name"
                                    required>
                                    <option value="">-Select-</option>
                                    @foreach ($salespersonamc as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Contact Person</label>
                                <input class="form-control" type="text" name="contact_person" id="contact_person"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Mobile No</label>
                                <input class="form-control" type="text" name="mobile_no" id="mobile_no" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Location of Work</label>
                                <input type="text" class="form-control" name="location_of_work" id="location_of_work"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <input type="date" class="form-control" name="service_date" id="service_date"
                                    required min="{{ date('Y-m-d') }}" onchange="check_date()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <select type="time" class="form-control" name="service_time" id="service_time"
                                    required onchange="check_time()">
                                    <option value="">Select</option>
                                    <option value="00:00:00">12:00 AM</option>
                                    <option value="00:30:00">12:30 AM</option>
                                    <option value="01:00:00">01:00 AM</option>
                                    <option value="01:30:00">01:30 AM</option>
                                    <option value="02:00:00">02:00 AM</option>
                                    <option value="02:30:00">02:30 AM</option>
                                    <option value="03:00:00">03:00 AM</option>
                                    <option value="03:30:00">03:30 AM</option>
                                    <option value="04:00:00">04:00 AM</option>
                                    <option value="04:30:00">04:30 AM</option>
                                    <option value="05:00:00">05:00 AM</option>
                                    <option value="05:30:00">05:30 AM</option>
                                    <option value="06:00:00">06:00 AM</option>
                                    <option value="06:30:00">06:30 AM</option>
                                    <option value="07:00:00">07:00 AM</option>
                                    <option value="07:30:00">07:30 AM</option>
                                    <option value="08:00:00">08:00 AM</option>
                                    <option value="08:30:00">08:30 AM</option>
                                    <option value="09:00:00">09:00 AM</option>
                                    <option value="09:30:00">09:30 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="10:30:00">10:30 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="11:30:00">11:30 AM</option>
                                    <option value="12:00:00">12:00 PM</option>
                                    <option value="12:30:00">12:30 PM</option>
                                    <option value="13:00:00">01:00 PM</option>
                                    <option value="13:30:00">01:30 PM</option>
                                    <option value="14:00:00">02:00 PM</option>
                                    <option value="14:30:00">02:30 PM</option>
                                    <option value="15:00:00">03:00 PM</option>
                                    <option value="15:30:00">03:30 PM</option>
                                    <option value="16:00:00">04:00 PM</option>
                                    <option value="16:30:00">04:30 PM</option>
                                    <option value="17:00:00">05:00 PM</option>
                                    <option value="17:30:00">05:30 PM</option>
                                    <option value="18:00:00">06:00 PM</option>
                                    <option value="18:30:00">06:30 PM</option>
                                    <option value="19:00:00">07:00 PM</option>
                                    <option value="19:30:00">07:30 PM</option>
                                    <option value="20:00:00">08:00 PM</option>
                                    <option value="20:30:00">08:30 PM</option>
                                    <option value="21:00:00">09:00 PM</option>
                                    <option value="21:30:00">09:30 PM</option>
                                    <option value="22:00:00">10:00 PM</option>
                                    <option value="22:30:00">10:30 PM</option>
                                    <option value="23:00:00">11:00 PM</option>
                                    <option value="23:30:00">11:30 PM</option>
                                </select>
                                {{--  <input type="time" class="form-control" name="service_time" id="service_time" required onchange="check_time()">  --}}
                                <script>
                                    function check_date() {
                                        var amcDate = new Date($('#date').val());
                                        var serviceDate = new Date($('#service_date').val());
                                        if (amcDate > serviceDate) {
                                            $('#service_date').val('');
                                            $('#service_date').focus();
                                        }
                                    }

                                    function check_time() {
                                        var selected_date = $('#service_date').val();
                                        var selected_time = $('#service_time').val();
                                        var dateToCompare = new Date(selected_date + ' ' + selected_time);
                                        var currentDate = new Date();
                                        if (dateToCompare > currentDate) {

                                        } else {
                                            $('#service_time').val('');
                                            $('#service_time').focus();
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Source</label>
                                <select class="form-control" name="source" id="source" required>
                                    <option selected value="">Select</option>
                                    <option value="Email">Email</option>
                                    <option value="Whatsapp">Whatsapp</option>
                                    <option value="Phone">Phone</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Engineer</label>
                                <select class="form-control js-example-basic-single" name="service_engineer[]"
                                    id="service_engineer" required multiple>
                                    @if (count($salesperson) > 0)
                                        @foreach ($salesperson as $list)
                                            <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <a onclick="add_scope_of_work()" class="btn-sm btn-primary float-right"><i
                                        class="fa fa-plus-square" aria-hidden="true"></i></a>

                                <table width="100%">
                                    <tr>
                                        <td width="1%">1. </td>
                                        <td><input type="text" class="form-control" name="scope_of_work[]"
                                                id="scope_of_work_1" required></td>
                                    </tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                        <tr id="row_{{ $i }}" style="display:none;">
                                            <td>{{ $i }}. </td>
                                            <td><input type="text" class="form-control" name="scope_of_work[]"
                                                    id="scope_of_work_{{ $i }}"></td>
                                        </tr>
                                    @endfor
                                </table>
                                <input type="hidden" id="scope_of_work_row_id" value="1" />
                                <script>
                                    function add_scope_of_work() {
                                        var scope = $('#scope_of_work_row_id').val();
                                        if ($('#scope_of_work_' + scope).val() != "") {
                                            scope++;
                                            $('#row_' + scope).css('display', '');
                                            $('#scope_of_work_row_id').val(scope);
                                            $('#scope_of_work_' + scope).prop("required", true);
                                        }
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add AMC</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Service-->


    
 
<div class="modal fade" id="ModalAddProfessionalServicesRequest" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Project Request</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-request-add','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-ps-service-request-add']) }}

            <input type="hidden" name="amc_id" id="amc_id">

            <div class="modal-body">
                <div class="row">

                     <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Deal ID')<span></span></label>

                                    <input class="form-control" id="add_deal_id" type="text" required name="add_deal_id" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Date')<span></span></label>
                                     <input class="form-control" type="date" name="date" id="add_date" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                <div class="mb-3">
                                    <label for="" class="form-label">Customer Name</label>
                                    <select class="form-control js-example-basic-single" name="add_cust_name" id="add_cust_name"
                                        required>
                                        <option value="">-Select-</option>
                                        @foreach ($customers_AddRequest as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                              </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Contact Person')<span></span></label>

                                    <input class="form-control" id="add_contact_person" type="text" required name="contact_person" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                    <input class="form-control" id="add_mobile_no" type="text" required name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Service Engineer')<span></span></label>
                                    <select required id="add_engineer" name="add_engineer[]" class="form-control js-example-basic-single" multiple>
                                        <option></option>
                                    @php $englist=@App\SysHelper::get_engineer_list();
                                    foreach($englist as $list)                                    
                                        echo '<option value="'.$list->user_id.'" >'.$list->full_name.'</option>';
                                    @endphp
                                 </select>  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Location of Work')<span></span></label>

                                    <input class="form-control" id="add_location_of_work" type="text" autocomplete="off" required name="location_of_work" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Service Date')<span></span></label>

                                    <input class="form-control" id="add_service_date" type="date" required name="service_date"  value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Service Time')<span></span></label>

                                    <input class="form-control" id="add_service_time" type="time" required name="service_time" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Attachment')<span></span></label>

                                    <input class="form-control" id="attachment" type="file" name="attachment" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Scope of Work</label>
                            <a onclick="add_scope_of_work()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                            
                            <table width="100%">
                                <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" required></td></tr>
                                @for ($i=2; $i<=20; $i++)
                                <tr id="row_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_{{ $i }}"></td></tr>
                                @endfor
                            </table>
                            <input type="hidden" id="scope_of_work_row_id" value="1" />
                            <script>
                                function add_scope_of_work(){
                                    var scope = $('#scope_of_work_row_id').val();
                                    if($('#scope_of_work_'+scope).val() != ""){
                                        scope++;
                                        $('#row_'+scope).css('display','');
                                        $('#scope_of_work_row_id').val(scope);
                                        $('#scope_of_work_'+scope).prop("required", true);
                                    }
                                }
                            </script>

                        </div>
                    </div>


                </div>

            </div>
           

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Add Request')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>




    <!-- Modal Support-->
        <div class="modal fade" id="ModalSupport" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-sales-req-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="support_id" value="0" />
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="number" class="form-control" name="deal_id" id="deal_id" required>
                                </div>
                            </div>


                            <div class="col-md-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Customer Name</label>
                                        <select class="form-control js-example-basic-single" name="add_cust_name" id="sales_add_cust_name"
                                            required>
                                            <option value="">-Select-</option>
                                            @foreach ($customer_salesreq as $value)
                                                <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                            @endforeach
                                        </select>
                                  </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="col-md-4">
                               <div class="mb-3">
                                        <label class="form-label">@lang('Contact Person')<span></span></label>
                                        <input class="form-control" id="sales_add_contact_person" type="text" required name="contact_person" value="">
                               </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('Mobile No')<span></span></label>

                                <input class="form-control" id="sales_add_mobile_no" type="text" required name="mobile" value="">
                            </div>
                        </div>

                          <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input type="text" class="form-control" name="add_site_name" id="sales_add_site_name" required>
                                </div>
                            </div>

                         



                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">@lang('Service Date')<span></span></label>
                                    <input class="form-control" id="add_service_date" type="date" required name="service_date" value="">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">@lang('Service Time')<span></span></label>
                                    <input class="form-control" id="add_service_time" type="time" required name="service_time" value="">

                                </div>
                            </div>

                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="txtlbl">@lang('Service Engineer')<span></span></label>
                                    <select required id="add_engineer1" name="add_engineer[]" class="form-control js-example-basic-single" multiple>
                                        <option></option>
                                    @php $englist=@App\SysHelper::get_engineer_list();
                                    foreach($englist as $list)                                    
                                        echo '<option value="'.$list->user_id.'" >'.$list->full_name.'</option>';
                                    @endphp
                                 </select>  
                                </div>
                            </div>

                            <div class="col-md-4">
                        
                            <div class="mb-3">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Attachment')<span></span></label>

                                    <input class="form-control" id="attachment" type="file" name="attachment" value="">
                            </div>
                           
                        </div>
                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="" class="form-label">Scope of Work</label>
                                            <a onclick="add_scope_of_work()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a>

                                            <table width="100%">
                                                <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" required></td></tr>
                                                @for ($i = 2; $i <= 20; $i++)
                                                <tr id="row_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_{{ $i }}"></td></tr>
                                                @endfor
                                            </table>
                                            <input type="hidden" id="scope_of_work_row_id" value="1" />
                                            <script>
                                                function add_scope_of_work(){
                                                    var scope = $('#scope_of_work_row_id').val();
                                                    if($('#scope_of_work_'+scope).val() != ""){
                                                        scope++;
                                                        $('#row_'+scope).css('display','');
                                                        $('#scope_of_work_row_id').val(scope);
                                                        $('#scope_of_work_'+scope).prop("required", true);
                                                    }
                                                }
                                            </script>
                                        </div>
                                    </div>

                            {{-- <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Support Person</label>
                                    <select class="form-control js-example-basic-single" name="support_person_id[]" id="support_person_id" multiple required>
                                        <option value="">-Select-</option>
                                        @foreach ($staff_support as $value)
                                        <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Support</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <!-- Modal Service-->



    <script>
        $(document).on("change", "#cust_name", function() {
            var id = $("#cust_name").val();
            get_cust_name1(id);
        });

        function get_cust_name1(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-customer-details') }}";
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
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                                .address2 + ', ' + dataResult['data'][i].city;

                            $("#contact_person").val(name.replace('null ', '').replace('null', ''));
                            $("#mobile_no").val(dataResult['data'][i].mobile);
                            $("#location_of_work").val(dataResult['data'][i].address);
                        }
                    } else {
                        $("#contact_person").val();
                        $("#mobile_no").val();
                        $("#location_of_work").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

      
      
        
    </script>


<script>
     $(document).on("change", "#add_cust_name", function() {
            var id = $("#add_cust_name").val();
            get_cust_name2(id);
        });

        function get_cust_name2(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-customer-details') }}";
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
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                                .address2 + ', ' + dataResult['data'][i].city;

                            $("#add_contact_person").val(name.replace('null ', '').replace('null', ''));
                            $("#add_mobile_no").val(dataResult['data'][i].mobile);
                            $("#add_location_of_work").val(dataResult['data'][i].address);
                        }
                    } else {
                        $("#add_contact_person").val();
                        $("#add_mobile_no").val();
                        $("#add_location_of_work").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

</script>



<script>
     $(document).on("change", "#sales_add_cust_name", function() {
            var id = $("#sales_add_cust_name").val();
            get_cust_name3(id);
        });

        function get_cust_name3(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-customer-details') }}";
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
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                                .address2 + ', ' + dataResult['data'][i].city;

                            $("#sales_add_contact_person").val(name.replace('null ', '').replace('null', ''));
                            $("#sales_add_mobile_no").val(dataResult['data'][i].mobile);
                            $("#sales_add_site_name").val(dataResult['data'][i].address);
                        }
                    } else {
                        $("#sales_add_contact_person").val();
                        $("#sales_add_mobile_no").val();
                        $("#sales_add_site_name").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

</script>

                <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
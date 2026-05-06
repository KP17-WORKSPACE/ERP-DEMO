@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    
<?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">
                </h2>
            </div>
            <div>
                <a href="{{ url('crm-ps-track-service-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Project Service List</a>
            </div>
        </div>


        @if(@isset($psdata))
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">Project Service ID : {{ $psdata->doc_number }}</h2>
                    @if ($psdata->deal_id != '')
                    <span class="mb-1">Deal ID : {{ App\SysHelper::get_code_from_dealid($psdata->deal_id) }}</span>
                    @endif
                    <p class="mb-2 text-white-100 text-uppercase">Date : {{ date('d/M/Y', strtotime($psdata->date)) }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Sales Person : {{ $psdata->ownername->full_name }}</p>
                    {{--  <span class="mb-1">AMC Period : {{date('d/M/Y', strtotime(@$psdata->start_date))}} to {{ date('d/M/Y', strtotime(@$psdata->end_date)) }}</span>  --}}
                    <a class="btn-sm btn-danger" data-toggle="modal" data-target="#ModalProfessionalServicesRequest" data-backdrop="static" data-keyboard="false" style="cursor: pointer; width:90px;">Add Request</a>
                    
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Details</h2>                    
                    <b class="mb-2">{{ $psdata->custname->name }}</b>
                    <p class="mb-2 text-white-100 text-uppercase">Contact Person: {{ $psdata->contact_person }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Contact Number: {{ $psdata->mobile }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Location of Work: {{ $psdata->location_of_work }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Amount: {{ $psdata->amount }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Description</h2>
                    {{ $psdata->deal_description }}
                </div>
            </div>
            
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Service Details</h2>
                    @if (count($service_request) > 0)
                    @foreach ($service_request as $sr)
                        Service Date: {{ date('d/M/Y', strtotime($sr->work_date)) }}<br />
                        Time: {{ date('h:i A', strtotime($sr->work_time_from)) }} to {{ date('h:i A', strtotime($sr->work_time_to)) }}<br />
                        Comments: {{ $sr->comments }}<br />
                        Engineer: {{ $sr->engineerid->full_name }}
                        <hr />
                    @endforeach                    
                    @endif

                    @if (count($service_request_work) > 0)
                    @php $i=1; @endphp
                    <b>Scope of Work:-</b>
                        @foreach ($service_request_work AS $w)
                            {{ $i }}. {{ $w->work }} <br />
                            @php $i++; @endphp
                        @endforeach                        
                    @endif
                </div>
            </div>

        </div>
        @endif



        <!-- Modal Professional Services Request -->
        <div class="modal fade" id="ModalProfessionalServicesRequest" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Project Service Request</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-track-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-ps-service-track-submit']) }}
        
                    <input type="hidden" name="amc_id" id="amc_id" value="{{ $psdata->id }}">
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Date')<span></span></label>
                                             <input class="form-control" type="date" name="date" id="date" required value="{{ date('Y-m-d', strtotime(@$psdata->date)) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Customer Name')<span></span></label>
                                            <input class="form-control" type="text" required  value="{{ $psdata->custname->name }}" readonly>        
                                            <input id="cust_name" type="hidden" required name="cust_name" value="{{ $psdata->cust_name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Contact Person')<span></span></label>
        
                                            <input class="form-control" id="contact_person" type="text" required name="contact_person" value="{{ $psdata->contact_person }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Mobile No')<span></span></label>
        
                                            <input class="form-control" id="mobile" type="text" required name="mobile" value="{{ $psdata->mobile }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Location of Work')<span></span></label>
        
                                            <input class="form-control" id="location_of_work" type="text" autocomplete="off" required name="location_of_work" value="{{ $psdata->location_of_work }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Service Date')<span></span></label>
        
                                            <input class="form-control" id="service_date" type="date" required name="service_date" min="{{ date('Y-m-d') }}" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Service Time')<span></span></label>
        
                                            <input class="form-control" id="service_time" type="time" required name="service_time" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Service Engineer')<span></span></label>
                                            <select id="engineer" name="engineer[]" class="form-control js-example-basic-single" multiple>
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
                                        <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" value="{{ $psdata->deal_description }}" required></td></tr>
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
                                        function view_scope_of_work2(id,dat){
                                            $('#row_'+id).css('display','');
                                            $('#scope_of_work_'+id).val(dat);
                                        }
                                    </script>

                                    
                                @if (count($service_request_work)>0)
                                <input type="hidden" id="a_count" value="{{ count($service_request_work) }}" />
                                @for ($i=1; $i <= count($service_request_work); $i++)
                                <input type="hidden" id="a_{{ $i }}" value="{{ $service_request_work[$i-1]->work }}" />
                                @endfor
                                @endif
                                <script>
                                    var abc = $('#a_count').val();
                                    for(i=1; i <= abc; i++){
                                        var val= $('#a_'+i).val();
                                        view_scope_of_work2(i,val);
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
        <!-- Modal Professional Services Request -->
    
    

    <!-- Modal Support Activity -->
    <div class="modal fade" id="ModalActivity" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Support Activity</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                
                @if(@isset($support))
                    <input type="hidden" name="support_id" value="{{ $support->id }}" />
                    @php
                    $value = date('m-d-Y');
                    if(isset($support) ){
                        @$support_date = date('Y-m-d', strtotime(@$support->support_date));
                        @$time_from = date('H:i', strtotime(@$support->time_from));
                        @$time_to = date('H:i', strtotime(@$support->time_to));
                    }
                    @endphp
                @else
                    <input type="hidden" name="support_id" value="0" />
                @endif
                
                <input type="hidden" name="service_id" value="{{ $support->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="activity_date" id="activity_date" value="{{ $support_date }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">From</label>
                                <input type="time" class="form-control" name="activity_from" id="activity_from" value="{{ $time_from }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">To</label>
                                <input type="time" class="form-control" name="activity_to" id="activity_to" value="{{ $time_to }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Document</label>
                                <input type="file" class="form-control mr-5" name="activitydoc" id="activitydoc">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Activity</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Support Activity -->
    
    <!-- Modal Support Close -->
    <div class="modal fade" id="ModalActivityClose" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Close This Task</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-close', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                
                @if(@isset($support))
                    <input type="hidden" name="support_id" value="{{ $support->id }}" />
                @else
                    <input type="hidden" name="support_id" value="0" />
                @endif
                
                <input type="hidden" name="service_id" value="{{ $support->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Document</label>
                                <input type="file" class="form-control mr-5" name="closingdoc" id="closingdoc">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Close Task</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Support Close -->



<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
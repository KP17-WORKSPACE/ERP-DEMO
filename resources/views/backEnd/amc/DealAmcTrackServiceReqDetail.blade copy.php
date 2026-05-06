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
                <a href="{{ url('crm-ps-service-list-req') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Project Service Request List</a>
            </div>
        </div>


        @if(@isset($psdata))
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">Project Service Request ID : {{ $psdata->doc_number }}</h2>
                    @if ($psdata->deal_id != '')
                    <span class="mb-1">Deal ID : {{ App\SysHelper::get_code_from_dealid($psdata->deal_id) }}</span>
                    @endif
                    <p class="mb-2 text-white-100 text-uppercase">Date : {{ date('d/M/Y', strtotime($psdata->date)) }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Sales Person : {{ $psdata->ownername->full_name }}</p>

                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Details</h2>                    
                    <b class="mb-2">{{ $psdata->custname->name }}</b>
                    <p class="mb-2 text-white-100 text-uppercase">Contact Person: {{ $psdata->contact_person }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Contact Number: {{ $psdata->mobile }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Location of Work: {{ $psdata->location_of_work }}</p>
                    {{--  <p class="mb-2 text-white-100 text-uppercase">Amount: {{ $psdata->amount }}</p>  --}}
                    <p class="mb-2 text-white-100 text-uppercase">Attachment: <a target="_blank" class="text-primary mr-1" href="{{asset('public/uploads/crm_amc_doc/')}}/{{ @$psdata->attachment }}"><i class="fa fa-download" aria-hidden="true"></i> Download</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Scope Of Work</h2>
                    @if (count($service_request_work) > 0)
                    @php $i=1; @endphp
                        @foreach ($service_request_work AS $w)
                            {{ $i }}. {{ $w->work }} <br />
                            @php $i++; @endphp
                        @endforeach                        
                    @endif
                    
                    <?php
                        $st = array_map('intval', explode(',',$psdata->engineer));
                        $engineername="";
                        if(count($st)>0){
                        foreach($st as $u){
                            $s = $staff->where('user_id',$u)->pluck('full_name');                            
                            if($engineername == ""){
                                $engineername .= $s[0];
                            } else { $engineername .= ", ".$s[0]; }
                        }
                        }
                    ?>
                    <br />
                    <p class="mb-2 text-white-100 text-uppercase">Engineer : {{ $engineername }}</p>
                </div>
            </div>
            
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Service Details</h2>
                    @if (count($service_request)>0)
                    @foreach ($service_request as $sr)
                    Service Date: {{ date('d/M/Y', strtotime($sr->work_date)) }}<br />
                    Time: {{ date('h:i A', strtotime($sr->work_time_from)) }} to {{ date('h:i A', strtotime($sr->work_time_to)) }}<br />
                    Comments: {{ $sr->comments }}<br />
                    Engineer: {{ $sr->engineerid->full_name }}
                    <hr />
                    @endforeach
                    @endif

                    
                </div>
            </div>

        </div>
        @endif



    
    

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
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
                <a href="{{ url('crm-amc-service-request-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> AMC Service Request List</a>
            </div>
        </div>

        @if(@isset($amcdata))
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">AMC ID : {{ $amcdata->doc_number }}</h2>
                    <p class="mb-2 text-white-100 text-uppercase">Date : {{ date('d/M/Y', strtotime($amcdata->date)) }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Service Date : {{ date('d/M/Y', strtotime($amcdata->service_date)) }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Service Time : {{ date('h:i A', strtotime($amcdata->service_time)) }}</p>
                    <span class="mb-1">Service Engineer : 
                        <?php
                        $st = explode(',', $amcdata->service_engineer);
                        $engineername="";
                        if(count($st)>0){
                            foreach($st as $u){
                                $s = $staff->where('user_id',$u)->pluck('full_name');
                                if($engineername==""){
                                    $engineername .= $s[0];
                                } else { $engineername .= ", " . $s[0]; }
                                
                            }
                        }
                        ?>
                        {{ $engineername }}</span>
                    <span class="mb-1">Source : {{ $amcdata->source }}</span>
                    
                    
                    
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Details</h2>
                    <b class="mb-2">{{ $amcdata->custname->name }}</b>
                    <p class="mb-2 text-white-100 text-uppercase">Contact Person : {{ $amcdata->contact_person }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Mobile No : {{ $amcdata->mobile_no }}</p>

                    <p class="mb-2 text-white-100 text-uppercase">Location of Work: {{ $amcdata->location_of_work }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Scope of Work</h2>
                    
                    @php
                                $sw = $amc_work->where('amc_id',$amcdata->id);
                            @endphp
                            @if (count($sw) > 0)
                            @php $i=1; @endphp
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                @foreach ($sw AS $w)
                                <tr><td>
                                    {{ $i }}. {{ $w->work }} <br />
                                    @php $i++; @endphp
                                </td></tr>
                                @endforeach
                            </table>
                            @endif 

                </div>
            </div>
            
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Service Comments</h2>
                    @if (count($amc_comments)>0)
                    @foreach ($amc_comments as $dt)
                        Date: {{ date('d/M/Y h:i A', strtotime($dt->created_at)) }}<br />
                        Engineer: {{ $dt->full_name }}<br />
                        Work: {{ $dt->work }}<br />
                        Comments: {{ $dt->comments }}<br />
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
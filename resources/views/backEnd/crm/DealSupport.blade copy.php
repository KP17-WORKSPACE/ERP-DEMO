@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    
<?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
            </div>
            <div>
                <a href="{{ url('crm-deal-support-requested-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Pre-Sales Request List</a>
            </div>
        </div>


        @if(@isset($support))
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100 @if($support->status==3) bg-3 @else bg-1 @endif">
                    <h2 class="head">Pre-Sales ID : {{ $support->doc_number }}</h2>
                    <p class="mb-2 text-white-100 text-uppercase">Location : {{ $support->site_name }}</p>
                    <p class="mb-2 text-white-100 text-uppercase">Sales Person : {{ $support->salesperson->full_name }}</p>
                    <span class="mb-1">Date & Time : {{date('d/M/Y', strtotime(@$support->support_date))}} . {{ date('h:i A', strtotime(@$support->time_from)) }} . {{ date('h:i A', strtotime(@$support->time_to)) }}</span>
                    @if ($support->deal_id != '')
                    <span class="mb-1">Deal ID : {{ $support->deal_id }}</span>
                    @endif
                    {{--  <div class="text-capitalize">Status : <b class="">
                        @if($support->status==1) <span class="btn-warning btn-badge py-1 px-2">New</span>@endif
                        @if($support->status==2) <span class="btn-info btn-badge py-1 px-2">Open</span> @endif
                        @if($support->status==3) <span class="btn-success btn-badge py-1 px-2">Close</span> @endif</b>
                        @if($support->status==4) <span class="btn-dark btn-badge py-1 px-2">Cancel</span> @endif</b>                         
                        @if($support->status!=3) <button class="btn btn-danger btn-xs pt-0 pb-0 float-right" data-toggle="modal" data-target="#ModalActivityClose">Close Task</button> @endif
                    </div>  --}}
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Support Info</h2>                    
                    <?php $sp = explode(",",$support->support_person_id);
                        foreach($sp as $s) { ?>
                            <div class="border border-info p-2 m-1 rounded-lg">
                            <h6 class="sub-head text-capitalize text-dark">{{ App\SysHelper::get_user_detail($s)->full_name }}</h6>
                            <span class="mb-1"> <span class="font-semibold">Mob No :</span> {{ App\SysHelper::get_user_detail($s)->mobile }}</span>
                            <span class="mb-1"><span class="font-semibold">Mail :</span> {{ App\SysHelper::get_user_detail($s)->email }}</span></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        @endif


        <div class="row">
            <div class="col-lg-6 ">

                @if($support->status==3)
                <div class="p-3 card mb-3 bg-2">
                    <div class="justify-content-between align-items-center">
                        <h2 class="page-heading">Task Closing Note</h2>
                    </br>
                    
                <div class="pl-3 pr-3 pb-3 pt-2 card mb-2 ">

                    <b class="mb-2 text-dark">Closing Date: {{date('d/M/Y h:i A', strtotime(@$support->close_at))}}</b>
                    <p class="mb-0 text-dark">{!! nl2br($support->close_remarks) !!}
                        @if ($support->closingdoc!="")<br /><br />
                            <a class="btn-xs btn-purple p-0" href="{{asset('public/uploads/crm_deal_support_doc/')}}/{{ $support->closingdoc }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View Document&nbsp;&nbsp;</a>
                        @endif
                    </p>
                    <p class="text-danger">{{ $support->closeby->full_name }}, Closed on {{date('d/M/Y h:i A', strtotime(@$support->close_at))}}</p>                    
                </div>

                    </div>
                </div>
                @endif



                <div class="p-3 card mb-3 bg-2">
                    <div class="justify-content-between align-items-center">
                        <h2 class="page-heading">Support Activity</h2>
                    </br>
                    @if (count($support_activity) > 0)
                <div class="pl-3 pr-3 pb-3 pt-2 card mb-3 ">
                    @foreach ($support_activity as $val)
                    
                    {{--  @if ($val->created_by == Auth::user()->id)
                    <a href="{{url('crm-deal-service/'.$val->service_id.'/view/'.$val->id.'')}}">Edit</a>
                    @endif  --}}

                    @if ($val->activity_date != null)
                    <b class="mb-2 text-dark">Support Time: {{date('d/M/Y', strtotime(@$val->activity_date))}}, {{ date('h:i A', strtotime(@$val->activity_from)) }} to {{ date('h:i A', strtotime(@$val->activity_to)) }}</b>
                    @endif
                    
                    <p class="mb-0 text-dark">{!! nl2br($val->remarks) !!}
                        @if ($val->file!="")<br /><br />
                            <a class="btn-xs btn-purple p-0" href="{{asset('public/uploads/crm_deal_support_doc/')}}/{{ $val->file }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View Document&nbsp;&nbsp;</a>
                        @endif
                    </p>
                    <p class="text-danger">{{ $val->createdby->full_name }}, updated on {{date('d/m/Y h:i A', strtotime($val->created_at))}}</p>
                    <hr />
                    @endforeach
                </div>
                @endif                    
                    </div>
                </div>
            </div>
            <div class="col-lg-6 ">
                <div class="p-3 card mb-3 bg-2">
                    <div class="justify-content-between align-items-center">
                        <h2 class="page-heading">Scope of Work</h2>
                        @php $scope_of_work = explode('$',$support->remarks); @endphp
                        @php $i=1; @endphp
                        @foreach ($scope_of_work as $work)
                        {{ $i }}. {{ $work }}<br />
                        @php $i++; @endphp
                        @endforeach
                        
                    </div>
                </div>
                @if($support->status!=3)
                <div class="w-100">
                    {{--  <button class="w-100 btn btn-danger btn-sm mt-2" data-toggle="modal" data-target="#ModalActivity">UPDATE PRE-SALES REQUEST</button>  --}}
                </div>
                @endif
            </div>
        </div>


    
    

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
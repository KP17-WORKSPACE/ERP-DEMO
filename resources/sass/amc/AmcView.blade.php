@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();    
    @endphp

<?php try { ?>
<div class="container-fluid mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="mb-3">
            <h2 class="page-heading m-0">AMC {{ $amc->id }}</h2>
            <span class="page-label">Home - AMC Details</span>
        </div>
        <div>            
            <a class="btn btn-primary" data-toggle="modal" data-target="#ModalCollaboration">Asign Staff</a>
            <a href="{{ url('crm-amc-form') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New AMC</a>
            <a href="{{ url('crm-amc-deal-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> View AMC</a>
            <a href="{{url('crm-amc/'.$amc->id.'/edit')}}" type="button" class="btn btn-info"><i class="fa fa-edit"></i> Edit AMC</a>
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="p-4 card h-100 bg-1" style="background: #3a3a3a;">
                <h2 class="head">AMC Info 
                </h2>
                <p class="mb-2 text-white-100 text-uppercase">{{ $amc->customername->name }}</p>
                <span class="mb-1">{{ $amc->customername->address }}</span>
                <span class="mb-1">Contact Person : {{ $amc->cust_name }}</span>
                <span class="mb-1">M: {{ $amc->cust_no }} | E: {{ $amc->cust_email }}</span>
                <div class="text-capitalize">Status : 
                    @if($amc->status==1) <b style="background: #000000;" class="text-success p-1 pl-2 pr-2">Active</b> @endif
                            @if($amc->status==2) <b style="background: #000000;" class="text-warning p-1 pl-2 pr-2">Hold</b> @endif
                            @if($amc->status==3) <b style="background: #000000;" class="text-danger p-1 pl-2 pr-2">Inactive</b> @endif
                    <a href="#" class="edit btn-badge rejected py-1 px-3 font-weight-bold ml-2" onclick="updiv()">Edit</a>
                    <div class="border border-primary rounded bg-white text-sm p-2" id="div_update" style="display: none;">
                        <select class="dynamicstxt w-50" name="edit_status" id="edit_status" required>
                            <option value="">-Select-</option>
                            <option value="1">Active</option>
                            <option value="2">Hold</option>
                            <option value="3">Inactive</option>
                        </select>
                        <textarea class="form-control" name="lost_comments" rows="4" style="display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                        <button id="btn_edit_status" onclick="change_status({{ $amc->id }})" class="btn btn-xs btn-primary text-xs pt-0 pb-0">Change</button>
                    </div>
                    <script>
                        function updiv()
                        {
                            if($('#div_update').css('display') == 'none'){
                                $("#div_update").css("display", "block");
                            }
                            else{
                                $("#div_update").css("display", "none");
                            }
                        }
                        $('#edit_status').on('change', function(e) {
                            if ($('#edit_status').val() == 3) {
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
        </div>
        <div class="col-lg-4 mb-3">
            <div class="p-4 card h-100">
                <h2 class="head">Aditional Info</h2>
                <span class="mb-1"> <span class="font-semibold">Updated On :</span> {{ date('d/m/Y H:i:s', strtotime(@$amc->updated_at)) }}</span>
                @if($amc->cust_designation != "")<span class="mb-1"> <span class="font-semibold">Designation : </span> {{ $amc->cust_designation }}</span>@endif
                        @if($amc->address != "")<span class="mb-1"> <span class="font-semibold">Address :</span> {{ $amc->address }}</span>@endif
                        @if($amc->country!="")<span class="mb-1"> <span class="font-semibold">Country :</span> {{ $amc->country }}</span>@endif
                        <span class="mb-1"><span class="font-semibold">AMC Value : {{ App\SysHelper::currancy_format_deal($amc->amc_value,$amc->company_id) }}</span></span>
                        
                @if($amc->file !="")
                <?php $file = explode("|",$amc->file); ?>
                <span class="mb-1"> <span class="font-semibold">Attachment :</span>
                @foreach ($file as $f)
                    <a class="btn btn-success p-0" href="{{asset('public/uploads/crm_amc_doc/')}}/{{ $f }}" target="_blank">&nbsp;View & Download&nbsp;</a>
                @endforeach
                @endif
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="p-4 card h-100">
                <h2 class="head">Owner Info </h2>
                <h6 class="sub-head text-capitalize text-dark">{{ $amc->ownername->first_name }} {{ $amc->ownername->middle_name }} {{ $amc->ownername->last_name }}</h6>
                <p class="mb-1"> M: {{ $amc->ownername->mobile }} | E: {{ $amc->ownername->email }}</p>
                <p class="mb-1">Added On : {{ date('d/m/Y H:i:s', strtotime(@$amc->created_at)) }}</p>
                <p class="mb-1">Added By : {{ @$amc->createdby->first_name }}</p>
            </div>
        </div>
    </div>


        


    <div class="row">
        <div class="col-lg-6 mb-3 h-100">

            @if (count($asign)>0)
            <div class="p-3 card mb-3">
                <h5 class="sub-head m-0">Assigned To : 
                    @foreach ($asign as $val)
                    <span class="btn-primary btn-badge py-1 px-3 font-weight-bold">{{ $val->userid->full_name }}</span>
                    @endforeach
                </h5>
            </div>
            @endif

            @if($amc->tags != "")
            <div class="p-3 card mb-3">
                <h5 class="sub-head m-0">Tags : 
                    <?php $myArray = explode(',', $amc->tags); ?>
                    @foreach ($myArray as $item)
                    <span class="btn-primary btn-badge py-1 px-3 font-weight-bold">{{ $item }}</span>
                        
                    @endforeach
                </h5>
            </div>
            @endif

            @if($amc->remarks != "")
            <div class="p-3 card mb-3">
                <h5 class="sub-head m-0">Remarks : 
                    {!! nl2br($amc->remarks) !!}
                </h5>
            </div>
            @endif

            <div class="p-4 card">
                <div>
                    <label for="" class="font-weight-bold">Internal Note</label>                    
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-comments-add', 'method' => 'POST', 'id' => 'crm-amc-comments-add']) }}
                    <textarea name="comments" class="form-control" id="" cols="10" rows="3" required></textarea>
                    <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                    <input type="hidden" id="commentsid" name="commentsid" value="{{ $amc->id }}"/>
                    <div class="mt-2 justify-content-end d-flex">
                        <button type="submit" class=" btn-small">Add Note</button>
                    </div>
                    {{ Form::close() }}
                </div>
                
                @if(isset($comments))
                <div class="notes border py-2 px-3 mt-3">
                    @foreach ($comments as $cmts)
                    <div>
                        @if ($cmts->created_by == Auth::user()->id)
                        <a href="{{url('crm-amc-comments/'.$cmts->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-window-close text-sm text-danger float-right" aria-hidden="true"></i></a>                            
                        @endif
                        <p class="mb-0">{!! nl2br($cmts->comments) !!}
                        @if ($cmts->commentsdoc!="")<br /><br />
                            <a class="btn btn-xs btn-dark p-0 pl-1 pr-1" href="{{asset('public/uploads/crm_amc_doc/')}}/{{ $cmts->commentsdoc }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View File</a>
                        @endif
                        </p>
                        <p class="text-muted text-right">{{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($cmts->created_at))}}</p>
                    </div>
                    <hr>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            
            <div class="p-3 card mb-3 bg-2">
                <h2 class="sub-head m-0">AMC Period: 
                    {{ date('d-M-Y', strtotime($amc->from_date)) }} TO {{ date('d-M-Y', strtotime($amc->to_date)) }}
                    &nbsp;
                    <span class="border p-1">{{ App\SysHelper::get_days_from_dates($amc->from_date,$amc->to_date) }} Days</span>
                </h2>
            </div>
            
            <div class="p-4 card" style="border: solid 1px #e74a3b;">
                <div>
                    <a href="#" type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalAMCSupport"><i class="fa fa-plus"></i> ADD AMC SUPPORT</a>
                </div>
                
                @if(isset($support))
                    @foreach ($support as $cmts)
                    <div class="notes border py-2 px-3 mt-3">
                        <div>
                            @if ($cmts->created_by == Auth::user()->id)
                            <a href="{{url('crm-amc-support/'.$cmts->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-window-close text-sm text-danger float-right" aria-hidden="true"></i></a>                            
                            @endif
                            <p class="mb-0"><b>Support Type : </b>@if($cmts->support_type==1) <span class="btn btn-primary p-0 pl-1 pr-1">Onsite Support</span> @else <span class="btn btn-info p-0 pl-1 pr-1">Remote Support</span> @endif
                            
                            @if ($cmts->commentsdoc!="")
                                <a class="btn btn-xs btn-dark p-0 pl-1 pr-1" href="{{asset('public/uploads/crm_amc_doc/')}}/{{ $cmts->commentsdoc }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                            @endif

                            @if($cmts->support_person_id != "")
                                <?php $myArray = explode(',', $cmts->support_person_id); ?>
                                @foreach ($myArray as $item)
                                    <span class="btn btn-success p-0 pl-1 pr-1">{{ App\SysHelper::get_user_detail($item)->full_name }}</span>                                        
                                @endforeach
                            @endif

                            </p>
                            <hr />
                            <p class="mb-0">
                                <b>Support Date & Time : </b><span class="p-0 pl-1 pr-1">{{ date('d-M-Y', strtotime($cmts->support_date)) }}</span>
                                <b>|</b> <span class="p-0 pl-1 pr-1">{{ date('h:i A', strtotime($cmts->support_time_from)) }}</span>
                                <b>To</b> <span class="p-0 pl-1 pr-1">{{ date('h:i A', strtotime($cmts->support_time_to)) }}</span>
                            </p>
                            <hr />
                            <p class="mb-0"><b>Comments :-</b><br /> {!! nl2br($cmts->comments) !!}</p>
                            <p class="text-muted text-right">{{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($cmts->created_at))}}</p>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

        </div>

        <div class="col-lg-6 h-100 mb-3">
        </div>

    </div>


</div>


<script>    
    
    function change_status(id) {
        $("#loading_bg").css("display", "block");
        var status = $("#edit_status").val();
        var comments = $("#lost_comments").val();
        var amc_id = $("#commentsid").val();
    
        if (status == "" || status <= 0) {
            alert("Please Choose Status");
            $("#edit_status").focus();
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (status == 3 && comments == "") {
            alert("Please Enter Comments");
            $("#lost_comments").focus();
            $("#loading_bg").css("display", "none");
            return false;            
        }
        $("#btn_edit_status").attr('disabled', true);
    
        var action = "{{ URL::to('crm-amc-update-status') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                status: status,
                comments: comments,
                amc_id: amc_id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                } else {
                    //$("#loading_bg").css("display", "none");
                    //alert("Renewed! Please update and continue");
                    location.reload(true);
                }
            }
        });
    }
    
        </script>

    <!-- Modal AMC Support-->
    <div class="modal fade" id="ModalAMCSupport" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add AMC Support</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-support-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amc_id" value="{{ $amc->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Support Type</label>
                                <select class="form-control js-example-basic-single" name="support_type" id="support_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1">Onsite Support</option>
                                    <option value="2">Remote Support</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Support Date</label>
                                <input type="date" class="form-control" name="support_date" id="support_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">From</label>
                                <input type="time" class="form-control" name="support_time_from" id="support_time_from" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">To</label>
                                <input type="time" class="form-control" name="support_time_to" id="support_time_to" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Support Description</label>
                                <textarea class="form-control" name="comments" id="comments" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">Attach File
                                <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Support Person</label>
                                <select class="form-control js-example-basic-single" name="support_person_id[]" id="support_person_id" multiple required>
                                    <option value="">-Select-</option>
                                    @foreach ($support_staff as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal AMC Support-->

    <!-- Modal Collaboration-->
    <div class="modal fade" id="ModalCollaboration" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Staffs</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-asign-staff', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="staff_amc_id" value="{{ $amc->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" multiple>
                                    @foreach ($support_staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if (isset($asign)) @foreach ($asign as $coll)
                                        @if ($coll->user_id == $value->user_id) selected @endif
                                            @endforeach
                                    @endif >{{ @$value->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Collaboration-->

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
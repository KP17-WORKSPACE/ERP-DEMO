@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
    
    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Project  Request</h2>
                <span class="page-label">Home - Project  Request</span>
            </div>
            <div>

                 <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddProfessionalServicesRequest"
                    data-backdrop="static" data-keyboard="false">Add Request</a>

                <a type="button" href="{{ url('crm-ps-track-service-list') }}" class="btn btn-info"><i class="fa fa-filter mr-1"></i>Project List</a>

                <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-list-req', 'method' => 'POST', 'id' => 'crm-amc-search']) }}
                <div class="row">
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">Track ID</label>
                        <input class="form-control" id="search_ps_id" type="text" autocomplete="off" name="search_ps_id" value="{{ $ctrl_ps_id }}">
                    </div>
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>
                        <input class="form-control" id="search_deal_id" type="text" autocomplete="off" name="search_deal_id" value="{{ $ctrl_deal_id }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Customer Name</label>
                        <select class="form-control js-example-basic-single" name="search_customer_name" id="search_customer_name">
                            <option value="">-Select-</option>
                            @foreach ($customer as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_customer_name == $value->id) selected @endif>{{ @$value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Engineer</label>
                        <select class="form-control" name="search_sales_person" id="search_sales_person">
                            <option value="">Select</option>
                            @if(count($salesperson)>0)
                                @foreach ($salesperson as $list)
                                    <option value="{{ $list->user_id }}" @if($ctrl_sales_person == $list->user_id) selected @endif>{{ $list->full_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Date From</label>
                        <input class="form-control" id="search_from_date" type="date" autocomplete="off" name="search_from_date" value="{{ $ctrl_from_date }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Date To</label>
                        <input class="form-control" id="search_to_date" type="date" autocomplete="off" name="search_to_date" value="{{ $ctrl_to_date }}">
                    </div>
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">Status</label>
                        <select class="form-control" name="search_status" id="search_status">
                            <option value="1" @if($ctrl_search_status == 1) selected @endif>Pending</option>
                            <option value="2" @if($ctrl_search_status == 2) selected @endif>Completed</option>
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
                    <table class="table table-bordered table-striped" id="" width="100%" cellspacing="0">
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
                                <th width="70px">@lang('PS ID')</th>
                                <th width="50px">@lang('Deal ID')</th>
                                <th width="80px">@lang('Date')</th>
                                <th>@lang('Cust Name')</th>
                                <th>@lang('Engineer')</th>
                                <th>@lang('Scope of Work')</th>
                                <th>@lang('Service Date')</th>
                                <th>@lang('Service Time')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Attachment')</th>
                                <th width="150px">@lang('Action')</th>
                            </tr>
                        </thead>

                        <tbody>

                            @if(count($psData)>0)
                            @foreach($psData as $value)
                            <tr @if(@$value->is_delete == 1) class="bg-dark" @endif>
                                <td><a href="{{url('crm-ps-service-detail/'.$value->id)}}" target="_blank">{{@$value->doc_number}}</a></td>
                                <td><a href="{{url('get-url-deal-track/'.$value->deal_code->code)}}" target="_blank">{{@$value->deal_code->code}}</a></td>
                                <td>{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                <td>{{@$value->custname->name}}</td>
                                <?php
                                $engineername="";
                                if($value->engineer !=""){
                                    $st = explode(',', $value->engineer);
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
                                <td>{{@$engineername}}</td>
                                <?php
                                $w = $work->where('service_id',$value->id)->pluck('work');
                                $wo="";
                                if(count($w)>0){
                                    foreach($w as $wr){
                                        if($wo==""){
                                            $wo .= $wr;
                                        } else { $wo .= ", ".$wr; }
                                        
                                    }
                                }
                            ?>
                                <td>{{@$wo}}</td>

                                <td>{{date('d/m/Y', strtotime(@$value->service_date))}}</td>
                                <td>{{date('H:i A', strtotime(@$value->service_time))}}</td>
                                
                                <td>{!! App\SysHelper::get_ps_status(@$value->status) !!}</td>

                                <td>

                              @if(!empty($value->attachment))
                                    <a href="{{ url('public/uploads/crm_amc_doc/' . $value->attachment) }}" target="_blank" class="text-primary">View</a>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                                                                
                                
                                </td>


                                <td><a target="_blank" class="btn-sm btn-warning mr-1" href="{{asset('public/uploads/crm_amc_doc/')}}/{{ @$value->attachment }}"><i class="fa fa-download" aria-hidden="true"></i>
                                </a>
                                    <a class="btn-sm btn-primary" data-toggle="modal"
                                            data-target="#servicecomments_{{ $value->id }}" style="cursor: pointer;"
                                            data-backdrop="static" data-keyboard="false"><i class="fa fa-comments"
                                                aria-hidden="true"></i></a>
                                    <a class="btn-sm btn-primary" onclick="edit_service_request({{ $value->id }})"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                    @if(@$value->is_delete == 0)
                                        <a class="btn-sm btn-danger" onclick="return confirm('Are you sure?')" href="{{ url('crm-ps-service-request-deactivate/'.$value->id.'') }}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    @endif
                                    @if(@$value->is_delete == 1)
                                        <a class="btn-sm btn-info" onclick="return confirm('Are you sure?')" href="{{ url('crm-ps-service-request-activate/'.$value->id.'') }}"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                                    @endif
                                    
                                    <?php /*
                                    <input type="hidden" id="cid[]" value="{{@$value->id}}">
                                    @if(@$value->status != 1)
                                    <a data-id="{{@$value->id}}" class="btn-badge py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalDealTrack">
                                        Remarks</a>
                                    @endif
                                    @if(@$value->status == 1)
                                    <a data-id="{{@$value->id}}" class="crmajax btn-badge btn btn-info py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalDealTrackEdit" >
                                    <i class="fa fa-edit" aria-hidden="true"></i></a>
                                    @if(Auth::user()->role_id == 1)
                                    <a class="btn-sm btn-danger" href="{{url('crm-amc-service-track-list/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    @endif
                                   
                                    @endif

                                    */ ?>
                                   
                                </td>

                               

                               

                                </td>
                            </tr>

                            @endforeach                                
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @if (count($psData)>0)
                    @foreach ($psData as $ps)

                <div class="modal fade" id="servicecomments_{{ $ps->id }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Service Comments</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        @php
                                            $ps_comments_data = $ps_comments->where('ps_id',$ps->id);
                                        @endphp

                                        @if (count($ps_comments_data)>0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                     <th width="50%">Comment</th>
                                                    <th width="10%">Status</th>
                                                    <th style="width: 20%;">By</th>
                                                    <th width="20%">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ps_comments_data as $cmts)
                                                <tr>
                                                    <td>{{ $cmts->comments }}</td>
                                                    <td>
                                                        @if ($cmts->status == 1)
                                                            Pending
                                                        @else
                                                            Completed
                                                        @endif                                                    
                                                    </td>
                                                    <td>{{ $cmts->engineerid->full_name }}</td>
                                                    <td>{{ $cmts->created_at }}</td>
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

    <!-- Modal Edit Professional Services Request -->
<a id="btn_edit_professional_services_request" data-toggle="modal" data-target="#ModalEditProfessionalServicesRequest" data-backdrop="static" data-keyboard="false"></a>
<div class="modal fade" id="ModalEditProfessionalServicesRequest" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Project Request</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-request-update','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-ps-service-request-update']) }}

            <input type="hidden" name="amc_id" id="edit_amc_id">

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Date')<span></span></label>
                                     <input class="form-control" type="date" name="date" id="edit_date" required value="{{ date('Y-m-d') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Customer Name')<span></span></label>

                                    <input class="form-control" id="edit_cust_name" type="text" required name="cust_name" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Contact Person')<span></span></label>

                                    <input class="form-control" id="edit_contact_person" type="text" required name="contact_person" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                    <input class="form-control" id="edit_mobile" type="text" required name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Service Engineer')<span></span></label>
                                    <select id="edit_engineer" name="engineer[]" class="form-control js-example-basic-single" multiple>
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

                                    <input class="form-control" id="edit_location_of_work" type="text" autocomplete="off" required name="location_of_work" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Service Date')<span></span></label>

                                    <input class="form-control" id="edit_service_date" type="date" required name="service_date"  value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Service Time')<span></span></label>

                                    <input class="form-control" id="edit_service_time" type="time" required name="service_time" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                      
                    <div class="col-lg-3 mt-1">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                            <div class="input-effect">
                               <div class="">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="status_edit" id="status_edit">
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                </select>
                            </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-lg-3 mb-3">
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
                            <a onclick="edit_scope_of_work()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                            
                            <table width="100%" id="table_work">
                                <tbody></tbody>
                            </table>
                            <table width="100%">
                                @for ($i=20; $i<=40; $i++)
                                <tr id="row_edit_{{ $i }}" style="display:none;"><td width="1%"></td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_edit_{{ $i }}"></td><td width="1%"><a onclick="delete_scope_of_work({{ $i }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td></tr>
                                @endfor
                            </table>
                            <input type="hidden" id="scope_of_work_row_id_edit" value="19" />
                            <script>
                                function edit_scope_of_work(){
                                    var scope = $('#scope_of_work_row_id_edit').val();
                                    //var rowCount = document.getElementById('table_work').rows.length;
                                    //alert(rowCount);
                                    //if($('#scope_of_work_edit_'+scope).val() != ""){
                                        scope++;
                                        $('#row_edit_'+scope).css('display','');
                                        $('#scope_of_work_row_id_edit').val(scope);
                                        //$('#scope_of_work_edit_'+scope).prop("required", true);
                                    //}
                                }
                                function delete_scope_of_work(id){
                                        $('#row_edit_'+id).css('display','none');
                                        $('#scope_of_work_edit_'+id).val('');
                                }
                            </script>

                        </div>
                    </div>

                </div>

            </div>
           

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Update Request')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Edit Professional Services Request -->

    <script>
        function edit_service_request(id){
            get_ps_service_request_edit(id);
            $('#btn_edit_professional_services_request').click();
            get_ps_service_request_scope_of_work(id);
        }
        function get_ps_service_request_edit(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-ps-service-request-edit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        console.log(dataResult);
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                $("#edit_amc_id").val(dataResult['data'][i].id);
                                $("#edit_date").val(new Date(dataResult['data'][i].date).toLocaleDateString('en-CA'));

                                $("#edit_cust_name").val(dataResult['data'][i].name);
                                
                                //alert(dataResult['data'][i].cust_name);
                                //$("#select2-cust_name_edit-container").val(dataResult['data'][i].cust_name);

                                $("#edit_contact_person").val(dataResult['data'][i].contact_person);
                                $("#edit_mobile").val(dataResult['data'][i].mobile);
                                $("#edit_engineer").val(dataResult['data'][i].engineer);

                                const selectElement = document.getElementById("edit_engineer");
                                const valuesToSelect = dataResult['data'][i].engineer;
                                for (let i = 0; i < selectElement.options.length; i++) {
                                    const option = selectElement.options[i];
                                    if(option.value != ""){
                                    if (valuesToSelect.includes(option.value)) {
                                      option.selected = true; // Select the option
                                    }}
                                  }


                                $("#edit_location_of_work").val(dataResult['data'][i].location_of_work);
                                //$("#edit_scope_of_work").val(dataResult['data'][i].scope_of_work);

                                {{--  const scop = dataResult['data'][i].scope_of_work.split("$");
                                for(k=0; k < scop.length; k++){                                    
                                    $("#scope_of_work_edit_"+(k+1)).val(scop[k]);
                                    $("#row_edit_"+(k+1)).css('display','');
                                }  --}}

                                $("#edit_service_date").val(dataResult['data'][i].service_date);
                                $("#edit_service_time").val(dataResult['data'][i].service_time);
                                $("#status_edit").val(dataResult['data'][i].status);
                                {{--  get_amc_scope_of_work(dataResult['data'][i].id);  --}}


                            }
                        }
                        else{
                            $("#edit_amc_id").val();
                            $("#edit_date").val();
                            $("#edit_cust_name").val();
                            $("#edit_contact_person").val();
                            $("#edit_mobile").val();
                            $("#edit_engineer").val();
                            $("#edit_location_of_work").val();
                            //$("#edit_scope_of_work").val();
                            $("#edit_service_date").val();
                            $("#edit_service_time").val();
                        }
                        $('#edit_engineer').change();
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        function get_ps_service_request_scope_of_work(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-ps-service-request-work') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var tr = "";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){

                                tr += '<tr>\
                                    <td width=1% >' + (i+1) + '. <input type="hidden" value="' + dataResult['data'][i].id + '" name="scope_of_work_id[]"></td>\
                                    <td><input value="' + dataResult['data'][i].work + '" class="form-control" type="text" id="scope_of_work_edit_' + i + '" name="scope_of_work[]" autocomplete="off"></td><td width=1% ></td>\
                                </tr>';                                
                            }
                        }
                        else{

                        }
                        
                        $('#table_work tbody').empty();
                        $("#table_work tbody").append(tr);
                        $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {


            $('.crmajax').click(function() {
                id = $(this).data('id')
                //alert(id)
                


                var action = "crm-amc-service-track-id";
                $.ajax({
                    url: action,
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',

                        id: id,

                    },

                    success: function(dataResult) {
                        
                        var dataResult = JSON.parse(dataResult);
                        //alert(dataResult)
                       
                        $('#cust_name').val(dataResult.cust_name)
                        $('#start_date').val(dataResult.start_date)
                        $('#end_date').val(dataResult.end_date)
                        $('#amount').val(dataResult.amount)
                        $('#sales_person').val(dataResult.sales_person)
                        
                        $('#contact_person1').val(dataResult.contact_person)
                        $('#mobile1').val(dataResult.mobile)
                        $('#source1').val(dataResult.source)
                        $('#scope_work1').val(dataResult.scope_work)
                        $('#sales_person1').val(dataResult.sales_person)
                        $('#amount1').val(dataResult.amount)
                        $('#cust_name1').val(dataResult.cust_name)
                        $('#service_date_time1').val(dataResult.service_date_time)
                        $('#engineer1').val(dataResult.engineer)


                    }
                });
            });



            $('.eng').click(function() {
                //id = $(this).data('eng')
                id=document.getElementById('engineer1').value;
               // alert('id is==='+id)


                var action = "crm-amc-service-track-id";
                $.ajax({
                    url: action,
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',

                        id: id,

                    },

                    success: function(dataResult) {
                        
                        var dataResult = JSON.parse(dataResult);
                       // alert(dataResult)
                       
                        // $('#cust_name').val(dataResult.cust_name)
                        // $('#start_date').val(dataResult.start_date)
                        // $('#end_date').val(dataResult.end_date)
                        // $('#amount').val(dataResult.amount)
                        // $('#sales_person').val(dataResult.sales_person)
                        
                        // $('#contact_person1').val(dataResult.contact_person)
                        // $('#mobile1').val(dataResult.mobile)
                        // $('#source1').val(dataResult.source)
                        // $('#scope_work1').val(dataResult.scope_work)
                        // $('#sales_person1').val(dataResult.sales_person)
                        // $('#amount1').val(dataResult.amount)
                        // $('#cust_name1').val(dataResult.cust_name)
                        // $('#service_date_time1').val(dataResult.service_date_time)
                        // $('#engineer1').val(dataResult.engineer)
                        

                    }
                });
            });

            $('.btn-badge').click(function() {

                $('#order-id').val($(this).data('id'));
                $('#order-id1').val($(this).data('id'));
                $('#order-id').html($(this).data('id'));
               
            });

            $('.btn-badge1').click(function() {

                
                $('#order-id1').val($(this).data('id'));
                $('#order-id1').html($(this).data('id'));
                //alert($('#order-id').val())
               
            });
        });

        function addDate(id) {
            $("#deal_id").val(id);
            $("#deal_name").val(id);
            $("#btnpopup").click();
        }

        function editDate(id) {
            $("#deal_id_edit").val(id);
            $("#deal_name_edit").val(id);

            var a = $("#amc_edit_" + id).val();
            var r = $("#remarks_edit_" + id).val();
            var f = $("#datef_edit_" + id).val();
            var t = $("#datet_edit_" + id).val();

            $("#amcid").val(a);
            $("#remarks_edit").val(r);
            $("#from_date_edit").val(f);
            $("#to_date_edit").val(t);

            $("#btnpopupedit").click();


        }

       
    </script>

    <a href="#" id="btnpopup" type="button" data-toggle="modal" data-target="#ModalSupport"></a>
    <!-- Modal Support-->
    <div class="modal fade" id="ModalSupport" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add AMC Period</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-amc', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="deal_id" id="deal_id" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal Id</label>
                                <input type="text" readonly class="form-control" name="deal_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">From Date</label>
                                <input type="date" class="form-control" name="from_date" id="from_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">To Date</label>
                                <input type="date" class="form-control" name="to_date" id="to_date" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add AMC Date</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Service-->

    <script>

    </script>

    <a href="#" id="btnpopupedit" type="button" data-toggle="modal" data-target="#ModalSupportEdit"></a>
    <!-- Modal Support-->
    <div class="modal fade" id="ModalSupportEdit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update AMC Period</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>


            </div>
        </div>
    </div>
    <!-- Modal Service-->


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
<!-- Modal Deal Track-->

<div class="modal fade" id="ModalDealTrack" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-track-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-submit123']) }}

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Contact Person')<span></span></label>
                                    <input type="hidden" name="amcid" id="order-id">
                                     <input class="form-control"  type="text" required name="contact_person">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Scope of Work')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="scope_work" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label>

                                    <input class="form-control" id="delivery_date" type="date" autocomplete="off" required name="service_date_time" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Source')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="source" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Engineer')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="engineer" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
           

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <!-- <input type="hidden" id="deal_id" name="deal_id" value="" /> -->
                <!-- <button type="submit" class="btn btn-info" value="save" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Save')</button> -->
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Submit For Approval')</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Deal Track-->




<div class="modal fade" id="ModalDealTrackEdit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Professional Service Requests

</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-track-edit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-service-track-edit']) }}

            <div class="modal-body">
                <div class="row">

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Customer Name1')<span></span></label>

                                     <input class="form-control" id="cust_name" type="text" required name="cust_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Start Date')<span></span></label>

                                     <input class="form-control" id="start_date" type="date" required name="start_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('End Date')<span></span></label>

                                     <input class="form-control" id="end_date" type="date" required name="end_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Amount')<span></span></label>

                                     <input class="form-control" id="amount" type="text" required name="amount">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Sales Person')<span></span></label>

                                     <input class="form-control" id="sales_person1" type="text" required name="sales_person">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Contact Person')<span></span></label>
                                    <input type="hidden" name="amcid1" id="order-id1">
                                     <input class="form-control" id="contact_person1" type="text" required name="contact_person">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                    <input class="form-control" id="mobile1" type="text" required name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Scope of Work')<span></span></label>

                                    <input class="form-control" id="scope_work1" type="text" required name="scope_work" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label>

                                    <input class="form-control" id="service_date_time1" type="date" autocomplete="off" required name="service_date_time" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Source')<span></span></label>

                                    <input class="form-control" id="source1" type="text" required name="source" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Engineer')<span></span></label>

                                    <select  class='eng' id="engineer1" name="engineer"  class="form-control js-example-basic-single" >

				 @php $englist=@App\SysHelper::get_engineer_list();
				foreach($englist as $list)
				
					echo '<option value="'.$list->user_id.'" >'.$list->full_name.'</option>';

				 @endphp

                                 </select>  

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <!-- <input type="hidden" id="deal_id" name="deal_id" value="" /> -->
                <!-- <button type="submit" class="btn btn-info" value="save" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Save')</button> -->
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit1" id="btnSubmit1"><span class="ti-check"></span>@lang('Submit For Approval')</button>
            </div>

            {{ Form::close() }}

        </div>
    </div>
</div>



<!-- Modal Deal Track-->




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


<script>
     $(document).on("change", "#add_cust_name", function() {
            var id = $("#add_cust_name").val();
            get_cust_name(id);
        });

        function get_cust_name(id) {
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


@endsection
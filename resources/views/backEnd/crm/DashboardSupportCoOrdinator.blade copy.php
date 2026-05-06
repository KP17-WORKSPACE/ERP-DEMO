@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp


    <?php try { ?>
    <div class="container-fluid mb-4">
        <div class="d-sm-flex justify-content-between">
        <div class="shadow" style="background: #36b9cc4a; color: #000; padding: 20px; margin-bottom: 20px; width: 100%;">
                <h2 class="page-heading m-0" style="font-size: 25px; font-weight: normal;"><span class=""><i class="fa fa-quote-left" aria-hidden="true"></i><br />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 19px; font-weight:normal; font-style: italic;">{{ App\SysHelper::get_quote_text() }}</span></h2>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />
        </div>
        <div>
            @if($d_full_name == "")
            <div class="p-2" style="background: #0b2262; color: #e5e5e5; min-width: 350px; width: 100%; height: auto; margin: 0px 0px 15px 0px;">
                @if (file_exists(@session('logged_session_data.staff_photo')))
                    <img class="rounded float-right" src="{{ file_exists(@session('logged_session_data.staff_photo')) ? asset(session('logged_session_data.staff_photo')) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="" height="85px">
                @else
                    <img class="rounded float-right" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="" height="85px">
                @endif
                <h6 class="font-weight-700">{{ session('logged_session_data.full_name') }}</h6>
                <b>{{ session('logged_session_data.designation_name') }}</b><br /><br />
                <i><i class="fa fa-calendar" aria-hidden="true"></i> {{ date('l, F j, Y') }}</i>
            </div>
            @else
            <div class="p-2" style="background: #0b2262; color: #e5e5e5; min-width: 350px; width: 100%; height: auto; margin: 0px 0px 15px 0px;">
                @if (file_exists(@session('logged_session_data.staff_photo')))
                    <img class="rounded float-right" src="{{ file_exists($d_staff_photo) ? asset($d_staff_photo) : asset('public/uploads/staff/demo/staff.jpg') }}" alt="" height="85px">
                @else
                    <img class="rounded float-right" src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="" height="85px">
                @endif
                <h6 class="font-weight-700">{{ $d_full_name }}</h6>
                <b>{{ $d_designation }}</b><br /><br />
                <i><i class="fa fa-calendar" aria-hidden="true"></i> {{ date('l, F j, Y') }}</i>
            </div>
            @endif
        </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">PS Approval</h4>
                        <a href="{{url('crm-deal-track-list/0')}}" class=" btn-small p-0 pl-2 pr-2">View All</a>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th>Deal</th>
                                        <th>Company</th>
                                        <th>Owner</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($pending_approval)>0)
                                    @foreach ($pending_approval as $top)
                                    <tr>
                                        <td><a href="{{url('crm-deal-track-approval/'.$top->id.'')}}" title="View Deal Track" class="text-dark">{{ $top->dealid->code }}</a></td>
                                        <td><div style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?php try {?>{{ $top->customername->name }}<?php } catch (\Exception $e) { }?></div></td>
                                        <td>{{ $top->ownername->full_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime($top->date)) }}</td>
                                        <td>{!! App\SysHelper::get_deal_status_log($top->accounts,$top->sales,$top->purchease,$top->invoice,$top->delivery,$top->receivables) !!}
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
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Project New Ticket</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Track ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$project_new)>0)
                                    @foreach (@$project_new as $dt)

                                    <input type="hidden" id="list_custname_{{ $dt->id }}" value="{{@$dt->custname->name}}" />
                                    <input type="hidden" id="contact_person_{{@$dt->id}}" value="{{ @$dt->contact_person }}" />
                                    <input type="hidden" id="mobile_{{@$dt->id}}" value="{{ @$dt->mobile }}" />
                                    <input type="hidden" id="location_of_work_{{@$dt->id}}" value="{{ @$dt->location_of_work }}" />
                                    <input type="hidden" id="deal_description_{{@$dt->id}}" value="{{ @$dt->deal_description }}" />

                                    <tr>
                                        <td><a onclick="add_professional_services_request({{@$dt->id}})" class="text-primary" style="cursor: pointer;">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->date)) }}</td>
                                        <td>{{ @$dt->custname->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Pre-Sales Support New</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Support ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$pre_sales_support_new)>0)
                                    @foreach (@$pre_sales_support_new as $dt)
                                    <tr>
                                        <td><a target="_blank" class="text-primary" onclick="pre_add_professional_services_request({{ $dt->id }})">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->support_date)) }}</td>
                                        <td>{{ @$dt->customer->name }}</td>
                                    </tr>

                                    
                                    <input type="hidden" id="pre_customer_id_{{@$dt->id}}" value="{{@$dt->customer_id}}">
                                    <input type="hidden" id="pre_customer_name_{{@$dt->id}}" value="{{@$dt->customer->name}}">
                                    <input type="hidden" id="pre_contact_person_{{@$dt->id}}" value="{{ @$dt->customer->first_name }} {{ @$dt->customer->last_name }}" />
                                    <input type="hidden" id="pre_mobile_{{@$dt->id}}" value="{{ @$dt->dealid->cust_no }}" />
                                    <input type="hidden" id="pre_location_of_work_{{@$dt->id}}" value="{{ @$dt->site_name }}" />
                                    <input type="hidden" id="pre_support_date_{{@$dt->id}}" value="{{ @$dt->support_date }}" />
                                    <input type="hidden" id="pre_time_from_{{@$dt->id}}" value="{{ @$dt->time_from }}" />
                                    <input type="hidden" id="pre_work_{{@$dt->id}}" value="{{ @$dt->remarks }}" />
                                    <input type="hidden" id="pre_date_{{@$dt->id}}" value="{{ date('Y-m-d', strtotime(@$dt->created_at)) }}" />
                                    <input type="hidden" id="pre_support_person_id_{{@$dt->id}}" value="{{ @$dt->support_person_id }}" />
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
            <script>
                function pre_add_professional_services_request(id){
        
                    var custid = $('#pre_customer_id_'+id).val();
                    var custname = $('#pre_customer_name_'+id).val();
                    var contact_person =  $('#pre_contact_person_'+id).val();
                    var mobile =  $('#pre_mobile_'+id).val();
                    var location_of_work =  $('#pre_location_of_work_'+id).val();            
                    var support_date =  $('#pre_support_date_'+id).val();
                    var time_from =  $('#pre_time_from_'+id).val();
                    var work = $('#pre_work_'+id).val();
                    var edit_date = $('#pre_date_'+id).val();
        
                    const inputString = work;
                    const itemsArray = inputString.split('$');
                    console.log(itemsArray);
                                
                    for(i=1; i <= itemsArray.length; i++){
                        var itm = itemsArray[i-1];
                        $('#pre_scope_of_work2_'+i).val(itm);
                        //add_scope_of_work2();
                    }
                    
                    for(k=1; k <= 20; k++){
                            $('#pre_row2_'+k).css('display','none');
                    }
        
                    $('#pre_engineer').change();
                    for(j=1; j <= itemsArray.length; j++){
                        if($('#pre_scope_of_work2_'+j).val()==""){
                            $('#pre_row2_'+j).css('display','none');
                        } else { $('#pre_row2_'+j).css('display',''); }
                    }
                    
        
                    $('#pre_pre_sales_id').val(id);
                    $('#pre_date').val(edit_date);
                    $('#pre_cust_id').val(custid);
                    $('#pre_cust_name').val(custname);
                    $('#pre_contact_person').val(contact_person);
                    $('#pre_mobile').val(mobile);
                    $('#pre_location_of_work').val(location_of_work);
                    $('#pre_service_date').val(support_date);
                    $('#pre_service_time').val(time_from);
        
                    $('#btn_pre_add_professional_services_request').click();
                }
            </script>
        
        <!-- Modal Professional Services Request -->
        <a id="btn_pre_add_professional_services_request" data-toggle="modal" data-target="#ModalPreProfessionalServicesRequest" data-backdrop="static" data-keyboard="false"></a>
        <div class="modal fade" id="ModalPreProfessionalServicesRequest" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list-request-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-support-list-request-submit']) }}
        
                    <input type="hidden" name="pre_sales_id" id="pre_pre_sales_id">
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Date')<span></span></label>
                                             <input class="form-control" type="date" name="date" id="pre_date" required value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Customer Name')<span></span></label>
                                            <input class="form-control" id="pre_cust_name" type="text" required name="cust_name" value="" readonly>
                                            <input id="cust_id" type="hidden" required name="cust_id" value="" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Contact Person')<span></span></label>
        
                                            <input class="form-control" id="pre_contact_person" type="text" required name="contact_person" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Mobile No')<span></span></label>
        
                                            <input class="form-control" id="pre_mobile" type="text" required name="mobile" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Location of Work')<span></span></label>
        
                                            <input class="form-control" id="pre_location_of_work" type="text" autocomplete="off" required name="location_of_work" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Service Date')<span></span></label>
        
                                            <input class="form-control" id="pre_service_date" type="date" required name="service_date" min="{{ date('Y-m-d') }}" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Service Time')<span></span></label>
        
                                            <input class="form-control" id="pre_service_time" type="time" required name="service_time" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="col-lg-4 mb-3">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label class="txtlbl">@lang('Service Engineer')<span></span></label>
                                            <select id="pre_engineer" name="engineer[]" class="form-control js-example-basic-single" multiple>
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
        
                                            <input class="form-control" id="pre_attachment" type="file" name="attachment" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Scope of Work</label>                            
                                    <table width="100%">
                                        <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="pre_scope_of_work2_1" required></td>
                                            <td width="1%"><a onclick="pre_add_scope_of_work2()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a></td></tr>
                                        @for ($i=2; $i<=20; $i++)
                                        <tr id="pre_row2_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="pre_scope_of_work2_{{ $i }}"></td>
                                            <td><a class="btn-sm btn-danger" style="float: right;" onclick="pre_delete_scope_of_work2({{ $i }})"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                        </tr>
                                        @endfor
                                    </table>
                                    <input type="hidden" id="pre_scope_of_work_row2_id" value="1" />
                                    <script>
                                        function pre_add_scope_of_work2(){
                                            var scope = $('#scope_of_work_row2_id').val();                                    
                                                scope++;
                                                $('#pre_row2_'+scope).css('display','');
                                                $('#pre_scope_of_work_row2_id').val(scope);
                                                //$('#scope_of_work2_'+scope).prop("required", true);
                                        }
                                        function pre_delete_scope_of_work2(id){
                                                $('#pre_row2_'+id).css('display','none');
                                                $('#pre_scope_of_work2_'+id).val('');
                                                $('#pre_scope_of_work2_'+id).prop("required", false);
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

            
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">AMC Pending Ticket</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Track ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$amc_pending)>0)
                                    @foreach (@$amc_pending as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-amc-service-request-detail/'.$dt->id) }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                        <td>{{ @$dt->custname->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Project Pending Ticket</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Track ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$project_pending)>0)
                                    @foreach (@$project_pending as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-ps-service-detail/'.$dt->id) }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                        <td>{{ @$dt->custname->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Pre-Sales Support Pending</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Support ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$pre_sales_support_pending)>0)
                                    @foreach (@$pre_sales_support_pending as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-deal-support/'.$dt->id.'/view') }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->support_date)) }}</td>
                                        <td>{{ @$dt->customer->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">AMC Completed Ticket</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Track ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$amc_completed)>0)
                                    @foreach (@$amc_completed as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-amc-service-request-detail/'.$dt->id) }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                        <td>{{ @$dt->custname->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
            


            
            


            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Project Completed Ticket</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Track ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$project_completed)>0)
                                    @foreach (@$project_completed as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-ps-service-detail/'.$dt->id) }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                        <td>{{ @$dt->custname->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
            
            
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">Pre-Sales Support Completed</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Support ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count(@$pre_sales_support_completed)>0)
                                    @foreach (@$pre_sales_support_completed as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-deal-support/'.$dt->id.'/view') }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->support_date)) }}</td>
                                        <td>{{ @$dt->customer->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>

            {{--  <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title m-0">AMC New Ticket</h4>
                    </div>
                    <div class="card-body pt-0  max-height">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped2">
                                <thead>
                                    <tr>
                                        <th width="70px">Track ID</th>
                                        <th width="70px">Date</th>
                                        <th>Customer Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1; @endphp
                                    @if(count(@$amc_new)>0)
                                    @foreach (@$amc_new as $dt)
                                    <tr>
                                        <td><a target="_blank" href="{{ url('crm-amc-detail/'.$dt->id) }}">{{ @$dt->doc_number }}</a></td>
                                        <td>{{ date('d/m/Y', strtotime(@$dt->date)) }}</td>
                                        <td>{{ @$dt->custname->name }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>  --}}

            


{{--  NEW PS REQUEST END  --}}

        </div>
    </div>


    <script>
        function add_professional_services_request(id){

            var custname = $('#list_custname_'+id).val();
            var contact_person =  $('#contact_person_'+id).val();
            var mobile =  $('#mobile_'+id).val();
            var location_of_work =  $('#location_of_work_'+id).val();
            var description =  $('#deal_description_'+id).val();

            $('#amc_id').val(id);
            $('#cust_name').val(custname);
            $('#contact_person').val(contact_person);
            $('#mobile').val(mobile);
            $('#location_of_work').val(location_of_work);
            $('#scope_of_work_1').val(description);
            $('#btn_add_professional_services_request').click();
        }
    </script>

<!-- Modal Professional Services Request -->
<a id="btn_add_professional_services_request" data-toggle="modal" data-target="#ModalProfessionalServicesRequest" data-backdrop="static" data-keyboard="false"></a>
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

            <input type="hidden" name="amc_id" id="amc_id">

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Date')<span></span></label>
                                     <input class="form-control" type="date" name="date" id="date" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Customer Name')<span></span></label>

                                    <input class="form-control" id="cust_name" type="text" required name="cust_name" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Contact Person')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="contact_person" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                    <input class="form-control" id="mobile" type="text" required name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Location of Work')<span></span></label>

                                    <input class="form-control" id="location_of_work" type="text" autocomplete="off" required name="location_of_work" value="">
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
<!-- Modal Professional Services Request -->
    
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
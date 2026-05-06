@extends('backEnd.masterpage')
@section('mainContent')

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Pre-Sales Request</h2>
                <span class="page-label">Home - Pre-Sales Request</span>
            </div>
            <div>
                    <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalSupport"
                        data-backdrop="static" data-keyboard="false">Add Request</a>
                <a href="{{ url('crm-deal-support-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Pre-Sales List</a>
                <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-requested-list', 'method' => 'POST', 'id' => 'crm-support-search']) }}
            <div class="row">
                <div class="col-md-1 mb-2">
                    <label for="" class="form-check-label">Pre-Sales ID</label>
                    <input class="form-control" id="search_support_id" type="text" autocomplete="off" name="search_support_id" value="{{ @$ctrl_support_id }}">
                </div>
                <div class="col-md-1 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" id="search_deal_id" type="text" autocomplete="off" name="search_deal_id" value="{{ @$ctrl_deal_id }}">
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
                        @if(count($salesperson) > 0)
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
                        <option value="">-Select-</option>
                        <option value="1" @if($ctrl_status == 1) selected @endif >Pending</option>
                        <option value="2" @if($ctrl_status == 2) selected @endif >Added</option>
                        <option value="3" @if($ctrl_status == 3) selected @endif >Completed</option>
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

                                    <th>@lang('ID')</th>
                                    <th>@lang('Deal No')</th>
                                    <th>@lang('Date ')</th>
                                    <th>@lang('Customer Name')</th>
                                    <th>@lang('Engineer')</th>
                                    <th>@lang('Service Date')</th>
                                    <th>@lang('Service Time')</th>
                                    <th>@lang('Scope of Work')</th>
                                    <th>@lang('Status')</th>
                                   
                                    <th style="text-align: right;" align="right" width="200px">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if(count($support) > 0)
                                            @foreach($support as $value)
                                    <tr @if(@$value->is_delete == 1) class="bg-dark" @endif>
                                        <td><a target="_blank" href="{{url('crm-deal-support/' . $value->id . '/view')}}">{{@$value->doc_number}}</a></td>
                                        <td>
                                            @if ($value->deal_id != "")
                                                <a href="{{url('get-url-deal/' . $value->dealid->code)}}" target="_blank">{{@$value->dealid->code}}</a>
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>{{date('d/m/Y', strtotime(@$value->created_at))}}</td>
                                        <td>{{@$value->name}}</td>
                                        <?php
            $engineername = "";
            if ($value->support_person_id != "") {
                $st = explode(',', $value->support_person_id);
                if (count($st) > 0) {
                    foreach ($st as $u) {
                        $s = $staff->where('user_id', $u)->pluck('full_name');
                        if ($engineername == "") {
                            $engineername .= $s[0];
                        } else {
                            $engineername .= ", " . $s[0];
                        }

                    }
                }
            }
                                        ?>
                                        <td>{{ @$engineername }}</td>
                                        <td>{{date('d/m/Y', strtotime(@$value->support_date))}}</td>
                                        <td>{{date('h:i A', strtotime(@$value->time_from))}}</td>
                                        <td>
                                            <div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                                @php $scope_of_work = explode('$', $value->remarks); @endphp
                                                @foreach ($scope_of_work as $work)
                                                    {{ $work }}, 
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            {!! @App\SysHelper::get_pre_sales_status($value->status) !!}
                                        </td>
                                        
                                        <td align="right">

                                            

                                     

                                            <input type="hidden" id="customer_id_{{@$value->id}}" value="{{@$value->customer_id}}">
                                            <input type="hidden" id="date_{{@$value->id}}" value="{{date('Y-m-d', strtotime(@$value->created_at))}}">
                                            <input type="hidden" id="customer_name_{{@$value->id}}" value="{{@$value->customer->name}}">
                                            <input type="hidden" id="contact_person_{{@$value->id}}" value="{{ @$value->contact_person }}" />
                                            <input type="hidden" id="mobile_{{@$value->id}}" value="{{ @$value->mobile }}" />
                                            <input type="hidden" id="location_of_work_{{@$value->id}}" value="{{ @$value->site_name }}" />
                                            <input type="hidden" id="support_date_{{@$value->id}}" value="{{ @$value->support_date }}" />
                                            <input type="hidden" id="time_from_{{@$value->id}}" value="{{ @$value->time_from }}" />
                                            <input type="hidden" id="work_{{@$value->id}}" value="{{ @$value->remarks }}" />
                                            <input type="hidden" id="support_person_{{@$value->id}}" value="{{ @$value->support_person_id }}" />
                                            <input type="hidden" id="presales_status_id{{@$value->id}}" value="{{@$value->status}}">

                                            @if($value->file != "")
                                            <a class="btn-sm btn-warning" href="{{url('public/uploads/crm_deal_support_doc/' . $value->file)}}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                            @endif

                                               <a class="btn-sm btn-primary" data-toggle="modal"
                                            data-target="#servicecomments_{{ $value->id }}" style="cursor: pointer;"
                                            data-backdrop="static" data-keyboard="false"><i class="fa fa-comments"
                                                aria-hidden="true"></i></a>

                                            @if(@$value->status == 2)
                                                <a onclick="edit_support_request({{@$value->id}})" class="btn-sm btn-primary" style="cursor: pointer;"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>

                                                @if(@$value->is_delete == 1)
                                                <a href="{{ url('crm-deal-support-list/' . $value->id . '/restore') }}" onclick="return confirm('Are you sure you want to restore?')" class="btn-sm btn-success" style="cursor: pointer;"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                                                @else
                                                <a href="{{ url('crm-deal-support-list/' . $value->id . '/delete') }}" onclick="return confirm('Are you sure you want to delete?')" class="btn-sm btn-danger" style="cursor: pointer;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                @endif

                                            @endif
                                            {{--  @if(Auth::user()->role_id == 1)
                                            <a class="btn-sm btn-danger" href="{{url('crm-deal-support/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @endif  --}}
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
                    @if (count($support) > 0)
                        @foreach ($support as $ps)

                    <div class="modal fade" id="servicecomments_{{ $ps->id }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Pre-Sales Comments</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">

                                            @php
            $comments_data = $supportactivity->where('support_id', $ps->id);
                                            @endphp

                                            @if (count($comments_data) > 0)
                                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="80%">Comment</th>
                                                        <th width="20%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($comments_data as $cmts)
                                                    <tr>
                                                        <td>{{ $cmts->remarks }}</td>
                                                        <td>{{ $cmts->createdby->full_name }}<br />{{ $cmts->created_at }}</td>
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


    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

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
                                        <select class="form-control js-example-basic-single" name="add_cust_name" id="add_cust_name"
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
                                        <input class="form-control" id="add_contact_person" type="text" required name="contact_person" value="">
                               </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">@lang('Mobile No')<span></span></label>

                                <input class="form-control" id="add_mobile_no" type="text" required name="mobile" value="">
                            </div>
                        </div>

                          <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input type="text" class="form-control" name="add_site_name" id="add_site_name" required>
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
                                    <select required id="add_engineer" name="add_engineer[]" class="form-control js-example-basic-single" multiple>
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



    <!-- Edit Modal Professional Services Request -->
    <a id="btn_add_professional_services_request" data-toggle="modal" data-target="#ModalProfessionalServicesRequest" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalProfessionalServicesRequest" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Pre-Sales Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-support-list-request-submit']) }}

                <input type="hidden" name="pre_sales_id" id="pre_sales_id">

                <div class="modal-body">
                    <div class="row">
                      
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Customer Name')<span></span></label>
                                        <input class="form-control" id="cust_name" type="text" required name="cust_name" value="" readonly>
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

                                        <input class="form-control" id="service_date" type="date" required name="service_date" value="">
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
                                        @php $englist = @App\SysHelper::get_engineer_list();
    foreach ($englist as $list)
        echo '<option value="' . $list->user_id . '" >' . $list->full_name . '</option>';
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
                          <div class="col-lg-2 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label for="" class="form-check-label">Status</label>
                                        <select class="form-control" name="presales_status" id="presales_status">
                                            
                                            <option value="1" >Pending</option>
                                            <option value="2">Added</option>
                                            <option value="3">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                          
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <table width="100%">
                                    <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work2_1" required></td><td width="1%"><a onclick="add_scope_of_work2()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a></td></tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                    <tr id="row2_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work2_{{ $i }}"></td><td><a class="btn-sm btn-danger" onclick="delete_scope_of_work2({{ $i }})"><i class="fa fa-trash" aria-hidden="true"></i></a></td></tr>
                                    @endfor
                                </table>
                                <input type="hidden" id="scope_of_work_row2_id" value="1" />
                                <script>
                                    function add_scope_of_work2(){
                                        var scope = $('#scope_of_work_row2_id').val();
                                            scope++;
                                            $('#row2_'+scope).css('display','');
                                            $('#scope_of_work_row2_id').val(scope);
                                            //$('#scope_of_work2_'+scope).prop("required", true);
                                    }
                                    function delete_scope_of_work2(id){
                                            $('#row2_'+id).css('display','none');
                                            $('#scope_of_work2_'+id).val('');
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
    <!-- Edit Modal Professional Services Request -->

    <!-- Add New Modal Professional Services Request -->
    <a id="btn_add_new_professional_services_request" data-toggle="modal" data-target="#ModalProfessionalServicesRequestNew" data-backdrop="static" data-keyboard="false"></a>
    <div class="modal fade" id="ModalProfessionalServicesRequestNew" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list-request-add-new', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-support-list-request-submit']) }}

                <input type="hidden" name="pre_sales_id" id="new_pre_sales_id">

                <div class="modal-body">
                    <div class="row">
                        
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Customer Name')<span></span></label>
                                        <input class="form-control" id="new_cust_name" type="text" required name="cust_name" value="" readonly>
                                        <input id="new_cust_id" type="hidden" required name="cust_id" value="" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Contact Person')<span></span></label>

                                        <input class="form-control" id="new_contact_person" type="text" required name="contact_person" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                        <input class="form-control" id="new_mobile" type="text" required name="mobile" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Location of Work')<span></span></label>

                                        <input class="form-control" id="new_location_of_work" type="text" autocomplete="off" required name="location_of_work" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Service Date')<span></span></label>

                                        <input class="form-control" id="new_service_date" type="date" required name="service_date" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Service Time')<span></span></label>

                                        <input class="form-control" id="new_service_time" type="time" required name="service_time" value="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Service Engineer')<span></span></label>
                                        <select id="new_engineer" name="engineer[]" class="form-control js-example-basic-single" multiple>
                                            <option></option>
                                        @php $englist = @App\SysHelper::get_engineer_list();
    foreach ($englist as $list)
        echo '<option value="' . $list->user_id . '" >' . $list->full_name . '</option>';
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

                                        <input class="form-control" id="new_attachment" type="file" name="attachment" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <table width="100%">
                                    <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="new_scope_of_work2_1" required></td><td width="1%"><a onclick="new_add_scope_of_work2()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a></td></tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                    <tr id="new_row2_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="new_scope_of_work2_{{ $i }}"></td><td><a class="btn-sm btn-danger" onclick="new_delete_scope_of_work2({{ $i }})"><i class="fa fa-trash" aria-hidden="true"></i></a></td></tr>
                                    @endfor
                                </table>
                                <input type="hidden" id="new_scope_of_work_row2_id" value="1" />
                                <script>
                                    function new_add_scope_of_work2(){
                                        var scope = $('#new_scope_of_work_row2_id').val();
                                            scope++;
                                            $('#new_row2_'+scope).css('display','');
                                            $('#new_scope_of_work_row2_id').val(scope);
                                            //$('#scope_of_work2_'+scope).prop("required", true);
                                    }
                                    function new_delete_scope_of_work2(id){
                                            $('#new_row2_'+id).css('display','none');
                                            $('#new_scope_of_work2_'+id).val('');
                                    }
                                </script>

                            </div>
                        </div>

                    </div>

                </div>


                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="new_btnSubmit"><span class="ti-check"></span>@lang('Add Request')</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Add New Modal Professional Services Request -->
    <script>
        function edit_support_request(id){
            var custid = $('#customer_id_'+id).val();
            var c_date = $('#date_'+id).val();
            var custname = $('#customer_name_'+id).val();
            var contact_person =  $('#contact_person_'+id).val();
            var mobile =  $('#mobile_'+id).val();
            var location_of_work =  $('#location_of_work_'+id).val();            
            var support_date =  $('#support_date_'+id).val();
            var time_from =  $('#time_from_'+id).val();
            var work = $('#work_'+id).val();
            var support_person = $('#support_person_'+id).val();
            var presales_status_id = $('#presales_status_id'+id).val();
            

            const inputString = work;
            const itemsArray = inputString.split('$');
            console.log(itemsArray);

            for(i=1; i <= itemsArray.length; i++){
                var itm = itemsArray[i-1];
                $('#scope_of_work2_'+i).val(itm);
                if(itm!=""){
                    add_scope_of_work2();
                }
            }

            const supportString = support_person;
            const supportArray = supportString.split(',');
            console.log(supportArray);

            var values=support_person;

            // 🔥 Clear previous selection
            $("#engineer").val([]);

            
            $.each(values.split(","), function(i,e){
                $("#engineer option[value='" + e + "']").prop("selected", true);
            });


            $('#pre_sales_id').val(id);
            $('#date').val(c_date);
            $('#cust_id').val(custid);
            $('#cust_name').val(custname);
            $('#contact_person').val(contact_person);
            $('#mobile').val(mobile);
            $('#location_of_work').val(location_of_work);
            $('#service_date').val(support_date);
            $('#service_time').val(time_from);
            $('#presales_status').val(presales_status_id);


            $('#btn_add_professional_services_request').click();
            $('#engineer').change();
            for(j=1; j <= 20; j++){
                if($('#scope_of_work2_'+j).val()==""){
                    $('#row2_'+j).css('display','none');
                }
            }
        }
        function add_new_support_request(id){
            var custid = $('#customer_id_'+id).val();
            var c_date = $('#date_'+id).val();
            var custname = $('#customer_name_'+id).val();
            var contact_person =  $('#contact_person_'+id).val();
            var mobile =  $('#mobile_'+id).val();
            var location_of_work =  $('#location_of_work_'+id).val();            
            var support_date =  $('#support_date_'+id).val();
            var time_from =  $('#time_from_'+id).val();
            var work = $('#work_'+id).val();
            var support_person = $('#support_person_'+id).val();

            const inputString = work;
            const itemsArray = inputString.split('$');
            console.log(itemsArray);

            for(i=1; i <= itemsArray.length; i++){
                var itm = itemsArray[i-1];
                $('#new_scope_of_work2_'+i).val(itm);
                if(itm!=""){
                    new_add_scope_of_work2();
                }
            }

            const supportString = support_person;
            const supportArray = supportString.split(',');
            console.log(supportArray);

            var values=support_person;
            $.each(values.split(","), function(i,e){
                $("#new_engineer option[value='" + e + "']").prop("selected", true);
            });


            $('#new_pre_sales_id').val(id);
            $('#new_date').val(c_date);
            $('#new_cust_id').val(custid);
            $('#new_cust_name').val(custname);
            $('#new_contact_person').val(contact_person);
            $('#new_mobile').val(mobile);
            $('#new_location_of_work').val(location_of_work);
            $('#new_service_date').val(support_date);
            $('#new_service_time').val(time_from);

            $('#btn_add_new_professional_services_request').click();
            $('#new_engineer').change();
            for(j=1; j <= 20; j++){
                if($('#new_scope_of_work2_'+j).val()==""){
                    $('#new_row2_'+j).css('display','none');
                }
            }
        }
    </script>




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
                            $("#add_site_name").val(dataResult['data'][i].address);
                        }
                    } else {
                        $("#add_contact_person").val();
                        $("#add_mobile_no").val();
                        $("#add_site_name").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

</script>

    <!-- Modal Professional Services Request -->

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
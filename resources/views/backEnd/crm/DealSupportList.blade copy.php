@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Pre-Sales List</h2>
            <span class="page-label">Home - Pre-Sales List</span>
        </div>
        <div>
            {{--  <a href="#" type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalSupport"><i class="fa fa-plus"></i> Add New Pre-Sales</a>  --}}
            <a href="{{ url('crm-deal-support-requested-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Pre-Sales Request List</a>
            <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list', 'method' => 'POST', 'id' => 'crm-support-search']) }}
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
                    <option value="">-Select-</option>
                    <option value="1" @if($ctrl_status==1) selected @endif >Pending</option>
                    <option value="2" @if($ctrl_status==2) selected @endif >Added</option>
                    <option value="3" @if($ctrl_status==3) selected @endif >Completed</option>
                </select>


                {{--  <select class="form-control" name="search_status" id="search_status">
                    <option value="0" @if($ctrl_search_status == 0) selected @endif>Pending</option>
                    <option value="1" @if($ctrl_search_status == 1) selected @endif>Added</option>
                </select>  --}}
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
                                <th>Service Date</th>
                                <th>Service Time</th>
                                <th>Sales Person</th>
                                <th>@lang('Scope of Work')</th>
                                <th>@lang('Status')</th>
                                <th width="90px">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if(count($support)>0)
                                        @foreach($support as $value)
                                <tr @if(@$value->is_delete == 1) class="bg-dark" @endif>
                                    <td><a target="_blank" href="{{url('crm-deal-support-list/'.$value->id.'/view')}}">{{@$value->doc_number}}</a></td>
                                    <td><a target="_blank" href="{{url('crm-deals/'.$value->deal_id.'/view')}}">{{@$value->dealid->code}}</a></td>
                                    <td>{{date('d/m/Y', strtotime(@$value->created_at))}}</td>
                                    <td>{{@$value->name}}</td>
                                    <?php
                                        $engineername="";
                                        if($value->support_person_id != ""){
                                            $st = explode(',', $value->support_person_id);
                                            if(count($st)>0){
                                                foreach($st as $u){
                                                    $s = $staff->where('user_id',$u)->pluck('full_name');
                                                    if($engineername==""){
                                                        $engineername .= $s[0];
                                                    } else { $engineername .= ", " . $s[0]; }
                                                    
                                                }
                                            }
                                        }
                                            ?>
                                    <td>{{date('d/m/Y', strtotime(@$value->support_date))}}</td>
                                    <td>{{date('h:i A', strtotime(@$value->time_from))}}</td>
                                    <td>{{ @$value->salesperson->full_name }}</td>
                                    <td>
                                        <div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;" id="text_div_{{ @$value->id }}" onmouseover="show_full_text({{ @$value->id }})" onmouseout="hide_full_text({{ @$value->id }})">
                                            @php $scope_of_work = explode('$',$value->remarks); @endphp
                                            @foreach ($scope_of_work as $work)
                                                {{ $work }}, 
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        {!! @App\SysHelper::get_pre_sales_status($value->status) !!}
                                    </td>
                                    <td>
                                        <input type="hidden" id="customer_id_{{@$value->id}}" value="{{@$value->customer_id}}">
                                        <input type="hidden" id="customer_name_{{@$value->id}}" value="{{@$value->customer->name}}">
                                        <input type="hidden" id="contact_person_{{@$value->id}}" value="{{ @$value->customer->first_name }} {{ @$value->customer->last_name }}" />
                                        <input type="hidden" id="mobile_{{@$value->id}}" value="{{ @$value->mobile }}" />
                                        <input type="hidden" id="location_of_work_{{@$value->id}}" value="{{ @$value->site_name }}" />
                                        <input type="hidden" id="support_date_{{@$value->id}}" value="{{ @$value->support_date }}" />
                                        <input type="hidden" id="time_from_{{@$value->id}}" value="{{ @$value->time_from }}" />
                                        <input type="hidden" id="work_{{@$value->id}}" value="{{ @$value->remarks }}" />
                                        <input type="hidden" id="date_{{@$value->id}}" value="{{ date('Y-m-d', strtotime(@$value->created_at)) }}" />
                                        <input type="hidden" id="support_person_id_{{@$value->id}}" value="{{ @$value->support_person_id }}" />
                                        
                                        @if(@$value->status == 1)
                                        <a onclick="add_professional_services_request({{@$value->id}})" class="btn-sm btn-primary" style="cursor: pointer;">Add Request</a>
                                        @else
                                        <a onclick="edit_professional_services_request({{@$value->id}})" class="btn-sm btn-info" style="cursor: pointer;"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        
                                        @if(@$value->is_delete == 1)
                                        <a href="{{ url('crm-deal-support-list/'.$value->id.'/restore') }}" onclick="return confirm('Are you sure you want to restore?')" class="btn-sm btn-success" style="cursor: pointer;"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                                        @else
                                        <a href="{{ url('crm-deal-support-list/'.$value->id.'/delete') }}" onclick="return confirm('Are you sure you want to delete?')" class="btn-sm btn-danger" style="cursor: pointer;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif

                                        @endif

                                        {{--  <a class="btn-sm btn-info" href="{{url('crm-deal-support/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>  --}}
                                        {{--  @if(Auth::user()->role_id == 1)
                                        <a class="btn-sm btn-danger" href="{{url('crm-deal-support/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif  --}}
                                    </td>
                                </tr>
                                  
                                @endforeach
                                @endif
                                <script>
                                    function show_full_text(id) {
                                        $('#text_div_'+id).css('overflow','');
                                        $('#text_div_'+id).css('white-space','wrap');
                                        $('#text_div_'+id).css('position','fixed');
                                        $('#text_div_'+id).css('background','#a5a5a5');
                                        $('#text_div_'+id).css('color','#ffffff');
                                        $('#text_div_'+id).css('padding','5px');
                                        $('#text_div_'+id).css('margin-top','-13px');
                                        $('#text_div_'+id).css('z-index','9999');
                                    }
                                    function hide_full_text(id) {
                                        $('#text_div_'+id).css('overflow','hidden');
                                        $('#text_div_'+id).css('white-space','nowrap');
                                        $('#text_div_'+id).css('position','');
                                        $('#text_div_'+id).css('background','');
                                        $('#text_div_'+id).css('color','');
                                        $('#text_div_'+id).css('padding','');
                                        $('#text_div_'+id).css('margin-top','');
                                        $('#text_div_'+id).css('z-index','');
                                    }
                                </script>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="support_id" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="" class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" id="site_name" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal Id</label>
                                <input type="number" class="form-control" name="deal_id" id="deal_id" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="support_date" id="support_date" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">From</label>
                                <input type="time" class="form-control" name="time_from" id="time_from" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">To</label>
                                <input type="time" class="form-control" name="time_to" id="time_to" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
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
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="sales_person_id" id="sales_person_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($staff_sales as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Support Person111</label>
                                <select class="form-control js-example-basic-single" name="support_person_id[]" id="support_person_id" multiple required>
                                    <option value="">-Select-</option>
                                    @foreach ($staff_support as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
        function add_professional_services_request(id){

            var custid = $('#customer_id_'+id).val();
            var custname = $('#customer_name_'+id).val();
            var contact_person =  $('#contact_person_'+id).val();
            var mobile =  $('#mobile_'+id).val();
            var location_of_work =  $('#location_of_work_'+id).val();            
            var support_date =  $('#support_date_'+id).val();
            var time_from =  $('#time_from_'+id).val();
            var work = $('#work_'+id).val();
            var edit_date = $('#date_'+id).val();

            const inputString = work;
            const itemsArray = inputString.split('$');
            console.log(itemsArray);
                        
            for(i=1; i <= itemsArray.length; i++){
                var itm = itemsArray[i-1];
                $('#scope_of_work2_'+i).val(itm);
                add_scope_of_work2();
            }
            
            for(k=1; k <= 20; k++){
                    $('#row2_'+k).css('display','none');
            }

            $('#engineer').change();
            for(j=1; j <= itemsArray.length; j++){
                if($('#scope_of_work2_'+j).val()==""){
                    $('#row2_'+j).css('display','none');
                } else { $('#row2_'+j).css('display',''); }
            }
            

            $('#pre_sales_id').val(id);
            $('#date').val(edit_date);
            $('#cust_id').val(custid);
            $('#cust_name').val(custname);
            $('#contact_person').val(contact_person);
            $('#mobile').val(mobile);
            $('#location_of_work').val(location_of_work);
            $('#service_date').val(support_date);
            $('#service_time').val(time_from);

            $('#btn_add_professional_services_request').click();
        }
    </script>

<!-- Modal Professional Services Request -->
<a id="btn_add_professional_services_request" data-toggle="modal" data-target="#ModalProfessionalServicesRequest" data-backdrop="static" data-keyboard="false"></a>
<div class="modal fade" id="ModalProfessionalServicesRequest" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list-request-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-support-list-request-submit']) }}

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
                            <table width="100%">
                                <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work2_1" required></td>
                                    <td width="1%"><a onclick="add_scope_of_work2()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a></td></tr>
                                @for ($i=2; $i<=20; $i++)
                                <tr id="row2_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work2_{{ $i }}"></td>
                                    <td><a class="btn-sm btn-danger" style="float: right;" onclick="delete_scope_of_work2({{ $i }})"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                </tr>
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
                                        $('#scope_of_work2_'+id).prop("required", false);
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


<script>
    function edit_professional_services_request(id){

        var custid = $('#customer_id_'+id).val();
        var custname = $('#customer_name_'+id).val();
        var contact_person =  $('#contact_person_'+id).val();
        var mobile =  $('#mobile_'+id).val();
        var location_of_work =  $('#location_of_work_'+id).val();            
        var support_date =  $('#support_date_'+id).val();
        var time_from =  $('#time_from_'+id).val();
        var work = $('#work_'+id).val();
        var edit_date = $('#date_'+id).val();
        var support_person = $('#support_person_id_'+id).val();

        const inputString = work;
        const itemsArray = inputString.split('$');
        console.log(itemsArray);
                    
        for(i=1; i <= itemsArray.length; i++){
            var itm = itemsArray[i-1];
            $('#edit_scope_of_work2_'+i).val(itm);
            add_scope_of_work2();
        }
        
        for(k=1; k <= 20; k++){
                $('#edit_row2_'+k).css('display','none');
        }

        for(j=1; j <= itemsArray.length; j++){
            if($('#edit_scope_of_work2_'+j).val()==""){
                $('#edit_row2_'+j).css('display','none');
            } else { $('#edit_row2_'+j).css('display',''); }
        }

        var array = support_person.split(',').map(Number);
        $('#edit_engineer').val(array);
        
        $('#edit_pre_sales_id').val(id);
        $('#edit_date').val(edit_date);
        $('#edit_cust_id').val(custid);
        $('#edit_cust_name').val(custname);
        $('#edit_contact_person').val(contact_person);
        $('#edit_mobile').val(mobile);
        $('#edit_location_of_work').val(location_of_work);
        $('#edit_service_date').val(support_date);
        $('#edit_service_time').val(time_from);

        $('#btn_edit_professional_services_request').click();
        
        $('#edit_engineer').change();
    }
</script>

<!-- Modal Professional Services Request EDIT -->
<a id="btn_edit_professional_services_request" data-toggle="modal" data-target="#ModalProfessionalServicesRequestEdit" data-backdrop="static" data-keyboard="false"></a>
<div class="modal fade" id="ModalProfessionalServicesRequestEdit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Pre-Sales Request</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-update','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-support-update']) }}

        <input type="hidden" name="pre_sales_id" id="edit_pre_sales_id">

        <div class="modal-body">
            <div class="row">
               
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Customer Name')<span></span></label>
                                <input class="form-control" id="edit_cust_name" type="text" required name="cust_name" value="" readonly>
                                <input id="edit_cust_id" type="hidden" required name="cust_id" value="" readonly>
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
                                <label class="txtlbl">@lang('Location of Work')<span></span></label>

                                <input class="form-control" id="edit_location_of_work" type="text" autocomplete="off" required name="location_of_work" value="">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Service Date')<span></span></label>

                                <input class="form-control" id="edit_service_date" type="date" required name="service_date" min="{{ date('Y-m-d') }}" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Service Time')<span></span></label>

                                <input class="form-control" id="edit_service_time" type="time" required name="service_time" value="">
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
                                @endphp
                                @foreach($englist as $list)                                    
                                    <option value="{{ $list->user_id }}" >{{ $list->full_name }}</option>
                                @endforeach
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

                                <input class="form-control" id="edit_attachment" type="file" name="attachment" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="" class="form-label">Scope of Work</label>                            
                        <table width="100%">
                            <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="edit_scope_of_work2_1" required></td>
                                <td width="1%"><a onclick="edit_add_scope_of_work2()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a></td></tr>
                            @for ($i=2; $i<=20; $i++)
                            <tr id="edit_row2_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="edit_scope_of_work2_{{ $i }}"></td>
                                <td><a class="btn-sm btn-danger" style="float: right;" onclick="edit_delete_scope_of_work2({{ $i }})"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                            </tr>
                            @endfor
                        </table>
                        <input type="hidden" id="edit_scope_of_work_row2_id" value="1" />
                        <script>
                            function edit_add_scope_of_work2(){
                                var scope = $('#edit_scope_of_work_row2_id').val();                                    
                                    scope++;
                                    $('#edit_row2_'+scope).css('display','');
                                    $('#edit_scope_of_work_row2_id').val(scope);
                                    //$('#scope_of_work2_'+scope).prop("required", true);
                            }
                            function edit_delete_scope_of_work2(id){
                                    $('#edit_row2_'+id).css('display','none');
                                    $('#edit_scope_of_work2_'+id).val('');
                                    $('#edit_scope_of_work2_'+id).prop("required", false);
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
<!-- Modal Professional Services Request EDIT -->

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
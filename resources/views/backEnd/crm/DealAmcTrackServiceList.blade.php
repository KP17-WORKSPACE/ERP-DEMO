@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Professional Services


</h2>
                <span class="page-label">Home - Professional Services


</span>
            </div>
            <div>
                <!-- <a href="{{ url('crm-amc-form') }}" class="btn btn-info" type="button">Add New AMC</a> -->

                <a type="button" href="{{ url('crm-amc-service-list-req') }}" class="btn btn-primary"><i class="fa fa-filter mr-1"></i>Requested List</a>

                <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-track-list', 'method' => 'POST', 'id' => 'crm-amc-search']) }}
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Sales Person</label>
                        <input class="form-control" id="sales_person" type="text" autocomplete="off" name="sales_person" value="{{ $sales_person }}">
                    </div>
                    <!-- <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">AMC Expiry Date</label>
                    <input class="form-control datepicker" id="to_date" type="date" autocomplete="off" name="to_date" value="{{ $ctrl_date }}">
                </div> -->

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
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
                                <th>@lang('Service ID')</th>
                                <th>@lang('Deal ID')</th>
                                <th>@lang('Date ')</th>
                                <th>@lang('Cust Name')</th>
                                <th>@lang('Invoicing')</th>
                                <th>@lang('Start Date')</th>
                                <th>@lang('End Date')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Sales Person')</th>
                                <th>@lang('Description')</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($support as $value)
                            <tr>
                                <td>{{@$value->id}}</td>
                                <!-- <td>
                                        <a class="text-dark" href="{{url('crm-deals/'.$value->id.'/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></a>
                                    </td> -->
                                <td>{{@$value->deal_code->code}}</td>
                                <td>{{date('d-M-Y', strtotime(@$value->date))}}</td>
                                <td>{{@$value->cust_name}}</td>
                                <td></td>
                                <td>{{date('d-M-Y', strtotime(@$value->start_date))}}</td>
                                <td>{{date('d-M-Y', strtotime(@$value->end_date))}}</td>

                                <td>{{@$value->amount}}</td>
                                <td>{{@$value->ownername->full_name}}</td>
                                <td></td>
                                <td><input type="hidden" id="cid[]" value="{{@$value->id}}">
                                    @if(@$value->status != 1)
                                    <a data-id="{{@$value->id}}" class="btn-badge btn btn-info py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalDealTrack" title="Click to Fullfill">
                                        Add Requesting</a>
                                    @endif
                                    @if(@$value->status == 1)
                                    <a data-id="{{@$value->id}}" id="crmajax" class="btn-badge btn btn-info  py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalDealTrackEdit" title="Click to Fullfill">
                                    <i class="fa fa-edit" aria-hidden="true"></i></a>
                                    @if(Auth::user()->role_id == 1)
                            <a class="btn-sm btn-danger" href="{{url('crm-amc-track-list/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @endif
                                   
                                    @endif
                                   
                                </td>

                               

                               

                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <script>
        $(document).ready(function() {


            $('#crmajax').click(function() {
                id = $(this).data('id')


                var action = "crm-amc-track-id";
                $.ajax({
                    url: action,
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',

                        id: id,

                    },

                    success: function(dataResult) {
                        
                        var dataResult = JSON.parse(dataResult);
                       
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
                <h5 class="modal-title" id="exampleModalLabel">Add Professional Services

</h5>
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

				<select    name="engineer" class="form-control js-example-basic-single" >

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
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Add Request')</button>
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
                <h5 class="modal-title" id="exampleModalLabel">Edit Request</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-track-edit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-edit']) }}

            <div class="modal-body">
                <div class="row">

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Customer Name')<span></span></label>

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

                                     <input class="form-control" id="sales_person" type="text" required name="sales_person">
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

                                    <input class="form-control" id="engineer1" type="text" required name="engineer" value="">
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





@endsection

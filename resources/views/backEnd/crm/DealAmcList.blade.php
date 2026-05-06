@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">AMC List</h2>
            <span class="page-label">Home - AMC List</span>
        </div>
        <div>
            <a href="{{ url('crm-amc-form') }}" class="btn btn-info" type="button">Add New AMC</a>

            <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>    
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-deal-list', 'method' => 'POST', 'id' => 'crm-amc-search']) }}
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">AMC ID</label>
                    <input class="form-control" id="amc_id" type="text" autocomplete="off" name="amc_id" value="{{ $ctrl_amc_id }}">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="" class="form-check-label">AMC Expiry Date</label>
                    <input class="form-control datepicker" id="to_date" type="date" autocomplete="off" name="to_date" value="{{ $ctrl_date }}">
                </div>

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
                                            <th>@lang('AMC ID')</th>
                                            <th>@lang('Company Name')</th>
                                            <th>@lang('Owner')</th>
                                            <th>@lang('AMC From Date')</th>
                                            <th>@lang('AMC To Date')</th>
                                            <th>@lang('AMC Value')</th>
                                            <th>@lang('Billing Cycle')</th>
                                            <th>@lang('Status')</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                        @foreach($support as $value)
                                <tr>
                                    <td>{{@$value->id}}</td>
                                    <td>
                                        <a class="text-dark" href="{{url('crm-deals/'.$value->id.'/view')}}"><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->customername->name}}</div></a>
                                    </td>
                                    <td>{{@$value->ownername->full_name}}</td>
                                    <td>
                                        @if (date('Y-m-d', strtotime(@$value->to_date)) < date('Y-m-d'))
                                        <span class="danger btn-badge py-1 px-2">{{date('d-M-Y', strtotime(@$value->from_date))}}</span>
                                        @else
                                        <span class="info btn-badge py-1 px-2">{{date('d-M-Y', strtotime(@$value->from_date))}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (date('Y-m-d', strtotime(@$value->to_date)) < date('Y-m-d'))
                                        <span class="danger btn-badge py-1 px-2">{{date('d-M-Y', strtotime(@$value->to_date))}} </span>
                                        @else
                                        <span class="info btn-badge py-1 px-2">{{date('d-M-Y', strtotime(@$value->to_date))}}</span>
                                        @endif
                                    </td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{ @$value->amc_value }}</div></td>
                                    <td> -- </td>
                                    <td>
                                        @if($value->status==1) <span class="success btn-badge py-1 px-2">Active</span>@endif
                                        @if($value->status==2) <span class="warning btn-badge py-1 px-2">Hold</span> @endif
                                        @if($value->status==3) <span class="danger btn-badge py-1 px-2">Inactive</span> @endif
                                    </td>
                                    <td>
                                        <a class="btn-sm btn-primary" href="{{url('crm-amc/'.$value->id.'/view')}}" title="View Deal" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>

                                        <?php /*
                                        @if (@$value->from_date != null)
                                        <input type="hidden" id="amc_edit_{{$value->id}}" value="{{ @$value->amcid }}" />
                                        <input type="hidden" id="remarks_edit_{{$value->id}}" value="{{ @$value->remarks }}" />
                                        <input type="hidden" id="datef_edit_{{$value->id}}" value="{{ @$value->from_date }}" />
                                        <input type="hidden" id="datet_edit_{{$value->id}}" value="{{ @$value->to_date }}" />
                                        <a class="btn-sm btn-danger" href="#" onclick="editDate({{$value->id}})" title="Set AMC Date"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        @endif
                                        */ ?>

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

    function addDate(id)
    {
        $("#deal_id").val(id);
        $("#deal_name").val(id);
        $("#btnpopup").click();
    }
    function editDate(id)
    {
        $("#deal_id_edit").val(id);
        $("#deal_name_edit").val(id);

        var a = $("#amc_edit_"+id).val();
        var r = $("#remarks_edit_"+id).val();
        var f = $("#datef_edit_"+id).val();
        var t = $("#datet_edit_"+id).val();
        
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
                                <input type="text" readonly class="form-control" name="deal_name" id="deal_name">
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
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amcid" id="amcid" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">AMC Id</label>
                                <input type="text" readonly class="form-control" name="deal_name" id="deal_name_edit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="from_date" id="from_date_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="to_date" id="to_date_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control js-example-basic-single" name="company_name" id="company_name" required>
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                    <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->company_name) ? (@$edit->company_name == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Designation</label>
                                <input class="form-control" type="text" name="cust_designation" autocomplete="off" id="cust_designation" value="{{ isset($edit) ? (!empty(@$edit->cust_designation) ? @$edit->cust_designation : old('cust_designation')) : old('cust_designation') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Mobile</label>
                                <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Address</label>
                                <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Owner</label>
                                <input type="date" class="form-control" name="to_date" id="to_date_edit" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="remarks" id="remarks_edit" rows="10" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update AMC Date</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Service-->


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
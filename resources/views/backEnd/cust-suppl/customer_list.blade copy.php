@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Customer Register</h2>
            <span class="page-label">Home - Customer Register</span>
        </div>
        <div>
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
            <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalMergeDuplicateCustomer">Merge Duplicate</a>
                <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalMergeCustomer">Merge</a>
            @endif

            <input type="hidden" name="copy_url" id="copy_url" value="{{ url('customer-from/'.session('logged_session_data.company_id')) }}" />
            <a class="btn btn-success" id="copy-button">Copy URL</a>
            <script>
                $('#copy-button').click(function() {
                  var textToCopy = $('#copy_url').val();
                  var tempTextarea = $('<textarea>');
                  $('body').append(tempTextarea);
                  tempTextarea.val(textToCopy).select();
                  document.execCommand('copy');
                  tempTextarea.remove();
                  alert("Copied!");
                });
            </script>
            <a href="{{ url('customer-from-list') }}" type="button" class="btn btn-success">Form Submited</a>
            <a href="{{ url('customers-pending') }}" type="button" class="btn btn-info">Sales Pending</a>
            <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Customer</a>
            <a href="{{ url('customer-import') }}" type="button" class="btn btn-warning">Import</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>

        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customers', 'method' => 'get', 'id' => 'crm-deals-search']) }}
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Company Name</label>
                    <input class="form-control" type="text" autocomplete="off" name="company_name" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Contact Name</label>
                        <input class="form-control" type="text" autocomplete="off" name="contact_name" value="">
                </div>

                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Email</label>
                    <input class="form-control" type="text" autocomplete="off" name="email" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Sales Person</label>
                    <select class="form-control js-example-basic-single" name="sales_person">
                        <option value="">-Select-</option>
                        @foreach ($staff as $value)
                        <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Country</label>
                    <select class="form-control js-example-basic-single" name="vat_country">
                        <option value="">-Select-</option>
                        @foreach ($countries as $value)
                        <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">State</label>
                    <select class="form-control js-example-basic-single" name="vat_state">
                        <option value="">-Select-</option>
                        @foreach ($states as $value)
                        <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                        @endforeach
                    </select>
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
                        <tr>
                            {{-- <th>@lang('lang.sl') @lang('lang.no')</th> --}}
                            {{-- <th>@lang('lang.photo')</th> --}}
                            <th>@lang('Customer Name')</th>
                            <th>@lang('Contcat Person')</th>
                            <th>@lang('Mobile')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Information')</th>
                            <th style="width: 110px;">@lang('lang.action')</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $serialcount=1; @endphp
                        @foreach($customer as $value)
                        <tr @if($value->status == 2) class="bg-dark" @endif>
                            {{-- <td>{{@$serialcount++}}</td> --}}
                            {{-- <td>
                                <img height="100" width="100" src="{{ file_exists(@$value->staff_photo) ? asset($value->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}" alt="">
                            </td> --}}
                            <td><a class="text-dark" href="{{url('view-customer')}}/{{@$value->id}}">
                                {{--  <div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">  --}}
                                    {{@$value->code}} - {{@$value->name}}</a> @if(@$value->internal==1) <i class="fa fa-info-circle text-primary" aria-hidden="true" title="Internal Customer"></i> @endif
                            </td>                                        
                            <td>
                                {{@$value->contcat_person}}
                            </td>
                            <td>
                                {{@$value->mobile}}
                            </td>
                            <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                {{@$value->email}}</div>
                            </td>
                            <td>
                                @if(@$value->status == 2)
                                <span class="text-dark">Deleted</span>
                                @elseif (@$value->status == 3)
                                <span class="text-warning">Inactive</span>
                                @else
                                <span class="text-success">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(@$value->status==2)
                                <span class="text-dark">Deleted</span>
                                @elseif(App\SysHelper::get_company_status($value)==0)
                                <span class="text-danger">Incomplete</span>
                                @else
                                <span class="text-success">Complete</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn-sm btn-info" href="{{url('view-customer')}}/{{@$value->id}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <a class="btn-sm btn-primary" href="{{url('customer-edit', $value->id)}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('customer-restore/'.$value->id)}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('customer-inactive/'.$value->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                                @endif
                            </td>
                        </tr>
                  
                @endforeach
                    
                    </tbody>
                    {{--  <footer>
                        <tr>
                            <td colspan="6">
                                {{ $customer->appends(request()->input())->links() }}
                            </td>
                        </tr>
                    </footer>  --}}
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalMergeCustomer" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Merge Customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        {!! Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-merge', 'method' => 'post', 'id' => 'customer-merge']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">From Customer
                    <select id="from_account" name="from_account[]" class="form-control js-example-basic-single" multiple required>
                        @foreach ($customer_list as $data)
                            <option value="{{ $data->id }}">{{ $data->account_code }} - {{ $data->account_name }}</option>                    
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">To Customer
                    <select id="to_account" name="to_account" class="form-control js-example-basic-single" required>
                        <option value="">Select</option>
                        @foreach ($customer_list as $data)
                            <option value="{{ $data->id }}">{{ $data->account_code }} - {{ $data->account_name }}</option>                    
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to Merge this?');">Merge</button>
        </div>
        {!! Form::close() !!}

      </div>
    </div>
  </div>
  
<div class="modal fade" id="ModalMergeDuplicateCustomer" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Merge Duplicate Customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        {!! Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-merge-duplicate', 'method' => 'post', 'id' => 'customer-merge-duplicate']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">Duplicate Customer
                    <div class="form-control" style="height: 500px; overflow-y: scroll; overflow-x: hidden;">
                        @php
                        $duplicate_customer = collect($duplicate_customer);
                        @endphp

                        @foreach ($duplicate_customer->groupBy('duplicate_name') as $duplicateName => $groupedCustomers)
                            <div class="form-control mb-2" style="height: auto;">
                                <input class="float-right" type="checkbox" id="duplicate_name_{{ $duplicateName }}" name="duplicate_name[]" value="{{ $duplicateName }}" checked >
                                @foreach ($groupedCustomers as $customer)
                                    <label for="vehicle1">
                                        {{ $customer->account_code }} - {{ $customer->account_name }}
                                    </label><br />
                                @endforeach
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to Merge this?');">Merge</button>
        </div>
        {!! Form::close() !!}

      </div>
    </div>
  </div>

  

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
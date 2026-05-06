@extends('backEnd.masterpage')
@section('mainContent')

@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Supplier Register</h2>
            <span class="page-label">Home - Supplier Register</span>
        </div>
        <div>
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
            <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalMergeDuplicateSupplier">Merge
                Duplicate</a>
            <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#ModalMergeSupplier">Merge</a>
            @endif
            <input type="hidden" name="copy_url" id="copy_url"
                value="{{ url('supplier-from/'.session('logged_session_data.company_id')) }}" />
            <a class="btn btn-success" id="copy-button">Copy URL</a>
            <script>
                $('#copy-button').click(function () {
                    var textToCopy = $('#copy_url').val();
                    var tempTextarea = $('<textarea>');
                    $('body').append(tempTextarea);
                    tempTextarea.val(textToCopy).select();
                    document.execCommand('copy');
                    tempTextarea.remove();
                    alert("Copied!");
                });
            </script>
            <a href="{{ url('supplier-from-list') }}" type="button" class="btn btn-warning"><i class="far fa fa-plus"
                    aria-hidden="true"></i> Pending List</a>
            <a href="{{ url('add-supplier') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New
                Supplier</a>
            <a href="{{ url('supplier-import') }}" type="button" class="btn btn-warning"><i class="far fa fa-plus"
                    aria-hidden="true"></i> Import</a>

            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button"
                aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>

        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'suppliers', 'method' => 'get', 'id'
            => 'crm-deals-search']) }}
            <div class="row">
                <div class="col-md-3 mb-2 ">
                    <label for="" class="form-check-label">Supplier Name</label>
                    <input class="form-control" type="text" autocomplete="off" name="company_name" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Contact Person</label>
                    <input class="form-control" type="text" autocomplete="off" name="contact_name" value="">
                </div>

                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Email</label>
                    <input class="form-control" type="text" autocomplete="off" name="email" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">VAT Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="vat" value="">
                </div>
                {{-- <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Sales Person</label>
                    <select class="form-control js-example-basic-single" name="sales_person">
                        <option value="">-Select-</option>
                        @foreach ($staff as $value)
                        <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                        @endforeach
                    </select>
                </div> --}}

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
                            <th>@lang('Supplier Name')</th>
                            <th>@lang('Contcat Person')</th>
                            <th>@lang('Contact Number')</th>
                            <th>@lang('VAT Number')</th>
                            <th>@lang('Mobile')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Status')</th>
                            <th width="105px;">@lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $serialcount=1; @endphp
                        @foreach($supplier as $value)
                        <tr @if($value->status == 2) class="bg-dark" @endif>
                            {{-- <td>{{@$serialcount++}}</td> --}}
                            {{-- <td>
                                <img height="100" width="100"
                                    src="{{ file_exists(@$value->staff_photo) ? asset($value->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}"
                                    alt="">
                            </td> --}}
                            <td><a class="text-dark" href="{{url('view-supplier')}}/{{@$value->id}}">
                                    {{-- <div
                                        style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                        --}}
                                        {{@$value->code}} - {{@$value->name}}</a>
                            </td>
                            <td>
                                {{@$value->contcat_person}}
                            </td>
                            <td>
                                {{@$value->contcat_number}}
                            </td>
                            <td>
                                {{@$value->vat_number}}
                            </td>
                            <td>
                                {{@$value->mobile}}
                            </td>
                            <td>
                                <div
                                    style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    {{@$value->email}}</div>
                            </td>
                            <td>
                                @if(@$value->status==2)
                                <span class="text-danger">Inactive</span>
                                @else
                                <span class="text-success">Active</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn-sm btn-info" href="{{url('view-supplier')}}/{{@$value->id}}"><i
                                        class="fa fa-eye" aria-hidden="true"></i></a>

                                <a class="btn-sm btn-primary" href="{{url('supplier-edit', $value->id)}}"><i
                                        class="fa fa-edit" aria-hidden="true"></i></a>

                                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                @if (@$value->status == 2)
                                <a class="btn-sm btn-warning" href="{{url('supplier-restore/'.$value->id)}}"
                                    onclick="return confirm('Are you sure you want to restore this item?');"><i
                                        class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                <a class="btn-sm btn-danger" href="{{url('supplier-inactive/'.$value->id)}}"
                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                        class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    {{-- <footer>
                        <tr>
                            <td colspan="7">
                                {{ $supplier->appends(request()->input())->links() }}
                            </td>
                        </tr>
                    </footer> --}}
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="ModalMergeSupplier" data-backdrop="static" data-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Merge Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {!! Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-merge', 'method' =>
            'post', 'id' => 'supplier-merge']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">From Supplier
                        <select id="from_account" name="from_account[]" class="form-control js-example-basic-single"
                            multiple required>
                            @foreach ($supplier_list as $data)
                            <option value="{{ $data->id }}">{{ $data->account_code }} - {{ $data->account_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">To Supplier
                        <select id="to_account" name="to_account" class="form-control js-example-basic-single" required>
                            <option value="">Select</option>
                            @foreach ($supplier_list as $data)
                            <option value="{{ $data->id }}">{{ $data->account_code }} - {{ $data->account_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Are you sure you want to Merge this?');">Merge</button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>

<div class="modal fade" id="ModalMergeDuplicateSupplier" data-backdrop="static" data-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Merge Duplicate Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {!! Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-merge-duplicate', 'method'
            => 'post', 'id' => 'supplier-merge-duplicate']) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">Duplicate Supplier
                        <div class="form-control" style="height: 500px; overflow-y: scroll; overflow-x: hidden;">
                            @php
                            $duplicate_customer = collect($duplicate_customer);
                            @endphp

                            @foreach ($duplicate_customer->groupBy('duplicate_name') as $duplicateName =>
                            $groupedCustomers)
                            <div class="form-control mb-2" style="height: auto;">
                                <input class="float-right" type="checkbox" id="duplicate_name_{{ $duplicateName }}"
                                    name="duplicate_name[]" value="{{ $duplicateName }}">
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
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Are you sure you want to Merge this?');">Merge</button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>



@endsection
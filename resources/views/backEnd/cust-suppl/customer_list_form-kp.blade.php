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
                <h2 class="page-heading m-0">Customer Pending List</h2>
                <span class="page-label">Home - Customer Pending List</span>
            </div>
            <div>
                <input type="hidden" name="copy_url" id="copy_url"
                    value="{{ url('customer-from/' . session('logged_session_data.company_id')) }}" />
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
                <a href="{{ url('customers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer
                    List</a>
                <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New
                    Customer</a>
                <a href="{{ url('customer-import') }}" type="button" class="btn btn-warning"><i class="far fa fa-plus"
                        aria-hidden="true"></i> Import</a>
                <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button"
                    aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>

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
                    <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                {{-- <th>@lang('lang.sl') @lang('lang.no')</th> --}}
                                {{-- <th>@lang('lang.photo')</th> --}}
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Contcat Person')</th>
                                <th>@lang('Mobile')</th>
                                <th>@lang('Email')</th>
                                <th>@lang('lang.action')</th>
                            </tr>
                        </thead>

                        <tbody>

                            @php $serialcount=1; @endphp
                            @foreach ($customer as $value)
                                <tr>
                                    {{-- <td>{{@$serialcount++}}</td> --}}
                                    {{-- <td>
                                <img height="100" width="100" src="{{ file_exists(@$value->staff_photo) ? asset($value->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}" alt="">
                            </td> --}}
                                    <td><a class="text-dark">
                                            <div
                                                style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                                {{ @$value->name }}</div>
                                        </a>
                                    </td>
                                    <td>
                                        {{ @$value->first_name }}
                                    </td>
                                    <td>
                                        {{ @$value->mobile }}
                                    </td>
                                    <td>
                                        <div
                                            style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                            {{ @$value->email }}</div>
                                    </td>
                                    <td>
                                        <a class="btn-sm btn-primary" href="{{ url('customer-form-edit', $value->id) }}"><i
                                                class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-danger" href="{{ url('customer-form-delete', $value->id) }}"
                                            onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                class="fa fa-trash" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                        <footer>
                            <tr>
                                <td colspan="5">
                                    {{ $customer->appends(request()->input())->links() }}
                                </td>
                            </tr>
                        </footer>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

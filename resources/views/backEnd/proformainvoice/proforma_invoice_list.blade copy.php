@extends('backEnd.masterpage')
@section('mainContent')

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Proforma Invoice</h2>
                <span class="page-label">Home - Proforma Invoice</span>
            </div>
            <div>
                <a href="{{ url('proforma-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('proforma-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                <tr>
                                    <td colspan="11">
                                        @if (session()->has('message-success'))
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
                                <th>@lang('Doc Date')</th>
                                <th>@lang('Deal Id')</th>
                                <th>@lang('QTN No')</th>
                                <th>@lang('PI No')</th>
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Salesman Name')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count =1; @endphp
                            @foreach ($quotations as $value)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                    <td><a href="{{url('get-url-deal-track/'.@$value->deal_code->code)}}" target="_blank">{{ @$value->deal_code->code }}</a></td>
                                    <td>{{ @$value->deal_code->code }}</td>
                                    <td>{{ @$value->doc_number }}</td>
                                    <td>{{ @$value->customername->name }}</td>
                                    <td>{{ @$value->salesman->full_name }}</td>
                                    <td>
                                        <a class="btn-sm btn-success" href="{{url('proforma-invoice/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                        {{--  <a class="btn-sm btn-primary" href="{{url('proforma-invoice/'.$value->id.'/edit')}}" class="btn-small"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-info" href="{{url('proforma-invoice/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>  --}}

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>


@endsection

@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    @endphp


<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h1 class="h3 mb-0 text-gray-800 page-heading">Upload</h1>
            <span class="page-label">Home - Upload</span>
        </div>
        <div>
            <a href="{{ url('crm-leads') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Lead</a>
            <a href="{{ url('crm-leads/show') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> View Leads</a>
        </div>
    </div>

    <div class="card p-4 d-flex mb-3">
        <div class="row justify-content-center">
            <div class="col-md-10 p-4 border rounded">
                <h2 class="sub-head mb-4">Upload</h2>
                <hr>

    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
        @if (session()->has('message-success'))
            <p class="text-success">
                {{ session()->get('message-success') }}
            </p>
        @elseif(session()->has('message-danger'))
            <p class="text-danger">
                {{ session()->get('message-danger') }}
            </p>
        @endif
    @endif


    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-uploadimg','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}

                    <div class="row">
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO')<span></span></label><br />
                                <div class="form-group files">
                                    <input type="file" class="form-control" multiple="multiple" name="lpo[]">
                                  </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">                        
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Upload</button>
                    </div>
                    {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@endsection

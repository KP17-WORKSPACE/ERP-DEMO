@extends('backEnd.masterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Postdated Receipt</h2>
                <span class="page-label">Home - Postdated Receipt</span>
            </div>
            <div>
                <a href="{{ url('postdatedreceipt-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('postdatedreceipt') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                            <tr>
                                <td colspan="6">
                                    @if (session()->has('message-success-delete'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message-success-delete') }}
                                        </div>
                                    @elseif(session()->has('message-danger-delete'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger-delete') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th> @lang('Doc Number')</th>
                            <th> @lang('Doc Date')</th>
                            <th> @lang('Receipt Mode')</th>
                            <th> @lang('Narration')</th>
                            <th> @lang('Created By')</th>
                            <th> @lang('lang.action')</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        @if (isset($postdatedreceipt))
                            @foreach ($postdatedreceipt as $value)
                                <tr>
                                    <td>{{ @$value->doc_number }}
                                    </td>
                                    <td>
                                        {{date('d-M-Y', strtotime(@$value->doc_date))}}
                                    </td>
                                    <td>
                                        {{ @$value->account->account_name }}
                                    </td>
                                    <td>
                                        {{ @$value->narration }}
                                    </td>
                                    <td>
                                        {{ @$value->createdby->full_name }}
                                    </td>
                                    <td>
                                        
                            {{--  <a class="btn-sm btn-info" href="{{url('crm-deals/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>  --}}
                            <a class="btn-sm btn-primary" href="{{url('postdatedreceipt/' . @$value->id . '/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                        
                                    </td>
                                </tr>
    
                            @endforeach
                        @endif
                    </tbody>
                </table>
        </div>

    </div>

@endsection
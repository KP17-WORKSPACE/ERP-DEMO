@extends('backEnd.masterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Journal Voucher</h2>
                <span class="page-label">Home - Journal Voucher</span>
            </div>
            <div>
                <a href="{{ url('journalvoucher-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('journalvoucher') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
    
            </div>
        </div>
        
        <div class="card shadow mb-4 p-4">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
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
                                <th width="100px"> @lang('Doc Number')</th>
                                <th width="100px"> @lang('Doc Date')</th>
                                <th> @lang('Narration')</th>
                                <th width="100px" class="text-right"> @lang('Amount')</th>
                                <th width="200px" class="pl-3"> @lang('Created By')</th>
                                <th>@lang('Attachment')</th>
                                <th width="100px"> @lang('lang.action')</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            @if (isset($journalvoucher))
                                @foreach ($journalvoucher as $value)
                                    <tr @if($value->status == 2) class="bg-dark" @endif  @if(@$value->credit_amount == "") class="text-danger" @endif >
                                        <td><a href="{{url('journalvoucher/' . @$value->id . '/view')}}">{{ @$value->doc_number }}</a>
                                        </td>
                                        <td>
                                            {{ @$value->doc_date }}
                                        </td>
                                        <td>
                                            {{ @$value->narration  }}
                                        </td>
                                        <td class="text-right">
                                            {{ @App\SysHelper::com_curr_format(@$value->credit_amount,'','',',') }}
                                            {{--  {{ @$value->debit_amount }}  --}}
                                        </td>
                                        <td class="pl-3">
                                            {{ @$value->createdby->full_name }}
                                        </td>
                                        <td>
                                           @if (empty(@$value->attach))
                                               
                                           @else
                                               @foreach (explode(',', @$value->attach) as $att)
                                                   <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                               @endforeach
                                           @endif
                                       </td>
                                        <td>
                                            @if ((Auth::user()->role_id == 1 || Auth::user()->id == @$value->created_by) && $value->status != 0)
                                            <a class="btn-sm btn-primary" href="{{url('journalvoucher/' . @$value->id . '/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            @if (@$value->status == 2)
                                                <a class="btn-sm btn-warning" href="{{url('journalvoucher/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                            @else
                                                <a class="btn-sm btn-danger" href="{{url('journalvoucher/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @endif
                                            @endif
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
    

@endsection
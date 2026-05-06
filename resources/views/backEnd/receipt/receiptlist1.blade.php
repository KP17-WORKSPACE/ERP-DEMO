@extends('backEnd.masterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Receipts</h2>
                <span class="page-label">Home - Receipt</span>
            </div>
            <div>
                <a href="{{ url('receipt-add') }}" target="_blank" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('receipt') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                <a type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-search"></i> Search</a>
            </div>
        </div>
        
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">
                
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt', 'method' => 'post', 'id' => 'receipt-search']) }}
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Doc Number</label>
                        <input class="form-control" id="doc_number" type="text" autocomplete="off" name="doc_number" value="{{ $ctrl_doc_number }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Receipt Mode</label>
                        <select class="form-control js-example-basic-single" name="receipt_mode" id="receipt_mode">
                            <option value="">-Select-</option>
                            @if (count($receipt_mode_list)>0)
                            @foreach ($receipt_mode_list as $li)
                                <option value="{{ $li["id"] }}" @if($ctrl_receipt_mode==$li["id"]) selected @endif>{{ $li["account_name"] }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Receipt Through</label>
                        <select class="form-control js-example-basic-single" name="receipt_through" id="receipt_through">
                            <option value="">-Select-</option>
                            <option value="0">Cash</option>
                            <option value="1">Bank Transfer</option>
                            <option value="2">CDC Cheque</option>
                            <option value="3">PDC Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Account Name</label>
                        <select class="form-control js-example-basic-single" name="account_name" id="account_name">
                            <option value="">-Select-</option>
                            @foreach ($accounts as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_account_name ==$value->id) selected @endif>{{ @$value->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Amount</label>
                        <input class="form-control datepicker" id="amount" type="text" autocomplete="off" name="amount" value="{{ $ctrl_amount }}">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Doc Date</label>
                        <input class="form-control datepicker" id="doc_date" type="date" autocomplete="off" name="doc_date" value="{{ $ctrl_doc_date }}">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Receipt Date</label>
                        <input class="form-control datepicker" id="receipt_date" type="date" autocomplete="off" name="receipt_date" value="{{ $ctrl_receipt_date }}">
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Cheque Date</label>
                        <input class="form-control datepicker" id="cheque_date" type="date" autocomplete="off" name="cheque_date" value="{{ $ctrl_cheque_date }}">
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Cheque Number</label>
                        <input class="form-control datepicker" id="cheque_number" type="text" autocomplete="off" name="cheque_number" value="{{ $ctrl_cheque_number }}">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Cheque Bank Name</label>
                        <input class="form-control datepicker" id="cheque_bank_name" type="text" autocomplete="off" name="cheque_bank_name" value="{{ $ctrl_cheque_bank_name }}">
                    </div>
                    
                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>
                        <input class="form-control datepicker" id="deal_id" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id }}">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="" class="form-check-label">Created By</label>
                        <select class="form-control js-example-basic-single" name="created_by" id="created_by">
                            <option value="">-Select-</option>
                            @foreach ($staff_list as $value)
                            <option value="{{ @$value->user_id }}" @if($ctrl_created_by ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
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

        <div class="card p-4 mb-2">

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
                        <th style="width: 70px;"> @lang('Doc Number')</th>
                        <th> @lang('Mode')</th>
                        <th> @lang('Receipt Mode')</th>
                        <th> @lang('Receipt Through')</th>
                        <th> @lang('Account Name')</th>
                        <th class="text-right"> @lang('Amount')</th>                        
                        <th> @lang('Doc Date')</th>
                        <th> @lang('Receipt Date')</th>
                        <th> @lang('Cheque Date')</th>
                        <th> @lang('Cheque Number')</th>
                        <th> @lang('Cheque Bank Name')</th>
                        <th> @lang('Deal ID')</th>
                        <th> @lang('Created By')</th>
                        <th> @lang('Narration')</th>
                        <th width="110px"> @lang('lang.action')</th>
                    </tr>
                </thead>

                <tbody>
                    @if (isset($receipt))
                        @foreach ($receipt as $value)
                            <tr @if($value->status == 2) class="bg-dark" @endif @if(@$value->type==2) class="text-danger" @endif>
                                <td><a href="{{url('receipt/' . @$value->id . '/view')}}">{{ @$value->doc_number }}</a></td>
                                <td>
                                    @if(@$value->mode == 1) Cash
                                    @else Bank
                                    @endif
                                </td>
                                <td>{{ @$value->account->account_name }}</td>
                                <td>
                                    @if(@$value->mode == 1) Cash
                                    @else
                                        @if(@$value->receipt_through == 1) Bank Transfer
                                        @elseif(@$value->receipt_through == 2) CDC Cheque
                                        @else PDC Cheque
                                        @endif
                                    @endif
                                </td>
                                <td>{{ @$value->account_name }}</td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format(abs(@$value->debit_amount - @$value->credit_amount),2,'.',',') }}</td>
                                <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                                <td>{{date('d/m/Y', strtotime(@$value->receipt_date))}}</td>
                                <td>@if(@$value->mode == 2 && @$value->receipt_through != 1) {{date('d/m/Y', strtotime(@$value->cheque_date))}} @endif</td>
                                <td>{{ @$value->cheque_number }}</td>
                                <td>{{ @$value->cheque_bank_name }}</td>
                                
                                
                                <td>
                                    @php
                                    $dealid =  explode(',', $value->deal_id);
                                    @endphp
                                    @foreach($dealid as $d)
                                    @php $deal_code = @App\SysHelper::get_code_from_dealid($d); @endphp
                                        <a href="{{url('get-url-deal-track/'.$deal_code)}}" target="_blank">{{ $deal_code }}</a>
                                    @endforeach
                                    
                                
                                
                                </td>



                                <td>{{ @$value->full_name }}</td>
                                <td>{{ @$value->narration }}</td>
                                <td>
                                    <a class="btn-sm btn-info" href="{{url('receipt/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    
                                    <a class="btn-sm btn-primary" href="{{url('receipt/' . @$value->id . '/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                    @if (@$value->status == 2)
                                        <a class="btn-sm btn-warning" href="{{url('restore-receipt/'.$value->id)}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                    @else
                                        <a class="btn-sm btn-danger" href="{{url('delete-receipt/'.$value->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
    </div>


    </div>

@endsection
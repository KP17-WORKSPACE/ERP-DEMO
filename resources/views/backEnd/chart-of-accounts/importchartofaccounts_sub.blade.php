@extends('backEnd.newmasterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<?php try { ?>
    
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Chartofaccounts Sub Import
                </h4>
                <div class="purchase-order-content-header-right">
                <a class="btn btn-light" href="{{ url('chartofaccounts-import') }}"> Account Import</a>
                <a class="btn btn-light" href="{{ url('chartofaccounts-import-sub') }}"> Sub Account Import</a>
                <a class="btn btn-light" href="{{ url('chartofaccounts-add') }}"> Account</a>
                <a class="btn btn-light" href="{{ url('chartofaccounts-add-sub') }}"> Sub Account</a>
                <a class="btn btn-light" href="{{ url('chartofaccounts') }}"> Chart of Account</a>
                <a class="btn btn-light" href="{{ url('accountgroupsub2-add') }}"> Sub Group</a>
                <a class="btn btn-light" href="{{ url('accountgroupsub-add') }}"> Group</a>
                    {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
<div class="row">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'chartofaccounts-import-sub-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <div class="boxed-formctrl">
                        <div class="add-visitor">
                            <div class="row mb-10">
                                <div class="col-lg-12">
                                    @if (session()->has('message-success'))
                                        <div class="alert alert-success mb-20">
                                            {{ session()->get('message-success') }}
                                        </div>
                                    @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Choose File<span>*.csv</span> (<a href="{{ url('public/uploads/product_upload/chartofaccounts_import_sub_sample_file.csv') }}" target="_blank">Sample File</a>)</label>
                                        <input class="form-control" type="file" accept=".csv" name="import_file" required>
                                    </div>
                                </div>
                             <div class="col-lg-2 mt-4">
    <div class="input-effect d-flex align-items-center gap-2">
        
        <button type="submit" class="btn btn-light">
            <span class="ti-check"></span> Submit
        </button>

        @if (count($data) > 0)
            <a href="{{ url('chartofaccounts-import-sub-clear') }}" 
               class="btn btn-light">
                Clear Data
            </a>
        @endif

    </div>
</div>
                            </div>                            
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
</div>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">

<div class="row">
<div class="col-lg-12">
                    <table class="table table-hover form-item-table" width="100%" cellspacing="0">
                            <thead>
                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                    <tr>
                                        <td colspan="11">
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
                                    <th width="350px">Sub Account Name</th>
                                    <th width="350px">Account Name</th>
                                    <th width="150px" class="text-end">Debit Amount</th>
                                    <th width="150px" class="text-end">Credit Amount</th>
                                    <th width="150px" class="text-center">Date</th>
                                    <th width="150px" class="text-start">Department</th>
                                    <th width="150px" class="text-center">Prepaid/Accrued Exp</th>
                                   
                                </tr>
                            </thead>

                            <tbody>
                                @php $check = 0; @endphp
                                @if (count($data)>0)
                                    @foreach ($data as $value)
                                    @php
                                        $account_sub_name_id = $account_name->where('account_name',$value->sub_account_name)->max('account_name');
                                        $account_name_id = $account_name->where('account_name',$value->account_name)->max('account_name');
                                        $group_id = $account_group->where('title',$value->group)->max('title');
                                        $group_sub_id = $account_group_sub->where('title',$value->subgroup)->max('title');
                                        $group_sub2_id = $account_group_sub2->where('title',$value->subgroup2)->max('title');
                                    @endphp

                                        <tr>
                                            <td @if($account_sub_name_id != "") @php $check = 1; @endphp class="bg-warning text-dark" @endif>{{ @$value->sub_account_name }}</td>
                                            <td @if($account_name_id == "") @php $check = 1; @endphp class="bg-danger text-white" @endif>{{ @$value->account_name }}</td>
                                       
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->debit_amount,2,'.',',') }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->credit_amount,2,'.',',') }}</td>
                                            <td class="text-center">{{ @App\SysHelper::normalizeToDmy(@$value->sub_account_date) }}</td>
                                            <td class="text-start">{{ @$value->department }}</td>
                                            <td class="text-center">{{ @$value->yes_no }}</td>
                                    @endforeach
                                @endif
                            </tbody>
                            <?php try{ ?>
                            <footer>
                                <tr>
                                    <td colspan="4">
                                    </td>
                                </tr>
                            </footer>
                            <?php }catch (\Exception $e) { } ?>
                        </table>
                </div>
                @if (count($data) > 0 && $check == 0)
                <div class="col-lg-12 text-center d-flex justify-content-center mt-4">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'chartofaccounts-import-sub-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @if (session()->has('message-success'))
                            <div class="alert alert-success mb-20">
                                {{ session()->get('message-success') }}
                            </div>
                        @elseif(session()->has('message-danger'))
                            <div class="alert alert-danger">
                                {{ session()->get('message-danger') }}
                            </div>
                        @endif
                               <button class="btn btn-light" type="submit">
                                            <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Import Data
                                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
                @endif
</div>
                </div>
            </div>


        </div>
    </div>
</div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

@section('script')
    <script>

$(document).ready(function()
    {
        // Stop user to press enter in textbox
        $("input:text").keypress(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
});

    </script>
@endsection

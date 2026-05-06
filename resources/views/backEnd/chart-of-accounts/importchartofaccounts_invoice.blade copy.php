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
            <h2 class="page-heading m-0">Chartofaccounts Invoice Import</h2>
            <span class="page-label">Home - Chartofaccounts Invoice Import</span>
        </div>
        <div>
            <a href="{{ url('chartofaccounts-import-invoice') }}" type="button" class="btn btn-warning"><i class="fa fa-plus"></i> Import Invoices</a>
            <a href="{{ url('chartofaccounts-opening-balance') }}" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Opening Balance</a>
            <a href="{{ url('chartofaccounts-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Account</a>
            <a href="{{ url('chartofaccounts-add-sub') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Sub Account</a>
            <a href="{{ url('chartofaccounts') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Chart of Account</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'chartofaccounts-import-invoice-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                        <label class="txtlbl">Choose File<span>*.csv</span> (<a href="{{ url('public/uploads/product_upload/chartofaccounts_invoice_import_sample_file.csv') }}" target="_blank">Sample File</a>)</label>
                                        <input class="form-control" type="file" accept=".csv" name="import_file" required>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl"><br />
                                        <button class="btn btn-primary mt-2">
                                            <span class="ti-check"></span> Submit
                                        </button>
                                        @if (count($data)>0)
                                        <a href="{{ url('chartofaccounts-import-invoice-clear') }}" class="btn btn-info mt-2">Clear Data</a> @endif
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


                
                <div class="col-lg-12">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
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
                                    <th width="200px">Account Code</th>
                                    <th>Account Name</th>
                                    <th width="150px">Invoice No</th>
                                    <th width="150px">Invoice Date</th>
                                    <th width="150px" class="text-right">Debit Amount</th>
                                    <th width="150px" class="text-right">Credit Amount</th>
                                    <th width="150px" class="">PO No</th>
                                    <th width="200px" class="">Payment Terms</th>
                                    <th width="100px" class="">Due Date</th>
                                    <th width="100px" class="">Deal Id</th>
                                    <th width="100px" class="">Bill No</th>
                                    <th width="100px" class="">Bill Date</th>
                                    <th width="100px" class="">Sales Person</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (count($data)>0)
                                    @foreach ($data as $value)
                                    @php
                                        $account_id = $account_name->where('account_code',$value->account_code)->max('id');
                                    @endphp

                                        <tr @if($account_id == 0) class="bg-warning" @endif>
                                            <td>{{ @$value->account_code }}</td>
                                            <td>{{ @$value->account_name }}</td>
                                            <td>{{ @$value->invoice_no }}</td>
                                            <td>{{ date('d/m/Y', strtotime(@$value->invoice_date)) }}</td>
                                            <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->debit_amount,2,'.',',') }}</td>
                                            <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->credit_amount,2,'.',',') }}</td>
                                            <td>{{ @$value->po_no }}</td>
                                            <td>{{ @$value->payment_terms }}</td>
                                            <td>{{ date('d/m/Y', strtotime(@$value->due_date)) }}</td>
                                            <td>{{ @$value->deal_id }}</td>
                                            <td>{{ @$value->bill_no }}</td>
                                            <td>{{ @$value->bill_date }}</td>
                                            <td>{{ @$value->sales_person }}</td>
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
                @if (count($data)>0)
                <div class="col-lg-12 text-center">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'chartofaccounts-import-invoice-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @if (session()->has('message-success'))
                            <div class="alert alert-success mb-20">
                                {{ session()->get('message-success') }}
                            </div>
                        @elseif(session()->has('message-danger'))
                            <div class="alert alert-danger">
                                {{ session()->get('message-danger') }}
                            </div>
                        @endif
                            <button class="btn btn-danger mt-2">
                                <span class="ti-check"></span> Import Data
                            </button>
                    </div>
                    {{ Form::close() }}
                </div>
                @endif

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

@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Chart of Accounts Opening Balance</h2>
                <span class="page-label">Home - Chart of Accounts Opening Balance</span>
            </div>
            <div>
                <a href="{{ url('chartofaccounts-import-invoice') }}" type="button" class="btn btn-warning"><i class="fa fa-plus"></i> Import Invoices</a>
                <a href="{{ url('chartofaccounts-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Account</a>
                <a href="{{ url('chartofaccounts-add-sub') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Sub Account</a>
                <a href="{{ url('chartofaccounts') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Chart of Account</a>
            </div>
        </div>


    

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
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
                                <th style="width: 120px;">@lang('Account Code')</th>
                                <th>@lang('Sub Account Name')</th>
                                <th style="width: 120px;">@lang('Transaction Date')</th>
                                <th style="width: 120px;" class="text-right">@lang('Debit Amount')</th>
                                <th style="width: 120px;" class="text-right">@lang('Credit Amount')</th>
                                <th style="width: 250px;" class="pl-4">@lang('Invoice Number')</th>
                            </tr>
                        </thead>
        
                        <tbody>
                            @if (isset($account))
                                @foreach ($account as $value)
                                @if (@$value->debit_amount > 0 || @$value->credit_amount > 0)
                                <tr>
                                    <td style="font-size: 12px;">
                                        <a href="{{url('chartofaccounts/' . @$value->account_id . '/edit')}}" target="_blank">
                                        {{ @$value->account_code }}</a>
                                    </td>
                                    <td style="font-size: 12px;">
                                        {{ @$value->accounts->account_name }}
                                    </td>
                                    <td style="font-size: 12px;">
                                        {{date('d/m/Y', strtotime(@$value->transaction_date))}}
                                    </td>
                                        <?php
                                            $inv_list = $invoice->where('account_id',$value->account_id);
                                            $d1 = @App\SysHelper::com_curr_format($inv_list->sum('debit_amount'),2,'.',',');
                                            $c1 = @App\SysHelper::com_curr_format($inv_list->sum('credit_amount'),2,'.',',');
                                            $d_class=""; $c_class="";

                                            if (strpos(optional($value)->account_code, 'CUS') !== false){
                                                if(@App\SysHelper::com_curr_format($value->debit_amount,2,'.',',') != $d1){
                                                    $d_class="text-danger";
                                                }
                                            }
                                            if (strpos(optional($value)->account_code, 'SUP') !== false){
                                                if(@App\SysHelper::com_curr_format($value->credit_amount,2,'.',',') != $c1){
                                                    $c_class="text-danger";
                                                }
                                            }
                                        ?>
                                    <td style="font-size: 12px;" class="text-right {{ $d_class }}">
                                        {{ @App\SysHelper::com_curr_format(@$value->debit_amount,2,'.',',') }}
                                    </td>
                                    <td style="font-size: 12px;" class="text-right {{ $c_class }}">
                                        {{ @App\SysHelper::com_curr_format(@$value->credit_amount,2,'.',',') }}
                                    </td>
                                    <td class="pl-4">
                                        @php $invoice_list = $invoice->where('account_id',$value->account_id); @endphp
                                        @if (count($invoice_list)>0)
                                        {{--  @foreach ($invoice_list as $list)
                                            {{ $list->invoice_no }}, 
                                        @endforeach  --}}
                                        {{-- <button class="btn-sm btn-danger" data-toggle="modal" data-target=".bd-example-modal-lg-{{ $value->account_id }}" data-backdrop="static" data-keyboard="false">View & Edit</button> --}}                                        
                                        <a class="btn-sm btn-danger" href="{{url('chartofaccounts-opening-balance-edit/'.$value->account_id)}}">View & Edit</a>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right font-weight-bold pr-2">{{ @App\SysHelper::com_curr_format($account->sum('debit_amount'),2,'.',',') }}</td>
                                <td class="text-right font-weight-bold pr-2">{{ @App\SysHelper::com_curr_format($account->sum('credit_amount'),2,'.',',') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>

    </div>


    <script>
        $('#dataTable').DataTable({
    "pageLength": -1,         // Show all records
    "lengthMenu": [[-1], ["All"]]  // Only show "All" as an option
});
        </script>

    @if (isset($account))
    @foreach ($account as $value)
    <div class="modal fade bd-example-modal-lg-{{ $value->account_id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{{ @$value->account_code }} - {{ @$value->accounts->account_name }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                @php $invoice_list = $invoice->where('account_id',$value->account_id); @endphp
                @if (count($invoice_list)>0)
                <table class="table table-bordered table-striped" id="dataTable_{{ $value->account_id }}" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th class="text-right">Debit Amount</th>
                            <th class="text-right">Credit Amount</th>
                            <th class="text-right"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($invoice_list as $list)
                    <tr>
                        <td><input type="text" class="form-control" id="invoice_no_{{ $list->id }}" value="{{ $list->transaction_no }}" /></td>
                        <td><input type="date" class="form-control" id="invoice_date_{{ $list->id }}" value="{{ $list->transaction_date }}" /></td>
                        <td><input type="text" class="form-control text-right" id="debit_amount_{{ $list->id }}" value="{{ $list->debit_amount }}" /></td>
                        <td><input type="text" class="form-control text-right" id="credit_amount_{{ $list->id }}" value="{{ $list->credit_amount }}" /></td>
                        <td>
                            <input type="hidden" id="account_id_{{ $list->id }}" value="{{ $value->account_id }}" />
                            <button class="btn-sm btn-warning" onclick="update_invoice({{ $list->id }})"><i class="fa fa-edit" aria-hidden="true"></i></button>
                            <button class="btn-sm btn-danger" onclick="delete_invoice({{ $list->id }})"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        @php
                        $d = $invoice_list->sum('debit_amount');
                        $c = $invoice_list->sum('credit_amount');
                        @endphp
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format($d,2,'.',',') }}</td>
                            <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format($c,2,'.',',') }}</td>
                            <td></td>
                        </tr>
                        <tr style="background: #e3e3e3;">
                            <td colspan="2" class="text-right">Closing Balance</td>
                            @if($d > $c)
                            <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format(($d-$c),2,'.',',') }}</td>
                            <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format((0),2,'.',',') }}</td>
                            @else
                            <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format((0),2,'.',',') }}</td>
                            <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format(($c-$d),2,'.',',') }}</td>
                            @endif
                            <td></td>
                            <input type="hidden" id="closing_balance_{{ $value->account_id }}" value="{{ abs(abs($d)-abs($c)) }}" />
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            {{--  <button type="button" class="btn btn-primary">Save changes</button>  --}}
            </div>
        </div>
        </div>
    </div>
    @endforeach
    @endif

    <script>
        function update_invoice(id){
            $("#loading_bg").css("display", "block");
            var invoice_no = $('#invoice_no_'+id).val();
            var invoice_date = $('#invoice_date_'+id).val();
            var debit_amount = $('#debit_amount_'+id).val();
            var credit_amount = $('#credit_amount_'+id).val();
            var account_id = $('#account_id_'+id).val();
            var action = "{{ URL::to('chartofaccounts-invoice-update') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    invoice_no: invoice_no,
                    invoice_date: invoice_date,
                    debit_amount: debit_amount,
                    credit_amount: credit_amount,
                    account_id: account_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        alert("Updated Successfully!");
                        location.reload(true);
                    }
                }
            });
        }

        function delete_invoice(id){
            $("#loading_bg").css("display", "block");
            var account_id = $('#account_id_'+id).val();
            var action = "{{ URL::to('chartofaccounts-invoice-delete') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    account_id: account_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        alert("Deleted Successfully!");
                        location.reload(true);
                    }
                }
            });
        }

    </script>
    
    <script>
        $(document).ready(function() {
            $("#btnSubmit").click(function() {
                setTimeout(function() {
                    disableButton();
                }, 0);
            });

            function disableButton() {
                //$("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection

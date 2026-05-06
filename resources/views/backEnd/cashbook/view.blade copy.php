@extends('backEnd.masterpage')
@section('mainContent')

@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<style>
.border { border: solid 1px #e3e6f0; }
</style>

    <?php try { ?>
        
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Cash Book</h2>
            <span class="page-label">Home - Cash Book</span>
        </div>
        <div>
            <a href="{{ url('receipt-add/cashbook') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Receipts</a>
            <a href="{{ url('payment-add/cashbook') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Payments</a>
            <a href="{{ url('journalvoucher-add/cashbook') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Journal Voucher</a>
            {{--  <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Customer</a>
            <a href="{{ url('customers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer List</a>
            <a href="{{ url('customer-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Profile</button></a>  --}}
        </div>
    </div>
    <div class="card p-4 mb-4">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'cashbook', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <div class="row">
                <div class="col-md-4 mb-20">
                    <div class="input-effect">
                        <label>@lang('Account')</label>
                        <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                            @foreach ($accounts as $val)
                                <option value="{{ @$val->id }}" @if(isset($account_id)) @if(@$account_id == @$val->id) selected @endif @endif >{{ @$val->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('From Date')</label>
                                <input class="form-control" id="from_date" type="date" name="from_date" value="{{ @$from_date }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('To Date')</label>
                                <input class="form-control" id="to_date" type="date" name="to_date" value="{{ @$to_date }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 mt-4">
                    <div class="input-effect" id="sectionSubgroupDiv">
                        <button class="btn btn-primary">
                            <span class="ti-search"></span>
                            @lang('Search')
                        </button>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
    </div>

    <div class="card p-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card-body p-0">
                    <h5 style="text-align: center;"></h5>
                    <div class="table-responsive-sm">
                        <table class="table" style="border: solid 1px #e3e6f0;">
                          <thead>
                            <tr>
                                <th class="border text-center" width="10%">Date</th>
                                <th class="border text-center" width="10%">Doc No</th>
                                <th class="border text-center" width="20%">Particular</th>
                                <th class="border text-center" width="10%">Debit</th>
                                <th class="border text-center" width="10%">Credit</th>
                                <th class="border text-center" width="10%">Balance</th>
                                <th class="border text-center" width="30%">Narration</th>
                            </tr>
                          </thead>                          
                          <tbody>
                            
                            <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>
                            @if (count($data)>0)
                            @foreach ($data as $dt)
                            
                            <?php try { ?>
                                @if($dt!="")
                            <tr>
                                <td class="border pl-2">{{ date('d/m/Y', strtotime($dt["transaction_date"])) }}</td>
                                <td class="border pl-2">
                                    @if(substr($dt["transaction_no"], 0, 2)=="JV")
                                        <a href="{{url('journalvoucher/' . $dt['transaction_id'] . '/view')}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="CR")
                                        <a href="{{url('receipt/' . $dt['transaction_id'] . '/view')}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="BR")
                                        <a href="{{url('receipt/' . $dt['transaction_id'] . '/view')}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="CP")
                                        <a href="{{url('payment/' . $dt['transaction_id'] . '/view')}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="BP")
                                        <a href="{{url('payment/' . $dt['transaction_id'] . '/view')}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @else
                                        {{ $dt["transaction_no"] }}
                                    @endif
                                </td>
                                <td class="border pl-2">{{ $dt["account_name"] }}</td>
                                <td class="border text-right pr-2">{{ @App\SysHelper::com_curr_format($dt["debit_amount"], 2, '.', ',') }} @php $total_dr += $dt["debit_amount"]; @endphp </td>
                                <td class="border text-right pr-2">{{ @App\SysHelper::com_curr_format($dt["credit_amount"], 2, '.', ',') }} @php $total_cr += $dt["credit_amount"]; @endphp </td>
                                <td class="border text-right pr-2">
                                    <?php $tot -= $dt["credit_amount"] ?>
                                    <?php $tot += $dt["debit_amount"] ?>
                                    {{ @App\SysHelper::com_curr_format($tot, 2, '.', '') }}
                                </td>
                                <td class="border pl-2">{{ $dt["remarks"] }}</td>
                            </tr>
                            @endif
                            <?php }catch (\Exception $e) {  } ?>

                            @endforeach
                            @endif

                          </tbody>
                          <thead>
                            <tr>
                                <th class="border"></th>
                                <th class="border"></th>
                                <th class="border"></th>
                                <th class="border text-right pr-2">{{ @App\SysHelper::com_curr_format($total_dr, 2, '.', '') }}</th>
                                <th class="border text-right pr-2">{{ @App\SysHelper::com_curr_format($total_cr, 2, '.', '') }}</th>
                                <th class="border text-right pr-2">{{ @App\SysHelper::com_curr_format($total_dr - $total_cr, 2, '.', '') }}</th>
                                <th class="border"></th>
                            </tr>
                          </thead>
                        </table>
                      </div>


                    <div class="row mb-3">
                        <label class="col-lg-4 text-muted"></label>
                        <div class="col-lg-8">
                            <span class="font-weight-bold text-gray-800"></span>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>



    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    
@endsection

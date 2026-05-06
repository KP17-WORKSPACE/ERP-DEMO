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
                    <h2 class="page-heading m-0">Supplier Ledger</h2>
                    <span class="page-label">Home - Supplier Ledger</span>
                </div>
                <div>
                    {{--  <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Customer</a>
                    <a href="{{ url('customers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer List</a>
                    <a href="{{ url('customer-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Profile</button></a>  --}}
                </div>
            </div>
            
            <div class="card p-4 mb-2">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplierledger', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    <div class="row">
                                        <div class="col-md-4 mb-20">
                                            <div class="input-effect">
                                                <label>@lang('Account')</label>
                                                <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                                                    <option data-display="Account *" value="">@lang('Account Name') *</option>
                                                    @foreach ($accounts as $val)
                                                        <option value="{{ @$val->id }}" @if(isset($account_name)) @if(@$account_name == @$val->id) selected @endif @endif >{{ @$val->account_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label>@lang('From Date')</label>
                                                        @php
                                                        $value = date('Y-m-d');
                                                        if(isset($editData) && !empty($from_date1) ){ @$value = date('Y-m-d', strtotime(@$from_date1)); }
                                                        else{ if(!empty(old('from_date'))){ @$value = old('from_date');}else{@$value = $from_date1; } }
                                                        @endphp
                                                        <input class="form-control" id="from_date" type="date" name="from_date" value="{{ @$value }}" autocomplete="off">
                                                        @if ($errors->has('from_date'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('from_date') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label>@lang('To Date')</label>
                                                        @php
                                                        $value = date('Y-m-d');
                                                        if(isset($editData) && !empty($to_date1) ){ @$value = date('Y-m-d', strtotime(@$to_date1)); }
                                                        else{ if(!empty(old('to_date'))){ @$value = old('to_date'); }else{ @$value = $to_date1; } }
                                                        @endphp
                                                        <input class="form-control" id="to_date" type="date" name="to_date" value="{{ @$value }}" autocomplete="off">
                                                        @if ($errors->has('to_date'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('to_date') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--  <div class="col-md-3 mb-20">
                                            <div class="input-effect">
                                                <label>@lang('Duration')</label>
                                                <select class="form-control" name="period" id="period">
                                                    <option value="">@lang('')</option>
                                                    <option value="1">@lang('All')</option>
                                                    <option value="2">@lang('Today')</option>
                                                    <option value="3">@lang('This Month')</option>
                                                    <option value="4">@lang('This Quarter')</option>
                                                    <option value="5">@lang('This Financial Year')</option>
                                                    <option value="6">@lang('Yesterday')</option>
                                                    <option value="7">@lang('Previous Month')</option>
                                                    <option value="8">@lang('Previous Quarter')</option>
                                                    <option value="9">@lang('Previous Financial Year')</option>
                                                    <option value="10">@lang('Previous Financial Year to Date')</option>
                                                    <option value="11">@lang('Month Start (to Date)')</option>
                                                    <option value="12">@lang('Month End (from Date)')</option>
                                                    <option value="13">@lang('Year Start (to Date)')</option>
                                                    <option value="14">@lang('Year End (from Date)')</option>
                                                </select>
                                            </div>
                                        </div>  --}}
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
                <table class="table" style="border: solid 1px #e3e6f0;">
                    <thead>
                      <tr>
                          <th class="border text-center" width="10%">Date</th>
                          <th class="border text-center" width="10%">Doc No</th>
                          <th class="border text-center" width="30%">Particular</th>
                          <th class="border text-center" width="13%">Debit</th>
                          <th class="border text-center" width="13%">Credit</th>
                          <th class="border text-center" width="14%">Balance</th>
                          <th class="border text-center" width="10%">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                        <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>
                      @if (isset($data))
                          @foreach ($data as $dt)
                          @if($dt->transaction_type=="bankreceipt" && $dt->credit_amount=='0.00')
                          
                          @elseif($dt->transaction_type=="postdatedreceipt" && $dt->credit_amount=='0.00')
                          
                          @elseif($dt->transaction_type=="bankpayment" && $dt->debit_amount=='0.00')
                          
                          @elseif($dt->transaction_type=="postdatedpayment" && $dt->debit_amount=='0.00')
    
                          @else
                          <tr>
                            <td class="border pl-2">{{ date('d-M-Y', strtotime($dt->transaction_date)) }}</td>
                              <td class="border pl-2">{{ $dt->transaction_no }}</td>
                              <td class="border pl-2">{{ $dt->accounts->account_name }}</td>
                              <td class="border text-right pr-2">{{ $dt->debit_amount }} @php $total_dr += $dt->debit_amount; @endphp </td>
                              <td class="border text-right pr-2">{{ $dt->credit_amount }} @php $total_cr += $dt->credit_amount; @endphp </td>
                              <td class="border text-right pr-2">
                                  <?php $tot += $dt->debit_amount ?>
                                  <?php $tot -= $dt->credit_amount ?>
                                  {{ @App\SysHelper::com_curr_format(trim($tot,'-'), 2, '.', '') }}
                              </td>
                              <td class="border pl-2">{{ $dt->remarks }}</td>
                          </tr>
                          @endif
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
                          <th class="border text-right pr-2">{{ @App\SysHelper::com_curr_format(trim($total_dr - $total_cr,'-'), 2, '.', '') }}</th>
                          <th class="border"></th>
                      </tr>
                    </thead>
                  </table>
            </div>
        </div>
    
        <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
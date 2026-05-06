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
                <h2 class="page-heading m-0">General Ledger</h2>
                <span class="page-label">Home - General Ledger</span>
            </div>
            <div>
                {{--  <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Customer</a>
                <a href="{{ url('customers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer List</a>
                <a href="{{ url('customer-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Profile</button></a>  --}}
            </div>
        </div>
        
        <div class="card p-4 mb-2">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'generalledger', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="row">
                                    <div class="col-md-4 mb-20">
                                        <div class="input-effect">
                                            <label>@lang('Account')</label>
                                            <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                                                <option data-display="Account *" value="">@lang('Account Name') *</option>
                                                @foreach ($accounts as $val)
                                                    <option value="{{ @$val->id }}" @if(@$account_id == @$val->id) selected @endif >{{ @$val->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    @php
                                                    $value = date('Y-m-01');
                                                    if(isset($from_date) && !empty($from_date) ){ @$value = date('Y-m-d', strtotime(@$from_date)); }
                                                    @endphp
                                                    <label>@lang('From Date')</label>
                                                    <input class="form-control" id="from_date" type="date" name="from_date" value="{{ @$value }}" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if(isset($to_date) && !empty($to_date) ){ @$value = date('Y-m-d', strtotime(@$to_date)); }
                                                    @endphp
                                                    <label>@lang('To Date')</label>
                                                    <input class="form-control" id="to_date" type="date" name="to_date" value="{{ @$value }}" autocomplete="off">
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
            <?php $is_merge = App\SysHelper::ledger_merge_account($account_id); ?>
            <?php $is_merge_notvat = App\SysHelper::ledger_merge_account_notvat($account_id); ?>
            <?php $is_merge_vat = App\SysHelper::ledger_merge_account_vat($account_id); ?>

            

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
                  
                    <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0;?>
                  @if (isset($data))
                      @for ($i=0; $i < count($data); $i++)
                      <?php $ac_id = strtolower($data[$i]["account_name"]); ?>

                      @if(($ac_id == "purchase" || $ac_id == "sales" || $ac_id == "purchase return" || $ac_id == "sales return") && $is_merge == true)
                      
                      <?php
                        $date = date('d-M-Y', strtotime($data[$i]["transaction_date"]));
                        $trn_no = $data[$i]["transaction_no"];
                        $acc_name = $data[$i]["account_name"];
                        $rem = $data[$i]["remarks"];
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        $i++;
                        $deb += $data[$i]["debit_amount"];
                        $cre += $data[$i]["credit_amount"];
                      ?>

                      <tr>
                        <td class="border pl-2">{{ $date }}</td>
                        <td class="border pl-2">{{ $trn_no }}</td>
                        <td class="border pl-2">{{ $acc_name }}</td>
                        <td class="border text-right pr-2">{{ $deb }} @php $total_dr += $deb; @endphp </td>
                        <td class="border text-right pr-2">{{ $cre }} @php $total_cr += $cre; @endphp </td>
                        <td class="border text-right pr-2">
                        @if ($group == 1 || $group == 3)
                            @php $tot += ($deb); @endphp
                            @php $tot -= ($cre); @endphp
                        @endif
                        @if ($group == 2 || $group == 4 || $group == 5)
                            @php $tot += ($cre); @endphp
                            @php $tot -= ($deb); @endphp
                        @endif
                            {{ @App\SysHelper::com_curr_format(($tot), 2, '.', '') }}
                        </td>
                        <td class="border pl-2">{{ $rem }}</td>
                      </tr>



                      @else




                      <tr>
                        <td class="border pl-2">{{ date('d/m/Y', strtotime($data[$i]["transaction_date"])) }}</td>
                        <td class="border pl-2">{{ $data[$i]["transaction_no"] }}</td>
                        <td class="border pl-2">{{ $data[$i]["account_name"] }}</td>
                        <td class="border text-right pr-2">{{ $data[$i]["debit_amount"] }} @php $total_dr += $data[$i]["debit_amount"]; @endphp </td>
                        <td class="border text-right pr-2">{{ $data[$i]["credit_amount"] }} @php $total_cr += $data[$i]["credit_amount"]; @endphp </td>
                        <td class="border text-right pr-2">
                        @if ($group == 1 || $group == 3)
                            @php $tot += ($data[$i]["debit_amount"]); @endphp
                            @php $tot -= ($data[$i]["credit_amount"]); @endphp
                        @endif
                        @if ($group == 2 || $group == 4 || $group == 5)
                            @php $tot += ($data[$i]["credit_amount"]); @endphp
                            @php $tot -= ($data[$i]["debit_amount"]); @endphp
                        @endif
                            {{ @App\SysHelper::com_curr_format(($tot), 2, '.', '') }}
                        </td>
                        <td class="border pl-2">{{ $data[$i]["remarks"] }}</td>
                      </tr>
                      @endif

                      @endfor
                  @endif

                </tbody>
                <thead>
                  <tr>
                      <th class="border"></th>
                      <th class="border"></th>
                      <th class="border"></th>
                      <th class="border text-right pr-2"></th>
                      <th class="border text-right pr-2"></th>
                      @if ($group == 1 || $group == 3)
                        <th class="border text-right pr-2">{{ @App\SysHelper::com_curr_format(($total_dr - $total_cr), 2, '.', '') }}</th>
                      @endif
                      @if ($group == 2 || $group == 4 || $group == 5)
                        <th class="border text-right pr-2">{{ @App\SysHelper::com_curr_format(($total_cr - $total_dr), 2, '.', '') }}</th>
                      @endif
                  </tr>
                </thead>
              </table>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
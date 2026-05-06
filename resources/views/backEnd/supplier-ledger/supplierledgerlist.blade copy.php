@extends('backEnd.master')
@section('mainContent')
    @php
    function showPicName($data){
    $name = explode('/', $data);
    return $name[3];
    }


    @endphp
    <link href="{{ asset('public/css/add_staff.css') }}" type="text/css" rel="stylesheet">

    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
    @endphp
    <link href="{{ asset('public/css/add_staff.css') }}" type="text/css" rel="stylesheet">
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('Supplier Ledger')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('Supplier Ledger')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">                
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="main-title">
                                {{-- <h3 class="mb-0"> @lang('General Ledger') @lang('lang.list')</h3> --}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box">
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplierledger-search', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="row">
                                    <div class="col-lg-4 mb-20">
                                        <div class="input-effect">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('account_name') ? ' is-invalid' : '' }}" name="account_name" id="account_name">
                                                <option data-display="Account *" value="">@lang('Account Name') *</option>
                                                @foreach ($accounts as $val)
                                                    <option value="{{ @$val->id }}" @if(isset($account_name)) @if(@$account_name == @$val->id) selected @endif @endif >{{ @$val->account_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('account_name'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('account_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    @php
                                                    $value = date('m/d/Y');
                                                    if(isset($editData) && !empty($from_date1) ){ @$value = date('m/d/Y', strtotime(@$from_date1)); }
                                                    else{ if(!empty(old('from_date'))){ @$value = old('from_date');}else{@$value = $from_date1; } }
                                                    @endphp
                                                    <input class="primary-input date" id="from_date" type="text" name="from_date" value="{{ @$value }}" autocomplete="off">
                                                    <label>@lang('From Date')</label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('from_date'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('from_date') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="end-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    @php
                                                    $value = date('m/d/Y');
                                                    if(isset($editData) && !empty($to_date1) ){ @$value = date('m/d/Y', strtotime(@$to_date1)); }
                                                    else{ if(!empty(old('to_date'))){ @$value = old('to_date'); }else{ @$value = $to_date1; } }
                                                    @endphp
                                                    <input class="primary-input date" id="to_date" type="text" name="to_date" value="{{ @$value }}" autocomplete="off">
                                                    <label>@lang('To Date')</label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('to_date'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('to_date') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="end-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-20">
                                        <div class="input-effect">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('period') ? ' is-invalid' : '' }}" name="period" id="period">
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
                                    </div>
                                    <div class="col-lg-1 mb-20">
                                        <div class="input-effect" id="sectionSubgroupDiv">
                                            <button class="primary-btn fix-gr-bg">
                                                <span class="ti-search"></span>
                                                @lang('Search')
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <br />



                    <div class="row">

                        <div class="col-lg-12">
                           
                            <table class="white-box sstable display school-table p-2" cellspacing="0" width="100%">
                                
                                <thead>
                                    <tr>
                                        <th> @lang('Doc Date')</th>
                                        <th> @lang('Doc No')</th>
                                        <th> @lang('Particular')</th>
                                        <th> @lang('Type')</th>
                                        <th> @lang('Debit Amount')</th>
                                        <th> @lang('Credit Amount')</th>
                                        <th> @lang('Balance')</th>
                                        <th> @lang('Bill No')</th>
                                        <th> @lang('Bill Date')</th>
                                        <th> @lang('Created By')</th>
                                    </tr>
                                </thead>
                
                                <tbody>
                                    @php
                                       $account_head='';
                                       $str_balance = 0.00;
                                                    $str_d_c;

                                                    $str_total_debit_amount = 0.00;
                                                    $str_total_credit_amount = 0.00;
                                                    $str_total_balance = 0.00;
                                                    
                                                    $str_total_pdc = 0.00;
                                                    $str_total_pdc_debit_amount = 0.00;
                                                    $str_total_pdc_credit_amount = 0.00;
                                                    $str_total_pdc_balance = 0.00;
                                    @endphp
                                    
                                    @if (isset($search_result))
                                        @foreach ($search_result as $value)
                                        
                                        @if($account_head != @$value->account_name)
                                        <tr style="background-color:#808080; color:#fff;">
                                            <td colspan="10"><b>{{ @$value->account_name }}</b></td>
                                        </tr>
                                        
                                        @if (isset($search_result_openning_balance))
                                        <tr style="background-color:#a27240; color:#fff;">
                                            <td colspan="4"><b>Opening Balance</b></td>
                                            <td>
                                                @php
                                                    $a = collect($search_result_openning_balance)->where('account_id', @$value->id)->first();
                                                    

                                                @endphp
                                                @if($a->DebitAmount > $a->CreditAmount) <?php $str_balance = ($a->DebitAmount - $a->CreditAmount); $str_total_debit_amount += $str_balance; $str_d_c = 'Dr'; ?> {{ @App\SysHelper::com_curr_format((float)$str_balance, 2, '.', '') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($a->DebitAmount < $a->CreditAmount) <?php $str_balance = ($a->CreditAmount - $a->DebitAmount); $str_total_credit_amount += $str_balance; $str_d_c = 'Cr'; ?> {{ @App\SysHelper::com_curr_format((float)$str_balance, 2, '.', '') }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ @App\SysHelper::com_curr_format((float)$str_balance, 2, '.', '') }} {{ $str_d_c }}
                                            </td>
                                            <td colspan="3">
                                            </td>
                                        </tr>
                                        @endif
                                            <?php $account_head = @$value->account_name; ?>
                                        @else
                                            <?php $account_head = @$value->account_name; ?>
                                        @endif

                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime(@$value->entry_date)) }}</td>
                                            {{-- @if(str_contains(@$value->bi_doc_no, 'PIV'))
                                            <td><a target="_blank" href="{{ url('purchase-invoice/' . substr(@$value->bi_doc_no, 4)) }}"> {{ @$value->bi_doc_no }}</a></td>
                                            @else
                                            <td><a target="_blank" href="{{ url('sales-invoice/' . substr(@$value->bi_doc_no, 4)) }}"> {{ @$value->bi_doc_no }}</a></td>
                                            @endif --}}
                                            <td>{{ @$value->account_name }}</td>
                                            <td>{{ @$value->transaction_type }}</td>
                                            <td>@if(@$value->DebitAmount > 0) {{ @$value->DebitAmount }} <?php $str_total_debit_amount += @$value->DebitAmount; ?> @endif</td>
                                            <td>@if(@$value->CreditAmount > 0) {{ @$value->CreditAmount }} <?php $str_total_credit_amount += @$value->CreditAmount; ?> @endif</td>
                                            <td>
                                                <?php @$str_balance += $value->DebitAmount; @$str_balance -= $value->CreditAmount;
                                                    $str_total_balance = $str_balance;
                                                    echo str_replace('-','',@App\SysHelper::com_curr_format((float)@$str_balance, 2, '.', ''));
                                                    if (strpos($str_balance, '-') !== false) { echo ' Cr'; }else{ echo ' Dr'; }
                                                ?>
                                            </td>
                                            {{-- <td>{{ @$value->bi_doc_no }}</td> --}}
                                            {{-- <td>{{ date('d-m-Y', strtotime(@$value->bi_doc_date)) }}</td> --}}
                                            <td>{{ @$value->createdby->full_name }}</td>
                                        </tr>
                                        @endforeach

                                        <tr style="background-color:#a27240; color:#fff;">
                                            <td colspan="4"><b>Sub Total</b></td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)$str_total_debit_amount, 2, '.', '') }}</td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)$str_total_credit_amount, 2, '.', '') }}</td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)$str_total_balance, 2, '.', '') }}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <?php $str_total_pdc = $str_total_balance; ?>
                                        <tr>
                                            <td colspan="10"><b>List Of PDC's</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="10"><b>Receipts</b></td>
                                        </tr>
                                        
                                        @if (isset($search_result_pdc))
                                        @foreach ($search_result_pdc as $pdc)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($pdc->entry_date)) }}</td>
                                            <td colspan="2">{{ $pdc->transaction_id }}</td>
                                            <td>{{ $pdc->transaction_type }}</td>
                                            <td>@if($pdc->transaction_type == 'postdatedreceipt') {{ $pdc->amount }} <?php $str_total_pdc += $pdc->amount;?> <?php $str_total_pdc_debit_amount += $pdc->amount;?> @endif</td>
                                            <td>@if($pdc->transaction_type == 'postdatedpayment') {{ $pdc->amount }} <?php $str_total_pdc -= $pdc->amount;?> <?php $str_total_pdc_credit_amount += $pdc->amount;?> @endif</td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)($str_total_pdc), 2, '.', '') }}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        <tr style="background-color:#a27240; color:#fff;">
                                            <td colspan="4"><b>Sub Total Post Dated Receipts</b></td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)($str_total_pdc_debit_amount), 2, '.', '')}}</td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)($str_total_pdc_credit_amount), 2, '.', '')}}</td>
                                            <td>{{ @App\SysHelper::com_curr_format((float)($str_total_pdc_balance), 2, '.', '')}}</td>
                                            <td colspan="3"></td>
                                        </tr>

                                        <tr style="background-color:#a27240; color:#fff;">
                                            <td colspan="4"><b>Sub Total ({{$account_head}})</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td colspan="3"></td>
                                        </tr>

                                        <tr style="background-color:#808080; color:#fff;">
                                            <td colspan="4"><b>Report Total</b></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td colspan="3"></td>
                                        </tr>

                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
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
                <h1>@lang('Trial Balance')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                    <a href="#">@lang('Trial Balance')</a>
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
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'trialbalance-search', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="row">
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

                        <div class="col-lg-6 pr-0">
                            <table class="white-box sstable display school-table p-2" cellspacing="0" width="100%">                                
                                <thead>
                                    <tr>
                                        <th> @lang('Doc Date')</th>
                                        <th> @lang('Doc Nunmber')</th>
                                        <th> @lang('Particular')</th>
                                        <th> @lang('Debit Amount')</th>
                                    </tr>
                                </thead>                
                                <tbody>
                                    @php
                                    $total_dr_amount=0;
                                    $sub_total_dr_amount=0;
                                    @endphp
                                    @if (isset($search_result))                                    
                                        @php
                                            $search_result_d = collect($search_result)->whereIn('id', [1,2,11,15,22])->all();
                                        @endphp
                                        @foreach ($search_result_d as $value)
                                        <tr>
                                            <td><b>{{ @$value->title }}</b></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>{{ $total_dr_amount = abs(@$value->dr_amount-@$value->cr_amount) }}</b></td>
                                            <?php $sub_total_dr_amount += $total_dr_amount; ?>
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr style="background-color:#a27240; color:#fff;">
                                        <td colspan="3"><b>Sub Total</b></td>
                                        <td>{{ @App\SysHelper::com_curr_format((float)$sub_total_dr_amount, 2, '.', '') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 pl-0">
                            <table class="white-box sstable display school-table p-2" cellspacing="0" width="100%">                                
                                <thead>
                                    <tr>
                                        <th> @lang('Doc Date')</th>
                                        <th> @lang('Doc Nunmber')</th>
                                        <th> @lang('Particular')</th>
                                        <th> @lang('Debit Amount')</th>
                                    </tr>
                                </thead>                
                                <tbody>
                                    @php
                                    $total_cr_amount=0;
                                    $sub_total_cr_amount=0;
                                    @endphp                                    
                                    @if (isset($search_result))                                    
                                        @php
                                            $search_result_c = collect($search_result)->whereNotIn('id', [1,2,11,15,22])->all();
                                            //[1,2,11,15,22] - [sundry debtors | fixed assets | loans & advances | purchase account | sales account]
                                        @endphp
                                        @foreach ($search_result_c as $value)
                                        <tr>
                                            <td><b>{{ @$value->title }}</b></td>

                                            
                                            <td></td>
                                            <td></td>
                                            <td><b>{{ $total_cr_amount = abs(@$value->cr_amount-@$value->dr_amount) }}</b></td>
                                            <?php $sub_total_cr_amount += $total_cr_amount; ?>
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr style="background-color:#a27240; color:#fff;">
                                        <td colspan="3"><b>Sub Total</b></td>
                                        <td>{{ @App\SysHelper::com_curr_format((float)$sub_total_cr_amount, 2, '.', '') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'trialbalance-search', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
        <div class="modal fade admin-query" id="trialbalance_search_popup_win">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Trial Balance</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="pdp_account_id">
                        <input type="hidden" id="pdp_account_id_amount">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-6 mb-20">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                @php
                                                $value = date('m/d/Y');
                                                if(isset($editData) && !empty($from_date1) ){ @$value = date('m/d/Y', strtotime(@$from_date1)); }
                                                else{ @$value = date('01/01/Y'); }
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
                                <div class="col-lg-6 mb-20">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                @php
                                                $value = date('m/d/Y');
                                                if(isset($editData) && !empty($to_date1) ){ @$value = date('m/d/Y', strtotime(@$to_date1)); }
                                                else{ @$value = date('m/d/Y'); }
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
                            </div>    
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                        <div class="mt-40 d-flex justify-content-between">
                                            <button class="primary-btn fix-gr-bg">
                                                <span class="ti-search"></span>
                                                @lang('Search')
                                            </button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}

@endsection

@section('script')
<script type="text/javascript">


    var sttr1 = window.location.pathname;
    if (sttr1.indexOf("search") >= 0){

    }else{
    $(window).on('load', function() {
        $('#trialbalance_search_popup_win').modal('show');
    });
}
</script>
@endsection
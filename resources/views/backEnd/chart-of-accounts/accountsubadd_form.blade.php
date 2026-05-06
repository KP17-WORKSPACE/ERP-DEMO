<?php
$com_ids = session('logged_session_data.company_id');
$accounts = @App\SysChartofAccounts::select('id', 'account_name', 'account_code')
    ->whereRaw("find_in_set($com_ids,sys_chartofaccounts.company_access)")
    ->where('main_account_id', 0)
    ->where('account_code', 'like', 'ACC%')
    ->get();
            $com_id = session('logged_session_data.company_id');

$sub_accounts = @App\SysChartofAccounts::whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', '!=', 0)->orderBy('group', 'asc')->orderBy('subgroup', 'asc')->orderBy('subgroup2', 'asc')->get();
            $account_tran = @App\SysChartofAccountsTransaction::select('account_id', 'transaction_date', 'debit_amount', 'credit_amount')->where('company_id', $com_id)->where('transaction_type', 'openingbalance')->get();

?>
<div class="modal modal-draggable  fade" id="accountSubModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mb-0" id="editModalLabel">Sub Account -
                    {{ @App\SysHelper::get_new_sub_account_code() }}</h4>
                <div class="d-flex align-items-center">
                    <a class="btn btn-light btn-sm me-2" style="padding: 2px 5px" href="{{ url('chartofaccounts-import-sub') }}">
                        <i class="ico icon-outline-import text-success"></i> Import
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            {{-- @if (isset($editData))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-sub-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
            @else --}}
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-store-sub', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            {{-- @endif --}}

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="catid" id="catid" value="2">
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">
                        <div class="row">
                            <!-- <div class="col-4 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Sub Account Code') <span>*</span> </label>
                                   
                                </div>
                            </div> -->

                             <input
                                        class="txtbx primary-input form-control {{ $errors->has('account_code') ? 'is-invalid' : ' ' }}"
                                        type="hidden" name="account_code"
                                        value="{{ isset($editData) ? @$editData->account_code : @App\SysHelper::get_new_sub_account_code() }}"
                                        required>

                                 <style>
                                    /* suggestion list initially inside input container */
                                    #account_name_add_list_sub {
                                        position: absolute;
                                        top: 100%;
                                        left: 0;
                                        width: 100%;
                                    }
                                    #account_name_add_list_sub ul {
                                        position: relative;
                                        margin: 0;
                                        padding: 0;
                                        list-style: none;
                                        background: #fff;
                                        border: 1px solid #ddd;
                                        border-top: none;
                                        z-index: 2100; /* higher than modal */
                                        max-height: 400px;
                                        overflow-y: auto;
                                        overflow-x: hidden;
                                        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                                        border-radius: 0 0 6px 6px;
                                    }
                                    #account_name_add_list_sub a {
                                      padding: 5px;
                                    }
                                </style>

                            <div class="col-3 mb-2">

                                <div class="input-effect" style="position: relative;">
                                    <label class="form-label"> @lang('Sub Account Name') <span>*</span> </label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('account_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="account_name_sub" name="account_name"
                                        value="{{ isset($editData) ? @$editData->account_name : old('account_name') }}"
                                        required>
                                    <div id="account_name_add_list_sub">
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            var $list = $('#account_name_add_list_sub');
                                            var $input = $('#account_name_sub');

                                            function positionList() {
                                                var offset = $input.offset();
                                                $list.css({
                                                    top: offset.top + $input.outerHeight(),
                                                    left: offset.left,
                                                    width: $input.outerWidth()
                                                });
                                            }

                                            $input.keyup(function() {
                                                var query = $(this).val();
                                                if (query != '') {
                                                    var _token = $('input[name="_token"]').val();
                                                    $.ajax({
                                                        url: "{{ route('autocomplete.account_name') }}",
                                                        method: "POST",
                                                        data: {
                                                            query: query,
                                                            _token: _token
                                                        },
                                                        success: function(data) {
                                                            $list.html(data);
                                                            $list.appendTo('body');
                                                            positionList();
                                                            $list.fadeIn();
                                                        }
                                                    });
                                                }
                                            });
                                            $(window).on('resize scroll', positionList);

                                            $('#account_name_add_list_sub').on('click', 'li', function() {
                                                $input.val($(this).text());
                                                $list.fadeOut();
                                            });

                                            $(document).on('click', function(e) {
                                                if (!$(e.target).closest('#account_name_sub, #account_name_add_list_sub').length) {
                                                    $list.fadeOut();
                                                }
});
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Select Account') <span>*</span> </label>
                                    <select class="form-control js-example-basic-single" name="main_account_id"
                                        id="main_account_id" required>
                                        <option value=""></option>
                                        @if (isset($accounts))
                                            @foreach ($accounts as $val)
                                                <option value="{{ @$val->id }}"
                                                    @if (isset($editData)) @if (@$editData->main_account_id == @$val->id) selected @endif
                                                    @endif >{{ @$val->account_code }} -
                                                    {{ @$val->account_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                            @php
                                $departments = @App\SmHumanDepartment::all();
                            @endphp

                             
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Department') <span>*</span> </label>
                                    <div class="input-effect" id="sectionDepartmentDiv">
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" 
                                            name="department_id" id="department_id" required>
                                            <option value=""></option>
                                            @if (isset($departments))
                                                @foreach ($departments as $val)
                                                    <option value="{{ @$val->id }}">{{ @$val->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-3 mb-4">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Prepaid/Accrued Exp') <span>*</span> </label>
                                    <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="credit_account_status" id="credit_account_status" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            

                             <!-- toggle for opening balance inputs (sub-account modal) -->
                            <div class="col-4 mb-2 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Opening Balance"
                            data-bs-placement="top" type="checkbox" id="toggleOpeningBalanceSubAdd">
                                    
                                </div>
                            </div>


                            <div class="col-4 mb-2 opening-balance">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Opening Balance Dr') <span>*</span> </label>
                                    @php
                                        $debit_amount = '0.00';
                                        if (isset($editData_tran)) {
                                            $debit_amount = $editData_tran->debit_amount;
                                        }
                                    @endphp
                                    <input
                                        class="primary-input form-control {{ $errors->has('debit_amount') ? 'is-invalid' : ' ' }}"
                                        type="text" id="debie_amount_sub" name="debit_amount" value="{{ $debit_amount }}" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="col-4 mb-2 opening-balance">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Opening Balance Cr') <span>*</span> </label>
                                    @php
                                        $credit_amount = '0.00';
                                        if (isset($editData_tran)) {
                                            $credit_amount = $editData_tran->credit_amount;
                                        }
                                    @endphp
                                    <input
                                        class="primary-input form-control {{ $errors->has('credit_amount') ? 'is-invalid' : ' ' }}"
                                        type="text" id="credit_amount_sub" name="credit_amount" value="{{ $credit_amount }}" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                                    <script>
    // apply comma formatting on blur (when focus leaves input)
    $(document).ready(function() {


        $('#debie_amount_sub, #credit_amount_sub').on('blur', function() {
            var formatted = formatAmount($(this).val());
            $(this).val(formatted);
        });

    });
</script>

                            <div class="col-4 mb-2 opening-balance">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Date') <span>*</span> </label>
                                    @php
                                        $value = session('opening_balance_date_sub');

                                        if (is_null($value)) {
                                            $value = Carbon\Carbon::now()->format('d/m/Y'); // today's date in d/m/Y
}

if (isset($editData_tran) && !empty($editData_tran->transaction_date)) {
    $value = Carbon\Carbon::parse($editData_tran->transaction_date)->format(
        'd/m/Y',
                                            );
                                        }else{
                                                $value = @App\SysHelper::normalizeToDmy(App\SysCompany::select('opening_balance_date')->where('id', session('logged_session_data.company_id'))->first()->opening_balance_date);
                                        }
                                    @endphp
                                    <input
                                        class="primary-input form-control date-picker {{ $errors->has('opening_balance_date') ? 'is-invalid' : ' ' }}"
                                        type="text" name="opening_balance_date" value="{{ $value }}"
                                        required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>






                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if (isset($editData))
                    <button class="btn btn-light" id="btnSubAccountSubmit" name="btnSubmit" value="update">
                        <i class="ico icon-outline-bookmark-opened  text-success"></i> Save
                    </button>
                @else
                    <button class="btn btn-light" id="btnSubAccountSubmit">
                        <i class="ico  icon-outline-bookmark-opened  text-success"></i> Save
                    </button>
                @endif

            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>




<div class="modal modal-draggable fade" id="SubAccountTableModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <style>
    #table-head th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
  
    </style>
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width:1300px;width:1300px;left:90px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editGroupModalLabel" style=" padding-left: 11px;">Sub Accounts</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card m-0 p-0">
                    <div class="card-body bg-white m-0 p-0">

                        <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                            <thead id="table-head">
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
                                    <th class="text-center" style="padding-left: 14px"  style="width:250px">@lang('Sub Account Code')</th>
                                    <th style="width:300px"> @lang('Sub Account Name')</th>
                                    <th style="width:150px">@lang('Account Name')</th>
                                    <th>@lang('Main Heads')</th>
                                    <th>@lang('Group')</th>
                                    <th>@lang('Sub Group')</th>
                                    <th class="text-end">@lang('Opening Dr')</th>
                                    <th class="text-end">@lang('Opening Cr')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center" style="width:150px">@lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (isset($sub_accounts))
                                    @foreach ($sub_accounts as $value)
                                        <tr @if ($value->status == 2) class="bg-dark" @endif>
                                            <td class="text-center" style="padding-left: 14px">
                                                {{ @$value->account_code }}
                                            </td>
                                            <td>
                                                {{ @$value->account_name }}
                                            </td>
                                            <td>
                                                <span
                                                    class="">{{ @$value->mainaccount->account_name }}</span>
                                            </td>
                                            <td>
                                                {{ @$value->groupname->title }}
                                            </td>
                                            <td>
                                                {{ @$value->subgroupname->title }}
                                            </td>
                                            <td>
                                                {{ @$value->subgroup2name->title }}
                                            </td>

                                            <?php $tran_amt = $account_tran->where('account_id', $value->id)->first(); ?>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$tran_amt->debit_amount, '', '', ',') ?? '0.00' }}
                                            </td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format(@$tran_amt->credit_amount, '', '', ',') ?? '0.00' }}

                                            </td>

                                            <td class="text-center">
                                                @if (@$value->status == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-dark">Deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center align-items-center">

                                                    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                                        <a class="btn-sm btn btn-light editSubAccountBtn2"
                                                        data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Sub Account"
                            data-bs-placement="top"
                                                            data-id="{{ $value->id }}" {{-- href="{{ url('chartofaccounts-sub/' . @$value->id . '/edit') }}" --}}><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-pen-2"></i></a>



                                                        @if (@$value->status == 2)
                                                            <a class="btn-sm btn btn-light"
                                                             data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Restore Sub Account"
                            data-bs-placement="top"
                                                                href="{{ url('chartofaccounts-sub/' . $value->id . '/restore') }}"
                                                                onclick="return confirm('Are you sure you want to restore this item?');"><i
                                                                    class="ico icon-bold-restart text-dark"
                                                                    style="font-size: 16px;"></i></a>
                                                        @else
                                                            <a class="btn-sm btn btn-light"
                                                             data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Sub Account"
                            data-bs-placement="top"
                                                                href="{{ url('chartofaccounts-sub/' . $value->id . '/delete') }}"
                                                                onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                                    style="font-size: 16px"
                                                                    class="ico icon-outline-trash-bin-minimalistic"></i></a>
                                                        @endif

                                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                                            <a class="btn-sm btn btn-light moveToSubGroupBtn"
                                                             data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Move to Main Account"
                            data-bs-placement="top" data-id="{{ $value->id }}" data-name="{{ $value->account_name }}"
                                                                >Move</a>
                                                        @endif
                                                    @endif
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                @if($sub_accounts->count() < 10)

                                <tr>
                                    <td style="height:200px"></td>
                                </tr>

                                @endif



                            </tbody>
                        </table>

                    </div>
                </div>
            </div>



        </div>
    </div>
</div>



    <!-- Edit Sub Account Modal -->
    <div class="modal modal-draggable side-panel fade" id="editSubAccountModal2" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel2">Edit Sub Account - <span
                            id="edit_account_codesups2"></span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0 bg-white">
                    <div class="card mb-0 mt-0 bg-white">
                        <div class="card-body bg-white">

                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-sub-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'editSubAccountForm2']) }}

                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="catid" id="catid" value="2">

                            <div class="row">
                                

                                      <input class="txtbx primary-input form-control" type="hidden"
                                            id="edit_account_code" name="account_code" value="" required>

                                <div class="col-3 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Sub Account Name') <span>*</span> </label>
                                        <input class="txtbx primary-input form-control" type="text"
                                            id="edit_account_name" name="account_name" value="" required>
                                        <div id="edit_account_name_add_list"></div>
                                    </div>
                                </div>
                                <div class="col-3 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Main Account') <span>*</span> </label>
                                        <select class="form-control js-example-basic-single" name="main_account_id"
                                            id="edit_main_account_id" required>
                                            <option value=""></option>
                                            @if (isset($accounts))
                                                @foreach ($accounts as $val)
                                                    <option value="{{ $val->id }}">{{ $val->account_code }} -
                                                        {{ $val->account_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-3 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Department') <span>*</span> </label>
                                        <div class="input-effect" id="sectionDepartmentDiv">
                                            <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="department_id" id="edit_department_id" required>
                                                <option value="0"></option>
                                                @if (isset($departments))
                                                    @foreach ($departments as $val)
                                                        <option value="{{ @$val->id }}">{{ @$val->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Prepaid/Accrued Exp') <span>*</span> </label>
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="credit_account_status" id="edit_credit_account_status" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                              

                                <!-- toggle for opening balance inputs (edit-sub modal) -->
                                <div class="col-4 mb-2 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="toggleOpeningBalanceSubEdit2">
                                    </div>
                                </div>

                                <div class="col-4 mb-4 opening-balance">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Opening Balance Dr') <span>*</span> </label>
                                        <input class="primary-input form-control" type="text" id="edit_debit_amount"
                                            name="debit_amount" value="0.00" required>
                                    </div>
                                </div>

                                <div class="col-4 mb-4 opening-balance">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Opening Balance Cr') <span>*</span> </label>
                                        <input class="primary-input form-control" type="text" id="edit_credit_amount"
                                            name="credit_amount" value="0.00" required>
                                    </div>
                                </div>

                                       <script>
    // apply comma formatting on blur (when focus leaves input)
    $(document).ready(function() {

     

        $('#edit_debit_amount, #edit_credit_amount').on('blur', function() {
            var formatted = formatAmount($(this).val());
            $(this).val(formatted);
        });

    });
</script>

                                <div class="col-4 mb-4 opening-balance">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Date') <span>*</span> </label>
                                        <input class="primary-input form-control date-picker" type="text"
                                            id="edit_opening_balance_date" name="opening_balance_date" value=""
                                            required>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="submit" id="btnSubmit" name="btnSubmit" value="update">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>


<script>
    $(document).ready(function() {

        // Autocomplete for Sub Account Name
        $('#editSubAccountModal2 #edit_account_name').keyup(function() {
            var query = $(this).val();
            if (query != '') {
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{ route('autocomplete.account_name') }}",
                    method: "POST",
                    data: {
                        query: query,
                        _token: _token
                    },
                    success: function(data) {
                        $('#editSubAccountModal2 #edit_account_name_add_list').fadeIn().html(data);
                    }
                });
            }
        });

        $('#editSubAccountModal2 #edit_account_name_add_list').on('click', 'li', function() {
            $('#editSubAccountModal2 #edit_account_name').val($(this).text());
            $('#editSubAccountModal2 #edit_account_name_add_list').fadeOut();
        });

        // Open modal and fill data
        $('.editSubAccountBtn2').on('click', function() {
            let subaccountId = $(this).data('id');
            console.log("Account ID:", subaccountId);
            $("#loading_bg").show();

            $.ajax({
                url: '/chartofaccounts-sub/' + subaccountId + '/get-edit',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        alert('Error: ' + response.message);
                        $("#loading_bg").hide();
                        return;
                    }

                    let editData = response.editData;
                    let editData_tran = response.editData_tran;

                    // show code in header span
                    $('#editSubAccountModal2 #edit_account_codesups2').text(editData.account_code);
                    $('#editSubAccountModal2 #edit_account_code').val(editData.account_code);
                    // also update modal title attribute just in case
                    $('#editSubAccountModal2 .modal-title').text('Edit Sub Account - ' + editData.account_code);
                    $('#editSubAccountModal2 #edit_account_name').val(editData.account_name);
                    $('#editSubAccountModal2 #edit_main_account_id').val(editData.main_account_id).trigger(
                        'change');
                    $('#editSubAccountModal2 #edit_department_id').val(editData.department_id).trigger('change');
                    $('#editSubAccountModal2 #edit_credit_account_status').val(editData.yes_no).trigger('change');
                    if (editData_tran) {
                        $('#editSubAccountModal2 #edit_debit_amount').val(formatAmount(editData_tran.debit_amount));
                        $('#editSubAccountModal2 #edit_credit_amount').val(formatAmount(editData_tran.credit_amount));
                        $('#editSubAccountModal2 #edit_opening_balance_date').val(editData_tran.transaction_date ?
                            editData_tran.transaction_date.split('-').reverse().join(
                                '/') : '');
                    } else {
                        $('#editSubAccountModal2 #edit_debit_amount').val('0.00');
                        $('#editSubAccountModal2 #edit_credit_amount').val('0.00');
                        $('#editSubAccountModal2 #edit_opening_balance_date').val('');
                    }

                    $('#editSubAccountForm2').attr('action', '/chartofaccounts-sub-update/' +
                        editData.id);

                    $("#loading_bg").hide();
                    $('#editSubAccountModal2').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('An error occurred while fetching data. Please try again later.');
                    $("#loading_bg").hide();
                }
            });
        });

    });

    $(document).ready(function(){
    ctLoadFromContainer($('#accountSubModal')); // initialize add modal status
   
    // ensure opening balance fields start hidden (only inside this modal)
    $('#accountSubModal .opening-balance').hide();

    // opening balance toggle logic scoped to sub modal
    // reset when show
    $('#accountSubModal').on('show.bs.modal', function() {
        $(this).find('.opening-balance').hide();
        $(this).find('#toggleOpeningBalanceSubAdd').prop('checked', false);
    });

    $('#toggleOpeningBalanceSubAdd').change(function() {
        if (this.checked) {
            $('#accountSubModal .opening-balance').show();
        } else {
            $('#accountSubModal .opening-balance').hide();
        }
    });

    // edit modal toggle behaviour
    $('#editSubAccountModal2').on('show.bs.modal', function() {
        $(this).find('.opening-balance').hide();
        $(this).find('#toggleOpeningBalanceSubEdit2').prop('checked', false);
    });
    $('#toggleOpeningBalanceSubEdit2').change(function() {
        if (this.checked) {
            $('#editSubAccountModal2 .opening-balance').show();
        } else {
            $('#editSubAccountModal2 .opening-balance').hide();
        }
    });
    
  });



   
  
</script>


<script>
        $(document).ready(function () {
            $('.moveToSubGroupBtn').on('click', function () {
                var accountId = $(this).data('id');
                var accountName = $(this).data('name');
                $('#move_account_id_subgroup').val(accountId);
                $('#move_account_name').text(accountName);
                $('#move_account_header').text(accountName);
                $('#move_account_id_header').text(accountId);
                $('#moveToSubGroup').modal('show');
            });
        });
    </script>

          <div class="modal modal-draggable side-panel fade" id="moveToSubGroup" data-bs-backdrop="false"
                                            tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="moveToSubGroupLabel">
                                                            Move <strong><span id="move_account_header"></span></strong>
                                                           to Account
                                                        </h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>

                                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-sub-move', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="input-effect">
                                                                    <input type="hidden" id="move_account_id_subgroup"
                                                                        name="move_account_id_subgroup" value="" />
                                                                           @php
    $accountgroupsub = @App\SysAccountGroupSub2::where('status', 1)->orderBy('group_id')->get();
                            @endphp


                                                                    <label 
                                                                        class="font-weight-bold">Sub Group</label> 

                                                                    <select id="group_account" name="group_account" class="form-control js-example-basic-single"
                                     required>

                                        @foreach ($accountgroupsub as $data)
                                        <option value="{{ $data->id }}">{{ $data->title }}</option>
                                    @endforeach

                                </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button  class="btn btn-light add-btn ms-2"
                                                            type="submit">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            Move
                                                        </button>
                                                    </div>
                                                    {{ Form::close() }}

                                                </div>
                                            </div>
                                        </div>

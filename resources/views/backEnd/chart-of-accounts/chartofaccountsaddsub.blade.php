@extends('backEnd.newmasterpage')
@section('mainContent')
        @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        @endphp



        <div class="content-container col-12">

       <div class="smart_search_wrapper">
                    <div id="smart_search_list"></div>
                </div>

            <h4 style="position: fixed; margin-top: 7px;">Sub Accounts</h4>
              <div class="purchase-order-content-header-right" style="margin-top:-14px">
              <input type="text" class="form-control w-25 rounded" id="tableSearch"
                    placeholder="Search..." />

                <!-- <input type="text" class="form-control w-25 rounded" id="smart_search" name="smart_search"
                    placeholder="Search..." />
                <div id="smart_search_list"></div>
                <script>
                    $(document).ready(function() {

                        $("#smart_search").on("keyup", function() {
                            let query = $(this).val().trim();

                            if (query.length > 3) {
                                $.ajax({
                                    url: "{{ route('chartofaccounts.search') }}",
                                    method: "GET",
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        $("#smart_search_list").html(data).show();
                                    }
                                });
                            } else {
                                $("#smart_search_list").hide();
                            }
                        });

                        function checkSearchInput() {
                            let query = $("#smart_search").val().trim();
                            if (query.length < 3) {
                                $("#smart_search_list").hide();
                            }
                        }
                        setInterval(checkSearchInput, 1000);

                        $(document).on("click", function(e) {
                            if (!$(e.target).closest("#smart_search, #smart_search_list").length) {
                                $("#smart_search_list").hide();
                            }
                        });

                    });
                </script>
                <style>
                    .smart_search_wrapper {
                        position: relative;
                        display: block;
                        width: 100%;
                    }
                    #smart_search_list {
                        display: none;
                        position: absolute;
                        top: 100%;
                        left: 40px;
                        right: 0;
                        width: 95%;
                        max-height: 350px;
                        overflow-y: auto;
                        background: #fff;
                        border: 1px solid #ccc;
                        border-radius: 8px;
                        z-index: 9999;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                        margin-top: 51px;
                    }
                </style>
                <script>
                    $(document).ready(function() {
                        $("#smart_search").on("keyup", function() {
                            let value = $(this).val().trim();

                            if (value.length >= 2) {
                                $("#smart_search_list").show();
                            } else {
                                $("#smart_search_list").hide();
                            }
                        });
                    });
                </script> -->


                {{-- <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#addGroupModal"
                                aria-expanded="false">
                                <i class="ico icon-outline-add-square"></i> Add
                            </button> --}}

                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-add-square text-success"></i> Add
                    </button>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#groupModal"><i
                                    class="ico title-15 icon-outline-add-square me-2 text-success"></i> Group</a>
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#subgroupModal"><i
                                    class="ico title-15 icon-outline-add-square me-2 text-success"></i> Sub
                                Group</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#accountModal"><i
                                    class="ico title-15 icon-outline-add-square me-2 text-success"></i> Account</a>
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#accountSubModal"><i
                                    class="ico title-15 icon-outline-add-square me-2 text-success"></i> Sub
                                Account</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                data-bs-target="#accountSubEmployeeModal"><i
                                    class="ico title-15 icon-outline-add-square me-2 text-success"></i> Employee
                                Account</a>
                        </li>
                    </ul>
                </div>

                @include('backEnd.accounts.accountgroupsubadd_form')
                @include('backEnd.accounts.accountgroupsub2add_form')
                @include('backEnd.chart-of-accounts.accountadd_form')
                @include('backEnd.chart-of-accounts.accountsubadd_form')
                @include('backEnd.chart-of-accounts.accountsubemployeeadd_form')



                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-document-text text-success"></i> List
                    </button>
                    <ul class="dropdown-menu">

                        <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts') }}"><i
                                    class="ico icon-outline-document-text title-15 me-2"></i> Chart of Accounts</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="{{ url('accountgroupsub-add') }}"><i
                                    class="ico icon-outline-document-text title-15 me-2"></i> Group</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="{{ url('accountgroupsub2-add') }}"><i
                                    class="ico icon-outline-document-text title-15 me-2"></i> Sub Group</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts-add') }}"><i
                                    class="ico icon-outline-document-text title-15 me-2"></i> Account</a></li>

                        <li><a class="dropdown-item d-flex align-items-center"
                                href="{{ url('chartofaccounts-opening-balance') }}"><i
                                    class="ico icon-outline-document-text title-15 me-2"></i> Opening Balance</a>
                        </li>



                    </ul>
                </div>

                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                    @include('backEnd.chart-of-accounts.accountmerge_form')
                    @include('backEnd.chart-of-accounts.accountsubmerge_form')
                @endif

                <div class="dropdown" id="custom-dropdown">
                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ico icon-outline-hamburger-menu"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">


                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#collapseMerge" data-bs-toggle="collapse"
                                aria-expanded="false" onclick="event.stopPropagation();">
                                <span class="text-muted"><i class="ico icon-outline-link-square title-15 me-2"></i> Merge</span>

                            </a>
                        </li>
                        <li>
                            <div class="collapse" id="collapseMerge">
                                <ul class="list-unstyled  mb-0">
                                    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                                data-bs-target="#ModalMergeAccount" onclick="event.stopPropagation();"><i
                                                    class="ico icon-outline-link-square title-15 me-2"></i> Account Merge</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                                                data-bs-target="#ModalMergeSubAccount" onclick="event.stopPropagation();"><i
                                                    class="ico icon-outline-link-square title-15 me-2"></i> Sub Account
                                                Merge</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#collapseMove" data-bs-toggle="collapse"
                                aria-expanded="false" onclick="event.stopPropagation();">
                                <span class="text-muted"><i class="ico icon-outline-move-to-folder title-15 me-2"></i>
                                    Move</span>

                            </a>
                        </li>
                        <li>
                          <div class="collapse" id="collapseMove">
                            <ul class="list-unstyled  mb-0">
                                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                    <li><a class="dropdown-item d-flex align-items-center"
                                            href="#" data-bs-toggle="modal"
                                            data-bs-target="#ModalMoveAccount" onclick="event.stopPropagation();"><i
                                                class="ico icon-outline-move-to-folder title-15 me-2"></i> Account Move</a>
                                    </li>
                                    <li><a class="dropdown-item d-flex align-items-center"
                                            href="#"  data-bs-toggle="modal"
                                            data-bs-target="#ModalMoveSubAccount" onclick="event.stopPropagation();"><i
                                                class="ico icon-outline-move-to-folder title-15 me-2"></i> Sub Account
                                            Move</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        </li>





                    </ul>
                </div>
                <style>
                    /* Increase width of all dropdown menus */
                    #custom-dropdown .dropdown-menu {
                        min-width: 180px;
                        /* default minimum width */
                        width: auto;
                        /* adjust width automatically based on content */
                        max-width: 400px;
                        /* optional maximum width */
                    }

                    /* Optional: prevent text from wrapping */
                    #custom-dropdown .dropdown-item {
                        white-space: nowrap;
                    }
                </style>

            </div>


  @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                @include('backEnd.chart-of-accounts.account_move_form')
                @include('backEnd.chart-of-accounts.subaccount_move_form')

        @endif

            <div class="card mb-3">
                <div class="card-body p-0">
                    <table class="table table-hover bordered-table table-fixed-header data-table" id="long-list" style="table-layout: fixed;width:100%">
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
                                <th class="text-center">@lang('Code')</th>

                                <th>@lang('Main Heads')</th>
                                <th>@lang('Group')</th>
                                <th>@lang('Sub Group')</th>
                                <th style="width:250px">@lang('Account Name')</th>
                                  <th> @lang('Sub Account Name')</th>

                                <th class="text-end">@lang('Opening Dr')</th>
                                <th class="text-end">@lang('Opening Cr')</th>
                                <th class="text-center">@lang('Date')</th>
                                <th class="text-center">@lang('Status')</th>
                                <th class="text-center">@lang('lang.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if (isset($sub_accounts))
                                @foreach ($sub_accounts as $value)
                                    <tr @if ($value->status == 2) style="background-color: #c3c3c3;" @endif>
                                        <td class="text-center">
                                            {{ @$value->account_code }}
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

                                        <td>
                                            <span class="">{{ @$value->mainaccount->account_name }}</span>
                                        </td>
                                           <td>
                                            {{ @$value->account_name }}
                                        </td>


                                        <?php        $tran_amt = $account_tran->where('account_id', $value->id)->first(); ?>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$tran_amt->debit_amount, '', '', ',') ?? '0.00' }}
                                        </td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$tran_amt->credit_amount, '', '', ',') ?? '0.00' }}

                                        </td>

                                        <td class="text-center">
                                            @php
            $editData_tran = @App\SysChartofAccountsTransaction::select('transaction_date', 'debit_amount', 'credit_amount')->where('account_id', $value->id)->where('company_id', session('logged_session_data.company_id'))->where('transaction_type', 'openingbalance')->first();

                                            @endphp
                                            {{ @$editData_tran->transaction_date != '' ? @App\SysHelper::normalizeToDmy(@$editData_tran->transaction_date) : '' }}  
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
                                                    <a class="btn-sm btn btn-light editSubAccountBtn"
                                                        data-id="{{ $value->id }}" {{-- href="{{ url('chartofaccounts-sub/' . @$value->id . '/edit') }}" --}}><i
                                                            style="font-size: 16px" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Sub Account"
                            data-bs-placement="top" class="ico icon-outline-pen-2"></i></a>



                                                    @if (@$value->status == 2)
                                                        <a class="btn-sm btn btn-light"
                                                            href="{{ url('chartofaccounts-sub/' . $value->id . '/restore') }}"
                                                            onclick="return confirm('Are you sure you want to restore this item?');"><i
                                                                class="ico icon-bold-restart text-dark" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Restore Sub Account"
                            data-bs-placement="top"
                                                                style="font-size: 16px;"></i></a>
                                                    @else
                                                        <a class="btn-sm btn btn-light"
                                                            href="{{ url('chartofaccounts-sub/' . $value->id . '/delete') }}"
                                                            onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                                style="font-size: 16px"
                                                                class="ico icon-outline-trash-bin-minimalistic" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Sub Account"
                            data-bs-placement="top"></i></a>
                                                    @endif

                                                    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                                        <a class="btn-sm btn btn-light moveToSubGroupBtn" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Move To Account"
                            data-bs-placement="top"  style="font-size:12px" data-id="{{ $value->id }}" data-name="{{ $value->account_name }}" href="javascript:void(0)">Move</a>
                                                    @endif
                                                @endif
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif



                        </tbody>
                    </table>
                </div>
            </div>




        </div>

        <div class="modal modal-draggable fade" id="ModalAddEmployeeSubAccount" data-backdrop="static" data-keyboard="false"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Employee Sub Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    {!! Form::open([
        'class' => 'form-horizontal',
        'files' => true,
        'url' => 'chartofaccounts-employee-sub-store',
        'method' => 'post',
        'id' => 'chartofaccounts-employee-sub-store',
    ]) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">Employee Name
                                <input type="text" id="employee_name" name="employee_name" class="form-control" required
                                    onchange="set_acc_name()" />
                            </div>
                            <div class="col-md-6">Accounts To Create
                                <div class="form-control" style="height: auto;">
                                    <input type="checkbox" id="account_id_emp1" name="account_id_emp[]"
                                        value="employee_telephone_expenses" checked>
                                    <label for="account_id_emp1"><span id="accname_1"></span> Telephone Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp2" name="account_id_emp[]"
                                        value="employee_airfare_expenses" checked>
                                    <label for="account_id_emp2"><span id="accname_2"></span> Airfare Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp3" name="account_id_emp[]"
                                        value="employee_food_expenses" checked>
                                    <label for="account_id_emp3"><span id="accname_3"></span> Food Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp4" name="account_id_emp[]"
                                        value="employee_salary" checked>
                                    <label for="account_id_emp4"><span id="accname_4"></span> Salary</label><br>
                                    <input type="checkbox" id="account_id_emp5" name="account_id_emp[]"
                                        value="employee_gratuity" checked>
                                    <label for="account_id_emp5"><span id="accname_5"></span> Gratuity</label><br>
                                    <input type="checkbox" id="account_id_emp6" name="account_id_emp[]"
                                        value="employee_visa_expenses" checked>
                                    <label for="account_id_emp6"><span id="accname_6"></span> Visa Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp7" name="account_id_emp[]"
                                        value="employee_travelling_expenses" checked>
                                    <label for="account_id_emp7"><span id="accname_7"></span> Travelling Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp8" name="account_id_emp[]"
                                        value="employee_parking_expenses" checked>
                                    <label for="account_id_emp8"><span id="accname_8"></span> Parking Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp9" name="account_id_emp[]"
                                        value="employee_petrol_expenses" checked>
                                    <label for="account_id_emp9"><span id="accname_9"></span> Petrol Expenses</label><br>
                                    <input type="checkbox" id="account_id_emp10" name="account_id_emp[]"
                                        value="employee_vehicle_maintenance" checked>
                                    <label for="account_id_emp10"><span id="accname_10"></span> Vehicle Maintenance</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Are you sure you want to Create this Accounts?');">Create
                            Accounts</button>
                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>


        <div class="modal modal-draggable side-panel fade" id="addSubAccountModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">Add Sub Account -
                            {{ @App\SysHelper::get_new_sub_account_code() }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-store-sub', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}


                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                <input type="hidden" name="catid" id="catid" value="2">

                                <div class="row">
                                    <div class="col-4 mb-4">
                                        <div class="input-effect">
                                            <label class="form-label"> @lang('Account Code') <span>*</span> </label>
                                            <input
                                                class="txtbx primary-input form-control {{ $errors->has('account_code') ? 'is-invalid' : ' ' }}"
                                                type="text" name="account_code"
                                                value="{{ isset($editData) ? @$editData->account_code : @App\SysHelper::get_new_sub_account_code() }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <div class="input-effect">
                                            <label class="form-label"> @lang('Sub Account Name') <span>*</span> </label>
                                            <input
                                                class="txtbx primary-input form-control {{ $errors->has('account_name') ? 'is-invalid' : ' ' }}"
                                                type="text" id="account_name" name="account_name"
                                                value="{{ isset($editData) ? @$editData->account_name : old('account_name') }}"
                                                required>
                                            <div id="account_name_add_list">
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    $('#account_name').keyup(function() {
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
                                                                    $('#account_name_add_list').fadeIn();
                                                                    $('#account_name_add_list').html(data);
                                                                }
                                                            });
                                                        }
                                                    });
                                                    $('#account_name_add_list').on('click', 'li', function() {
                                                        $('#account_name').val($(this).text());
                                                        $('#account_name_add_list').fadeOut();
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <div class="input-effect">
                                            <label class="form-label"> @lang('Main Account') <span>*</span> </label>
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

                                    <div class="col-4 mb-4">
                                        <div class="input-effect">
                                            <label class="form-label"> @lang('Prepaid/Accrued Exp') <span>*</span> </label>
                                            <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="credit_account_status" id="credit_account_status" required>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-4">
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
                                                type="text" name="debit_amount" value="{{ $debit_amount }}" required>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>

                                    <div class="col-4 mb-4">
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
                                                type="text" name="credit_amount" value="{{ $credit_amount }}" required>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>

                                    <div class="col-4 mb-4">
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

                        <button class="btn btn-light" id="btnSubmit">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                        </button>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>



        <!-- Edit Sub Account Modal -->
        <div class="modal modal-draggable side-panel fade" id="editSubAccountModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">Edit Sub Account - <span
                                id="edit_account_codesups"></span></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-sub-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'editSubAccountForm']) }}

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

                                    @php
                                        $departments = @App\SmHumanDepartment::all();
                                    @endphp
                                    <div class="col-3 mb-4">
                                        <div class="input-effect">
                                            <label class="form-label"> @lang('Department') <span>*</span> </label>
                                            <div class="input-effect" id="sectionDepartmentDiv">
                                                <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" 
                                                    name="department_id" id="edit_department_id" required>
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

                                      <!-- toggle for opening balance inputs (edit sub-account modal) -->
                            <div class="col-4 mb-2 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="toggleOpeningBalanceSubEdit">
                                    
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



        <script>
            function set_acc_name() {
                var ename = $('#employee_name').val();
                $('#accname_1').text(ename);
                $('#accname_2').text(ename);
                $('#accname_3').text(ename);
                $('#accname_4').text(ename);
                $('#accname_5').text(ename);
                $('#accname_6').text(ename);
                $('#accname_7').text(ename);
                $('#accname_8').text(ename);
                $('#accname_9').text(ename);
                $('#accname_10').text(ename);
            }

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


        <script>
            $(document).ready(function() {

                // Autocomplete for Sub Account Name
                $('#edit_account_name').keyup(function() {
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
                                $('#edit_account_name_add_list').fadeIn().html(data);
                            }
                        });
                    }
                });

                $('#edit_account_name_add_list').on('click', 'li', function() {
                    $('#edit_account_name').val($(this).text());
                    $('#edit_account_name_add_list').fadeOut();
                });

                // Open modal and fill data
                $('.editSubAccountBtn').on('click', function() {
                    let subaccountId = $(this).data('id');
                    console.log("Account ID:", subaccountId);
                    $("#loading_bg").show();

                    $.ajax({
                        url: '/chartofaccounts-sub/' + subaccountId + '/get-edit',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log("AJAX Response:", response);
                            if (response.error) {
                                alert('Error: ' + response.message);
                                $("#loading_bg").hide();
                                return;
                            }

                            let editData = response.editData;
                            let editData_tran = response.editData_tran;

                            console.log("Edit Data:", editData.account_code);

                            // Scope to modal to avoid clobbering elements with duplicate IDs elsewhere
                            const $modal = $('#editSubAccountModal');

                            $modal.find('#edit_account_codesups').text(editData.account_code);
                            $modal.find('#edit_account_code').val(editData.account_code).prop('value', editData.account_code);
                            $modal.find('#edit_account_name').val(editData.account_name);
                            $modal.find('#edit_main_account_id').val(editData.main_account_id).trigger('change');
                            $modal.find('#edit_department_id').val(editData.department_id).trigger('change');
                            $modal.find('#edit_credit_account_status').val(editData.yes_no).trigger('change');
                            if (editData_tran) {
                                $modal.find('#edit_debit_amount').val(formatAmount(editData_tran.debit_amount));
                                $modal.find('#edit_credit_amount').val(formatAmount(editData_tran.credit_amount));
                                $modal.find('#edit_opening_balance_date').val(editData_tran.transaction_date ? editData_tran.transaction_date.split('-').reverse().join('/') : '');
                            } else {
                                $modal.find('#edit_debit_amount').val('0.00');
                                $modal.find('#edit_credit_amount').val('0.00');
                                $modal.find('#edit_opening_balance_date').val('');
                            }

                            $modal.find('#editSubAccountForm').attr('action', '/chartofaccounts-sub-update/' + editData.id);

                            $("#loading_bg").hide();
                            $modal.modal('show');
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
        </script>
     <script>
            $(document).ready(function() {
                function setManualWidths() {
                    var $table = $('.table-fixed-header');
                    var $theadTh = $table.find('thead th');
                    var columnWidths = [100, 100, 100, 100, 200, 100, 100, 100, 100, 100, 150]; // 👈 define widths here in px

                    $theadTh.each(function(i) {
                        var w = columnWidths[i];
                        $(this).css('width', w + 'px');
                        $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
                    });
                }

                setManualWidths();
                $(window).on('resize', setManualWidths);
            });

              $(document).ready(function(){
    ctLoadFromContainer($('#editSubAccountModal')); // initialize add modal status
   
    // ensure opening balance fields start hidden (only inside this modal)
    // hide fields when modal shown and reset checkbox
    $('#editSubAccountModal').on('show.bs.modal', function() {
        $(this).find('.opening-balance').hide();
        $(this).find('#toggleOpeningBalanceSubEdit').prop('checked', false);
    });

    // opening balance toggle logic scoped to sub modal
    $('#toggleOpeningBalanceSubEdit').change(function() {
        if (this.checked) {
            $('#editSubAccountModal .opening-balance').show();
        } else {
            $('#editSubAccountModal .opening-balance').hide();
        }
    });
    
  });
        </script>


@endsection

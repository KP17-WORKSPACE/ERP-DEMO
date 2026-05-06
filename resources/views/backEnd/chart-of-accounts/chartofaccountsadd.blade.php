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

        <h4 style="position: fixed; margin-top: 7px;">Accounts</h4>
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
                  
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts-add-sub') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Account</a></li>
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
                <div class="row">
                    <div class="col-lg-12 mb-4">

                        <div class="table-responsive">
                            <table id="long-list" class="table table-hover bordered-table table-fixed-header data-table" style="table-layout: fixed;width:100%">

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
                                        <th class="text-center"> @lang('Code')</th>
                                        <th > @lang('Main Heads')</th>
                                        <th> @lang('Group')</th>
                                        <th> @lang('Sub Group')</th>
                                        <th style="width:150px"> @lang('Account Name')</th>
                                        <th class="text-end"> @lang('Opening Dr')</th>
                                        <th class="text-end"> @lang('Opening Cr')</th>
                                        <th class="text-center"> @lang('Date')</th>
                                        <th class="text-center"> @lang('Status')</th>
                                        <th style="width: 200px;" class="text-center"> @lang('lang.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if (isset($accounts))
                                        @foreach ($accounts as $value)
                                            <tr @if ($value->status == 2) class="bg-dark" @endif>
                                                <td class="text-center" style="">
                                                    {{ strtoupper(@$value->account_code) }}
                                                </td>
                                               
                                                <td style="">
                                                    {{ @$value->groupname->title }}
                                                </td>
                                                <td style="">
                                                    {{ @$value->subgroupname->title }}
                                                </td>
                                                <td style="">
                                                    {{ @$value->subgroup2name->title }}
                                                </td>
                                                 <td style="">
                                                    {{ @$value->account_name }}
                                                </td>
                                                <?php $tran_amt = $account_tran->where('account_id', $value->id)->first(); ?>
                                                <td class="text-end" style="">
                                                    {{ @App\SysHelper::com_curr_format(@$tran_amt->debit_amount, '', '', ',') ?? '0.00' }}
                                                </td>
                                                <td class="text-end" style="">
                                                    {{-- {{ @$tran_amt->credit_amount ?? '0.00' }} --}}
                                                    {{ @App\SysHelper::com_curr_format(@$tran_amt->credit_amount, '', '', ',') ?? '0.00' }}
                                                </td>

                                                <td class="text-center">

                                                    @php
                                                           $editData_tran = @App\SysChartofAccountsTransaction::select('transaction_date', 'debit_amount', 'credit_amount')
                ->where('account_id', $value->id)
                ->where('company_id', $value->company_id)
                ->where('transaction_type', 'openingbalance')
                ->first();
                                                    @endphp

                                                    {{ @$editData_tran->transaction_date != '' ? @App\SysHelper::normalizeToDmy(@$editData_tran->transaction_date) : '' }}
                                                </td>

                                                <td class="text-center" style="">
                                                    @if (@$value->status == 1)
                                                        <span class="text-success">&nbsp;&nbsp;Active</span>
                                                    @else
                                                        <span class="text-dark">&nbsp;&nbsp;Deleted</span>
                                                    @endif
                                                </td>
                                                <td style="" class="">

                                                    <div class="d-flex justify-content-center align-items-center">
                                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                                            <a class="btn-sm btn btn-light editAccountBtn"
                                                                data-id="{{ $value->id }}" {{-- href="{{ url('chartofaccounts/' . @$value->id . '/edit') }}" --}}><i
                                                                    style="font-size: 16px" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Account"
                            data-bs-placement="top"
                                                                    class="ico icon-outline-pen-2"></i></a>
                                                            @if (@$value->status == 2)
                                                                <a class="btn-sm btn btn-light" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Restore Account"
                            data-bs-placement="top"
                                                                    href="{{ url('chartofaccounts/' . $value->id . '/restore') }}"
                                                                    onclick="return confirm('Are you sure you want to restore this item?');"><i
                                                                        class="ico icon-bold-restart text-dark"
                                                                        style="font-size: 16px;"></i></a>
                                                            @else
                                                                <a class="btn-sm btn btn-light" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Account"
                            data-bs-placement="top"
                                                                    href="{{ url('chartofaccounts/' . $value->id . '/delete') }}"
                                                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                                        style="font-size: 16px"
                                                                        class="ico icon-outline-trash-bin-minimalistic"></i></a>
                                                            @endif

                                                            @if (@$value->main_account_id == 0)
                                                                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                                                    <a class="btn-sm btn btn-light text-dark moveToSubGroupBtn" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Move to Sub Group"
                            data-bs-placement="top" style="cursor:pointer;" data-id="{{ $value->id }}" data-name="{{ $value->account_name }}">
                                                                        <i class="ico icon-outline-move-to-folder"  style="font-size: 16px"></i>Sub Group
                                                                    </a>
                                                                    <a class="btn-sm btn btn-light text-dark"
                                                                        style="cursor: pointer;" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Move to Sub Account"
                            data-bs-placement="top"
                                                                        onclick="move_sub_account({{ $value->id }},'{{ $value->account_name }}')"><i
                                                                            class="ico icon-outline-move-to-folder" 
                                                                            style="font-size: 16px"></i> Sub
                                                                        Acc</a>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>



                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <script>
                                        function move_sub_account(id, name) {
                                            $('#move_account_id').val(id);
                                            $('#move_account_name').text(name);
                                            $('#link_move_popup').click();
                                        }
                                    </script>
                                    <script>
                                        $(document).ready(function() {
                                            $('.moveToSubGroupBtn').on('click', function() {
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
                                    <button id="link_move_popup" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        hidden></button>

                                    <div class="modal modal-draggable side-panel fade" id="exampleModal" data-bs-backdrop="false"
                                        tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLabel">Move Account to
                                                        Sub
                                                        Account</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-maintosub', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="input-effect">
                                                                <input type="hidden" id="move_account_id"
                                                                    name="move_account_id" value="" />

                                                                <label id="move_account_name"
                                                                    class="font-weight-bold"></label> <br />Move to

                                                                <select class="form-control js-example-basic-single"
                                                                    name="main_account_id" id="main_account_id" required>
                                                                    <option value=""></option>
                                                                    @if (isset($accounts2))
                                                                        @foreach ($accounts2 as $val)
                                                                            @if (@$val->main_account_id == 0)
                                                                                <option value="{{ @$val->id }}">
                                                                                    {{ @$val->account_code }} -
                                                                                    {{ @$val->account_name }}
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
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

                                    <div class="modal modal-draggable side-panel fade" id="moveToSubGroup" data-bs-backdrop="false"
                                        tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="moveToSubGroupLabel">
                                                        Move <strong><span id="move_account_header"></span></strong>
                                                         to Sub Group
                                                    </h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-move', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="input-effect">
                                                                <input type="hidden" id="move_account_id_subgroup"
                                                                    name="move_account_id_subgroup" value="" />
                                                                       @php
                              $accountgroupsub = @App\SysAccountGroupSub::where('status', 1)->orderBy('group_id')->get();
                        @endphp


                                                                <label id="move_account_name"
                                                                    class="font-weight-bold"></label> Group

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


                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>



            </div>
        </div>



    </div>


    {{-- @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)

        @include('backEnd.chart-of-accounts.accountmerge_form')
    @endif --}}

    <div class="modal modal-draggable side-panel fade" id="addAccountModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Account -
                        {{ @App\SysHelper::get_new_account_code() }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">

                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}


                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="catid" id="catid" value="2">

                            <div class="row">
                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Code') <span>*</span> </label>
                                        <input
                                            class="txtbx primary-input form-control {{ $errors->has('account_code') ? 'is-invalid' : ' ' }}"
                                            type="text" name="account_code"
                                            value="{{ isset($editData) ? @$editData->account_code : @App\SysHelper::get_new_account_code() }}"
                                            required>

                                        <span class="focus-border"></span>

                                        @if ($errors->has('contcat_person'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('contcat_person') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Name') <span>*</span> </label>
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
                                        <label class="form-label"> @lang('Account Sub Group') <span>*</span> </label>
                                        <div class="input-effect" id="sectionSubGroup2Div">
                                            <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single"
                                                name="subgroup2" id="subgroup2" onchange="fn_subgroup()">
                                                <option value="0"></option>
                                                @if (isset($accountgroupsub2))
                                                    @foreach ($accountgroupsub2 as $val)
                                                        <option value="{{ @$val->id }}">{{ @$val->title }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function fn_subgroup() {
                                        if ($('#subgroup2').val() == 6) {
                                            $('.bank_div').css('display', '');
                                        } else {
                                            $('.bank_div').css('display', 'none');
                                        }
                                    }

                                    function fn_subgroup2() {
                                        if ($('#edit_subgroup2').val() == 6) {
                                            $('.edit_bank_div').css('display', '');
                                        } else {
                                            $('.edit_bank_div').css('display', 'none');
                                        }
                                    }

                                    function fn_stl() {
                                        if ($('#stl').val() == 1) {
                                            $('#stl_limit_div').css('display', '');
                                            $('#stl_limit').prop('required', true);
                                        } else {
                                            $('#stl_limit_div').css('display', 'none');
                                            $('#stl_limit').prop('required', false);
                                        }
                                    }
                                </script>

                                <div class="col-4 mb-4 bank_div" style="display: none;">Beneficiary Name
                                    <input class="form-control" type="text" name="beneficiary_name"
                                        value="@if (isset($editData)) {{ @$editData->beneficiary_name }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">Bank Name
                                    <input class="form-control" type="text" name="bank_name"
                                        value="@if (isset($editData)) {{ @$editData->bank_name }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">A/c No.
                                    <input class="form-control" type="text" name="acc_no"
                                        value="@if (isset($editData)) {{ @$editData->acc_no }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">IBAN
                                    <input class="form-control" type="text" name="iban"
                                        value="@if (isset($editData)) {{ @$editData->iban }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">SWIFT Code
                                    <input class="form-control" type="text" name="swift_code"
                                        value="@if (isset($editData)) {{ @$editData->swift_code }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">Finance /Routing Code
                                    <input class="form-control" type="text" name="routing_code"
                                        value="@if (isset($editData)) {{ @$editData->routing_code }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">Branch
                                    <input class="form-control" type="text" name="branch"
                                        value="@if (isset($editData)) {{ @$editData->branch }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">Location
                                    <input class="form-control" type="text" name="branch_location"
                                        value="@if (isset($editData)) {{ @$editData->branch_location }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">Department
                                    <input class="form-control" type="text" name="stl_dept"
                                        value="@if (isset($editData)) {{ @$editData->stl_dept }} @endif">
                                </div>
                                <div class="col-4 mb-4 bank_div" style="display: none;">STL
                                    <select class="form-control" name="stl" id="stl" onchange="fn_stl()">
                                        <option value="0"
                                            @if (isset($editData)) @if ($editData->stl == 0) selected @endif
                                            @endif>Not Applicable</option>
                                        <option value="1"
                                            @if (isset($editData)) @if ($editData->stl == 1) selected @endif
                                            @endif>Applicable</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-4" id="stl_limit_div" style="display: none;">STL Limit
                                    <input class="form-control" type="text" name="stl_limit" id="stl_limit"
                                        value="@if (isset($editData)) {{ @App\SysHelper::com_curr_format(@$editData->stl_limit, 2, '.', ',') }} @endif"
                                        onchange="fn_stl_limit()">
                                </div>
                                <script>
                                    function fn_stl_limit() {
                                        $('#stl_limit').val(formatAmount($('#stl_limit').val()));
                                    }
                                </script>
                                @if (isset($editData))
                                    <script>
                                        $('#stl').change();
                                    </script>
                                @endif
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
                                            $companyOpeningDate = @optional(
                                                @App\SysCompany::select('opening_balance_date')
                                                    ->where('id', session('logged_session_data.company_id'))
                                                    ->first()
                                            )->opening_balance_date;
                                            $companyOpeningDate = !empty($companyOpeningDate)
                                                ? @App\SysHelper::normalizeToDmy($companyOpeningDate)
                                                : Carbon\Carbon::now()->format('d/m/Y');

                                            $value = session('opening_balance_date');

                                            if (is_null($value) || $value === '') {
                                                $value = $companyOpeningDate;
                                            }

                                            if (isset($editData_tran) && !empty($editData_tran->transaction_date)) {
                                                $value = Carbon\Carbon::parse($editData_tran->transaction_date)->format('d/m/Y');
                                            }
                                        @endphp
                                        <input
                                            class="primary-input form-control date-picker {{ $errors->has('opening_balance_date') ? 'is-invalid' : ' ' }}"
                                            type="text" id="opening_balance_date" name="opening_balance_date"
                                            value="{{ $value }}" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                            </div>

                            <script>
                                $('#subgroup2').change();
                            </script>


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


    <div class="modal modal-draggable side-panel fade" id="editAccountModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit Account - <span id="edit_account_codesups"></span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">

                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'editAccountForm']) }}
                            <input type="hidden" value="" name="cust_id">


                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="catid" id="catid" value="2">

                            <div class="row">
                                <!-- <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Code') <span>*</span> </label>
                                      

                                        <span class="focus-border"></span>

                                        @if ($errors->has('contcat_person'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('contcat_person') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div> -->
                                  <input
                                            class="txtbx primary-input form-control {{ $errors->has('account_code') ? 'is-invalid' : ' ' }}"
                                            type="hidden" id="edit_account_code" name="account_code" value=""
                                            required>

                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Name') <span>*</span> </label>
                                        <input
                                            class="txtbx primary-input form-control {{ $errors->has('account_name') ? 'is-invalid' : ' ' }}"
                                            type="text" id="edit_account_name" name="account_name" value=""
                                            required>

                                        <div id="edit_account_name_add_list">
                                        </div>
                                        <script>
                                            $(document).ready(function() {
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
                                                                $('#edit_account_name_add_list').fadeIn();
                                                                $('#edit_account_name_add_list').html(data);
                                                            }
                                                        });
                                                    }
                                                });
                                                $('#edit_account_name_add_list').on('click', 'li', function() {
                                                    $('#edit_account_name').val($(this).text());
                                                    $('#edit_account_name_add_list').fadeOut();
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>


                                <div class="col-3 mb-4">
                                    <input type="hidden" name="pre_sub_group2" value="">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Sub Group') <span>*</span> </label>
                                        <div class="input-effect" id="sectionSubGroup2Div">
                                            <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single"
                                                name="subgroup2" id="edit_subgroup2" onchange="fn_subgroup2()">
                                                <option value="0"></option>
                                                @if (isset($accountgroupsub2))
                                                    @foreach ($accountgroupsub2 as $val)
                                                        <option value="{{ @$val->id }}">{{ @$val->title }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $departments = @App\SmHumanDepartment::all();
                                @endphp

                                <div class="col-3 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Department') <span>*</span> </label>
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="department_id" id="edit_department_id" required>
                                            <option value=""></option>
                                            @if (isset($departments))
                                                @foreach ($departments as $val)
                                                    <option @if (isset($editData)) @if ($editData->department_id == @$val->id) selected @endif @endif value="{{ @$val->id }}">{{ @$val->name }}</option>
                                                @endforeach

                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-2 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Prepaid/Accrued Exp') <span>*</span> </label>
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="credit_account_status" id="edit_credit_account_status" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                                <script>
                                    function fn_subgroup2() {
                                        if ($('#edit_subgroup2').val() == 6) {
                                            $('.edit_bank_div').css('display', '');
                                        } else {
                                            $('.edit_bank_div').css('display', 'none');
                                        }
                                    }

                                    function fn_stl2() {
                                        if ($('#edit_stl').val() == 1) {
                                            $('#edit_stl_limit_div').css('display', '');
                                            $('#edit_stl_limit').prop('required', true);
                                        } else {
                                            $('#edit_stl_limit_div').css('display', 'none');
                                            $('#edit_stl_limit').prop('required', false);
                                        }
                                    }
                                </script>

                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Beneficiary Name
                                    <input class="form-control" type="text" id="edit_beneficiary_name"
                                        name="beneficiary_name" value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Bank Name
                                    <input class="form-control" type="text" id="edit_bank_name" name="bank_name"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">A/c No.
                                    <input class="form-control" type="text" id="edit_acc_no" name="acc_no"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">IBAN
                                    <input class="form-control" type="text" id="edit_iban" name="iban"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">SWIFT Code
                                    <input class="form-control" type="text" id="edit_swift_code" name="swift_code"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Finance /Routing Code
                                    <input class="form-control" type="text" id="edit_routing_code"
                                        name="routing_code" value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Branch
                                    <input class="form-control" type="text" id="edit_id_branch" name="branch"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Location
                                    <input class="form-control" type="text" id="edit_branch_location"
                                        name="branch_location"
                                        value="@if (isset($editData)) {{ @$editData->branch_location }} @endif">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Department
                                    <input class="form-control" type="text" id="edit_stl_dept" name="stl_dept"
                                        value="@if (isset($editData)) {{ @$editData->stl_dept }} @endif">
                                </div>
                               
                                <!-- hidden fields to store template coordinates in edit form -->
                                <input type="hidden" name="cheque_company_top" value="">
                                <input type="hidden" name="cheque_company_left" value="">
                                <input type="hidden" name="cheque_date_top" value="">
                                <input type="hidden" name="cheque_date_left" value="">
                                <input type="hidden" name="cheque_amount_w_top" value="">
                                <input type="hidden" name="cheque_amount_w_left" value="">
                                <input type="hidden" name="cheque_amount_top" value="">
                                <input type="hidden" name="cheque_amount_left" value="">
                                <input type="hidden" name="cheque_font_size" value="">
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">STL
                                    <select class="form-control" name="stl" id="edit_stl" onchange="fn_stl()">
                                        <option value="0"
                                            @if (isset($editData)) @if ($editData->stl == 0) selected @endif
                                            @endif>Not Applicable</option>
                                        <option value="1"
                                            @if (isset($editData)) @if ($editData->stl == 1) selected @endif
                                            @endif>Applicable</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-4" id="stl_limit_div" style="display: none;">STL Limit
                                    <input class="form-control" type="text" name="stl_limit" id="edit_stl_limit"
                                        value="@if (isset($editData)) {{ @App\SysHelper::com_curr_format(@$editData->stl_limit, 2, '.', ',') }} @endif"
                                        onchange="fn_stl_limit2()">
                                </div>
                                <script>
                                    function fn_stl_limit2() {
                                        $('#edit_stl_limit').val(formatAmount($('#edit_stl_limit').val()));
                                    }
                                </script>
                                @if (isset($editData))
                                    <script>
                                        $('#edit_stl').change();
                                    </script>
                                @endif
                                <!-- toggle for opening balance fields in edit modal -->
                                <div class="col-4 mb-2 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Opening Balance"
                            data-bs-placement="top" type="checkbox" id="edit_toggleOpeningBalance">
                                    
                                    </div>
                                </div>

                                <div class="col-4 mb-4 opening-balance-edit" style="display:none;">
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
                                            type="text" id="edit_debit_amount" name="debit_amount"
                                            value="{{ $debit_amount }}" required>
                                        <span class="focus-border"></span>
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

                                <div class="col-4 mb-4 opening-balance-edit" style="display:none;">
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
                                            type="text" id="edit_credit_amount" name="credit_amount"
                                            value="{{ $credit_amount }}" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                <div class="col-4 mb-4 opening-balance-edit" style="display:none;">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Date') <span>*</span> </label>
                                        @php
                                            $companyOpeningDate = @optional(
                                                @App\SysCompany::select('opening_balance_date')
                                                    ->where('id', session('logged_session_data.company_id'))
                                                    ->first()
                                            )->opening_balance_date;
                                            $companyOpeningDate = !empty($companyOpeningDate)
                                                ? @App\SysHelper::normalizeToDmy($companyOpeningDate)
                                                : Carbon\Carbon::now()->format('d/m/Y');

                                            $value = session('opening_balance_date');

                                            if (is_null($value) || $value === '') {
                                                $value = $companyOpeningDate;
                                            }

                                            if (isset($editData_tran) && !empty($editData_tran->transaction_date)) {
                                                $value = Carbon\Carbon::parse($editData_tran->transaction_date)->format('d/m/Y');
                                            }
                                        @endphp
                                        <input
                                            class="primary-input form-control date-picker {{ $errors->has('opening_balance_date') ? 'is-invalid' : ' ' }}"
                                            type="text" id="edit_opening_balance_date" name="opening_balance_date"
                                            value="{{ $value }}" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                 <!-- cheque template button inside edit modal -->
                                <div class="col-4 mb-4 edit_bank_div" style="display:none;">
                                    <label>Cheque Template</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" id="openChequeTemplateBtnEdit" class="btn btn-light btn-sm">
                                        <i class="ico icon-outline-document text-success" style="font-size:15px"></i>    Configure
                                        </button>
                                        <span class="cheque_template_status text-success small" id="cheque_template_status_edit"></span>
                                    </div>
                                </div>

                            </div>

                            <script>
                                $('#edit_subgroup2').change();
                            </script>


                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button class="btn btn-light" id="btnSubmit" name="btnSubmit" value="update">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>




    <script>
        $(document).ready(function() {
            // hide edit-opening fields initially
            $('.opening-balance-edit').hide();
            $('#edit_toggleOpeningBalance').prop('checked', false);

            $('#edit_toggleOpeningBalance').change(function() {
                if (this.checked) {
                    $('.opening-balance-edit').show();
                } else {
                    $('.opening-balance-edit').hide();
                }
            });

            $('.editAccountBtn').on('click', function() {
                let accountId = $(this).data('id');
                let companyOpeningDate = @json(
                    @App\SysHelper::normalizeToDmy(
                        @optional(
                            @App\SysCompany::select('opening_balance_date')
                                ->where('id', session('logged_session_data.company_id'))
                                ->first()
                        )->opening_balance_date
                    )
                );

                console.log("account ID:", accountId)
                $("#loading_bg").show();

                $.ajax({
                    url: '/chartofaccounts/' + accountId + '/get-edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert('Error: ' + response.message);
                            return;
                        }

                        console.log(response)

                        let editData = response.editData;
                        let editData_tran = response.editData_tran;

                        // Fill form fieldsedit_account_codesups
                        $('#editAccountModal #edit_account_name').val(editData.account_name);
                        $('#editAccountModal #edit_account_codesups').text(editData
                            .account_code);
                        $('#editAccountModal #edit_account_code').val(editData.account_code);
                        $('#editAccountModal #edit_subgroup2').val(editData.subgroup2).trigger(
                            'change');
                        $('#editAccountModal input[name="pre_sub_group2"]').val(editData.subgroup2);
                        $('#editAccountModal #edit_beneficiary_name').val(editData
                            .beneficiary_name);
                        $('#editAccountModal #edit_bank_name').val(editData.bank_name);
                        $('#editAccountModal #edit_acc_no').val(editData.acc_no);
                        $('#editAccountModal #edit_iban').val(editData.iban);
                        $('#editAccountModal #edit_swift_code').val(editData.swift_code);
                        $('#editAccountModal #edit_routing_code').val(editData.routing_code);
                        $('#editAccountModal #edit_id_branch').val(editData.branch);
                        $('#editAccountModal #edit_branch_location').val(editData
                            .branch_location);
                        $('#editAccountModal #edit_department_id').val(editData.department_id).trigger('change');
                        $('#editAccountModal #edit_credit_account_status').val(editData.yes_no).trigger('change');
                        $('#editAccountModal #edit_stl_dept').val(editData.stl_dept);
                        $('#editAccountModal #edit_stl').val(editData.stl).trigger('change');

                        if (editData_tran) {
                            $('#editAccountModal #edit_debit_amount').val(formatAmount(editData_tran
                                .debit_amount));
                            $('#editAccountModal #edit_credit_amount').val(formatAmount(editData_tran
                                .credit_amount));

                            $('#editAccountModal #edit_opening_balance_date').val(editData_tran
                                .transaction_date ? editData_tran
                                .transaction_date.split('-').reverse().join('/') : '');

                        
                        } else {
                            $('#editAccountModal #edit_debit_amount').val('0.00');
                            $('#editAccountModal #edit_credit_amount').val('0.00');
                            $('#editAccountModal #edit_opening_balance_date').val(
                                companyOpeningDate || ''
                            );
                            $('#edit_toggleOpeningBalance').prop('checked', false);
                            $('.opening-balance-edit').hide();
                        }

                        // load cheque template data into hidden inputs
                        if (response.cheque_template) {
                            var ct = response.cheque_template;
                            var container = $('#editAccountModal');
                            container.find('input[name="cheque_company_top"]').val(ct.company_top || '285px');
                            container.find('input[name="cheque_company_left"]').val(ct.company_left || '425px');
                            container.find('input[name="cheque_date_top"]').val(ct.date_top || '220px');
                            container.find('input[name="cheque_date_left"]').val(ct.date_left || '836px');
                            container.find('input[name="cheque_amount_w_top"]').val(ct.amount_w_top || '316px');
                            container.find('input[name="cheque_amount_w_left"]').val(ct.amount_w_left || '326px');
                            container.find('input[name="cheque_amount_top"]').val(ct.amount_top || '355px');
                            container.find('input[name="cheque_amount_left"]').val(ct.amount_left || '834px');
                            container.find('input[name="cheque_font_size"]').val(ct.font_size || '13px');
                        } else {
                            $('#editAccountModal input[name="cheque_company_top"]').val('285px');
                            $('#editAccountModal input[name="cheque_company_left"]').val('425px');
                            $('#editAccountModal input[name="cheque_date_top"]').val('220px');
                            $('#editAccountModal input[name="cheque_date_left"]').val('836px');
                            $('#editAccountModal input[name="cheque_amount_w_top"]').val('316px');
                            $('#editAccountModal input[name="cheque_amount_w_left"]').val('326px');
                            $('#editAccountModal input[name="cheque_amount_top"]').val('355px');
                            $('#editAccountModal input[name="cheque_amount_left"]').val('834px');
                            $('#editAccountModal input[name="cheque_font_size"]').val('13px');
                        }
                        // refresh template status / global variable for edit modal
                        if (typeof ctLoadFromContainer === 'function') {
                            ctLoadFromContainer($('#editAccountModal'));
                        }

                        // Show the modal
                        $("#loading_bg").hide();
                        $('#editAccountForm').attr('action', 'chartofaccounts-update/' +
                            editData.id);
                        $('#editAccountModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        alert('An error occurred while fetching data. Please try again later.');
                    }

                });
            });

        });


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
            function setManualWidths() {
                var $table = $('.table-fixed-header');
                var $theadTh = $table.find('thead th');
                var columnWidths = [100, 100, 100, 100, 200, 100, 100, 100, 100, 270]; // 👈 define widths here in px

                $theadTh.each(function(i) {
                    var w = columnWidths[i];
                    $(this).css('width', w + 'px');
                    $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
                });
            }

            setManualWidths();
            $(window).on('resize', setManualWidths);
        });
    </script>

@endsection

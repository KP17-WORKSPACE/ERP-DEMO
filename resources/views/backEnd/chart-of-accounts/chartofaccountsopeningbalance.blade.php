@extends('backEnd.newmasterpage')
@section('mainContent')


    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>




    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>



    <div class="content-container col-12">
           <div class="smart_search_wrapper">
                <div id="smart_search_list"></div>
            </div>
        <h4 style="position: fixed; margin-top: 7px;">Opening Balance</h4>
           <div class="purchase-order-content-header-right" style="margin-top:-14px">

            <input type="text" class="form-control w-25 rounded" id="smart_search" name="smart_search"
                placeholder="Search..." />
            
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
            </script>


            {{-- <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#addGroupModal"
                            aria-expanded="false">
                            <i class="ico icon-outline-add-square"></i> Add
                        </button> --}}

          
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="modal"
                    data-bs-target="#openingBalanceQuickUpdateModal">
                    <i class="ico icon-outline-add-square text-success"></i> Add
                </button>

              

            @include('backEnd.accounts.accountgroupsubadd_form')
            @include('backEnd.accounts.accountgroupsub2add_form')
            @include('backEnd.chart-of-accounts.accountadd_form')
            @include('backEnd.chart-of-accounts.accountsubadd_form')
            @include('backEnd.chart-of-accounts.accountsubemployeeadd_form')



            <!-- <div class="dropdown">
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
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts-add-sub') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Account</a></li>
                   



                </ul>
            </div> -->

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


                      <li>
                        <a class="dropdown-item d-flex align-items-center" href="#collapseList" data-bs-toggle="collapse"
                            aria-expanded="false" onclick="event.stopPropagation();">
                            <span class="text-muted"><i class="ico icon-outline-document-text title-15 me-2"></i>
                                List</span>

                        </a>
                    </li>
                    <li>
                        <div class="collapse" id="collapseList">
                            <ul class="list-unstyled  mb-0">
                                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                                   
                                               <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Chart of Accounts</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('accountgroupsub-add') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Group</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('accountgroupsub2-add') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Group</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts-add') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Account</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts-add-sub') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Account</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>

                    <li>
                         <a class="dropdown-item " href="{{ url('chartofaccounts-import-invoice')}}" >
                          <i class="ico icon-outline-import  title-15 me-2"></i>   Import Invoices

                        </a>
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

        <div class="card mb-3" style="box-shadow: none">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="long-list" class="table table-hover table-fixed-header" style="table-layout: fixed;width:100%">

                        <thead>
                            <tr>
                                <th class="text-center" style="width: 120px;">@lang('Account Code')</th>
                                <th>@lang('Sub Account Name')</th>
                                <th class="text-center" style="width: 120px;">@lang('Transaction Date')</th>
                                <th class="text-end" style="width: 120px;" class="text-right">@lang('Debit Amount')</th>
                                <th class="text-end" style="width: 120px;" class="text-end">@lang('Credit Amount')</th>
                                <th style="width: 150px;" class="text-center">@lang('Invoice Number')</th>
                            </tr>
                        </thead>


                        <tbody style="max-height: calc(100vh - 145px);">
                            @if (isset($account))
                                @php $recordCount = 0; @endphp
                                @foreach ($account as $value)
                                    @if (@$value->debit_amount > 0 || @$value->credit_amount > 0)
                                        @php $recordCount++; @endphp
                                        <tr>
                                            <td class="text-center">
                                                <a href="#" class="editAccountBtn2" data-id="{{ @$value->account_id }}" data-account-code="{{ @$value->account_code }}">
                                                    {{ @$value->account_code }}</a>
                                            </td>
                                            <td>
                                                {{ @$value->accounts->account_name }}
                                            </td>
                                            <td class="text-center">
                                                {{ date('d/m/Y', strtotime(@$value->transaction_date)) }}
                                            </td>
                                            <?php
                                            $inv_list = $invoice->where('account_id', $value->account_id);
                                            $d1 = @App\SysHelper::com_curr_format($inv_list->sum('debit_amount'), 2, '.', ',');
                                            $c1 = @App\SysHelper::com_curr_format($inv_list->sum('credit_amount'), 2, '.', ',');
                                            $d_class = '';
                                            $c_class = '';
                                            
                                            if (strpos(optional($value)->account_code, 'CUS') !== false) {
                                                if (@App\SysHelper::com_curr_format($value->debit_amount, 2, '.', ',') != $d1) {
                                                    $d_class = 'text-danger';
                                                }
                                            }
                                            if (strpos(optional($value)->account_code, 'SUP') !== false) {
                                                if (@App\SysHelper::com_curr_format($value->credit_amount, 2, '.', ',') != $c1) {
                                                    $c_class = 'text-danger';
                                                }
                                            }
                                            ?>
                                            <td class="text-end {{ $d_class }}">
                                                {{ @App\SysHelper::com_curr_format(@$value->debit_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-end {{ $c_class }}">
                                                {{ @App\SysHelper::com_curr_format(@$value->credit_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="">
                                                <div class="d-flex justify-content-center">
                                                    @php $invoice_list = $invoice->where('account_id',$value->account_id); @endphp
                                                    @if (count($invoice_list) > 0)
                                                        {{-- <a class="btn-sm btn-danger"
                                                    href="{{ url('chartofaccounts-opening-balance-edit/' . $value->account_id) }}">View
                                                    & Edit</a> --}}

                                                        <button data-account-id="{{ $value->account_id }}"    data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="View & Edit Invoice"
                            data-bs-placement="top"
                                                            class="btn-sm btn btn-light open-invoice-modal text-success"><i class="ico icon-outline-pen-2 text-dark" style="font-size:14px"></i></button>
                                                    @endif
                                                </div>

                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if($recordCount == 0)
                                    <tr><td colspan="6" class="text-center">No records found</td></tr>
                                @endif
                            @else
                                <tr><td colspan="6" class="text-center">No records found</td></tr>
                            @endif
                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-end font-weight-bold ">
                                   {{ @App\SysHelper::com_curr_format($account->sum('debit_amount'), 2, '.', ',') }}</th>
                                <th class="text-end font-weight-bold ">
                                    {{ @App\SysHelper::com_curr_format($account->sum('credit_amount'), 2, '.', ',') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>


    </div>







    <script>
        $('#dataTable').DataTable({
            "pageLength": -1, // Show all records
            "lengthMenu": [
                [-1],
                ["All"]
            ] // Only show "All" as an option
        });
    </script>



    <div class="modal  fade" id="invoiceModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl  modal-dialog-scrollable">

            <div id="invoiceModalBody">

            </div>


        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.open-invoice-modal').on('click', function() {
                var accountId = $(this).data('account-id');
                loadInvoiceModal(accountId); // reusable function

            });


        });


        function loadInvoiceModal(accountId) {
            var action = "{{ URL::to('chartofaccounts-opening-balance-edit') }}/" + accountId;
            $('#loading_bg').show();
            $.ajax({
                url: action,
                method: 'GET',
                success: function(response) {

                    $('#invoiceModalBody').html(response);
                    $('#invoiceModal').modal('show');
                },
                error: function() {
                    $('#invoiceModalBody').html(
                        '<p class="text-danger">Error loading details.</p>');
                },
                complete: function() {
                    $('#loading_bg').hide(); // Always hide loader after request completes
                }
            });
        }
    </script>
<script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
        var columnWidths = [120, 250, 120, 120, 120, 150]; // 👈 define widths in px

        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
        });

        // Apply the same widths to <tfoot>
        $tfootTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>


<div class="modal modal-draggable side-panel fade" id="editAccountModal2" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="/chartofaccounts-update/12" method="POST" enctype="multipart/form-data" id="editAccountForm2">
            @csrf
            <input type="hidden" name="_method" value="PUT">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Edit Account - <span id="edit_account_codesups"></span>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">

                    

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

@php
                $accountgroupsub2_edit = @App\SysAccountGroupSub2::where('status', 1)->get();
@endphp
                            <div class="col-4 mb-4">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Account Sub Group') <span>*</span> </label>
                                    <div class="input-effect" id="sectionSubGroup2Div">
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single"
                                            name="subgroup2" id="edit_subgroup2" onchange="fn_subgroup2()">
                                            <option value="0"></option>
                                            @if (isset($accountgroupsub2_edit))
                                                @foreach ($accountgroupsub2_edit as $val)
                                                    <option value="{{ @$val->id }}">{{ @$val->title }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
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
                                <div class="col-4 mb-4 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input"  data-bs-popover="popover"
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
                                        $value = session('opening_balance_date');

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
                                        type="text" id="edit_opening_balance_date" name="opening_balance_date"
                                        value="{{ $value }}" required>
                                    <span class="focus-border"></span>
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
       
            {{-- {{ Form::close() }} --}}
        </div>
         </form>
    </div>
</div>


<script>
    $(document).ready(function() {

    $('.opening-balance-edit').hide();
                    $('.opening-balance-edit').show();

            $('#edit_toggleOpeningBalance').prop('checked', true).trigger('change'); // default to showing opening balance fields

            $('#edit_toggleOpeningBalance').change(function() {
                if (this.checked) {
                    $('.opening-balance-edit').show();
                } else {
                    $('.opening-balance-edit').hide();
                }
            });

        // Use event delegation for dynamically loaded buttons
        $(document).on('click', '.editAccountBtn2', function() {
            let accountId = $(this).data('id');

            console.log("Clicked edit button for account ID:", accountId);

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
                    $('#editAccountModal2 #edit_account_name').val(editData.account_name);
                    $('#editAccountModal2 #edit_account_codesups').text(editData
                        .account_code);
                    $('#editAccountModal2 #edit_account_code').val(editData.account_code);
                    $('#editAccountModal2 #edit_subgroup2').val(editData.subgroup2).trigger(
                        'change');
                    $('#editAccountModal2 #edit_beneficiary_name').val(editData
                        .beneficiary_name);
                    $('#editAccountModal2 #edit_bank_name').val(editData.bank_name);
                    $('#editAccountModal2 #edit_acc_no').val(editData.acc_no);
                    $('#editAccountModal2 #edit_iban').val(editData.iban);
                    $('#editAccountModal2 #edit_swift_code').val(editData.swift_code);
                    $('#editAccountModal2 #edit_routing_code').val(editData.routing_code);
                    $('#editAccountModal2 #edit_id_branch').val(editData.branch);
                    $('#editAccountModal2 #edit_branch_location').val(editData
                        .branch_location);
                    $('#editAccountModal2 #edit_stl_dept').val(editData.stl_dept);
                    $('#editAccountModal2 #edit_stl').val(editData.stl).trigger('change');

                    if (editData_tran) {
                        $('#editAccountModal2 #edit_debit_amount').val(editData_tran
                            .debit_amount);
                        $('#editAccountModal2 #edit_credit_amount').val(editData_tran
                            .credit_amount);

                        $('#editAccountModal2 #edit_opening_balance_date').val(editData_tran
                            .transaction_date ? editData_tran
                            .transaction_date.split('-').reverse().join('/') : '');


                    } else {
                        $('#editAccountModal2 #edit_debit_amount').val('0.00');
                        $('#editAccountModal2 #edit_credit_amount').val('0.00');
                        $('#editAccountModal2 #edit_opening_balance_date').val('');
                        $('#edit_toggleOpeningBalance').prop('checked', false);

                    }

                    // Show the modal
                    $("#loading_bg").hide();
                    $('#editAccountForm2').attr('action', 'chartofaccounts-update/' +
                        editData.id);
                    $('#editAccountModal2').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('An error occurred while fetching data. Please try again later.');
                }

            });
        });

    });
</script>

<style>
    #openingBalanceQuickUpdateModal.ob-modal-underlay {
        z-index: 1040 !important;
    }

    #openingBalanceQuickUpdateModal.ob-modal-underlay .modal-dialog {
        z-index: 1041 !important;
    }
</style>

<div class="modal fade" id="openingBalanceQuickUpdateModal" data-bs-backdrop="false"tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Opening Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 position-relative">
                    <label for="ob_account_search" class="form-label">Account / Sub Account  
                        <i
                        class="ico title-15 icon-outline-add-square me-2 text-success" data-bs-toggle="modal"
                        data-bs-popover="popover"
                        data-bs-trigger="hover"
                        data-bs-delay="500"
                        data-bs-content="Add Account"
                        data-bs-placement="top"
                        data-bs-target="#accountModal"></i> 

                        <i data-bs-toggle="modal"
                            data-bs-target="#accountSubModal" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Sub Account"
                            data-bs-placement="top"
                        class="ico title-15 icon-outline-add-square me-2 text-success"></i>


                    </label>
                    <input type="text" id="ob_account_search" class="form-control" autocomplete="off"
                        placeholder="">
                    <input type="hidden" id="ob_account_id">
                    <div id="ob_account_search_list" class="list-group"
                        style="position: fixed; z-index: 2000; max-height: 220px; overflow-y: auto; display:none;"></div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="ob_debit_amount" class="form-label">Opening Balance Dr <span>*</span></label>
                        <input type="text" id="ob_debit_amount" class="form-control" value="0.00">
                    </div>
                    <div class="col-6">
                        <label for="ob_credit_amount" class="form-label">Opening Balance Cr <span>*</span></label>
                        <input type="text" id="ob_credit_amount" class="form-control" value="0.00">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="ob_update_btn" class="btn btn-light">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let obSearchTimer = null;
        const $searchInput = $('#ob_account_search');
        const $searchList = $('#ob_account_search_list');
        const $accountId = $('#ob_account_id');
        const $debit = $('#ob_debit_amount');
        const $credit = $('#ob_credit_amount');
        const csrfToken = "{{ csrf_token() }}";

        function positionSearchList() {
            const inputOffset = $searchInput.offset();
            if (!inputOffset) {
                return;
            }

            $searchList.css({
                top: (inputOffset.top + $searchInput.outerHeight() + 4) + 'px',
                left: inputOffset.left + 'px',
                width: $searchInput.outerWidth() + 'px'
            });
        }

        function resetOpeningBalanceModal() {
            $searchInput.val('');
            $accountId.val('');
            $debit.val('0.00');
            $credit.val('0.00');
            $searchList.hide().empty();
        }

        function escapeHtml(text) {
            return $('<div/>').text(text || '').html();
        }

        function renderSearchResults(items) {
            if (!items || items.length === 0) {
                $searchList.html('<button type="button" class="list-group-item list-group-item-action disabled">No matching records found</button>').show();
                return;
            }

            let html = '';
            items.forEach(function(item) {
                const label = (item.account_code + ' - ' + item.account_name + ' (' + item.type + ')');
                html += '<button type="button" class="list-group-item list-group-item-action ob-account-option" data-id="' + item.id + '" data-label="' + escapeHtml(label) + '">' +
                    '<div class="d-flex justify-content-between"><span>' + escapeHtml(item.account_code + ' - ' + item.account_name) + '</span><small class="text-muted">' + escapeHtml(item.type) + '</small></div>' +
                    '</button>';
            });

            positionSearchList();
            $searchList.html(html).show();
        }

        function loadOpeningBalanceDetails(accountId, labelText) {
            $('#loading_bg').show();
            $.ajax({
                url: "{{ url('chartofaccounts-opening-balance-details') }}/" + accountId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $searchInput.val(labelText);
                    $accountId.val(response.id);
                    $debit.val(response.debit_amount || '0.00');
                    $credit.val(response.credit_amount || '0.00');
                },
                error: function() {
                    alert('Unable to load opening balance details.');
                },
                complete: function() {
                    $('#loading_bg').hide();
                }
            });
        }

        $('#openingBalanceQuickUpdateModal').on('shown.bs.modal', function() {
            if (!$searchList.parent().is('body')) {
                $searchList.appendTo('body');
            }
            positionSearchList();
            $searchInput.trigger('focus');
        });

        $('#openingBalanceQuickUpdateModal').on('hidden.bs.modal', function() {
            resetOpeningBalanceModal();
        });

        $('#accountModal, #accountSubModal').on('show.bs.modal', function() {
            $searchList.hide().empty();
            $('#openingBalanceQuickUpdateModal').addClass('ob-modal-underlay');
        });

        $('#accountModal, #accountSubModal').on('hidden.bs.modal', function() {
            $('#openingBalanceQuickUpdateModal').removeClass('ob-modal-underlay');
        });

        $searchInput.on('keyup', function() {
            const q = $(this).val().trim();
            $accountId.val('');

            clearTimeout(obSearchTimer);
            if (q.length < 2) {
                $searchList.hide().empty();
                return;
            }

            obSearchTimer = setTimeout(function() {
                positionSearchList();
                $.ajax({
                    url: "{{ route('chartofaccounts-opening-balance-search') }}",
                    method: 'GET',
                    data: {
                        q: q
                    },
                    dataType: 'json',
                    success: function(response) {
                        renderSearchResults(response.data || []);
                    },
                    error: function() {
                        $searchList.hide().empty();
                    }
                });
            }, 300);
        });

        $(document).on('click', '.ob-account-option', function() {
            const accountId = $(this).data('id');
            const labelText = $(this).data('label');
            $searchList.hide().empty();
            loadOpeningBalanceDetails(accountId, labelText);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#ob_account_search, #ob_account_search_list').length) {
                $searchList.hide();
            }
        });

        $(window).on('resize scroll', function() {
            if ($searchList.is(':visible')) {
                positionSearchList();
            }
        });

        $('#openingBalanceQuickUpdateModal').on('scroll', function() {
            if ($searchList.is(':visible')) {
                positionSearchList();
            }
        });

        $('#ob_update_btn').on('click', function() {
            if (!$accountId.val()) {
                alert('Please select an account or sub account.');
                return;
            }

            $('#loading_bg').show();
            $.ajax({
                url: "{{ route('chartofaccounts-opening-balance-update') }}",
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: csrfToken,
                    account_id: $accountId.val(),
                    debit_amount: $debit.val(),
                    credit_amount: $credit.val()
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Updated successfully');
                    }
                    $('#openingBalanceQuickUpdateModal').modal('hide');
                    window.location.reload();
                },
                error: function(xhr) {
                    let message = 'Unable to update opening balance.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                },
                complete: function() {
                    $('#loading_bg').hide();
                }
            });
        });
    });
</script>


<script>
    
    
    // Apply formatting on typing
    $('#ob_debit_amount, #ob_credit_amount').on('blur', function () {
        
        // Remove invalid characters except decimal
        let rawValue = $(this).val().replace(/[^\d.]/g, '');
    
        // Prevent multiple decimals
        let parts = rawValue.split('.');
        if (parts.length > 2) {
            rawValue = parts[0] + '.' + parts[1];
        }
    
        $(this).val(formatAmount(rawValue));
    });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

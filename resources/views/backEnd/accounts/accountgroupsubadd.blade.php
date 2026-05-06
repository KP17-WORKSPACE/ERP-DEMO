@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="content-container col-9 page-chart-of-accounts">

           <div class="smart_search_wrapper">
                <div id="smart_search_list"></div>
            </div>

        <h4 style="position: fixed; margin-top: 7px;">Groups</h4>


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
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('accountgroupsub2-add') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Sub Group</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('chartofaccounts-add') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Account</a></li>
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



        {{-- <div class="card mb-3">
            <div class="card-body">
                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
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
                            <th> @lang('Main Heads')</th>
                            <th> @lang('Group')</th>
                            <th style="width:100px" class="text-center"> @lang('Status')</th>
                            <th style="width:100px" class="text-center"> @lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (isset($accountgroupsub))
                            @foreach ($accountgroupsub as $value)
                                <tr>
                                    <td>
                                        {{ @$value->groupid->title }}
                                    </td>
                                    <td>
                                        {{ @$value->title }}
                                    </td>
                                    <td class="text-center">
                                        @if (@$value->status == 1)
                                            <span class="text-success">Active</span>
                                        @else
                                            <span class="text-danger">InActive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            @if (Auth::user()->role_id == 1)
                                                <a class="btn btn-sm btn-light EditGroupBTN"
                                                    data-id="{{ $value->id }}"><i
                                                        style="font-size: 16px" class="ico icon-outline-pen-2"></i></a>
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ url('accountgroupsub/' . @$value->id . '/delete') }}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                        style="font-size: 16px"
                                                        class="ico icon-outline-trash-bin-minimalistic"></i></a>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div> --}}
@if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 27)
                @include('backEnd.chart-of-accounts.account_move_form')
                @include('backEnd.chart-of-accounts.subaccount_move_form')

        @endif

        <div class="card mb-3">
            <div class="card-body bg-white p-0">
                <table class="table table-hover bordered-table table-fixed-header data-table" id="long-list" style="table-layout: fixed;width:100%">
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
                            <th> @lang('Main Heads')</th>
                            <th> @lang('Group')</th>
                            <th style="width:100px" class="text-center"> @lang('Status')</th>
                            <th style="width:100px" class="text-center"> @lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if (isset($accountgroupsub))
                            @foreach ($accountgroupsub as $value)
                                <tr>
                                    <td>
                                        {{ @$value->groupid->title }}
                                    </td>
                                    <td>
                                        {{ @$value->title }}
                                    </td>
                                    <td class="text-center">
                                        @if (@$value->status == 1)
                                            <span class="text-success">Active</span>
                                        @else
                                            <span class="text-danger">InActive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            @if (Auth::user()->role_id == 1)
                                                <a class="btn btn-sm btn-light EditGroupBTN"
                                                    data-id="{{ $value->id }}" {{-- href="{{ url('accountgroupsub/' . @$value->id . '/edit') }}" --}}><i
                                                        style="font-size: 16px" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Group"
                            data-bs-placement="top" class="ico icon-outline-pen-2"></i></a>
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ url('accountgroupsub/' . @$value->id . '/delete') }}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                        style="font-size: 16px" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Delete Group"
                            data-bs-placement="top" class="ico icon-outline-trash-bin-minimalistic"></i></a>
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


    <!-- Edit Group Modal -->
    <div class="modal modal-draggable  side-panel fade" id="editGroupModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editGroupModalLabel">Edit Group</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body bg-white">

                            {{ Form::open(['id' => 'editGroupForm', 'class' => 'form-horizontal', 'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

                            <input type="hidden" name="url" id="edit_url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="date_of_joining" id="edit_date_of_joining"
                                value="{{ date('Y-m-d') }}">

                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="input-effect">
                                        <label class="txtlbl"> @lang('Group Name') <span>*</span> </label>
                                        <input class="form-control" type="text" id="edit_title" name="title"
                                            value="" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    <div class="input-effect">
                                        <label class="txtlbl"> @lang('Select Main Heads') <span>*</span> </label>
                                        <select class="form-control js-example-basic-single" name="group_id" id="edit_group_id" required>
                                            <option value=""></option>
                                            @if (isset($accountgroup))
                                                @foreach ($accountgroup as $val)
                                                    <option value="{{ $val->id }}">{{ $val->title }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="submit" id="edit_btnSubmit">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            $('.EditGroupBTN').on('click', function() {
                let groupid = $(this).data('id');

                console.log("Group ID:", groupid);
                $("#loading_bg").show();

                $.ajax({
                    url: '/accountgroupsub/' + groupid + '/get-edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert('Error: ' + response.message);
                            $("#loading_bg").hide();
                            return;
                        }

                        let editData = response.editData;

                        // Fill the form fields
                        $('#editGroupModal #edit_title').val(editData.title);
                        $('#editGroupModal #edit_group_id').val(editData.group_id).trigger(
                            'change');

                        // Set the form's action dynamically
                        $('#editGroupForm').attr('action', '/accountgroupsub-update/' + editData
                            .id);

                        $("#loading_bg").hide();
                        $('#editGroupModal').modal('show');
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
            $("#btnSubmit").click(function() {
                setTimeout(function() {
                    disableButton();
                }, 0);
            });

            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>

  <script>
        $(document).ready(function() {
            function setManualWidths() {
                var $table = $('.table-fixed-header');
                var $theadTh = $table.find('thead th');
                var columnWidths = [200, 200, 30, 30]; // 👈 define widths here in px

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

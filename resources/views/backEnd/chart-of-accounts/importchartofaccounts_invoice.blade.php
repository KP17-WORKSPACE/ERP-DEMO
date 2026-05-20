@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>



    <div class="content-container col-12">
        
           
       
                    <h4 style="position: fixed; margin-top: 7px;">
                        Chartofaccounts Invoice Import
                    </h4>
                  <div class="purchase-order-content-header-right">

            <input type="text" class="form-control w-25 rounded" id="smart_search" name="smart_search"
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
                #smart_search_list {
                    display: none;
                    position: fixed;
                    top: 80px;
                    left: 50%;
                    transform: translate(-50%);
                    width: 95%;
                    max-height: 90vh;
                    overflow-y: auto;
                    background: #fff;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    padding: 10px;
                    z-index: 999;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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


                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('accountgroupsub-add') }}"><i
                                class="ico icon-outline-document-text title-15 me-2"></i> Group</a></li>
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
                                            href="{{ url('chartofaccounts-add') }}"><i
                                                class="ico icon-outline-move-to-folder title-15 me-2"></i> Account Move</a>
                                    </li>
                                    <li><a class="dropdown-item d-flex align-items-center"
                                            href="{{ url('chartofaccounts-add-sub') }}"><i
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
             


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::open(['class' => 'form-horizontal', 'url' => 'chartofaccounts-import-invoice-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <div class="boxed-formctrl">
                                    <div class="add-visitor">
                                        <div class="row mb-10">
                                            <div class="col-lg-12">
                                                @if (session()->has('message-success'))
                                                    <div class="alert alert-success mb-20">
                                                        {{ session()->get('message-success') }}
                                                    </div>
                                                @elseif(session()->has('message-danger'))
                                                    <div class="alert alert-danger">
                                                        {{ session()->get('message-danger') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Choose File<span>*.csv</span> (<a
                                                            href="{{ url('public/uploads/product_upload/chartofaccounts_invoice_import_sample_file_dmy.csv') }}"
                                                            target="_blank">Sample File</a>)</label>
                                                    <input class="form-control" type="file" accept=".csv"
                                                        name="import_file" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 text-start">
                                                <div class="input-effect">

                                                    <div class="d-flex justify-content-start gap-2 mt-4">
                                                        <button class="btn btn-light">
                                                            <i class="ico icon-outline-import" style="font-size: 16px"></i> Import 
                                                        </button>
                                                        @if (count($data) > 0)
                                                            <a href="{{ url('chartofaccounts-import-invoice-clear') }}"
                                                                class="btn btn-light">Clear Data</a>
                                                        @endif
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-hover" id="long-list" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="200px">Account Code</th>
                                            <th>Account Name</th>
                                            <th class="text-center" width="150px">Invoice No</th>
                                            <th class="text-center" width="150px">Invoice Date</th>
                                            <th width="150px" class="text-end">Debit Amount</th>
                                            <th width="150px" class="text-end">Credit Amount</th>
                                            <th class="text-center" width="150px" class="">PO No</th>
                                            <th width="200px" class="">Payment Terms</th>
                                            <th class="text-center" width="100px" class="">Due Date</th>
                                            <th class="text-center" width="100px" class="">Deal Id</th>
                                            <th class="text-center" width="100px" class="">Bill No</th>
                                            <th class="text-center" width="100px" class="">Bill Date</th>
                                            <th width="100px" class="">Sales Person</th>
                                        </tr>
                                    </thead>

                                    <style>
                                        .bg-warning-custom {
                                            background-color: #fff3cd !important;
                                        }
                                    </style>

                                    <tbody>
                                        @if (count($data) > 0)
                                            @foreach ($data as $value)
                                                @php
                                                    $account_id = $account_name
                                                        ->where('account_code', $value->account_code)
                                                        ->max('id');
                                                @endphp

                                                <tr @if ($account_id == 0) class="bg-warning-custom" @endif>
                                                    <td class="text-center">{{ @$value->account_code }}</td>
                                                    <td>{{ @$value->account_name }}</td>
                                                    <td class="text-center">{{ @$value->invoice_no }}</td>
                                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->invoice_date)) }}</td>
                                                    <td class="text-end">
                                                        {{ @App\SysHelper::com_curr_format(@$value->debit_amount, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ @App\SysHelper::com_curr_format(@$value->credit_amount, 2, '.', ',') }}
                                                    </td>
                                                    <td class="text-center"> {{ @$value->po_no }}</td>
                                                    <td>{{ @$value->payment_terms }}</td>
                                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->due_date)) }}</td>
                                                    <td class="text-center">{{ @$value->deal_id }}</td>
                                                    <td class="text-center">{{ @$value->bill_no }}</td>
                                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->bill_date)) }}</td>
                                                    <td>{{ @$value->sales_person }}</td>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            @if (count($data) > 0)
                                <div class="col-lg-12 text-center d-flex justify-content-center">
                                    {{ Form::open(['class' => 'form-horizontal', 'url' => 'chartofaccounts-import-invoice-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                    @if (session()->has('message-success'))
                                        <div class="alert alert-success mb-20">
                                            {{ session()->get('message-success') }}
                                        </div>
                                    @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                    @endif

                                    <button class="btn btn-light" type="submit">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                                    </button>
                                </div>
                                {{ Form::close() }}
                        </div>
                        @endif

                    </div>
                </div>




           
      
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Stop user to press enter in textbox
            $("input:text").keypress(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endsection

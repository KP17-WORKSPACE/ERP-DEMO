@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp



    <div class="content-container col-9 page-chart-of-accounts">
        <h4 style="position: fixed; margin-top: 7px;">Chart of Accounts</h4>
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

        <style>
            .list-group-item {
                cursor: pointer;
                padding: 6px 10px;
             
            }

            .list-group-item:hover {
                background: #f8f9fa;
            }

            h6 {
                font-size: 13px;
                font-weight: bold;
                margin-bottom: 5px;
            }

    .inactive { background: #d9d9d9;}
            
     .active-li {
        background-color: #49925826 !important;
    }
    .list-group-item:focus,
    .list-group-item:active {
        background-color: inherit !important;
        box-shadow: none !important;
    }
</style>


<script>
    function selectItem(element, cssSelector) {
        document.querySelectorAll(cssSelector).forEach(li => {
            li.classList.remove("active-li");
        });
        void element.offsetWidth;
        element.classList.add("active-li");
    }
</script>

        <div class="row">

            {{-- Layer 1: Account Groups --}}
            <div class="col-2 border-end">
                <h6 class="px-2 py-1 border-bottom text-center">Heads</h6>
                <ul class="list-group">
                    @foreach ($accountgroup as $g)
                        <li class="list-group-item heads d-flex justify-content-between align-items-center"
                            onclick="selectItem(this,'.heads');  showLayer(2, 'group{{ $g->id }}')">
                            <span>{{ $g->title }}</span>
                            <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Layer 2: Sub Groups --}}
            <div class="col-2 border-end tab-content"> 
                @foreach ($accountgroup as $g)
                    @php
                        $subs = App\SysAccountGroupSub::where('group_id', $g->id)->where('status', 1)->get();
                    @endphp
                    <div class="tab-pane fade" id="group{{ $g->id }}">
                       


                        <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                            <h6 class="mb-0 text-center flex-grow-1">
                                Groups
                            </h6>
                            <!-- Compact button to open modal -->
                            <button type="button" class="btn btn-sm brn-light" data-bs-target="#GroupTableModal" data-bs-toggle="modal">
                                <i class="ico icon-outline-document-text title-15"></i>
                            </button>
                        </div>

                        <ul class="list-group">
                            @foreach ($subs as $s)
                                <li class="list-group-item groups d-flex justify-content-between align-items-center"
                                    onclick="selectItem(this,'.groups'); showLayer(3, 'sub{{ $s->id }}')">
                                    <span class="truncate-text-custom">{{ $s->title }}</span>
                                    <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            {{-- Layer 3: Sub Group2 --}}
            <div class="col-2 border-end tab-content">
                @foreach ($accountgroup as $g)
                    @php $subs = App\SysAccountGroupSub::where('group_id', $g->id)->where('status',1)->get(); @endphp
                    @foreach ($subs as $s)
                        @php $subs2 = App\SysAccountGroupSub2::where('sub_id',$s->id)->where('status',1)->get(); @endphp
                        <div class="tab-pane fade" id="sub{{ $s->id }}">
                             <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                            <h6 class="mb-0 text-center flex-grow-1">
                               Sub Groups
                            </h6>
                            <!-- Compact button to open modal -->
                            <button type="button" class="btn btn-sm brn-light" data-bs-target="#SubGroupTableModal" data-bs-toggle="modal">
                                <i class="ico icon-outline-document-text title-15"></i>
                            </button>
                        </div>
                            <ul class="list-group">
                                @foreach ($subs2 as $s2)
                                    <li class="list-group-item subgroups d-flex justify-content-between align-items-center"
                                        onclick="selectItem(this,'.subgroups'); showLayer(4, 'sub2{{ $s2->id }}')">
                                        <span class="truncate-text-custom">{{ $s2->title }}</span>
                                        <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Layer 4: Accounts --}}
            <div class="col-3 border-end tab-content">
                @foreach ($accountgroup as $g)
                    @php $subs = App\SysAccountGroupSub::where('group_id',$g->id)->where('status',1)->get(); @endphp
                    @foreach ($subs as $s)
                        @php $subs2 = App\SysAccountGroupSub2::where('sub_id',$s->id)->where('status',1)->get(); @endphp
                        @foreach ($subs2 as $s2)
                            @php
                                $account = App\SysChartofAccounts::where([
                                    'subgroup2' => $s2->id,
                                    'main_account_id' => 0,
                                ])
                                    ->whereRaw("find_in_set($com_id,company_access)")->orderby('account_name','asc')
                                    ->get();
                            @endphp
                            <div class="tab-pane fade" id="sub2{{ $s2->id }}">
                                 <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                                    <h6 class="mb-0 text-center flex-grow-1">
                                    Accounts
                                    </h6>
                                    <!-- Compact button to open modal -->
                                    <button type="button" class="btn btn-sm accountsmodalbtn">
                                        <i class="ico icon-outline-document-text title-15"></i>
                                    </button>
                                </div>
                                <ul class="list-group">
                                    @foreach ($account as $a)
                                        <li class="list-group-item accounts d-flex justify-content-between align-items-center @if($a->status!=1) inactive @endif"
                                            onclick="selectItem(this,'.accounts'); showLayer(5, 'acc{{ $a->id }}')">
                                            <span class="truncate-text-custom">{{ $a->account_code }} - {{ $a->account_name }}</span>
                                            <i style="font-size: 16px" class="ico icon-outline-alt-arrow-right"></i>

                                        </li>
                                    @endforeach
                                    @if ($account->isEmpty())
                                        <li class="list-group-item text-muted">No Accounts</li>
                                    @endif
                                </ul>
                            </div>
                        @endforeach
                    @endforeach
                @endforeach
            </div>

            {{-- Layer 5: Sub Accounts --}}
            <div class="col-3 tab-content">
                @foreach ($accountgroup as $g)
                    @php $subs = App\SysAccountGroupSub::where('group_id',$g->id)->where('status',1)->get(); @endphp
                    @foreach ($subs as $s)
                        @php $subs2 = App\SysAccountGroupSub2::where('sub_id',$s->id)->where('status',1)->get(); @endphp
                        @foreach ($subs2 as $s2)
                            @php
                                $accounts = App\SysChartofAccounts::where([
                                    'subgroup2' => $s2->id,
                                    'main_account_id' => 0,
                                ])
                                    ->whereRaw("find_in_set($com_id,company_access)")->orderby('account_name','asc')
                                    ->get();
                            @endphp
                            @foreach ($accounts as $a)
                                @php $subacc = $account_sub->where('main_account_id',$a->id); @endphp
                                <div class="tab-pane fade" id="acc{{ $a->id }}">
                                      <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom">
                                    <h6 class="mb-0 text-center flex-grow-1">
                                    Sub Accounts
                                    </h6>
                                    <!-- Compact button to open modal -->
                                    <button type="button" class="btn btn-sm brn-light" data-bs-target="#SubAccountTableModal" data-bs-toggle="modal">
                                        <i class="ico icon-outline-document-text title-15"></i>
                                    </button>
                                </div>
                                    <ul class="list-group">
                                        @foreach ($subacc as $sa)
                                            <li class="list-group-item @if($sa->status!=1) inactive @endif">
                                                 <span class="truncate-text-custom">{{ $sa->account_code }} - {{ $sa->account_name }}</span>     
                                            </li>
                                        @endforeach
                                        @if ($subacc->isEmpty())
                                            <li class="list-group-item text-muted">No Sub Accounts</li>
                                        @endif
                                    </ul>
                                </div>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </div>

        </div>








  
         <div id="AccountTableContent" class=" p-4">
                  
            </div>
   

    {{-- <div class="modal fade" id="AccountTableModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="AccountTableLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AccountTableLabel">Accounts</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded here dynamically -->
                <div id="AccountTableContent" class=" p-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div> --}}



    </div>

    <script>
        function showLayer(level, id) {
            // Hide all tab-panes in this level
            document.querySelectorAll(`.tab-content:nth-of-type(${level}) .tab-pane`)
                .forEach(el => el.classList.remove('show', 'active'));

            // Also hide all deeper levels
            for (let i = level + 1; i <= 5; i++) {
                document.querySelectorAll(`.tab-content:nth-of-type(${i}) .tab-pane`)
                    .forEach(el => el.classList.remove('show', 'active'));
            }

            // Show selected tab-pane
            let target = document.getElementById(id);
            if (target) {
                target.classList.add('show', 'active');
            }
        }
    </script>




    </div>







    {{-- *********** --}}



    <section class="admin-visitor-area mr-2 ml-2">
        <div class="container-fluid p-0">

            <div class="row">

                <div class="col-lg-12">


                    <div class="row">

                        <div class="col-lg-12">
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
                            {{-- <tr>
                                        <th> @lang('Account Name')</th>
                                        <th> @lang('Account Type')</th>
                                        <th> @lang('Status')</th>
                                        <th></th>
                                    </tr> --}}
                            {{-- <a  data-toggle="collapse" href="#collapseExample">Link with href</a>
                                    <div class="collapse" id="collapseExample">
                                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.
                                    </div> --}}

                            @if (isset($accountgroup) && 1 == 2)
                                <?php $a = 1; ?>
                                @foreach ($accountgroup as $value)
                                    <tr style="background-color: #000000 !important;">
                                        <td class=" text-white"><span class="ti-arrow-right"></span>&nbsp;&nbsp;&nbsp;<b>
                                                <a class="text-white" data-toggle="collapse"
                                                    href="#collapseExample1-{{ $a }}">{{ @$value->title }}</a></b>
                                        </td>
                                    </tr>
                                    @php
                                        $accountgroupsub = @App\SysAccountGroupSub::where('group_id', @$value->id)
                                            ->where('status', 1)
                                            ->get();
                                    @endphp
                                    @if (isset($accountgroupsub))
                                        <?php $b = 1; ?>
                                        @foreach ($accountgroupsub as $value2)
                                            <tr class="collapse show" id="collapseExample1-{{ $a }}">
                                                <td>&nbsp;&nbsp;&nbsp;<span
                                                        class="ti-arrow-right"></span>&nbsp;&nbsp;&nbsp;<b>
                                                        {{ @$value2->title }}</b></td>
                                            </tr>

                                            @php
                                                $accountgroupsub2 = @App\SysAccountGroupSub2::where(
                                                    'sub_id',
                                                    @$value2->id
                                                )
                                                    ->where('status', 1)
                                                    ->get();
                                            @endphp
                                            @if (isset($accountgroupsub2))
                                                <?php $i = 1; ?>
                                                @foreach ($accountgroupsub2 as $value4)
                                                    <tr>
                                                        <td><a data-toggle="collapse"
                                                                href="#collapseExample2-{{ $a }}-{{ $b }}">
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{ @$value4->title }}</b></a>
                                                        </td>
                                                    </tr>
                                                    @php $account = @App\SysChartofAccounts::where(['subgroup2' => @$value4->id,'status' => 1])->get() @endphp
                                                    @if (isset($account))
                                                        <?php $i = 1; ?>
                                                        @foreach ($account as $value3)
                                                            <tr
                                                                id="collapseExample2-{{ $a }}-{{ $b }}">
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                    {{ @$value3->account_code }}&nbsp;-&nbsp;{{ @$value3->account_name }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr class="collapse"
                                                            id="collapseExample2-{{ $a }}-{{ $b }}">
                                                            <td></td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @else
                                                @php $account = @App\SysChartofAccounts::where(['subgroup2' => @$value4->id,'status' => 1])->get() @endphp
                                                @if (isset($account))
                                                    <?php $i = 1; ?>
                                                    @foreach ($account as $value3)
                                                        <tr class="collapse"
                                                            id="collapseExample2-{{ $a }}-{{ $b }}">
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                {{ @$value3->account_code }}&nbsp;-&nbsp;{{ @$value3->account_name }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="collapse"
                                                        id="collapseExample2-{{ $a }}-{{ $b }}">
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endif

                                            <?php $b++; ?>
                                        @endforeach
                                    @endif
                                    <?php $a++; ?>
                                @endforeach
                            @endif



                            <style>
                                .mb-0>a {
                                    display: block;
                                    color: #000000;
                                    position: relative;
                                }

                                .mb-0>a:after {
                                    content: "\f078";
                                    /* fa-chevron-down */
                                    font-family: 'FontAwesome';
                                    position: absolute;
                                    right: 0;
                                }

                                .mb-0>a[aria-expanded="true"]:after {
                                    content: "\f077";
                                    /* fa-chevron-up */
                                }

                                .card-header {
                                    padding: 7px 10px 5px 10px;
                                }

                                .card-header h5 {
                                    font-size: 12px;
                                }

                                .card-body {
                                    padding: 5px 10px;
                                }

                                .card {
                                    background-color: #e5e5de;
                                    box-shadow: none;
                                    border-radius: 5px;
                                    margin-bottom: 5px;
                                }

                                .level4 {
                                    padding-left: 10px;
                                    font-weight: normal;
                                    color: #000000
                                }
                            </style>


                            {{-- @if (isset($accounts))
                                        <tr class="bg-secondary">
                                            <td colspan="4" class=" text-white"><b>Accounts</b></td>
                                        </tr>
                                        @foreach ($accounts as $value)
                                            <tr>
                                                <td><b>{{ @$value->account_name }}</b></td>
                                                <td>{{ @$value->accounttype->title }}</td>
                                                <td>@if (@$value->status == 1) Active @else Inactive @endif</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">@lang('lang.select')</button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item" href="{{ url('chartofaccounts/' . @$value->id . '/edit') }}"> <span class="ti-pencil-alt"></span> @lang('Edit Account')</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif --}}
                            {{-- @if (isset($supplier))
                                        <tr class="bg-secondary">
                                            <td colspan="4" class=" text-white"><b>Suppliers</b></td>
                                        </tr>
                                        @foreach ($supplier as $value)
                                            <tr>
                                                <td><b>{{ @$value->account_name }}</b></td>
                                                <td>{{ @$value->accounttype->title }}</td>
                                                <td>@if (@$value->status == 1) Active @else Inactive @endif</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">@lang('lang.select')</button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item" href="{{ url('chartofaccounts/' . @$value->id . '/edit') }}"> <span class="ti-pencil-alt"></span> @lang('Edit Account')</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif --}}
                            {{-- @if (isset($customer))
                                        <tr class="bg-secondary">
                                            <td colspan="4" class=" text-white"><b>Customers</b></td>
                                        </tr>
                                        @foreach ($customer as $value)
                                            <tr>
                                                <td><b>{{ @$value->account_name }}</b></td>
                                                <td>{{ @$value->accounttype->title }}</td>
                                                <td>@if (@$value->status == 1) Active @else Inactive @endif</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">@lang('lang.select')</button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item" href="{{ url('chartofaccounts/' . @$value->id . '/edit') }}"> <span class="ti-pencil-alt"></span> @lang('Edit Account')</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif --}}
                            {{-- @if (isset($suppliers))
                                        @foreach ($suppliers as $value)
                                            <tr>
                                                <td><b>{{ @$value->name }}</b></td>
                                                <td>Supplier</td>
                                                <td>Creditor</td>
                                                <td>@if (@$value->status == 1) Active @else Inactive @endif</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">@lang('lang.select')</button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item" href="{{ url('view-supplier/' . @$value->id) }}"> <span class="ti-file"></span>  @lang('View Supplier')</a>
                                                                <a class="dropdown-item" href="{{ url('supplier-edit/' . @$value->id) }}"> <span class="ti-pencil-alt"></span> @lang('Edit Supplier')</a>
                                                                <a class="dropdown-item" href="{{ url('#') }}"> <span class="ti-move"></span> @lang('Move Supplier')</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @if (isset($customers))
                                        @foreach ($customers as $value)
                                            <tr>
                                                <td><b>{{ @$value->name }}</b></td>
                                                <td>Customer</td>
                                                <td>Creditor</td>
                                                <td>@if (@$value->status == 1) Active @else Inactive @endif</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">@lang('lang.select')</button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="dropdown-item" href="{{ url('view-customer/' . @$value->id) }}"> <span class="ti-file"></span>  @lang('View Customer')</a>
                                                                <a class="dropdown-item" href="{{ url('customer-edit/' . @$value->id) }}"> <span class="ti-pencil-alt"></span> @lang('Edit Customer')</a>
                                                                <a class="dropdown-item" href="{{ url('#') }}"> <span class="ti-move"></span> @lang('Move Customer')</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>









<script>
    $(document).ready(function() {

        $('.accountsmodalbtn').on('click', function() {
         console.log("clicked")
      
        
            // Load content via AJAX
            $.ajax({
                url: '/load-account-modal-data',
                method: 'GET',
                beforeSend: function() {
                 $("#loading_bg").show();
                    
                },
                success: function(response) {
                    $('#AccountTableContent').html(response);
                        // Show the modal first
                     $('#AccountTableModal').modal('show');
                 $("#loading_bg").hide();

                },
                error: function(xhr, status, error) {
                    $('#AccountTableContent').html('<div class="alert alert-danger">Failed to load data. Please try again later.</div>');
                 $("#loading_bg").hide();

                }
            });
        });

    });
</script>

@endsection

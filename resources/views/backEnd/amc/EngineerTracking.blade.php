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

                localStorage.setItem('listViewEngTrack', 'long');
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

                localStorage.setItem('listViewEngTrack', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewEngTrack');
            if (savedView === 'long') {
                isFullList = false; // so that toggling once activates full view
                list_style_new();
            } else {
                // Default to short view
                isFullList = true; // so that toggling once activates short view
                list_style_new();
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewEngTrack', 'short');
                });
            });



        });
    </script>



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Service Request List
            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-list', 'method' => 'POST', 'id'
        => 'crm-amc-list']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_amc_id" id="search_record" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>



                {{-- {{ Form::close() }} --}}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Service Request List
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>

                        <ul class="dropdown-menu" style="">

                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#ModalAddServiceRequest"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-add-square text-success title-15 me-2"></i>AMC Request</a>
                            </li>

                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#ModalProfessionalServicesRequest"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-add-square text-success title-15 me-2"></i>Project
                                    Request</a>
                            </li>

                            <li>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#addPreSalesRequest"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-add-square text-success title-15 me-2"></i>Pre-Sales
                                    Request</a>
                            </li>
                        </ul>
                    </div>





                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-engineer-tracking', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                        <div class="row">

                            <div class="col-1">
                                <label class="form-label">Track ID</label>
                                <input class="form-control" id="search_track_id" type="text" autocomplete="off"
                                    name="search_track_id" value="{{ $ctrl_track_id }}">
                            </div>

                            <div class="col-1">
                                <label class="form-label">Deal ID</label>
                                <input class="form-control" id="search_deal_id" type="text" autocomplete="off"
                                    name="search_deal_id" value="{{ $ctrl_deal_id }}">

                            </div>

                            <div class="col-2">
                                <label class="form-label">Customer Name</label>
                                <select class="form-control js-example-basic-single" name="search_customer_name"
                                    id="search_customer_name">
                                    <option value="">-Select-</option>
                                    @foreach ($customer as $value)
                                        <option @if ($ctrl_customer == $value->id) selected @endif
                                            value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-2">
                                <label class="form-label">Engineer</label>
                                <select class="form-control" name="search_engineer" id="search_engineer">
                                    <option value="">Select</option>
                                    @if (count($salesperson) > 0)
                                        @foreach ($salesperson as $list)
                                            <option @if ($ctrl_engineer == $list->user_id)  @endif value="{{ $list->user_id }}">
                                                {{ $list->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>

                            <div class="col-1-5">
                                <label class="form-label">Service Date From</label>
                                <input class="form-control date-picker" id="search_from_date" type="text"
                                    autocomplete="off" name="search_from_date"
                                    value="{{ !empty($ctrl_from_date) ? date('d-m-Y', strtotime($ctrl_from_date)) : '' }}">
                            </div>

                            <div class="col-1-5">
                                <label class="form-label">Service Date To</label>
                                <input class="form-control date-picker" id="search_to_date" type="text"
                                    autocomplete="off" name="search_to_date"
                                    value="{{ !empty($ctrl_to_date) ? date('d-m-Y', strtotime($ctrl_to_date)) : '' }}">


                            </div>

                            <div class="col-1-5">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="search_status" id="search_status">
                                    <option value="">All</option>
                                    <option value="1" @if ($ctrl_status == 1) selected @endif>Pending
                                    </option>
                                    <option value="2" @if ($ctrl_status == 2) selected @endif>Completed
                                    </option>
                                </select>


                            </div>


                            <div class="col-1-5 filter-field d-none">
                                <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($data) > 0)
                    @foreach ($data as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link data-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" data-type="{{ $item->type }}" id="purchase-order-1-tab"
                                data-bs-toggle="tab" data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->cust_name }}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ @$item->doc_number }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            @if (@$item->work_date != null)
                                                {{ date('d/m/Y', strtotime(@$item->work_date)) }}
                                            @elseif(@$item->service_date != null)
                                                {{ date('d/m/Y', strtotime(@$item->service_date)) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @$item->deal_code }}

                                        </div>
                                    </div>
                                   
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    No Records
                @endif
            </ul>


            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th width="100px">@lang('Track No')</th>
                            <th width="100px">@lang('Deal ID')</th>
                            <th width="100px">@lang('Date')</th>
                            <th class="text-start">@lang('Customer Name')</th>
                            <th class="text-start">@lang('Enginer')</th>

                            <th width="100px">@lang('Service Date')</th>
                            <th width="100px">@lang('Time From')</th>
                            <th width="100px">@lang('Time To')</th>
                            <th width="100px">@lang('No. of Hrs')</th>
                            <th width="100px">@lang('Status')</th>
                            <th width="140px">@lang('Stage')</th>

                            <th class="text-center" style="width: 60px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>
                        @if (count($data))
                            @foreach ($data as $dt)
                                <tr>
                                    <td data-id="{{ $dt->id }}" data-type="{{ $dt->type }}"
                                        onclick="list_style_new()" class="text-center data-item">
                                        <a href="#">

                                            {{ $dt->doc_number }}



                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('crm-deals/show', $dt->deal_id) }}">
                                            {{ @$dt->deal_code }}</a>
                                    </td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($dt->date)) }}</td>
                                    <td>{{ $dt->cust_name }}</td>

                                    @if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32)
                                        <td>{{ Auth::user()->full_name }}</td>
                                    @else
                                        <?php
                                        $engineername = '';
                                        if ($dt->comment_by != null) {
                                            $s = $staff->where('user_id', $dt->comment_by)->pluck('full_name');
                                            $engineername = $s[0];
                                        } elseif ($dt->service_engineer != '') {
                                            $st = explode(',', $dt->service_engineer);
                                            if (count($st) > 0) {
                                                foreach ($st as $u) {
                                                    $s = $staff->where('user_id', $u)->pluck('full_name');
                                                    if ($engineername == '') {
                                                        $engineername .= $s[0];
                                                    } else {
                                                        $engineername .= ', ' . $s[0];
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <td>{{ $engineername }}</td>
                                    @endif

                                    <td class="text-center">
                                        @if ($dt->work_date != null)
                                            {{ date('d/m/Y', strtotime($dt->work_date)) }}
                                        @elseif($dt->service_date != null)
                                            {{ date('d/m/Y', strtotime($dt->service_date)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($dt->work_time_from != null)
                                            {{ date('h:i A', strtotime($dt->work_time_from)) }}
                                        @elseif($dt->service_time != null)
                                            {{ date('h:i A', strtotime($dt->service_time)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($dt->work_time_to != null)
                                            {{ date('h:i A', strtotime($dt->work_time_to)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($dt->tim != null)
                                            @if ($dt->tim < 60)
                                                {{ $dt->tim }} Min
                                            @else
                                                {{ @App\SysHelper::com_curr_format($dt->tim / 60, 2, ':', '') }} Hrs
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">

                                        @if (@$dt->type == 'AMC')
                                            @if (@$dt->amc_status == 5)
                                                <span class="text-success">Completed</span>
                                            @else
                                                <span class="text-danger">Pending</span>
                                            @endif
                                        @endif

                                        @if (@$dt->type == 'PS')
                                            @if (@$dt->ps_status == 2)
                                                <span class="text-success">Completed</span>
                                            @else
                                                <span class="text-danger">Pending</span>
                                            @endif
                                        @endif

                                        @if ($dt->type == 'PRESALES')
                                            @if (@$dt->status == 3)
                                                <span class="text-success">Completed</span>
                                            @elseif(@$dt->status == 2)
                                                Added
                                            @elseif(@$dt->status == 4)
                                                Cancel
                                            @else
                                                <span class="text-danger">Pending</span>
                                            @endif
                                        @endif



                                    </td>

                                    <td class="text-center">
                                        <?php
                                        $deal_stage = '';
                                        $track = $deal_track->where('deal_id', $dt->deal_id);
                                        if (count($track) > 0) {
                                            foreach ($track as $tr) {
                                                $deal_stage = $deal_stage = @App\SysHelper::deal_track_status3($tr->receivables, $tr->delivery, $tr->invoice, $tr->purchease, $tr->sales, $tr->accounts);
                                            }
                                        } else {
                                            $dl = $deals->where('id', $dt->deal_id);
                                            if (count($dl) > 0) {
                                                foreach ($dl as $d) {
                                                    $deal_stage = @App\SysHelper::deal_stage($d->stage);
                                                }
                                            }
                                        }
                                        ?>
                                        {!! $deal_stage !!}
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            @if ($dt->type == 'AMC')
                                                <button class="btn btn-sm btn-light" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#amccomments_{{ $dt->id }}"><i
                                                        class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i></button>
                                            @endif

                                            @if ($dt->type == 'PS')
                                                <button class="btn btn-sm btn-light" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#pscomments_{{ $dt->id }}"><i
                                                        class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i></button>
                                            @endif

                                            @if ($dt->type == 'PRESALES')
                                                <input type="hidden" id="pr_doc_number_{{ @$dt->id }}"
                                                    value="{{ @$dt->doc_number }}">
                                                <input type="hidden" id="pr_customer_id_{{ @$dt->id }}"
                                                    value="{{ @$dt->customer_id }}">
                                                <input type="hidden" id="pr_date_{{ @$dt->id }}"
                                                    value="{{ date('Y-m-d', strtotime(@$dt->created_at)) }}">
                                                <input type="hidden" id="pr_customer_name_{{ @$dt->id }}"
                                                    value="{{ @$dt->cust_name }}">
                                                <input type="hidden" id="pr_contact_person_{{ @$dt->id }}"
                                                    value="{{ @$dt->contact_person }}" />
                                                <input type="hidden" id="pr_mobile_{{ @$dt->id }}"
                                                    value="{{ @$dt->mobile }}" />
                                                <input type="hidden" id="pr_location_of_work_{{ @$dt->id }}"
                                                    value="{{ @$dt->site_name }}" />
                                                <input type="hidden" id="pr_support_date_{{ @$dt->id }}"
                                                    value="{{ @$dt->support_date ? date('d/m/Y', strtotime($dt->support_date)) : '' }}" />
                                                <input type="hidden" id="pr_time_from_{{ @$dt->id }}"
                                                    value="{{ @$dt->time_from }}" />
                                                <input type="hidden" id="pr_work_{{ @$dt->id }}"
                                                    value="{{ @$dt->remarks }}" />
                                                <input type="hidden" id="pr_support_person_{{ @$dt->id }}"
                                                    value="{{ @$dt->support_person_id }}" />
                                                <input type="hidden" id="pr_presales_status_id{{ @$dt->id }}"
                                                    value="{{ @$dt->scs_status }}">

                                                <button class="btn btn-sm btn-light" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#presalescomments_{{ $dt->id }}"><i
                                                        class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i></button>
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
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {

                        var id = $(this).data('id');
                        var type = $(this).data('type');



                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        if (type == 'AMC') {

                            // Update the browser URL to include selected ID (without reloading)
                            var newUrl = "{{ url('crm-engineer-tracking') }}/amc/" + id;
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);

                            var action = "{{ URL::to('crm-amc-service-request-detail') }}/" + id;
                            $('#loading_bg').show();

                            $.ajax({
                                url: action,
                                method: 'GET',
                                success: function(response) {
                                    $('#amc-details').html(response);
                                },
                                error: function() {
                                    $('#amc-details').html(
                                        '<p class="text-danger">No Details Available.</p>');
                                },
                                complete: function() {
                                    $('#loading_bg')
                                        .hide(); // Always hide loader after request completes
                                }
                            });
                        } else if (type == 'PS') {

                                 // Update the browser URL to include selected ID (without reloading)
                            var newUrl = "{{ url('crm-engineer-tracking') }}/ps/" + id;
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);

                            var action = "{{ URL::to('crm-ps-service-detail') }}/" + id;
                            $('#loading_bg').show();

                            $.ajax({
                                url: action,
                                method: 'GET',
                                success: function(response) {

                                    $('#amc-details').html(response);
                                },
                                error: function() {
                                    $('#amc-details').html(
                                        '<p class="text-danger">No Details Available.</p>');
                                },
                                complete: function() {
                                    $('#loading_bg')
                                        .hide(); // Always hide loader after request completes
                                }
                            });

                        } else if (type == 'PRESALES') {
                                     var newUrl = "{{ url('crm-engineer-tracking') }}/presales/" + id;
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);
                            var action = "{{ URL::to('crm-deal-support') }}/" + id + "/view";
                            $('#loading_bg').show();

                            $.ajax({
                                url: action,
                                method: 'GET',
                                success: function(response) {

                                    $('#amc-details').html(response);
                                },
                                error: function() {
                                    $('#amc-details').html(
                                        '<p class="text-danger">No Details Available.</p>');
                                },
                                complete: function() {
                                    $('#loading_bg')
                                        .hide(); // Always hide loader after request completes
                                }
                            });
                        }

                        // Update the browser URL to include selected ID (without reloading)
                        // var newUrl = "{{ url('crm-amc-service-request-list') }}/" + id;
                        // window.history.pushState({
                        //     path: newUrl
                        // }, '', newUrl);


                    });
                });
            </script>
            {{-- 
            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {

                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        // var newUrl = "{{ url('crm-ps-service-list-req') }}/" + id;
                        // window.history.pushState({
                        //     path: newUrl
                        // }, '', newUrl);


                    });
                });
            </script>

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {

                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        // var newUrl = "{{ url('crm-deal-support-requested-list') }}/" + id;
                        // window.history.pushState({
                        //     path: newUrl
                        // }, '', newUrl);


                    });
                });
            </script> --}}





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="amc-details">
                @if (!empty($selectedRecord) && is_array($selectedRecord))
                    @if ($firstRecord_type == 'AMC')
                        @include('backEnd.amc.DealAmcServiceRequestDetail', $selectedRecord)
                    @elseif($firstRecord_type == 'PS')
                        @include('backEnd.amc.DealAmcTrackServiceReqDetail', $selectedRecord)
                    @elseif($firstRecord_type == 'PRESALES')
                        @include('backEnd.crm.DealSupport', $selectedRecord)
                    @endif
                @else
                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">


                        {{-- <div class="text-center mb-4">
                            <div data-bs-toggle="modal" data-bs-target="#AddAmcModal"
                                class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" data-bs-toggle="modal"
                                data-bs-target="#AddAmcModal">AMC</h1>

                        </div> --}}

                    </div>
                @endif
            </div>


        </div>
    </div>











    @if (count($amc_list) > 0)
        @foreach ($amc_list as $amc)
            <div class="modal  fade" id="amccomments_{{ $amc->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">AMC - Scope of Work & Comments
                                ({{ $amc->doc_number }})
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $amc_comments_data = $amc_comments->where('amc_id', $amc->id);
                                            $sw = $amc_work->where('amc_id', $amc->id);
                                        @endphp

                                        @if (count($sw) > 0)
                                            <table class="table table-hover" id="long-list" width="100%"
                                                cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="7%">No</th>
                                                        <th width="93%">Scope of Work</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sw as $w)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td>{{ $w->work }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (count($amc_comments_data) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30%">Comment</th>
                                                        <th width="15%">Work Date</th>
                                                        <th width="10%">From</th>
                                                        <th width="10%">To</th>
                                                        <th width="10%">Status</th>
                                                        <th width="25%">Engineer / Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($amc_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($cmts->work_date)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_from)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    <span class=" text-dark">Pending</span>
                                                                @else
                                                                    <span class="text-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $cmts->engineerid->full_name }} <br>
                                                                <small
                                                                    class="text-muted">{{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        @endforeach
    @endif



    @if (count($ps_list) > 0)
        @foreach ($ps_list as $ps)
            <div class="modal  fade" id="pscomments_{{ $ps->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">Project Service - Scope of Work & Comments
                                ({{ $ps->doc_number }})
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $ps_comments_data = $ps_comments->where('ps_id', $ps->id);
                                            $sw = $ps_work->where('service_id', $ps->id);
                                        @endphp


                                        @if (count($sw) > 0)
                                            <table class="table table-hover" id="long-list" width="100%"
                                                cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="7%">No</th>
                                                        <th width="93%">Scope of Work</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sw as $w)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td>{{ $w->work }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (count($ps_comments_data) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30%">Comment</th>
                                                        <th width="15%">Work Date</th>
                                                        <th width="10%">From</th>
                                                        <th width="10%">To</th>
                                                        <th width="10%">Status</th>
                                                        <th width="25%">Engineer / Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($ps_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($cmts->work_date)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_from)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    <span class="text-dark">Pending</span>
                                                                @else
                                                                    <span class="text-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $cmts->engineerid->full_name }} <br>
                                                                <small
                                                                    class="text-muted">{{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        @endforeach
    @endif


    @if (count($presales_list) > 0)
        @foreach ($presales_list as $presales)
            <div class="modal  fade" id="presalescomments_{{ $presales->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">Presales - Scope of Work & Comments
                                ({{ $presales->doc_number }})
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $presales_comments_data = $presales_comments->where(
                                                'support_id',
                                                $presales->id,
                                            );
                                            $sw = $presales_work->where('support_id', $presales->id);
                                        @endphp

                                        @if (count($sw) > 0)
                                            <table class="table table-hover" id="long-list" width="100%"
                                                cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="7%" class="text-center">No</th>
                                                        <th width="93%">Scope of Work</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sw as $w)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td>{{ $w->work }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (count($presales_comments_data) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30%">Comment</th>
                                                        <th width="15%">Work Date</th>
                                                        <th width="10%">From</th>
                                                        <th width="10%">To</th>
                                                        <th width="10%">Status</th>
                                                        <th width="25%">Engineer / Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($presales_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($cmts->work_date)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_from)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    <span class="text-dark">Pending</span>
                                                                @else
                                                                    <span class="text-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $cmts->engineerid->full_name }} <br>
                                                                <small
                                                                    class="text-muted">{{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        @endforeach
    @endif


    <div class="modal  fade" id="ModalAddServiceRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg ">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list-add', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-service-request-list-add']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add AMC Request
                        ({{ @App\SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number') }}) </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" name="deal_id" id="deal_id">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label"> Date</label>
                                    <input type="text" class="form-control date-picker" name="date" id="date"
                                        value="{{ date('d/m/Y') }}" required>
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label"> Company Name</label>
                                    <select class="form-control js-example-basic-single" name="cust_name" id="cust_name"
                                        required>
                                        <option value="">-Select-</option>
                                        @foreach ($salespersonamc as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person" id="contact_person"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile_no" id="mobile_no" required>

                                </div>


                                <div class="col-6">
                                    <label for="" class="form-label">Location Of Work</label>
                                    <input type="text" class="form-control" name="location_of_work"
                                        id="location_of_work">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input type="text" class="form-control date-picker" name="service_date"
                                        id="service_date" required min="{{ date('d/m/Y') }}">
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <select type="time" class="form-control" name="service_time" id="service_time"
                                        required onchange="check_time()">
                                        <option value="">Select</option>
                                        <option value="00:00:00">12:00 AM</option>
                                        <option value="00:30:00">12:30 AM</option>
                                        <option value="01:00:00">01:00 AM</option>
                                        <option value="01:30:00">01:30 AM</option>
                                        <option value="02:00:00">02:00 AM</option>
                                        <option value="02:30:00">02:30 AM</option>
                                        <option value="03:00:00">03:00 AM</option>
                                        <option value="03:30:00">03:30 AM</option>
                                        <option value="04:00:00">04:00 AM</option>
                                        <option value="04:30:00">04:30 AM</option>
                                        <option value="05:00:00">05:00 AM</option>
                                        <option value="05:30:00">05:30 AM</option>
                                        <option value="06:00:00">06:00 AM</option>
                                        <option value="06:30:00">06:30 AM</option>
                                        <option value="07:00:00">07:00 AM</option>
                                        <option value="07:30:00">07:30 AM</option>
                                        <option value="08:00:00">08:00 AM</option>
                                        <option value="08:30:00">08:30 AM</option>
                                        <option value="09:00:00">09:00 AM</option>
                                        <option value="09:30:00">09:30 AM</option>
                                        <option value="10:00:00">10:00 AM</option>
                                        <option value="10:30:00">10:30 AM</option>
                                        <option value="11:00:00">11:00 AM</option>
                                        <option value="11:30:00">11:30 AM</option>
                                        <option value="12:00:00">12:00 PM</option>
                                        <option value="12:30:00">12:30 PM</option>
                                        <option value="13:00:00">01:00 PM</option>
                                        <option value="13:30:00">01:30 PM</option>
                                        <option value="14:00:00">02:00 PM</option>
                                        <option value="14:30:00">02:30 PM</option>
                                        <option value="15:00:00">03:00 PM</option>
                                        <option value="15:30:00">03:30 PM</option>
                                        <option value="16:00:00">04:00 PM</option>
                                        <option value="16:30:00">04:30 PM</option>
                                        <option value="17:00:00">05:00 PM</option>
                                        <option value="17:30:00">05:30 PM</option>
                                        <option value="18:00:00">06:00 PM</option>
                                        <option value="18:30:00">06:30 PM</option>
                                        <option value="19:00:00">07:00 PM</option>
                                        <option value="19:30:00">07:30 PM</option>
                                        <option value="20:00:00">08:00 PM</option>
                                        <option value="20:30:00">08:30 PM</option>
                                        <option value="21:00:00">09:00 PM</option>
                                        <option value="21:30:00">09:30 PM</option>
                                        <option value="22:00:00">10:00 PM</option>
                                        <option value="22:30:00">10:30 PM</option>
                                        <option value="23:00:00">11:00 PM</option>
                                        <option value="23:30:00">11:30 PM</option>
                                    </select>



                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Source</label>
                                    <select class="form-control" name="source" id="source" required>
                                        <option selected value="">Select</option>
                                        <option value="Email">Email</option>
                                        <option value="Whatsapp">Whatsapp</option>
                                        <option value="Phone">Phone</option>
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="attachment">
                                </div>

                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select class="form-control js-example-basic-single" name="service_engineer[]"
                                        id="service_engineer" required multiple>
                                        @if (count($salesperson) > 0)
                                            @foreach ($salesperson as $list)
                                                <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>





                                <div class="col-12 mt-2">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="addRow" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" id="taskTable">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRow">
                                                                <i class="ico icon-outline-trash-bin-minimalistic"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>



                                </div>




                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Request
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal  fade" id="ModalProfessionalServicesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-request-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Project Service Request
                        ({{ @App\SysHelper::get_new_code('sys_crm_ps_service_table', 'PR', 'doc_number') }})</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="amc_id" id="amc_id">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" name="add_deal_id" id="add_deal_id"
                                        value="">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" class="form-control date-picker" name="date" id="add_date"
                                        value="{{ date('d/m/Y') }}">
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <select class="form-control js-example-basic-single" name="add_cust_name"
                                        id="add_cust_name" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customers_AddRequest as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>

                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person"
                                        id="add_contact_person" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile" id="add_mobile_no"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">@lang('Location of Work')</label>
                                    <input class="form-control" type="text" name="location_of_work"
                                        id="add_location_of_work" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" type="text" name="service_date"
                                        id="service_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="service_time" type="time" required
                                        name="service_time" value="">

                                </div>

                                <div class="col-9">

                                    <label for="engineer" class="form-label">Service Engineer</label>
                                    <select id="engineer" name="add_engineer[]"
                                        class="form-control js-example-basic-single" multiple>
                                        <option value="">Select</option>
                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">

                                </div>






                                <div class="col-12 mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="addRow2" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" id="taskTable2">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRow2">
                                                                <i class="ico icon-outline-trash-bin-minimalistic"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>



                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal  fade" id="addPreSalesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-sales-req-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Pre-Sales Request
                        ({{ @App\SysHelper::get_new_code('sys_crm_support', 'PS', 'doc_number') }})
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="support_id" value="0" />




                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="number" class="form-control" name="deal_id" id="deal_id" required>
                                </div>




                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <select class="form-control js-example-basic-single" name="add_cust_name"
                                        id="sales_add_cust_name" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer_salesreq as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" id="sales_add_contact_person" type="text" required
                                        name="contact_person" value="">



                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" id="sales_add_mobile_no" type="text" required
                                        name="mobile" value="">

                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input type="text" class="form-control" name="add_site_name"
                                        id="sales_add_site_name" required>


                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" id="add_service_date" type="text" required
                                        name="service_date" value="">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="add_service_time" type="time" required
                                        name="service_time" value="">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">
                                </div>


                                <div class="col-6">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select required id="add_engineer" name="add_engineer[]"
                                        class="form-control js-example-basic-single" multiple>

                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>



                                <div class="col-12 mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="addRow3" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" id="taskTable3">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRow3">
                                                                <i class="ico icon-outline-trash-bin-minimalistic"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>



                                </div>



                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>




    <div class="modal fade" id="ModalEditServiceRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-service-request-list-add']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit (<span class="font-weight-600"
                            id="edit_amc_code"></span>) </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" id="amc_deal_id_edit" class="form-control" name="deal_id">
                                </div>

                                <input type="hidden" name="amcid_edit" id="amcid_edit" />
                                <div class="col-3">
                                    <label for="" class="form-label"> Date</label>
                                    <input type="text" class="form-control date-picker" name="date"
                                        id="amc_date_edit" value="{{ date('d/m/Y') }}" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label"> Company Name</label>
                                    <select class="form-control js-example-basic-single" name="cust_name"
                                        id="amc_cust_name_edit" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person"
                                        id="amc_contact_person_edit" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile_no" id="amc_mobile_no_edit"
                                        required>

                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Location Of Work</label>
                                    <input type="text" class="form-control" name="location_of_work"
                                        id="amc_location_of_work_edit">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input type="text" class="form-control date-picker" name="service_date"
                                        id="amc_service_date_edit" required>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <select type="time" class="form-control" name="service_time"
                                        id="amc_service_time_edit" required>
                                        <option value="">Select</option>
                                        <option value="00:00:00">12:00 AM</option>
                                        <option value="00:30:00">12:30 AM</option>
                                        <option value="01:00:00">01:00 AM</option>
                                        <option value="01:30:00">01:30 AM</option>
                                        <option value="02:00:00">02:00 AM</option>
                                        <option value="02:30:00">02:30 AM</option>
                                        <option value="03:00:00">03:00 AM</option>
                                        <option value="03:30:00">03:30 AM</option>
                                        <option value="04:00:00">04:00 AM</option>
                                        <option value="04:30:00">04:30 AM</option>
                                        <option value="05:00:00">05:00 AM</option>
                                        <option value="05:30:00">05:30 AM</option>
                                        <option value="06:00:00">06:00 AM</option>
                                        <option value="06:30:00">06:30 AM</option>
                                        <option value="07:00:00">07:00 AM</option>
                                        <option value="07:30:00">07:30 AM</option>
                                        <option value="08:00:00">08:00 AM</option>
                                        <option value="08:30:00">08:30 AM</option>
                                        <option value="09:00:00">09:00 AM</option>
                                        <option value="09:30:00">09:30 AM</option>
                                        <option value="10:00:00">10:00 AM</option>
                                        <option value="10:30:00">10:30 AM</option>
                                        <option value="11:00:00">11:00 AM</option>
                                        <option value="11:30:00">11:30 AM</option>
                                        <option value="12:00:00">12:00 PM</option>
                                        <option value="12:30:00">12:30 PM</option>
                                        <option value="13:00:00">01:00 PM</option>
                                        <option value="13:30:00">01:30 PM</option>
                                        <option value="14:00:00">02:00 PM</option>
                                        <option value="14:30:00">02:30 PM</option>
                                        <option value="15:00:00">03:00 PM</option>
                                        <option value="15:30:00">03:30 PM</option>
                                        <option value="16:00:00">04:00 PM</option>
                                        <option value="16:30:00">04:30 PM</option>
                                        <option value="17:00:00">05:00 PM</option>
                                        <option value="17:30:00">05:30 PM</option>
                                        <option value="18:00:00">06:00 PM</option>
                                        <option value="18:30:00">06:30 PM</option>
                                        <option value="19:00:00">07:00 PM</option>
                                        <option value="19:30:00">07:30 PM</option>
                                        <option value="20:00:00">08:00 PM</option>
                                        <option value="20:30:00">08:30 PM</option>
                                        <option value="21:00:00">09:00 PM</option>
                                        <option value="21:30:00">09:30 PM</option>
                                        <option value="22:00:00">10:00 PM</option>
                                        <option value="22:30:00">10:30 PM</option>
                                        <option value="23:00:00">11:00 PM</option>
                                        <option value="23:30:00">11:30 PM</option>
                                    </select>



                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Source</label>
                                    <select class="form-control" name="source" id="amc_source_edit" required>
                                        <option selected value="">Select</option>
                                        <option value="Email">Email</option>
                                        <option value="Whatsapp">Whatsapp</option>
                                        <option value="Phone">Phone</option>
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="attachment">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Status</label>
                                    <select class="form-control" name="status_edit" id="amc_status_edit">
                                        <option value="2">Pending</option>
                                        <option value="5">Completed</option>
                                    </select>
                                </div>


                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select class="form-control js-example-basic-single" name="service_engineer[]"
                                        id="amc_service_engineer_edit" required multiple>
                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>





                                <div class="col-12">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="addRowEdit" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0"
                                            id="amc_taskTableEdit">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRowEdit">
                                                                <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>



                                </div>




                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>







    <div class="modal  fade" id="ModalEditProfessionalServicesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit
                        (<span class="font-weight-600" id="ps_edit_doc_number"></span>)</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="amc_id" id="ps_edit_amc_id">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" id="ps_deal_id_edit" class="form-control" name="deal_id">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" class="form-control" name="date" id="ps_edit_date"
                                        value="{{ date('d/m/Y') }}" readonly>
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <input class="form-control" id="ps_edit_cust_name" type="text" required
                                        name="cust_name" value="" readonly>

                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person"
                                        id="ps_edit_contact_person" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile" id="ps_edit_mobile"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">@lang('Location of Work')</label>
                                    <input class="form-control" type="text" name="location_of_work"
                                        id="ps_edit_location_of_work" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" type="text" name="service_date"
                                        id="ps_edit_service_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="ps_edit_service_time" type="time" required
                                        name="service_time" value="">

                                </div>

                                <div class="col-9">

                                    <label for="engineer" class="form-label">Service Engineer</label>
                                    <select id="ps_edit_engineer" name="engineer[]"
                                        class="form-control js-example-basic-single" multiple>
                                        <option></option>
                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Status</label>
                                    <select class="form-control" name="status_edit" id="ps_status_edit">
                                        <option value="1">Pending</option>
                                        <option value="2">Completed</option>
                                    </select>

                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">

                                </div>






                                <div class="col-12 mt-2">


                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="ps_addRowEdit" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>


                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0"
                                            id="ps_taskTableEdit">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm ps_deleteRow">
                                                                <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>



                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal  fade" id="EditPreSales" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit (<span id="pr_edit_doc_number_txt"></span>)
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="pre_sales_id" id="pr_pre_sales_id">




                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <input class="form-control" id="pr_cust_name" type="text" required
                                        name="cust_name" value="" readonly>
                                    <input id="pr_cust_id" type="hidden" required name="cust_id" value=""
                                        readonly>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" id="pr_contact_person" type="text" required
                                        name="contact_person" value="">


                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" id="pr_mobile" type="text" required
                                        name="mobile" value="">


                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input class="form-control" id="pr_location_of_work" type="text"
                                        autocomplete="off" required name="location_of_work" value="">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" type="text" name="service_date"
                                        id="pr_service_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="pr_service_time" type="time" required
                                        name="service_time" value="">


                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">



                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Status</label>
                                    <select class="form-control" name="presales_status" id="pr_presales_status">

                                        <option value="1">Pending</option>
                                        <option value="2">Added</option>
                                        <option value="3">Completed</option>
                                    </select>



                                </div>




                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select id="pr_engineer" name="engineer[]"
                                        class="form-control js-example-basic-single" multiple>

                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>






                                <div class="col-12 mt-2">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" id="pr_addRowEdit" class="btn btn-light rounded-0"><i
                                                class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0"
                                            id="pr_taskTableEdit">

                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm pr_deleteRowEdit">
                                                                <i class="ico icon-outline-trash-bin-minimalistic "
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>



                                </div>



                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>











    <script>
        function edit_support_request(id) {
            var custid = $('#pr_customer_id_' + id).val();
            var c_date = $('#pr_date_' + id).val();
            var custname = $('#pr_customer_name_' + id).val();
            var contact_person = $('#pr_contact_person_' + id).val();
            var mobile = $('#pr_mobile_' + id).val();
            var location_of_work = $('#pr_location_of_work_' + id).val();
            var support_date = $('#pr_support_date_' + id).val();
            var doc_number = $('#pr_doc_number_' + id).val();
            var time_from = $('#pr_time_from_' + id).val();
            var work = $('#pr_work_' + id).val();
            var support_person = $('#pr_support_person_' + id).val();
            var presales_status_id = $('#pr_presales_status_id' + id).val();


            const inputString = work;
            const itemsArray = inputString.split('$');
            console.log(itemsArray);

            // for(i=1; i <= itemsArray.length; i++){
            //     var itm = itemsArray[i-1];
            //     $('#scope_of_work2_'+i).val(itm);
            //     if(itm!=""){
            //         add_scope_of_work2();
            //     }
            // }

            let tr = "";

            for (let i = 0; i < itemsArray.length; i++) {
                let serial = i + 1;

                tr += `
        <tr id="pr_row_edit_${i}">
            <td width="5%">
                <input type="text" class="form-control serial text-center" value="${serial}">
            </td>
            <td>
                <input type="text" class="form-control task" 
                       value="${itemsArray[i]}" 
                       name="scope_of_work[]" autocomplete="off">
            </td>
            <td width="5%">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light rounded-0 btn-sm pr_deleteRowEdit">
                        <i class="ico icon-outline-trash-bin-minimalistic" style="font-size: 16px"></i>
                    </button>
                </div>
            </td>
        </tr>`;
            }

            const supportString = support_person;
            const supportArray = supportString.split(',');
            console.log(supportArray);

            var values = support_person;

            // 🔥 Clear previous selection
            $("#pr_engineer").val([]);


            $.each(values.split(","), function(i, e) {
                $("#pr_engineer option[value='" + e + "']").prop("selected", true);
            });


            $('#pr_pre_sales_id').val(id);
            $('#pr_date').val(c_date);
            $('#pr_cust_id').val(custid);
            $('#pr_cust_name').val(custname);
            $('#pr_contact_person').val(contact_person);
            $('#pr_mobile').val(mobile);
            $('#pr_location_of_work').val(location_of_work);
            $('#pr_service_date').val(support_date);
            console.log(time_from);
            $('#pr_service_time').val(time_from);
            $('#pr_presales_status').val(presales_status_id);
            $('#pr_edit_doc_number_txt').text(doc_number);


            $('#pr_taskTableEdit tbody').empty();
            $('#pr_taskTableEdit tbody').html(tr);


            $('#EditPreSales').modal('show');
            $('#pr_engineer').change();

        }


        // Add row
        $('#pr_addRowEdit').click(function() {
            let rowCount = $('#pr_taskTableEdit tbody tr').length + 1;
            let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm pr_deleteRowEdit"><i
                                                                class="ico icon-outline-trash-bin-minimalistic "
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
            $('#pr_taskTableEdit tbody').append(newRow);
        });

        function updateSerialNumbersEdit() {
            $('#pr_taskTableEdit tbody tr').each(function(index) {
                $(this).find('.serial').val(index + 1);
            });
        }


        $(document).on('click', '.pr_deleteRowEdit', function() {
            $(this).closest('tr').remove();
            updateSerialNumbersEdit();
        });
    </script>





    <script>
        function edit_service_request_ps(id) {
            get_ps_service_request_edit(id);
            $('#ModalEditProfessionalServicesRequest').modal('show');
            get_ps_service_request_scope_of_work(id);
        }

        function get_ps_service_request_edit(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-ps-service-request-edit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#ps_edit_amc_id").val(dataResult['data'][i].id);
                            $("#ps_edit_doc_number").text(dataResult['data'][i].doc_number);
                            $("#ps_deal_id_edit").val(dataResult['data'][i].deal_code?.code || '');


                            date = new Date(dataResult['data'][i].date)
                                .toLocaleDateString(
                                    'en-CA');

                            $('#ps_edit_date').val(date ? date.split('-').reverse().join('/') : '');


                            $("#ps_edit_cust_name").val(dataResult['data'][i].name);

                            $("#ps_edit_contact_person").val(dataResult['data'][i].contact_person);
                            $("#ps_edit_mobile").val(dataResult['data'][i].mobile);
                            $("#ps_edit_engineer").val(dataResult['data'][i].engineer);

                            const selectElement = document.getElementById("ps_edit_engineer");
                            const valuesToSelect = dataResult['data'][i].engineer;
                            for (let i = 0; i < selectElement.options.length; i++) {
                                const option = selectElement.options[i];
                                if (option.value != "") {
                                    if (valuesToSelect.includes(option.value)) {
                                        option.selected = true; // Select the option
                                    }
                                }
                            }


                            $("#ps_edit_location_of_work").val(dataResult['data'][i].location_of_work);
                            //$("#edit_scope_of_work").val(dataResult['data'][i].scope_of_work);



                            $('#ps_edit_service_date').val(dataResult['data'][i].service_date ? dataResult[
                                'data'][
                                i
                            ].service_date.split('-').reverse().join('/') : '');

                            $("#ps_edit_service_time").val(dataResult['data'][i].service_time);
                            $("#status_edit").val(dataResult['data'][i].status);



                        }
                    } else {
                        $("#ps_edit_amc_id").val();
                        $("#ps_edit_date").val();
                        $("#ps_edit_cust_name").val();
                        $("#ps_edit_contact_person").val();
                        $("#ps_edit_mobile").val();
                        $("#ps_edit_engineer").val();
                        $("#ps_edit_location_of_work").val();
                        //$("#edit_scope_of_work").val();
                        $("#edit_service_date").val();
                        $("#edit_service_time").val();
                        $("#edit_doc_number").text('');

                    }
                    $('#ps_edit_engineer').change();
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_ps_service_request_scope_of_work(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-ps-service-request-work') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var tr = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {

                            let serial = i + 1;

                            tr += `
                            <tr id="ps_row_edit_${i}">
                                <td width="5%">
                                    <input type="hidden" value="${dataResult['data'][i].id}" name="scope_of_work_id[]">
                                    <input type="text" class="form-control serial text-center" value="${serial}">
                                </td>
                                <td>
                                    <input type="text" class="form-control task" 
                                        value="${dataResult['data'][i].work}" 
                                        name="scope_of_work[]" autocomplete="off">
                                </td>
                                <td width="5%">
                                    <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-light rounded-0 btn-sm ps_deleteRowEdit">
                                        <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px"></i>
                                    </button>
                                    </div>
                                </td>
                            </tr>`;
                        }
                    } else {

                    }

                    $('#ps_taskTableEdit tbody').empty();
                    $("#ps_taskTableEdit tbody").html(tr);
                    $("#ps_loading_bg").css("display", "none");
                }
            });
        }


        // Add row
        $('#ps_addRowEdit').click(function() {
            let rowCount = $('#ps_taskTableEdit tbody tr').length + 1;
            let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}"></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm ps_deleteRowEdit"><i
                                                                class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
            $('#ps_taskTableEdit tbody').append(newRow);
        });

        function updateSerialNumbersEdit() {
            $('#ps_taskTableEdit tbody tr').each(function(index) {
                $(this).find('.serial').val(index + 1);
            });
        }


        $(document).on('click', '.ps_deleteRowEdit', function() {
            $(this).closest('tr').remove();
            updateSerialNumbersEdit();
        });
    </script>










    <script>
        function get_cust_name(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-customer-details') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                    'data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                    i
                                ]
                                .address2 + ', ' + dataResult['data'][i].city;

                            $("#contact_person").val(name.replace('null ', '').replace('null', ''));
                            $("#mobile_no").val(dataResult['data'][i].mobile);
                            $("#location_of_work").val(dataResult['data'][i].address);
                        }
                    } else {
                        $("#contact_person").val();
                        $("#mobile_no").val();
                        $("#location_of_work").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }
        $(document).ready(function() {


            $(document).on("change", "#cust_name", function() {
                var id = $("#cust_name").val();
                get_cust_name(id);
            });







            // Function to update serial numbers
            function updateSerialNumbers() {
                $('#taskTable tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow').click(function() {
                let rowCount = $('#taskTable tbody tr').length + 1;
                let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow"><i
                                                                class="ico icon-outline-trash-bin-minimalistic"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            $(document).on("change", "#add_cust_name", function() {
                var id = $("#add_cust_name").val();
                get_cust_name(id);
            });

            function get_cust_name(id) {
                $("#loading_bg").css("display", "block");
                var action = "{{ URL::to('crm-amc-customer-details') }}";
                $.ajax({
                    url: action,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    cache: false,
                    success: function(dataResult) {
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                        if (dataResult['data'] != null) {
                            len = dataResult['data'].length;
                        }
                        console.log(dataResult);
                        if (len > 0) {
                            for (var i = 0; i < len; i++) {
                                var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                        'data'][i]
                                    .first_name + ' ' + dataResult['data'][i].last_name;
                                var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                        i
                                    ]
                                    .address2 + ', ' + dataResult['data'][i].city;

                                $("#add_contact_person").val(name.replace('null ', '').replace('null',
                                    ''));
                                $("#add_mobile_no").val(dataResult['data'][i].mobile);
                                $("#add_location_of_work").val(dataResult['data'][i].address);
                            }
                        } else {
                            $("#add_contact_person").val();
                            $("#add_mobile_no").val();
                            $("#add_location_of_work").val();
                        }
                        $("#loading_bg").css("display", "none");
                    }
                });
            }


            // Function to update serial numbers
            function updateSerialNumbers2() {
                $('#taskTable2 tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow2').click(function() {
                let rowCount = $('#taskTable2 tbody tr').length + 1;
                let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow2"><i
                                                                class="ico icon-outline-trash-bin-minimalistic"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable2 tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow2', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers2();
            });

        });
    </script>


    <script>
        $(document).ready(function() {

            $(document).on("change", "#sales_add_cust_name", function() {
                var id = $("#sales_add_cust_name").val();

                get_cust_name3(id);

            });

            function get_cust_name3(id) {
                $("#loading_bg").css("display", "block");
                var action = "{{ URL::to('crm-amc-customer-details') }}";
                $.ajax({
                    url: action,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    cache: false,
                    success: function(dataResult) {
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                        if (dataResult['data'] != null) {
                            len = dataResult['data'].length;
                        }
                        console.log(dataResult);
                        if (len > 0) {
                            console.log("hereeee")
                            for (var i = 0; i < len; i++) {
                                var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                        'data'][i]
                                    .first_name + ' ' + dataResult['data'][i].last_name;
                                var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                        i
                                    ]
                                    .address2 + ', ' + dataResult['data'][i].city;

                                $("#sales_add_contact_person").val(name.replace('null ', '').replace(
                                    'null', ''));
                                $("#sales_add_mobile_no").val(dataResult['data'][i].mobile);
                                $("#sales_add_site_name").val(dataResult['data'][i].address);
                            }
                        } else {
                            $("#sales_add_contact_person").val();
                            $("#sales_add_mobile_no").val();
                            $("#sales_add_site_name").val();
                        }
                        $("#loading_bg").css("display", "none");
                    }
                });
            }


            function updateSerialNumbers3() {
                $('#taskTable3 tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow3').click(function() {
                let rowCount = $('#taskTable3 tbody tr').length + 1;
                let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow3"><i
                                                                class="ico icon-outline-trash-bin-minimalistic"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable3 tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow3', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers3();
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            $('#search_record').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('eng-track.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {


                                let ims = `<li class="nav-item w-100" role="presentation">
                            <button
                                class="nav-link data-item"
                                data-id="${amc_list.id}" data-type="${amc_list.type}" id="purchase-order-1-tab"
                                data-bs-toggle="tab" data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.cust_name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                        ${amc_list.work_date 
    ? get_format_date(amc_list.work_date) 
    : (amc_list.service_date ? get_format_date(amc_list.service_date) : '')}
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${amc_list.deal_code}

                                        </div>
                                    </div>
                                   
                                </div>
                            </button>
                        </li>`;







                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>









    <script>
        function edit_service_request(id) {
            get_amc_service_request_edit(id);
            $('#ModalEditServiceRequest').modal('show');
        }

        function get_amc_service_request_edit(id) {
            $("#loading_bg").css("display", "block");
            console.log("ID: " + id);
            var action = "{{ URL::to('crm-amc-service-request-edit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);

                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#amcid_edit").val(dataResult['data'][i].id);
                            $("#amc_deal_id_edit").val(dataResult['data'][i].deal_code?.code || '');

                            $("#edit_amc_code").text(dataResult['data'][i].doc_number);

                            date = new Date(dataResult['data'][i].date)
                                .toLocaleDateString(
                                    'en-CA');

                            $('#amc_date_edit').val(date ? date.split('-').reverse().join('/') : '');



                            $("#amc_cust_name_edit").val(dataResult['data'][i].cust_name);

                            //alert(dataResult['data'][i].cust_name);
                            //$("#select2-cust_name_edit-container").val(dataResult['data'][i].cust_name);   


                            $("#amc_contact_person_edit").val(dataResult['data'][i].contact_person);
                            $("#amc_mobile_no_edit").val(dataResult['data'][i].mobile_no);
                            $("#amc_location_of_work_edit").val(dataResult['data'][i].location_of_work);

                            const scop = dataResult['data'][i].scope_of_work.split("$");
                            for (k = 0; k < scop.length; k++) {
                                $("#amc_scope_of_work_edit_" + (k + 1)).val(scop[k]);
                                $("#amc_row_edit_" + (k + 1)).css('display', '');
                            }


                            $('#amc_service_date_edit').val(dataResult['data'][i].service_date ? dataResult[
                                'data'][
                                i
                            ].service_date.split('-').reverse().join('/') : '');

                            $("#amc_service_time_edit").val(dataResult['data'][i].service_time);

                            if (dataResult['data'][i].status == 5) {
                                $("#amc_status_edit").val(dataResult['data'][i].status);
                            } else {
                                $("#amc_status_edit").val(2);
                            }

                            $("#amc_source_edit").val(dataResult['data'][i].source);


                            //$("#service_engineer_edit").val(dataResult['data'][i].service_engineer);
                            //$('#service_engineer_edit').removeClass('js-example-basic-single');
                            const selectElement = document.getElementById("amc_service_engineer_edit");
                            const valuesToSelect = dataResult['data'][i].service_engineer;

                            // FIX: Clear previous selections
                            // FIX: Clear previous selections
                            for (let i = 0; i < selectElement.options.length; i++) {
                                selectElement.options[i].selected = false;
                            }

                            if (valuesToSelect !== null) {
                                for (let i = 0; i < selectElement.options.length; i++) {
                                    const option = selectElement.options[i];
                                    if (valuesToSelect.includes(option.value)) {
                                        option.selected = true; // Select the option
                                    }
                                }
                            }


                            //$('#service_engineer_edit').addClass('js-example-basic-single');

                            get_amc_scope_of_work(dataResult['data'][i].id);


                        }
                    } else {
                        $("#amcid_edit").val();
                        $("#amc_date_edit").val();
                        $("#amc_cust_name_edit").val();
                        $("#amc_contact_person_edit").val();
                        $("#amc_mobile_no_edit").val();
                        $("#amc_location_of_work_edit").val();
                        $("#amc_scope_of_work_edit").val();
                        $("#amc_service_date_edit").val();
                        $("#amc_service_time_edit").val();
                        $("#amc_status_edit").val();
                        $("#amc_source_edit").val();
                        $("#amc_service_engineer_edit").val();
                        $("#edit_amc_code").text('');
                    }
                    $("#amc_cust_name_edit").change();
                    $("#amc_service_engineer_edit").change();
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_amc_scope_of_work(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-service-request-work') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var tr = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            let serial = i + 1;

                            tr += `
        <tr id="amc_row_edit_${i}">
            <td width="5%">
                <input type="hidden" value="${dataResult['data'][i].id}" name="scope_of_work_id[]">
                <input type="text" class="form-control serial text-center" value="${serial}">
            </td>
            <td>
                <input type="text" class="form-control task" 
                       value="${dataResult['data'][i].work}" 
                       name="scope_of_work[]" autocomplete="off">
            </td>
            <td width="5%">
                <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit">
                    <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px"></i>
                </button>
                </div>
            </td>
        </tr>`;
                        }
                    } else {

                    }

                    $('#amc_taskTableEdit tbody').empty();
                    $('#amc_taskTableEdit tbody').html(tr);
                    $("#loading_bg").css("display", "none");
                }
            });
        }


        // Add row
        $('#addRowEdit').click(function() {
            let rowCount = $('#amc_taskTableEdit tbody tr').length + 1;
            let newRow = `
      <tr>
        <td><input type="text" class="form-control serial text-center" value="${rowCount}" ></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit"><i
                                                                class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
            $('#amc_taskTableEdit tbody').append(newRow);
        });

        function updateSerialNumbersEdit() {
            $('#amc_taskTableEdit tbody tr').each(function(index) {
                $(this).find('.serial').val(index + 1);
            });
        }


        $(document).on('click', '.deleteRowEdit', function() {
            $(this).closest('tr').remove();
            updateSerialNumbersEdit();
        });
    </script>





    <?php } catch (\Exception $e) { ?> {{ $e }}
    <?php  } ?>
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')
    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            // Hide task cards whenever list view is toggled
            document.querySelectorAll('#task-cards').forEach(el => {
                el.classList.add('d-none');
            });

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

                localStorage.setItem('listViewPJReqList', 'long');
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

                localStorage.setItem('listViewPJReqList', 'short');

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
            const savedView = localStorage.getItem('listViewPJReqList');
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
                    localStorage.setItem('listViewPJReqList', 'short');
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
            <h4 class="mb-2">Project Request</h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-list-req', 'method' => 'POST', 'id' => 'crm-ps-service-list-req']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_ps_id" id="search_psreq_id" class="form-control"
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
                <h4 class="mb-2">Project Request List

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
                                <button data-bs-target="#ModalProfessionalServicesRequest" data-bs-toggle="modal"
                                    type="button" class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-add-square text-success title-15 me-2"></i> Request</button>
                            </li>

                            <li>
                                <a href="{{ url('crm-ps-track-service-list') }}"
                                    class="dropdown-item d-flex align-items-center "><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Project
                                    Service</a>
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

                <div class="card" style="width:100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-list-req', 'method' => 'POST', 'id' => 'crm-ps-service-list-req']) }}
                        <div class="row">

                            <div class="col-1 mb-2 filter-field d-none">
                                <label for="" class="form-label">Track ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="search_ps_id"
                                    value="{{ $ctrl_ps_id }}">
                            </div>

                            <div class="col-1 mb-2 filter-field d-none">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="search_deal_id"
                                    value="{{ $ctrl_deal_id }}">
                            </div>


                            <div class="col-2 mb-2 filter-field d-none">
                                <label for="" class="form-label">Customer Name</label>
                                <select class="form-control js-example-basic-single" name="search_customer_name"
                                    id="search_customer_name">
                                    <option value="">-Select-</option>
                                    @foreach ($customer as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_customer_name == $value->id) selected @endif>
                                            {{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-2 mb-2 filter-field d-none">
                                <label for="" class="form-label">Engineer</label>
                                <select class="form-control js-example-basic-single" name="search_sales_person"
                                    id="search_sales_person">
                                    <option value="">Select</option>
                                    @if (count($salesperson) > 0)
                                        @foreach ($salesperson as $list)
                                            <option value="{{ $list->user_id }}"
                                                @if ($ctrl_sales_person == $list->user_id) selected @endif>{{ $list->full_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>


                            <div class="col-1-5 mb-2 filter-field d-none">
                                @php
                                    // Ensure $ctrl_date is in d/m/Y for flatpickr
                                    if (!empty($ctrl_from_date)) {
                                        try {
                                            $ctrl_from_date = \Carbon\Carbon::parse($ctrl_from_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $ctrl_from_date = '';
                                        }
                                    }

                                    if (!empty($ctrl_to_date)) {
                                        try {
                                            $ctrl_to_date = \Carbon\Carbon::parse($ctrl_to_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $ctrl_to_date = '';
                                        }
                                    }
                                @endphp
                                <label for="" class="form-label">Service Date From</label>
                                <input class="form-control date-picker" type="text" autocomplete="off"
                                    name="search_from_date" id="search_from_date" value="{{ $ctrl_from_date }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Service Date To</label>
                                <input class="form-control date-picker" type="text" autocomplete="off"
                                    name="search_to_date" id="search_to_date" value="{{ $ctrl_to_date }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Status</label>
                                <div class="form-group">
                                    <select class="form-control" name="search_status" id="search_status">
                                        <option value="1" @if ($ctrl_search_status == 1) selected @endif>Pending
                                        </option>
                                        <option value="2" @if ($ctrl_search_status == 2) selected @endif>Completed
                                        </option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                </div>

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
                @if (count($psData) > 0)
                    @foreach ($psData as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link proj-request-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">{{ @$item->doc_number }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            {{ date('d/m/Y', strtotime(@$item->service_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            @if ($item->status == 2)
                                                <span class='text-success'>Completed</span>
                                            @else
                                                <span class='text-danger'>Pending</span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->custname->name }}</label>
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
                            <th class="text-center" width="70px">@lang('PS ID')</th>
                            <th class="text-center" width="70px">@lang('Deal No')</th>
                            <th class="text-center" width="100px">@lang('Date')</th>
                            <th class="text-start" width="200px">@lang('Customer Name')</th>
                            <th class="text-start" width="180px">@lang('Engineer')</th>
                            <th class="text-start" width="250px">@lang('Scope of Work')</th>
                            <th class="text-center" width="100px">@lang('Service Date')</th>
                            <th class="text-center" width="100px">@lang('Service Time')</th>
                            <th class="text-center" width="80px">@lang('Status')</th>
                            <th class="text-center" width="30px"><i class="ico icon-bold-paperclip"></i></th>
                            <th class="text-center" style="width: 140px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (count($psData) > 0)
                            @foreach ($psData as $value)
                                <tr @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                    <td class="text-center proj-request-item" data-id="{{ @$value->id }}"
                                        onclick="list_style_new()"><a>{{ @$value->doc_number }}</a></td>
                                    <td class="text-center">
                                        <a
                                            href="{{ url('get-url-deal-track/' . @$value->deal_code->code) }}">{{ @$value->deal_code->code }}</a>


                                    </td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                    <td>{{ @$value->custname->name }}</td>
                                    <?php
                                    $engineername = '';
                                    if ($value->engineer != '') {
                                        $st = explode(',', $value->engineer);
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
                                    <td>{{ @$engineername }}</td>
                                    <?php
                                    $w = $work->where('service_id', $value->id)->pluck('work');
                                    $wo = '';
                                    if (count($w) > 0) {
                                        foreach ($w as $wr) {
                                            if ($wo == '') {
                                                $wo .= $wr;
                                            } else {
                                                $wo .= ', ' . $wr;
                                            }
                                        }
                                    }
                                    ?>
                                    <td>{{ @$wo }}</td>

                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->service_date)) }}</td>
                                    <td class="text-center">{{ date('H:i A', strtotime(@$value->service_time)) }}</td>

                                    <td class="text-center">
                                        @if ($value->status == 2)
                                            <span class="text-success">Completed</span>
                                        @else
                                            <span class="text-danger">Pending</span>
                                        @endif
                                    </td>

                                    <td class="text-center">

                                        @if (!empty($value->attachment))
                                            <a href="{{ url('public/uploads/crm_amc_doc/' . $value->attachment) }}"
                                                target="_blank" class="text-dark"><i
                                                    class="ico icon-bold-paperclip"></i></a>
                                        @endif


                                    </td>


                                    <td>

                                        <div class="d-flex justify-content-center align-items-center gap-1">

                                            <a class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                data-bs-target="#servicecomments_{{ $value->id }}"
                                                style="cursor: pointer;" data-backdrop="static" data-keyboard="false"><i
                                                    class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                    style="font-size: 16px"></i></a>
                                            <a class="btn btn-sm btn-light"
                                                onclick="edit_service_request({{ $value->id }})"><i
                                                    class="ico icon-outline-pen-2 text-dark"
                                                    style="font-size: 16px;"></i></a>
                                            @if (@$value->is_delete == 0)
                                                <a class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                    href="{{ url('crm-ps-service-request-deactivate/' . $value->id . '') }}"><i
                                                        class="ico icon-outline-trash-bin-minimalistic"
                                                        style="font-size:16px" aria-hidden="true"></i></a>
                                            @endif
                                            @if (@$value->is_delete == 1)
                                                <a class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                    href="{{ url('crm-ps-service-request-activate/' . $value->id . '') }}"><i
                                                        class="ico icon-bold-restart text-dark"
                                                        style="font-size: 16px;"></i></a>
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
                    $(document).on('click', '.proj-request-item', function() {

                        var id = $(this).data('id');

                        $('.proj-request-item').removeClass('active');
                        $('.proj-request-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('crm-ps-service-list-req') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-ps-service-detail') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                $('#proj-request-details').html(response);
                            },
                            error: function() {
                                $('#proj-request-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="proj-request-details">

                @if (!empty($selectedProj) && is_array($selectedProj))
                    @include('backEnd.amc.DealAmcTrackServiceReqDetail', $selectedProj)
                @else
                    <div data-bs-target="#ModalProfessionalServicesRequest" data-bs-toggle="modal"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div data-bs-target="#ModalProfessionalServicesRequest" data-bs-toggle="modal"
                                class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer"
                                data-bs-target="#ModalProfessionalServicesRequest" data-bs-toggle="modal">Project Request
                            </h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                @endif

            </div>


        </div>
    </div>






    <div class="modal  fade" id="ModalProfessionalServicesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-request-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Request
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
                                    <input type="text" class="form-control date-picker" name="date" id="date"
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
                               <button type="button" id="addRow" class="btn btn-light rounded-0"><i
                                            class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                            </div>


                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" id="taskTable">
                                           
                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text" class="form-control serial text-center"
                                                            value="1"></td>
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
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
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
                        (<span class="font-weight-600" id="edit_doc_number"></span>)</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="amc_id" id="edit_amc_id">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" id="deal_id_edit" class="form-control" name="deal_id">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" class="form-control" name="date" id="edit_date"
                                        value="{{ date('d/m/Y') }}" readonly>
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <input class="form-control" id="edit_cust_name" type="text" required
                                        name="cust_name" value="" readonly>

                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person"
                                        id="edit_contact_person" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile" id="edit_mobile" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">@lang('Location of Work')</label>
                                    <input class="form-control" type="text" name="location_of_work"
                                        id="edit_location_of_work" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" type="text" name="service_date"
                                        id="edit_service_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="edit_service_time" type="time" required
                                        name="service_time" value="">

                                </div>

                                <div class="col-9">

                                    <label for="engineer" class="form-label">Service Engineer</label>
                                    <select id="edit_engineer" name="engineer[]"
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
                                    <select class="form-control" name="status_edit" id="status_edit">
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
                                  <button type="button" id="addRowEdit" class="btn btn-light rounded-0"><i
                                            class="ico icon-outline-add-square text-success"></i> Add
                                        </button>
                            </div>


                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0"
                                            id="taskTableEdit">
                                          
                                            <tbody>
                                                <tr>
                                                    <td width="5%"><input type="text" class="form-control serial text-center"
                                                            value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light rounded-0 btn-sm deleteRow">
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

    @if (count($psData) > 0)
        @foreach ($psData as $ps)
            <div class="modal  fade" id="servicecomments_{{ $ps->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">Service Comments ({{ $ps->doc_number }}) </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $ps_comments_data = $ps_comments->where('ps_id', $ps->id);
                                        @endphp

                                        @if (count($ps_comments_data) > 0)
                                            <table class="table table-hover" id="long-list" width="100%"
                                                cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="50%">Comment</th>
                                                        <th width="10%">Status</th>
                                                        <th style="width: 20%;">By</th>
                                                        <th width="20%">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($ps_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    Pending
                                                                @else
                                                                    Completed
                                                                @endif
                                                            </td>
                                                            <td>{{ $cmts->engineerid->full_name }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($cmts->created_at)->format('d M Y, h:i A') }}
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





    <script>
        // Add row
        $('#addRow').click(function() {
            let rowCount = $('#taskTable tbody tr').length + 1;
            let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}"></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow"><i
                                                                class="ico ico icon-outline-trash-bin-minimalistic text-dark"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
            $('#taskTable tbody').append(newRow);
        });

        function updateSerialNumbersEdit() {
            $('#taskTable tbody tr').each(function(index) {
                $(this).find('.serial').val(index + 1);
            });
        }


        $(document).on('click', '.deleteRow', function() {
            $(this).closest('tr').remove();
            updateSerialNumbersEdit();
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
                            $("#edit_amc_id").val(dataResult['data'][i].id);
                            $("#edit_doc_number").text(dataResult['data'][i].doc_number);
                            $("#deal_id_edit").val(dataResult['data'][i].deal_code?.code || '');


                            date = new Date(dataResult['data'][i].date)
                                .toLocaleDateString(
                                    'en-CA');

                            $('#edit_date').val(date ? date.split('-').reverse().join('/') : '');


                            $("#edit_cust_name").val(dataResult['data'][i].name);

                            $("#edit_contact_person").val(dataResult['data'][i].contact_person);
                            $("#edit_mobile").val(dataResult['data'][i].mobile);
                            $("#edit_engineer").val(dataResult['data'][i].engineer);

                            const selectElement = document.getElementById("edit_engineer");
                            const valuesToSelect = dataResult['data'][i].engineer;
                            for (let i = 0; i < selectElement.options.length; i++) {
                                const option = selectElement.options[i];
                                if (option.value != "") {
                                    if (valuesToSelect.includes(option.value)) {
                                        option.selected = true; // Select the option
                                    }
                                }
                            }


                            $("#edit_location_of_work").val(dataResult['data'][i].location_of_work);
                            //$("#edit_scope_of_work").val(dataResult['data'][i].scope_of_work);

                            {{--  const scop = dataResult['data'][i].scope_of_work.split("$");
                                for(k=0; k < scop.length; k++){                                    
                                    $("#scope_of_work_edit_"+(k+1)).val(scop[k]);
                                    $("#row_edit_"+(k+1)).css('display','');
                                }  --}}

                            $('#edit_service_date').val(dataResult['data'][i].service_date ? dataResult['data'][
                                i
                            ].service_date.split('-').reverse().join('/') : '');

                            $("#edit_service_time").val(dataResult['data'][i].service_time);
                            $("#status_edit").val(dataResult['data'][i].status);
                            {{--  get_amc_scope_of_work(dataResult['data'][i].id);  --}}


                        }
                    } else {
                        $("#edit_amc_id").val();
                        $("#edit_date").val();
                        $("#edit_cust_name").val();
                        $("#edit_contact_person").val();
                        $("#edit_mobile").val();
                        $("#edit_engineer").val();
                        $("#edit_location_of_work").val();
                        //$("#edit_scope_of_work").val();
                        $("#edit_service_date").val();
                        $("#edit_service_time").val();
                        $("#edit_doc_number").text('');

                    }
                    $('#edit_engineer').change();
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
                            <tr id="row_edit_${i}">
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

                    $('#taskTableEdit tbody').empty();
                    $("#taskTableEdit tbody").html(tr);
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>


    <script>
        // Add row
        $('#addRowEdit').click(function() {
            let rowCount = $('#taskTableEdit tbody tr').length + 1;
            let newRow = `
      <tr>
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}"></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit"><i
                                                                class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
            $('#taskTableEdit tbody').append(newRow);
        });

        function updateSerialNumbersEdit() {
            $('#taskTableEdit tbody tr').each(function(index) {
                $(this).find('.serial').val(index + 1);
            });
        }


        $(document).on('click', '.deleteRowEdit', function() {
            $(this).closest('tr').remove();
            updateSerialNumbersEdit();
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#search_psreq_id').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-psreq.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');

                        console.log("here")
                        // console.log(data);

                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                                console.log(amc_list);



                                let ims = `   <li class="nav-item w-100" role="presentation">
                            <button class="nav-link proj-request-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            ${get_format_date(amc_list.service_date)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                          
                                             ${amc_list.status == 2
                                        ? '<span class="text-success">Completed</span>'
                                        : '<span class="text-danger">Pending</span>'
                                    }

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.custname.name}</label>
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

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

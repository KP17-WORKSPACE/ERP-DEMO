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

                localStorage.setItem('listViewPJList', 'long');
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

                localStorage.setItem('listViewPJList', 'short');

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
            const savedView = localStorage.getItem('listViewPJList');
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
                    localStorage.setItem('listViewPJList', 'short');
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
            <h4 class="mb-2">Project Service

            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-track-service-list', 'method' => 'POST', 'id' => 'crm-ps-track-service-list']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_ps_id" id="search_ps_id" class="form-control"
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
                <h4 class="mb-2">Project Service List </h4>
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
                                <a href="{{ url('crm-ps-service-list-req') }}"
                                    class="dropdown-item d-flex align-items-center "><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Request
                                    List</a>
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

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-track-service-list', 'method' => 'POST', 'id' => 'crm-ps-track-service-list']) }}
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
                                <label for="" class="form-label">Sales Person</label>
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
                                        <option value="">Select</option>
                                        <option value="0" @if ($ctrl_search_status == 0) selected @endif>Pending
                                        </option>
                                        <option value="1" @if ($ctrl_search_status == 1) selected @endif>Added
                                        </option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                </div>

                            </div>



                            <div class="col-1-5  filter-field d-none">
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
                @if (count($support) > 0)
                    @foreach ($support as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link proj-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->custname->name }}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ @$item->doc_number }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$item->date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            
                                            @if ($item->status == 2)
                                                <span class='text-success'>Completed</span>
                                            @else
                                                <span class='text-danger'>Pending</span>
                                            @endif

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
                            <th class="text-center" width="70px">@lang('PS ID')</th>
                            <th class="text-center" width="70px">@lang('Deal No')</th>
                            <th class="text-center" width="100px">@lang('Date')</th>
                            <th class="text-start" width="200px">@lang('Customer Name')</th>
                            <th class="text-start" width="150px">@lang('Sales Person')</th>
                            <th class="text-start" width="250px">@lang('Description')</th>
                            <th class="text-center" width="100px">@lang('Status')</th>
                            <th class="text-center" style="width: 100px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($support as $value)
                            <tr>
                                <td class="text-center  proj-item" data-id="{{ @$value->id }}"
                                    onclick="list_style_new()"><a>{{ @$value->doc_number }}</a></td>
                                <td class="text-center"><a
                                        href="{{ url('get-url-deal-track/' . $value->deal_code->code) }}">{{ @$value->deal_code->code }}</a>
                                </td>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                <td>{{ @$value->custname->name }}</td>
                                <td>{{ @$value->ownername->full_name }}</td>
                                <td>{{ @$value->deal_description }}</td>
                                <td class="text-center">
                                    @if ($value->status == 2)
                                        <span class='text-success'>Completed</span>
                                    @else
                                        <span class='text-danger'>Pending</span>
                                    @endif
                                </td>
                                <td>


                                    <input type="hidden" id="cid[]" value="{{ @$value->id }}">
                                    <input type="hidden" id="list_custname_{{ $value->id }}"
                                        value="{{ @$value->custname->name }}" />
                                    <input type="hidden" id="contact_person_{{ @$value->id }}"
                                        value="{{ @$value->contact_person }}" />
                                    <input type="hidden" id="mobile_{{ @$value->id }}"
                                        value="{{ @$value->mobile }}" />
                                    <input type="hidden" id="location_of_work_{{ @$value->id }}"
                                        value="{{ @$value->location_of_work }}" />
                                    <input type="hidden" id="deal_description_{{ @$value->id }}"
                                        value="{{ @$value->deal_description }}" />
                                    <input type="hidden" id="add_docnumber_{{ @$value->id }}"
                                        value="{{ @$value->doc_number }}" />

                                    {{--  //service_date
                                    //service_time
                                    //engineer 
                                     --}}

                                    <div class="d-flex justify-content-center align-items-center gap-1">

                                        @if (@$value->status != 1)
                                            <a onclick="add_professional_services_request({{ @$value->id }})"
                                                class="btn btn-sm btn-light text-dark" style="cursor: pointer;"><i
                                                    style="font-size: 16px;"
                                                    class="ico icon-outline-add-square text-success"
                                                    aria-hidden="true"></i> Request</a>
                                        @endif
                                        @if (@$value->status == 1)
                                            <a onclick="edit_service_request({{ $value->id }})"
                                                class="btn btn-sm btn-light" style="cursor: pointer;">
                                                <i style="font-size: 16px;"
                                                    class="ico icon-outline-add-square text-success"
                                                    aria-hidden="true"></i></a>
                                        @endif

                                        @if (@$value->is_delete == 0)
                                            <a class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-ps-service-request-deactivate/' . $value->id . '') }}"><i
                                                    style="font-size: 16px;"
                                                    class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                    aria-hidden="true"></i></a>
                                        @endif
                                        @if (@$value->is_delete == 1)
                                            <a class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-ps-service-request-activate/' . $value->id . '') }}"><i
                                                    style="font-size: 16px;" class="ico icon-bold-restart text-dark"
                                                    aria-hidden="true"></i></a>
                                        @endif
                                    </div>


                                </td>





                                </td>
                            </tr>
                        @endforeach

                    </tbody>




                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.proj-item', function() {

                        var id = $(this).data('id');

                        $('.proj-item').removeClass('active');
                        $('.proj-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('crm-ps-track-service-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-ps-track-service-detail') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                $('#proj-details').html(response);
                            },
                            error: function() {
                                $('#proj-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="proj-details">
                @if (!empty($selectedProj) && is_array($selectedProj))
                    @include('backEnd.amc.DealAmcTrackServiceDetail', $selectedProj)
                @else
                    <p class="text-danger">No details available.</p>
                @endif
            </div>


        </div>
    </div>


    <div class="modal  fade" id="ModalProfessionalServicesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-track-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Request (<span class="font-weight-600"
                            id="add_req_docnumber"></span>)</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="amc_id" id="amc_id">



                                <div class="col-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" class="form-control date-picker" name="date" id="date"
                                        value="{{ date('d/m/Y') }}">
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <input class="form-control" id="cust_name" type="text" required name="cust_name"
                                        value="" readonly>

                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person" id="contact_person"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile" id="mobile" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">@lang('Location of Work')</label>
                                    <input class="form-control" type="text" name="location_of_work"
                                        id="location_of_work" required>
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
                                    @php
                                        $englist = \App\SysHelper::get_engineer_list();
                                    @endphp
                                    <label for="engineer" class="form-label">Service Engineer</label>
                                    <select id="engineer" name="engineer[]" class="form-control js-example-basic-single"
                                        multiple>
                                        <option value="">Select</option>


                                        @foreach ($englist as $list)
                                            <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                        @endforeach
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
                                                    <td width="5%"><input type="text"
                                                            class="form-control serial text-center" value="1"></td>
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
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Request
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>





    <script>
        function add_professional_services_request(id) {

            var custname = $('#list_custname_' + id).val();
            var contact_person = $('#contact_person_' + id).val();
            var mobile = $('#mobile_' + id).val();
            var location_of_work = $('#location_of_work_' + id).val();
            var description = $('#deal_description_' + id).val();
            var doc_number = $('#add_docnumber_' + id).val();


            $('#amc_id').val(id);
            $('#cust_name').val(custname);
            $('#add_req_docnumber').text(doc_number);
            $('#contact_person').val(contact_person);
            $('#mobile').val(mobile);
            $('#location_of_work').val(location_of_work);
            $('#scope_of_work_1').val(description);
            $('#ModalProfessionalServicesRequest').modal('show');
        }
    </script>


    <script>
        $(document).ready(function() {
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
        <td><input type="text" class="form-control serial text-center" value="${rowCount}"></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow"><i
                                                                class="ico icon-outline-trash-bin-minimalistic text-dark"
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

            $('#search_ps_id').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-ps.search') }}",
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
                            <button class="nav-link proj-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.custname.name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            ${get_format_date(amc_list.date)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                          
                                             ${amc_list.status == 2
                                        ? '<span class="text-success">Completed</span>'
                                        : '<span class="text-danger">Pending</span>'
                                    }

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

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

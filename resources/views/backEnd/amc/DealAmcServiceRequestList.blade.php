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

                localStorage.setItem('listViewAMCRequest', 'long');
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

                localStorage.setItem('listViewAMCRequest', 'short');

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
            const savedView = localStorage.getItem('listViewAMCRequest');
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
                    localStorage.setItem('listViewAMCRequest', 'short');
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
            <h4 class="mb-2">AMC Request List</h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list', 'method' => 'POST', 'id' => 'crm-amc-service-request-list']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_amc_id" id="search_amc_req" class="form-control"
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
                <h4 class="mb-2">AMC Request List

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
                                <button data-bs-target="#ModalAddServiceRequest" data-bs-toggle="modal" type="button"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-add-square text-success title-15 me-2"></i> Add
                                    Request</button>
                            </li>

                            <li>
                                <button data-bs-toggle="modal" data-bs-target="#ModalCopyLink" type="button"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-copy text-success title-15 me-2"></i> Copy URL</button>
                            </li>

                            <li>
                                <a href="{{ url('crm-amc-list') }}" class="dropdown-item d-flex align-items-center "><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> AMC
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

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list', 'method' => 'POST', 'id' => 'crm-amc-service-request-list']) }}
                        <div class="row">

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">AMC ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="search_amc_id"
                                    value="{{ $ctrl_amc_id }}">
                            </div>

                            <div class="col-2-5 mb-2 filter-field d-none">
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

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Service Enginer</label>
                                <select class="form-control js-example-basic-single" name="search_service_enginer"
                                    id="search_service_enginer">
                                    <option value="">Select</option>
                                    <option @if ($ctrl_service_enginer == 'NA') selected @endif value="NA">N/A (Not
                                        Allocated)</option>
                                    @if (count($engineer_list) > 0)
                                        @foreach ($engineer_list as $list)
                                            <option value="{{ $list->user_id }}"
                                                @if ($ctrl_service_enginer == $list->user_id) selected @endif>
                                                {{ $list->full_name }}</option>
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
                                        <option value="">All</option>
                                        <option value="2,3" @if ($ctrl_search_status == '2,3') selected @endif>Pending
                                        </option>
                                        <option value="5" @if ($ctrl_search_status == '5') selected @endif>Completed
                                        </option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                </div>

                            </div>



                            <div class="col-2 filter-field d-none">
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
                @if (count($amcdata) > 0)
                    @foreach ($amcdata as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link amc-request-item {{ $active_id == $item->id ? 'active' : '' }}"
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
                                            @if ($item->status == 5)
                                                <span class="text-success">Completed</span>
                                            @else
                                                <span class="text-danger">Pending</span>
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
                            <th width="100px">@lang('Track No')</th>
                            <th width="100px">@lang('Deal ID')</th>
                            <th width="100px">@lang('Date')</th>
                            <th class="text-start">@lang('Customer Name')</th>
                            <th class="text-start">@lang('Service Enginer')</th>
                            <th class="text-start" width="250px">@lang('Scope of Work')</th>
                            <th width="100px">@lang('Service Date')</th>
                            <th width="100px">@lang('Service Time')</th>
                            <th width="100px">@lang('Status')</th>
                            <th width="40px"><i class="ico icon-bold-paperclip"></i></th>
                            <th class="text-center" style="width: 170px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>
                        @foreach ($amcdata as $value)
                            <tr @if ($value->status == 3) style="color:#ff0000 !important;" @endif
                                @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                <td class="text-center amc-request-item" data-id="{{ @$value->id }}"
                                    onclick="list_style_new()"><a>{{ @$value->doc_number }}</a>
                                </td>
                                <td class="text-center">
                                    @if (@$value->code == '')
                                        --
                                    @else
                                        <a
                                            href="{{ url('get-url-deal-track/' . @$value->code) }}">{{ @$value->code }}</a>
                                    @endif
                                </td>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                <td>{{ @$value->custname->name }}</td>

                                @if ($value->service_engineer)
                                    <?php
                                    $st = explode(',', $value->service_engineer);
                                    $engineername = '';
                                    
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
                                    ?>
                                    <td>{{ @$engineername }}</td>
                                @else
                                    <td>N/A</td>
                                @endif
                                <td>
                                    @php
                                        $work = '';
                                        $scope_of_work = $amc_work->where('amc_id', $value->id);
                                        if (count($scope_of_work) > 0) {
                                            foreach ($scope_of_work as $sw) {
                                                if ($work == '') {
                                                    $work .= $sw->work;
                                                } else {
                                                    $work .= ' || ' . $sw->work;
                                                }
                                            }
                                        }
                                    @endphp
                                    {!! $work !!}
                                </td>



                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->service_date)) }}</td>
                                <td class="text-center">{{ date('h:i A', strtotime(@$value->service_time)) }}</td>

                                <td class="text-center">
                                    @if ($value->status == 5)
                                        <span class="text-success">Completed</span>
                                    @else
                                        <span class="text-danger">Pending</span>
                                    @endif
                                    {{-- {!! @App\SysHelper::get_amc_status($value->status) !!} --}}
                                </td>

                                <td class="text-center">
                                    @if (@$value->attachment == '')
                                        <span class="text-danger"></span>
                                    @else
                                        <a target="_blank"
                                            href="{{ asset('public/uploads/crm_amc_doc/') }}/{{ @$value->attachment }}"><i
                                                class="ico icon-bold-paperclip"></i></a>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <a class="btn btn-sm btn-light" data-bs-toggle="modal"
                                            data-bs-target="#servicecomments_{{ $value->id }}"
                                            style="cursor: pointer;" data-keyboard="false"><i
                                                class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                style="font-size: 16px"></i></a>

                                        <a target="_blank" class="btn btn-sm btn-light"
                                            href="{{ asset('public/uploads/crm_amc_doc/') }}/{{ @$value->attachment }}"><i
                                                class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i>
                                        </a>
                                        <a class="btn btn-sm btn-light"
                                            onclick="edit_service_request({{ $value->id }})"><i
                                                class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></a>
                                        @if (@$value->is_delete == 0)
                                            <a class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-service-request-deactivate/' . $value->id . '') }}"><i
                                                    class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                    style="font-size: 16px" aria-hidden="true"></i></a>
                                        @endif
                                        @if (@$value->is_delete == 1)
                                            <a class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-service-request-activate/' . $value->id . '') }}"><i
                                                    class="ico icon-bold-restart text-dark"
                                                    style="font-size: 16px;"></i></a>
                                        @endif
                                    </div>

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
                    $(document).on('click', '.amc-request-item', function() {

                        var id = $(this).data('id');

                        $('.amc-request-item').removeClass('active');
                        $('.amc-request-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('crm-amc-service-request-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-amc-service-request-detail') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                $('#amc-request-details').html(response);
                            },
                            error: function() {
                                $('#amc-request-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="amc-request-details">

                @if (!empty($selectedAMC) && is_array($selectedAMC))
                    @include('backEnd.amc.DealAmcServiceRequestDetail', $selectedAMC)
                @else
                    <p class="text-danger">No details available.</p>
                @endif

            </div>


        </div>
    </div>


    <div class="modal  fade" id="ModalAddServiceRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg ">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list-add', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-service-request-list-add']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add
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
                                        @foreach ($customer as $value)
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
                                        @if (count($engineer_list) > 0)
                                            @foreach ($engineer_list as $list)
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
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Request
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
                                    <input type="text" id="deal_id_edit" class="form-control" name="deal_id">
                                </div>

                                <input type="hidden" name="amcid_edit" id="amcid_edit" />
                                <div class="col-3">
                                    <label for="" class="form-label"> Date</label>
                                    <input type="text" class="form-control date-picker" name="date" id="date_edit"
                                        value="{{ date('d/m/Y') }}" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label"> Company Name</label>
                                    <select class="form-control js-example-basic-single" name="cust_name"
                                        id="cust_name_edit" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person"
                                        id="contact_person_edit" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile_no" id="mobile_no_edit"
                                        required>

                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Location Of Work</label>
                                    <input type="text" class="form-control" name="location_of_work"
                                        id="location_of_work_edit">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input type="text" class="form-control date-picker" name="service_date"
                                        id="service_date_edit" required>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <select type="time" class="form-control" name="service_time"
                                        id="service_time_edit" required>
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
                                    <select class="form-control" name="source" id="source_edit" required>
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
                                    <select class="form-control" name="status_edit" id="status_edit">
                                        <option value="2">Pending</option>
                                        <option value="5">Completed</option>
                                    </select>
                                </div>


                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select class="form-control js-example-basic-single" name="service_engineer[]"
                                        id="service_engineer_edit" required multiple>
                                        @if (count($engineer_list) > 0)
                                            @foreach ($engineer_list as $list)
                                                <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                            @endforeach
                                        @endif
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
                                            id="taskTableEdit">

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

    @if (count($amcdata) > 0)
        @foreach ($amcdata as $amc)
            <div class="modal  fade" id="servicecomments_{{ $amc->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">Service Comments ({{ $amc->doc_number }}) </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $amc_comments_data = $amc_comments->where('amc_id', $amc->id);
                                        @endphp

                                        @if (count($amc_comments_data) > 0)
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
                                                    @foreach ($amc_comments_data as $cmts)
                                                        <tr>
                                                            <td colspan="4">{{ $cmts->work }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    Pending
                                                                @else
                                                                    Completed
                                                                @endif
                                                            </td>
                                                            <td>{{ $cmts->full_name }}</td>
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

                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif


    <div class="modal side-panel fade" id="ModalCopyLink" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="">

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Copy URL</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Company Name</label>
                                    <select class="form-control js-example-basic-single" id="cust_name_copy" required>
                                        @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="copy-button" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-copy text-success"></i> Copy URL
                    </button>
                </div>
            </div>

            <input type="hidden" id="copy_company_id" value="{{ session('logged_session_data.company_id') }}" />
            <input type="hidden" id="copy_url" value="{{ url('crm-amc-service-request-customer') }}" />
            <script>
                $('#copy-button').click(function() {
                    var textToCopy = $('#copy_url').val();
                    var textToCopy2 = $('#cust_name_copy').val();
                    var textToCopy3 = $('#copy_company_id').val();

                    var finalText = textToCopy + '/' + textToCopy2 + '/' + textToCopy3;
                    console.log(finalText);

                    // Modern way
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(finalText).then(function() {
                            alert("Copied!");
                        }).catch(function(err) {
                            console.error("Clipboard error:", err);
                            alert("Failed to copy!");
                        });
                    } else {
                        // Fallback for older browsers
                        var tempTextarea = $('<textarea>');
                        $('body').append(tempTextarea);
                        tempTextarea.val(finalText).select();
                        document.execCommand('copy');
                        tempTextarea.remove();
                        alert("Copied!");
                    }
                });
            </script>

        </div>
    </div>




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
                           $("#deal_id_edit").val(dataResult['data'][i].deal_code?.code || '');

                            $("#edit_amc_code").text(dataResult['data'][i].doc_number);

                            date = new Date(dataResult['data'][i].date)
                                .toLocaleDateString(
                                    'en-CA');

                            $('#date_edit').val(date ? date.split('-').reverse().join('/') : '');



                            $("#cust_name_edit").val(dataResult['data'][i].cust_name);

                            //alert(dataResult['data'][i].cust_name);
                            //$("#select2-cust_name_edit-container").val(dataResult['data'][i].cust_name);   


                            $("#contact_person_edit").val(dataResult['data'][i].contact_person);
                            $("#mobile_no_edit").val(dataResult['data'][i].mobile_no);
                            $("#location_of_work_edit").val(dataResult['data'][i].location_of_work);

                            const scop = dataResult['data'][i].scope_of_work.split("$");
                            for (k = 0; k < scop.length; k++) {
                                $("#scope_of_work_edit_" + (k + 1)).val(scop[k]);
                                $("#row_edit_" + (k + 1)).css('display', '');
                            }


                            $('#service_date_edit').val(dataResult['data'][i].service_date ? dataResult['data'][
                                i
                            ].service_date.split('-').reverse().join('/') : '');

                            $("#service_time_edit").val(dataResult['data'][i].service_time);

                            if (dataResult['data'][i].status == 5) {
                                $("#status_edit").val(dataResult['data'][i].status);
                            } else {
                                $("#status_edit").val(2);
                            }

                            $("#source_edit").val(dataResult['data'][i].source);


                            //$("#service_engineer_edit").val(dataResult['data'][i].service_engineer);
                            //$('#service_engineer_edit').removeClass('js-example-basic-single');
                            const selectElement = document.getElementById("service_engineer_edit");
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
                        $("#date_edit").val();
                        $("#cust_name_edit").val();
                        $("#contact_person_edit").val();
                        $("#mobile_no_edit").val();
                        $("#location_of_work_edit").val();
                        $("#scope_of_work_edit").val();
                        $("#service_date_edit").val();
                        $("#service_time_edit").val();
                        $("#status_edit").val();
                        $("#source_edit").val();
                        $("#service_engineer_edit").val();
                        $("#edit_amc_code").text('');
                    }
                    $("#cust_name_edit").change();
                    $("#service_engineer_edit").change();
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
                    $('#taskTableEdit tbody').html(tr);
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
        <td><input type="text" class="form-control serial text-center" value="${rowCount}" ></td>
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
        // Add row
        $('#addRowEdit').click(function() {
            let rowCount = $('#taskTableEdit tbody tr').length + 1;
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

            $('#search_amc_req').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-amc.req.search') }}",
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

                                let formattedDate = new Date(amc_list.date)
                                    .toLocaleDateString('en-GB');


                                let ims = `<li class="nav-item w-100" role="presentation">
                            <button class="nav-link amc-request-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            ${formattedDate}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                    ${amc_list.status == 5
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

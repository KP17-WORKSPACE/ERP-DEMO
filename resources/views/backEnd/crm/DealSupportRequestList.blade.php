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

                localStorage.setItem('listViewPreReq', 'long');
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

                localStorage.setItem('listViewPreReq', 'short');

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
            const savedView = localStorage.getItem('listViewPreReq');
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
                    localStorage.setItem('listViewPreReq', 'short');
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
            <h4 class="mb-2">Pre-Sales Request

            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-requested-list', 'method' => 'POST', 'id' => 'crm-ps-track-service-list']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_support_id" id="search_support_id" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping"
                        value="{{ $ctrl_support_id }}">
                </div>



                {{-- {{ Form::close() }} --}}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Pre-Sales Request List </h4>
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
                                <button data-bs-toggle="modal" data-bs-target="#addPreSalesRequest" type="button"
                                    class="dropdown-item d-flex align-items-center text-success"><i
                                        class="ico icon-outline-add-square text-success title-15 me-2"></i>  Request</button>
                            </li>

                            <li>
                                <a href="{{ url('crm-deal-support-list')}}"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i>  Pre-Sales List</a>
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

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-requested-list', 'method' => 'POST', 'id' => 'crm-deal-support-requested-lis']) }}
                        <div class="row">

                            <div class="col-1 mb-2 filter-field d-none">
                                <label for="" class="form-label">Pre-Sales ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="search_support_id"
                                    value="{{ $ctrl_support_id }}">
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
                                            @if ($ctrl_customer_name == $value->id) selected @endif>{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-2 mb-2 filter-field d-none">
                                <label for="" class="form-label">Engineer</label>
                                <select class="form-control" name="search_sales_person" id="search_sales_person">
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
                                        <option value="1" @if ($ctrl_status == 1) selected @endif>Pending
                                        </option>
                                        <option value="2" @if ($ctrl_status == 2) selected @endif>Added
                                        </option>
                                        <option value="3" @if ($ctrl_status == 3) selected @endif>Completed
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
                            <button class="nav-link sales-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">{{ @$item->doc_number }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            {{ date('d/m/Y', strtotime(@$item->support_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ @$item->dealid->code }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->name }}</label>
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
                            <th class="text-center" width="70px">@lang('ID')</th>
                            <th class="text-center" width="70px">@lang('Deal No')</th>
                            <th class="text-center" width="100px">@lang('Date')</th>
                            <th class="text-start" width="200px">@lang('Customer Name')</th>
                            <th class="text-start" width="200px">@lang('Engineer')</th>
                            <th class="text-center" width="100px">@lang('Service Date')</th>
                            <th class="text-center" width="100px">@lang('Service Time')</th>

                            <th class="text-start" width="250px">@lang('Scope of Work')</th>
                            <th class="text-center" width="100px">@lang('Status')</th>
                            <th class="text-center" style="width: 130px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($support) > 0)
                            @foreach ($support as $value)
    
                                <tr @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                    <td class="text-center"><a target="_blank"
                                            href="{{ url('crm-deal-support/' . $value->id) }}">{{ @$value->doc_number }}</a>
                                    </td>
                                    <td class="text-center">
                                        @if ($value->deal_id != '')
                                            <a href="{{ url('get-url-deal/' . $value->dealid->code) }}"
                                                target="_blank">{{ @$value->dealid->code }}</a>
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->created_at)) }}</td>
                                    <td>{{ @$value->name }}</td>
                                    <?php
                                    $engineername = '';
                                    if ($value->support_person_id != '') {
                                        $st = explode(',', $value->support_person_id);
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
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->support_date)) }}</td>
                                    <td class="text-center">{{ date('h:i A', strtotime(@$value->time_from)) }}</td>
                                    <td>

                                        @php $scope_of_work = explode('$', $value->remarks); @endphp
                                        @foreach ($scope_of_work as $work)
                                            {{ $work }},
                                        @endforeach

                                    </td>
                                    <td class="text-center">
                                        @if ($value->status == 3)
                                            <span class="text-success">Completed</span>
                                        @elseif($value->status == 2)
                                            Added
                                        @elseif($value->status == 4)
                                            Cancel
                                        @else
                                            Pending
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <input type="hidden" id="doc_number_{{ @$value->id }}"
                                            value="{{ @$value->doc_number }}">
                                        <input type="hidden" id="customer_id_{{ @$value->id }}"
                                            value="{{ @$value->customer_id }}">
                                        <input type="hidden" id="date_{{ @$value->id }}"
                                            value="{{ date('Y-m-d', strtotime(@$value->created_at)) }}">
                                        <input type="hidden" id="customer_name_{{ @$value->id }}"
                                            value="{{ @$value->customer->name }}">
                                        <input type="hidden" id="contact_person_{{ @$value->id }}"
                                            value="{{ @$value->contact_person }}" />
                                        <input type="hidden" id="mobile_{{ @$value->id }}"
                                            value="{{ @$value->mobile }}" />
                                        <input type="hidden" id="location_of_work_{{ @$value->id }}"
                                            value="{{ @$value->site_name }}" />
                                        <input type="hidden" id="support_date_{{ @$value->id }}"
                                            value="{{ @$value->support_date ? date('d/m/Y', strtotime($value->support_date)) : '' }}" />
                                        <input type="hidden" id="time_from_{{ @$value->id }}"
                                            value="{{ @$value->time_from }}" />
                                        <input type="hidden" id="work_{{ @$value->id }}"
                                            value="{{ @$value->remarks }}" />
                                        <input type="hidden" id="support_person_{{ @$value->id }}"
                                            value="{{ @$value->support_person_id }}" />
                                        <input type="hidden" id="presales_status_id{{ @$value->id }}"
                                            value="{{ @$value->status }}">


                                        <div class="d-flex justify-content-start align-items-center gap-1">

                                            @if ($value->file != '')
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ url('public/uploads/crm_deal_support_doc/' . $value->file) }}"
                                                    target="_blank"><i
                                                        class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i></a>
                                            @endif

                                            <a class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                data-bs-target="#servicecomments_{{ $value->id }}"
                                                style="cursor: pointer;" data-backdrop="static" data-keyboard="false"><i
                                                    class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                    style="font-size: 16px"></i></a>

                                            @if (@$value->status == 2)
                                                <a onclick="edit_support_request({{ @$value->id }})"
                                                    class="btn btn-sm btn-light" style="cursor: pointer;"><i
                                                        class="ico icon-outline-pen-2 text-dark"
                                                        style="font-size: 16px;"></i></a>

                                                @if (@$value->is_delete == 1)
                                                    <a href="{{ url('crm-deal-support-list/' . $value->id . '/restore') }}"
                                                        onclick="return confirm('Are you sure you want to restore?')"
                                                        class="btn btn-sm btn-light" style="cursor: pointer;"><i
                                                            class="ico icon-bold-restart text-dark"
                                                            style="font-size: 16px;"></i></a>
                                                @else
                                                    <a href="{{ url('crm-deal-support-list/' . $value->id . '/delete') }}"
                                                        onclick="return confirm('Are you sure you want to delete?')"
                                                        class="btn btn-sm btn-light" style="cursor: pointer;"><i
                                                            class="ico icon-outline-trash-bin-minimalistic"
                                                            style="font-size: 16px" aria-hidden="true"></i></a>
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
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.sales-item', function() {

                        var id = $(this).data('id');

                        $('.sales-item').removeClass('active');
                        $('.sales-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('crm-deal-support-requested-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-deal-support') }}/" + id + "/view";
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {

                                $('#sales-details').html(response);
                            },
                            error: function() {
                                $('#sales-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="sales-details">
                @if (!empty($selectedSales) && is_array($selectedSales))
                    @include('backEnd.crm.DealSupport', $selectedSales)
                @else
                    <div data-bs-toggle="modal" data-bs-target="#addPreSalesRequest"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div data-bs-toggle="modal" data-bs-target="#addPreSalesRequest"
                                class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" data-bs-toggle="modal"
                                data-bs-target="#addPreSalesRequest">Pre-Sales Request</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                @endif
            </div>


        </div>
    </div>
    <div class="modal  fade" id="EditPreSales" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-list-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit (<span id="edit_doc_number_txt"></span>)
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="pre_sales_id" id="pre_sales_id">




                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <input class="form-control" id="cust_name" type="text" required name="cust_name"
                                        value="" readonly>
                                    <input id="cust_id" type="hidden" required name="cust_id" value=""
                                        readonly>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" id="contact_person" type="text" required
                                        name="contact_person" value="">


                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" id="mobile" type="text" required name="mobile"
                                        value="">


                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input class="form-control" id="location_of_work" type="text" autocomplete="off"
                                        required name="location_of_work" value="">

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


                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">



                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Status</label>
                                    <select class="form-control" name="presales_status" id="presales_status">

                                        <option value="1">Pending</option>
                                        <option value="2">Added</option>
                                        <option value="3">Completed</option>
                                    </select>



                                </div>




                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select id="engineer" name="engineer[]" class="form-control js-example-basic-single"
                                        multiple>

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
                                                                class="btn btn-light rounded-0 btn-sm deleteRowEdit">
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

    <div class="modal  fade" id="addPreSalesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-sales-req-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Request
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
                                        id="add_cust_name" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer_salesreq as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" id="add_contact_person" type="text" required
                                        name="contact_person" value="">



                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" id="add_mobile_no" type="text" required
                                        name="mobile" value="">

                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input type="text" class="form-control" name="add_site_name" id="add_site_name"
                                        required>


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
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Request
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    @if (count($support) > 0)
        @foreach ($support as $ps)
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
                                            $comments_data = $supportactivity->where('support_id', $ps->id);
                                        @endphp

                                        @if (count($comments_data) > 0)
                                            <table class="table table-bordered table-striped" width="100%"
                                                cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="50%">Comment</th>

                                                        <th style="width: 20%;">By</th>
                                                        <th width="20%">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->remarks }}</td>

                                                            <td>{{ $cmts->createdby->full_name }}</td>
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



    <script>
        function edit_support_request(id) {
            var custid = $('#customer_id_' + id).val();
            var c_date = $('#date_' + id).val();
            var custname = $('#customer_name_' + id).val();
            var contact_person = $('#contact_person_' + id).val();
            var mobile = $('#mobile_' + id).val();
            var location_of_work = $('#location_of_work_' + id).val();
            var support_date = $('#support_date_' + id).val();
            var doc_number = $('#doc_number_' + id).val();
            var time_from = $('#time_from_' + id).val();
            var work = $('#work_' + id).val();
            var support_person = $('#support_person_' + id).val();
            var presales_status_id = $('#presales_status_id' + id).val();


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
        <tr id="row_edit_${i}">
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
                    <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit">
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
            $("#engineer").val([]);


            $.each(values.split(","), function(i, e) {
                $("#engineer option[value='" + e + "']").prop("selected", true);
            });


            $('#pre_sales_id').val(id);
            $('#date').val(c_date);
            $('#cust_id').val(custid);
            $('#cust_name').val(custname);
            $('#contact_person').val(contact_person);
            $('#mobile').val(mobile);
            $('#location_of_work').val(location_of_work);
            $('#service_date').val(support_date);
            $('#service_time').val(time_from);
            $('#presales_status').val(presales_status_id);
            $('#edit_doc_number_txt').text(doc_number);


            $('#taskTableEdit tbody').empty();
            $('#taskTableEdit tbody').html(tr);


            $('#EditPreSales').modal('show');
            $('#engineer').change();

        }
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
                                $("#add_site_name").val(dataResult['data'][i].address);
                            }
                        } else {
                            $("#add_contact_person").val();
                            $("#add_mobile_no").val();
                            $("#add_site_name").val();
                        }
                        $("#loading_bg").css("display", "none");
                    }
                });
            }
        });
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
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRow"><i
                                                                class="ico icon-outline-trash-bin-minimalistic "
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
        <td width="5%"><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td width="5%"><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light rounded-0 btn-sm deleteRowEdit"><i
                                                                class="ico icon-outline-trash-bin-minimalistic "
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

            $('#search_support_id').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-pre-req.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                                let ims = `<li class="nav-item w-100" role="presentation">
                            <button class="nav-link sales-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                           ${get_format_date(amc_list.support_date)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            ${amc_list.deal_code}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.name}</label>
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

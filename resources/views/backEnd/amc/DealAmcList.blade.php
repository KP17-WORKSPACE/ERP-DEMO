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

                localStorage.setItem('listViewAMC', 'long');
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

                localStorage.setItem('listViewAMC', 'short');

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
            const savedView = localStorage.getItem('listViewAMC');
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
                    localStorage.setItem('listViewAMC', 'short');
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
            <h4 class="mb-2">AMC List <span class="text-success">
                    ({{ $ctrl_validity === '0' ? 'Active' : ($ctrl_validity === '1' ? 'Expired' : 'All') }})</span>
            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-list', 'method' => 'POST', 'id'
        => 'crm-amc-list']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_amc_id" id="search_amc" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping"
                        value="">
                </div>


                
                {{-- {{ Form::close() }} --}}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">AMC List <span class="text-success">
                        ({{ $ctrl_validity === '0' ? 'Active' : ($ctrl_validity === '1' ? 'Expired' : 'All') }})</span>
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
                                <a href="{{ url('crm-amc-service-request-list') }}"
                                    class="dropdown-item d-flex align-items-center"><i
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

                        {{ Form::open([
                            'class' => 'form-horizontal',
                            'files' => true,
                            'url' => 'crm-amc-list',
                            'method' => 'POST',
                            'id' => 'crm-amc-list',
                        ]) }}
                        <div class="row">

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">AMC ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="search_amc_id"
                                    value="{{ $ctrl_amc_id }}">
                            </div>

                            <div class="col-3 mb-2 filter-field d-none">
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
                                @php
                                    // Ensure $ctrl_date is in d/m/Y for flatpickr
                                    if (!empty($ctrl_date)) {
                                        try {
                                            $ctrl_date = \Carbon\Carbon::parse($ctrl_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $ctrl_date = '';
                                        }
                                    }

                                    if (!empty($ctrl_date2)) {
                                        try {
                                            $ctrl_date2 = \Carbon\Carbon::parse($ctrl_date2)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $ctrl_date2 = '';
                                        }
                                    }
                                @endphp
                                <label for="" class="form-label">Current AMC Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off"
                                    name="search_from_date" id="search_from_date" value="{{ $ctrl_date }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Expired AMC Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off"
                                    name="search_to_date" id="search_to_date" value="{{ $ctrl_date2 }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Validity</label>
                                <div class="form-group">
                                    <select class="form-control" name="validity" id="validity">
                                        <option @if ($ctrl_validity == '') selected @endif value="">All
                                        </option>
                                        <option value="0" @if ($ctrl_validity == '0') selected @endif>Active
                                        </option>
                                        <option value="1" @if ($ctrl_validity == '1') selected @endif>Expired
                                        </option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                </div>

                            </div>



                            <div class="col-md-3 filter-field d-none">
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
                            <button class="nav-link amc-item {{ $active_id == $item->id ? 'active' : '' }}"
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
                                            {{ date('d/m/Y', strtotime(@$item->start_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @App\SysHelper::com_curr_format($item->amount, 2, '.', ',') }}

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
                            <th width="100px">@lang('Sr No')</th>
                            <th width="100px">@lang('Deal ID')</th>
                            <th width="100px">@lang('Date')</th>
                            <th class="text-start" style="width: 150px;">@lang('Customer Name')</th>
                            <th width="100px">@lang('Start Date')</th>
                            <th width="100px">@lang('End Date')</th>
                            <th class="text-end" width="100px">@lang('Amount')</th>
                            <th class="text-start" width="110px">@lang('Sales Person')</th>
                            <th width="150px">@lang('AMC Track')</th>
                            <th width="70px">@lang('Status')</th>
                            <th width="100px">@lang('Invoicing')</th>
                            <th class="text-start" width="150px">@lang('Description')</th>
                            <th style="width: 140px;">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $amount_total = 0; ?>
                        @foreach ($amcdata as $value)
                            <tr @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                <td class="text-center amc-item " data-id="{{ @$value->id }}" onclick="list_style_new()"><a 
                                        >{{ @$value->doc_number }}</a></td>
                                <td class="text-center"><a
                                        href="{{ url('get-url-deal-track/' . @$value->deal_code->code) }}"
                                        >{{ @$value->deal_code->code }}</a></td>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                <td>{{ @$value->custname->name }}</td>

                                @if ($value->end_date < date('Y-m-d'))
                                    <td class="text-danger text-center">
                                        {{ date('d/m/Y', strtotime(@$value->start_date)) }}</td>
                                    <td class="text-danger text-center">{{ date('d/m/Y', strtotime(@$value->end_date)) }}
                                    </td>
                                    <td class="text-danger text-end">{{ @$value->amount }} </td>
                                @else
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->start_date)) }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->end_date)) }}</td>
                                    <td class="text-end">{{ @$value->amount }}
                                        <?php $amount_total += $value->amount; ?>
                                    </td>
                                @endif

                                @php $amcData = @App\SysHelper::getAMCEngAndRequestCount($value->cust_name); @endphp

                                <td>{{ $value->salesperson->full_name ?? '' }}</td>
                                <td class="text-center">
                                    <span class="badge border fw-normal rounded-0 text-dark"
                                        style="font-size: 12px; padding: 4px 8px;">
                                        {{ $amcData['amc_count'] }} Req, {{ $amcData['eng_count'] }} Engrs
                                    </span>
                                </td>

                                <td class="text-center">
                                    {!! $value->is_expired || $value->end_date < date('Y-m-d')
                                        ? '<span class="text-danger">Expired</span>'
                                        : '<span class="text-success">Active</span>' !!}
                                </td>


                                <td class="text-center">{{ $value->invoice ?? '' }}</td>

                                <td>

                                    {{ @$value->description }}
                                    @php $loc = $location_list->where('id',$value->cust_name)->max('address') @endphp
                                    <input type="hidden" id="location_{{ @$value->id }}"
                                        value="{{ $loc }}" />


                                </td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center gap-1">

                                        <a class="btn-sm btn btn-light"
                                            onclick="edit_service_request({{ $value->id }})"><i
                                                style="font-size: 16px;" class="ico icon-outline-pen-2 text-dark"
                                                aria-hidden="true"></i></a>

                                        @if (@$value->is_delete == 0)
                                            <a class="btn-sm btn btn-light" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-deactivate/' . $value->id . '') }}"><i
                                                    style="font-size: 16px;"
                                                    class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                    aria-hidden="true"></i></a>
                                        @endif
                                        @if (@$value->is_delete == 1)
                                            <a class="btn-sm btn btn-light" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-activate/' . $value->id . '') }}"><i
                                                    style="font-size: 16px;" class="ico icon-bold-restart text-dark"
                                                    aria-hidden="true"></i></a>
                                        @endif

                                        @if (@$value->is_delete == 0)
                                            @if (@$value->status == 1)
                                                <a class="btn btn-m btn-light text-dark"
                                                    onclick="add_service_request({{ $value->id }},'{{ $value->doc_number }}')"
                                                    title="Add Request"><i style="font-size: 16px;"
                                                        class="ico icon-outline-add-square text-success"
                                                        aria-hidden="true"></i>
                                                    Req</a>
                                            @elseif(@$value->status == 2)
                                                <a class="btn btn-m btn-light text-dark"
                                                    onclick="add_service_request({{ $value->id }},'{{ $value->doc_number }}')"
                                                    title="Add More Request"><i style="font-size: 16px;"
                                                        class="ico icon-outline-add-square text-success"
                                                        aria-hidden="true"></i>
                                                    Req</a>
                                                {{-- <a class="btn-sm btn-success"
                                        onclick="edit_service_request({{ $value->id }})">Service Requested</a> --}}
                                            @else
                                            @endif
                                        @endif

                                    </div>


                                </td>
                            </tr>
                        @endforeach

                    </tbody>


                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end fw-bold">{{ @App\SysHelper::com_curr_format($amount_total, 2, '.', ',') }}
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.amc-item', function() {

                        var id = $(this).data('id');

                        $('.amc-item').removeClass('active');
                        $('.amc-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('crm-amc-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-amc-detail') }}/" + id;
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
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="amc-details">
                @if (!empty($selectedAMC) && is_array($selectedAMC))
                    @include('backEnd.amc.DealAmcDetail', $selectedAMC)
                @else
                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div data-bs-toggle="modal" data-bs-target="#AddAmcModal"
                                class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" data-bs-toggle="modal"
                                data-bs-target="#AddAmcModal">AMC</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                @endif
            </div>


        </div>
    </div>


    <div class="modal side-panel fade" id="AddAmcModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 400px !important;left: 31%">
            {{ Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'crm-amc-add',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
            ]) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add
                        ({{ @App\SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number') }})</h4>
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
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" class="form-control date-picker" name="date"
                                        id="date">
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
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

                                <div class="col-3">
                                    <label for="" class="form-label">Start Date</label>
                                    <input class="form-control date-picker" type="text" name="start_date"
                                        id="start_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">End Date</label>
                                    <input class="form-control date-picker" type="text" name="end_date"
                                        id="end_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Invoicing</label>
                                    <div class="form-group">
                                        <select class="form-control" type="text" name="invoice" id="invoice"
                                            required>
                                            <option value="">-Select-</option>
                                            <option value="Monthly">Monthly</option>
                                            <option value="Quarterly">Quarterly</option>
                                            <option value="Half Yearly">Half Yearly</option>
                                            <option value="Yearly">Yearly</option>
                                        </select>
                                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                    </div>
                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Amount</label>
                                    <input class="form-control" type="number" step="any" name="amount"
                                        id="amount" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" type="text"
                                        name="sales_person" id="sales_person" required>
                                        <option value="">-Select-</option>
                                        @if (count($salesperson) > 0)
                                            @foreach ($salesperson as $dt)
                                                <option value="{{ $dt->user_id }}">{{ $dt->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Description</label>
                                    <input data-bs-toggle="modal" data-bs-target="#narrationModal" class="form-control"
                                        type="text" name="description" id="description">
                                </div>

                                @if (session('logged_session_data.company_id') == 1)

                                 <div class="col-3">                                        
                                            <label class="form-label">Company</label>
                                            <div class="form-group">
                                                 <select class="form-control js-example-basic-single" name="base_company" id="base_company" required>
                                                   
                                                    @foreach ($base_company_list as $value2)
                                                    
                                                    <option value="{{ @$value2->id }}" @if(session('logged_session_data.company_id') == @$value2->id) selected @endif>{{ @$value2->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                @endif

                                    


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


    <div class="modal side-panel fade" style="z-index: 2050;" id="descriptionModal" data-bs-backdrop="false"
        tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" style="height: 300px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Description</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label">Description:</label>
                                    <div class="form-group">
                                        <textarea type="text" class="form-control" id="add_description" style="height: 150px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" onclick="addDescription()">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal side-panel fade" id="ModalAddNewAMCEdit" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 600px !important;left: 31%">
            {{ Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'crm-amc-update',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
            ]) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit (<span class="font-weight-600" id="edit_doc_number"></span>)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">
                                <input type="hidden" name="amcid_edit" id="amcid_edit" />

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" id="deal_id_edit" class="form-control" name="deal_id">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" id="date_edit" class="form-control date-picker"
                                        name="date">
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <select id="cust_name_edit" class="form-control js-example-basic-single"
                                        name="cust_name" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input id="contact_person_edit" class="form-control" type="text"
                                        name="contact_person" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input id="mobile_no_edit" class="form-control" type="text" name="mobile_no"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Start Date</label>
                                    <input id="start_date_edit" class="form-control date-picker" type="text"
                                        name="start_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">End Date</label>
                                    <input id="end_date_edit" class="form-control date-picker" type="text"
                                        name="end_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Invoicing</label>
                                    <div class="form-group">
                                        <select id="invoice_edit" class="form-control" type="text" name="invoice"
                                            required>
                                            <option value="">-Select-</option>
                                            <option value="Monthly">Monthly</option>
                                            <option value="Quarterly">Quarterly</option>
                                            <option value="Half Yearly">Half Yearly</option>
                                            <option value="Yearly">Yearly</option>
                                        </select>
                                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                    </div>
                                </div>


                                <div class="col-3">
                                    <label for="" class="form-label">Amount</label>
                                    <input id="amount_edit" class="form-control" type="number" step="any"
                                        name="amount" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select id="sales_person_edit" class="form-control js-example-basic-single"
                                        type="text" name="sales_person" required>
                                        <option value="">-Select-</option>
                                        @if (count($salesperson) > 0)
                                            @foreach ($salesperson as $dt)
                                                <option value="{{ $dt->user_id }}">{{ $dt->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Description</label>
                                    <input id="description_edit" data-bs-toggle="modal" data-bs-target="#narrationModal"
                                        class="form-control" type="text" name="description">
                                </div>

                                <div class="col-3 mt-3">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="amc_status"
                                                id="amc_expired" value="expired">
                                            <label class="form-check-label" for="amc_expired">
                                                AMC <strong>Expired</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-3 mt-3">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="amc_status"
                                                id="amc_renew" value="renew">
                                            <label class="form-check-label" for="amc_renew">
                                                AMC <strong>Renew</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-6" id="expired_comment_wrapper" style="display: none;">
                                    <div>

                                        <label class="form-label"> Reason for Expiration <span
                                                class="text-muted">(required)</span></label>

                                        <textarea class="form-control" name="expired_comment" id="expired_comment" style="height: 100px;"></textarea>

                                    </div>
                                </div>

                                <div class="col-6" id="renewal_comment_wrapper" style="display: none;">
                                    <div>

                                        <label class="form-label"> Reason for Renewal <span
                                                class="text-muted">(required)</span></label>

                                        <textarea class="form-control" name="renewal_comment" id="renewal_comment" style="height: 100px;"></textarea>

                                    </div>

                                </div>

                                <script>
                                    $(document).ready(function() {

                                        // When any AMC status radio button is changed
                                        $('input[name="amc_status"]').on('change', function() {
                                            const selected = $(this).val();

                                            if (selected === 'expired') {
                                                // Show expired comment, hide renewal
                                                $('#expired_comment_wrapper').show();
                                                $('#renewal_comment_wrapper').hide();
                                            } else if (selected === 'renew') {
                                                // Show renewal comment, hide expired
                                                $('#renewal_comment_wrapper').show();
                                                $('#expired_comment_wrapper').hide();
                                            }
                                        });

                                    });
                                </script>

                                     @if (session('logged_session_data.company_id') == 1)

                                 <div class="col-3">                                        
                                            <label class="form-label">Company</label>
                                            <div class="form-group">
                                                 <select class="form-control js-example-basic-single" name="base_company" id="edit_base_company" required>
                                                   
                                                    @foreach ($base_company_list as $value2)
                                                    
                                                    <option value="{{ @$value2->id }}">{{ @$value2->company_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                @endif



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


    <div class="modal  fade" id="ModalAddServiceRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'crm-amc-add-service-request',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
            ]) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Request (<span class="font-weight-600" id="add_req_docnumber"></span>)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">
                                <input type="hidden" name="amc_id" id="amc_id" />

                                <div class="col-4">
                                    <label for="" class="form-label">Location Of Work</label>
                                    <input type="text" class="form-control" name="location_of_work"
                                        id="location_of_work">
                                </div>

                                <div class="col-2-5">
                                    <label for="" class="form-label">Service Date</label>
                                    <input type="text" class="form-control date-picker" name="service_date"
                                        id="service_date">
                                </div>

                                <div class="col-2-5">
                                    <label for="" class="form-label">Service Time</label>
                                    <div class="form-group">
                                        <select type="time" class="form-control" name="service_time"
                                            id="service_time" required>
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
                                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                    </div>
                                </div>


                                <div class="col-2-5">
                                    <label for="" class="form-label">Source</label>
                                    <div class="form-group">
                                        <select class="form-control" name="source" id="source" required>
                                            <option selected value="">Select</option>
                                            <option value="Email">Email</option>
                                            <option value="Whatsapp">Whatsapp</option>
                                            <option value="Phone">Phone</option>
                                        </select>
                                        <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="service_engineer[]"
                                            id="service_engineer" required multiple>
                                            @if (count($engineer_list) > 0)
                                                @foreach ($engineer_list as $list)
                                                    <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
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
                                                    <td  width="5%"><input type="text" class="form-control serial text-center"
                                                            value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td  width="5%">
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-light  text-dark rounded-0 btn-sm deleteRow">
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
                        <i class="ico icon-outline-add-square text-success text-success"></i> Add Request
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>




    <script>
        $(document).ready(function() {

            $(document).on("change", "#cust_name", function() {

                var id = $("#cust_name").val();
                get_cust_name(id);
            });

            function get_cust_name(id) {
                $("#loading_bg").css("display", "block");
                var action = "{{ URL::to('crm-leads-customername') }}";
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
                        if (len > 0) {
                            for (var i = 0; i < len; i++) {
                                var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                        'data'][i]
                                    .first_name + ' ' + dataResult['data'][i].last_name;
                                var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                        i
                                    ]
                                    .address2 + ', ' + dataResult['data'][i].city + ', ' + dataResult[
                                        'data'][i]
                                    .statename + ', ' + dataResult['data'][i].name;
                                $("#contact_person").val(name.replace('null ', '').replace('null', ''));
                                $("#mobile_no").val(dataResult['data'][i].mobile);
                            }
                        } else {
                            $("#contact_person").val();
                            $("#mobile_no").val();
                        }
                        $("#loading_bg").css("display", "none");
                    }
                });
            }

        });
    </script>

    <script>
        function add_service_request(id, doc_number) {
            $('#amc_id').val(id);

            $('#add_req_docnumber').text(doc_number);
            $('#location_of_work').val($('#location_' + id).val());
            $('#ModalAddServiceRequest').modal('show');

        }

        function edit_service_request(id) {
            get_amc_edit(id);
            $('#ModalAddNewAMCEdit').modal('show');
        }

        function get_amc_edit(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-edit') }}";
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




                    if (len > 0) {
                        for (var i = 0; i < len; i++) {

                            $("#amcid_edit").val(dataResult['data'][i].id);
                            $("#edit_doc_number").text(dataResult['data'][i].doc_number);
                            $("#deal_id_edit").val(dataResult['data'][i].code);
                            $('#date_edit').val(dataResult['data'][i].date.split(' ')[0] ? dataResult['data'][i]
                                .date.split(' ')[0].split('-').reverse().join('/') : '');

                            // $("#date_edit").val(dataResult['data'][i].date.split(' ')[0]);
                            $("#cust_name_edit").val(dataResult['data'][i].cust_name).trigger('change');
                            $("#contact_person_edit").val(dataResult['data'][i].contact_person);
                            $("#mobile_no_edit").val(dataResult['data'][i].mobile_no);
                            $('#start_date_edit').val(dataResult['data'][i].start_date ? dataResult['data'][i]
                                .start_date.split('-').reverse().join('/') : '');

                            // $("#start_date_edit").val(dataResult['data'][i].start_date);
                            // $("#end_date_edit").val(dataResult['data'][i].end_date);
                            $('#end_date_edit').val(dataResult['data'][i].end_date ? dataResult['data'][i]
                                .end_date.split('-').reverse().join('/') : '');

                            $("#invoice_edit").val(dataResult['data'][i].invoice);
                            $("#amount_edit").val(dataResult['data'][i].amount);
                            $("#sales_person_edit").val(dataResult['data'][i].sales_person).trigger('change');
                            $("#description_edit").val(dataResult['data'][i].description);

                            $("#edit_base_company").val(dataResult['data'][i].company_id).trigger('change');

                            

                            let today = new Date();
                            today.setHours(0, 0, 0, 0); // compare only date, ignore time

                            let endDate = new Date(dataResult['data'][i].end_date);
                            endDate.setHours(0, 0, 0, 0);

                            if (dataResult['data'][i].is_expired == 1 || endDate < today) {
                                $('#amc_expired').prop('checked', true).trigger('change');
                                // Fill reason for expiration if present
                                $('#expired_comment').val(dataResult['data'][i].comment ?? '');
                            }

                            if(dataResult['data'][i].is_expired == 0){
                             $('#amc_renew').prop('checked', true).trigger('change');
                                // Fill reason for expiration if present
                                $('#renewal_comment').val(dataResult['data'][i].comment ?? '');
                            }





                        }
                    } else {
                        $("#amcid_edit").val();
                        $("#deal_id_edit").val();
                        $("#date_edit").val();
                        $("#cust_name_edit").val();
                        $("#contact_person_edit").val();
                        $("#mobile_no_edit").val();
                        $("#start_date_edit").val();
                        $("#end_date_edit").val();
                        $("#invoice_edit").val();
                        $("#amount_edit").val();
                        $("#sales_person_edit").val();
                        $("#description_edit").val();
                        $('#amc_expired').prop('checked', true);

                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <script>
        let descriptionModal;
        document.addEventListener("DOMContentLoaded", function() {
            const descriptionElement = document.getElementById('descriptionModal');
            descriptionModal = new bootstrap.Modal(descriptionElement);
        });
        let currentDescriptionInput = null;

        $(document).on('click', 'input[name="description"]', function() {
            currentDescriptionInput = $(this);
            $('#add_description').val(currentDescriptionInput.val());
            descriptionModal.show();
        });

        function addDescription() {
            if (currentDescriptionInput) {
                const val = $('#add_description').val();
                currentDescriptionInput.val(val);
                descriptionModal.hide();
                currentDescriptionInput = null;
            }
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
            <button type="button" class="btn btn-light text-dark rounded-0 btn-sm deleteRow"><i
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

            $('#search_amc').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-amc.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');

                     

                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                              

                                let ims = `<li class="nav-item w-100" role="presentation">
                <button class="nav-link amc-item"
                    data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                    data-bs-target="#purchase-order-1" type="button" role="tab" aria-controls="purchase-order-1"
                    aria-selected="true">
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
                                ${get_format_date(amc_list.start_date)}</div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                               ${amc_list.formatted_amount}

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


    <?php } catch (\Exception $e) { ?> {{ $e }}
    <?php  } ?>
@endsection

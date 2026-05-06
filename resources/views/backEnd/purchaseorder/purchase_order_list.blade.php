@extends('backEnd.newmasterpage')
@section('mainContent')


   <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


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

                sessionStorage.setItem('listViewPOList', 'long');
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

                sessionStorage.setItem('listViewPOList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from sessionStorage (tab-specific)
        document.addEventListener('DOMContentLoaded', () => {
            // Check if we have customer_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('po_action');
            
            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewPOList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewPOList');
                if (savedView === 'long') {
                    isFullList = false; // so that toggling once activates full view
                    list_style_new();
                } else {
                    // Default to short view
                    isFullList = true; // so that toggling once activates short view
                    list_style_new();
                }
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('listViewPOList', 'short');
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
            <h4 class="mb-2">Purchase Order
            </h4>


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_po" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>

             
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Purchase Order List
                </h4>
                <div class="search-filter-container mb-0">

                       <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;margin-right: 100px" placeholder="Search">

                    <button type="button" class="btn btn-light list_style_search_btn mt-1" id="exportExcelPurchaseOrders" style="margin-right: 66px;">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>

                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order', 'method' => 'get', 'id' => 'purchase-order-search']) }}
                        <div class="row">

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="{{ $flt_documents_number }}">
                            </div>

                            <div class="col-md-3 mb-2 filter-field d-none">
                                <label for="" class="form-label">Supplier</label>
                                <select class=" js-account-select" name="supplier" id="supplier" style="width: 100%;">
                                    <option value=""></option>
                                    {{-- @foreach ($supplier_list as $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}
                                    </option>
                                    @endforeach --}}
                                </select>
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Customer</label>
                                <input class="form-control" type="text" autocomplete="off" name="customer"
                                    value="{{ $flt_customer }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number"
                                    value="{{ $flt_dealno }}">
                            </div>





                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">GRN Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="grn_number"
                                    value="{{ $flt_grnno }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Purchase Invoice Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_invoice_number"
                                    value="{{ $flt_purchase_invoice_no }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Purchase Return Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_return_number"
                                    value="{{ $flt_purchase_return_no }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Currency</label>
                                <div class="form-group">
                                    <select class="form-control" name="currency" id="currency">
                                        <option value=""></option>
                                        @foreach ($currency as $value)
                                            <option value="{{ @$value->id }}"
                                                @if ($flt_currency == $value->id) selected @endif>
                                                {{ @$value->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                </div>
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
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off"
                                    name="from_date" id="from_date" value="{{ $ctrl_date }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date"
                                    id="to_date" value="{{ $ctrl_date2 }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Attachment</label>

                                <div class="form-group">
                                    <select class="form-control" name="attachments" id="attachments">
                                        <option value="">Select</option>
                                        <option value="1" @if ($flt_attachments == 1) selected @endif>With
                                            Attachments Only
                                        </option>
                                        <option value="2" @if ($flt_attachments == 2) selected @endif>Without
                                            Attachments Only
                                        </option>
                                        <option value="3">All</option>
                                    </select>
                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                </div>
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if (@$filter_by == '') selected @endif>-Select-
                                    </option>
                                    <option value="this_month" @if (@$filter_by == 'this_month') selected @endif>This Month
                                    </option>
                                    <option value="today" @if (@$filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if (@$filter_by == 'this_week') selected @endif>This Week
                                    </option>
                                    <option value="last_week" @if (@$filter_by == 'last_week') selected @endif>Last Week
                                    </option>
                                    <option value="last_month" @if (@$filter_by == 'last_month') selected @endif>Last Month
                                    </option>
                                    <option value="this_quarter" @if (@$filter_by == 'this_quarter') selected @endif>This
                                        Quarter</option>
                                    <option value="pre_quarter" @if (@$filter_by == 'pre_quarter') selected @endif>
                                        Previous
                                        Quarter</option>
                                    <option value="this_year" @if (@$filter_by == 'this_year') selected @endif>This Year
                                    </option>
                                    <option value="last_year" @if (@$filter_by == 'last_year') selected @endif>Last Year
                                    </option>
                                </select>
                            </div>
                            <script>
                                function set_filter() {
                                    if ($('#from_date').val() != "" || $('#to_date').val() != "") {
                                        $('#filter_by').val('')
                                    }
                                }
                            </script>

                            <div class="col-md-3 filter-field d-none">
                                <button type="submit" class="btn btn-light mt-4 ">
                                    <i class="ico icon-outline-magnifer"></i> Filter
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
                @if (count($purchaseorder) > 0)
                    @php $count = 1; @endphp
                    @foreach ($purchaseorder as $value)
                        @if (@$value->status == 2)
                            @continue
                        @endif



                        @if ($pending_grn == 1 || $pending_pi == 1 || $pending_pr == 1)
                            @if ($pending_grn == 1 && $value->grn_no == '')
                                <li class="nav-item w-100" role="presentation">
                                    <button class="nav-link po-item {{ $active_id == $value->id ? 'active' : '' }}"
                                        data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                        data-bs-target="#purchase-order-1" type="button" role="tab"
                                        aria-controls="purchase-order-1" aria-selected="true">
                                        <div class="row w-100">
                                             <div class="col-12">
                                                <label class="form-control-plaintext truncate-text">
                                                    {{ @$value->accountname->account_name }}</label>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-control-plaintext" style="font-size: 11px">{{ @$value->doc_number }}</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="form-control-plaintext" style="font-size: 11px">
                                                    {{ date('d/m/Y', strtotime(@$value->po_date)) }}</div>
                                            </div>
                                            <div class="col-4 text-end ">
                                                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                    {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                                    {{ @$value->currency_name->code }}
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </button>
                                </li>
                            @endif
                            @if ($pending_pi == 1 && $value->piv_no == '')
                                <li class="nav-item w-100" role="presentation">
                                    <button class="nav-link po-item {{ $active_id == $value->id ? 'active' : '' }}"
                                        data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                        data-bs-target="#purchase-order-1" type="button" role="tab"
                                        aria-controls="purchase-order-1" aria-selected="true">
                                        <div class="row w-100">
                                             <div class="col-12">
                                                <label class="form-control-plaintext truncate-text">
                                                    {{ @$value->accountname->account_name }}</label>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-control-plaintext" style="font-size: 11px">{{ @$value->doc_number }}</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="form-control-plaintext" style="font-size: 11px">
                                                    {{ date('d/m/Y', strtotime(@$value->po_date)) }}</div>
                                            </div>
                                            <div class="col-4 text-end">
                                                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                    {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                                    {{ @$value->currency_name->code }}
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </button>
                                </li>
                            @endif
                            @if ($pending_pr == 1 && $value->prt_no == '')
                                <li class="nav-item w-100" role="presentation">
                                    <button class="nav-link po-item {{ $active_id == $value->id ? 'active' : '' }}"
                                        data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                        data-bs-target="#purchase-order-1" type="button" role="tab"
                                        aria-controls="purchase-order-1" aria-selected="true">
                                        <div class="row w-100">
                                             <div class="col-12">
                                                <label class="form-control-plaintext truncate-text">
                                                    {{ @$value->accountname->account_name }}</label>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-control-plaintext" style="font-size: 11px">{{ @$value->doc_number }}</div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="form-control-plaintext" style="font-size: 11px">
                                                    {{ date('d/m/Y', strtotime(@$value->po_date)) }}</div>
                                            </div>
                                            <div class="col-4 text-end">
                                                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                    {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                                    {{ @$value->currency_name->code }}
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </button>
                                </li>
                            @endif
                        @else
                            <li class="nav-item w-100" role="presentation">
                                <button class="nav-link po-item {{ $active_id == $value->id ? 'active' : '' }}"
                                    data-id="{{ $value->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                    data-bs-target="#purchase-order-1" type="button" role="tab"
                                    aria-controls="purchase-order-1" aria-selected="true">
                                    <div class="row w-100">
                                         <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">
                                                {{ @$value->accountname->account_name }}</label>
                                        </div>
                                        <div class="col-4" >
                                            <div class="form-control-plaintext" style="font-size: 11px">{{ @$value->doc_number }}</div>
                                        </div>
                                        <div class="col-4 text-center">
                                            <div class="form-control-plaintext" style="font-size: 11px">
                                                {{ date('d/m/Y', strtotime(@$value->po_date)) }}</div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                                {{ @$value->currency_name->code }}
                                            </div>
                                        </div>
                                       
                                    </div>
                                </button>
                            </li>
                        @endif
                    @endforeach
                @else
                    No Records
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th style="width: 80px;">@lang('PO Date')</th>
                            <th style="width: 60px;">@lang('PO No')</th>

                            <th style="width: 200px;" class="text-start">@lang('Supplier')</th>
                            <th style="width: 190px;" class="text-start">@lang('Customer')</th>
                            <th style="width: 80px;">@lang('GRN No')
                            </th>
                            <th style="width: 80px;">@lang('PIV No')
                            </th>
                            <th style="width: 80px;">@lang('PRT No')
                            </th>
                            <th style="width: 60px;">@lang('Deal No')</th>
                            <th style="width: 80px;">@lang('Sales Person')</th>
                            <th style="width: 60px;">@lang('Currency')</th>
                            <th style="width: 80px;" class="text-end">@lang('Amount')</th>
                            <th style="width: 30px;"> <i class="ico icon-bold-paperclip"></i> </th>
                            <th style="width: 90px;">@lang('Action')

                            </th>
                        </tr>
                    </thead>
                    <tbody>

                    <tbody>
                        @php $count = 1; @endphp
                        @foreach ($purchaseorder as $value)
                            @if ($pending_grn == 1 || $pending_pi == 1 || $pending_pr == 1)
                                @if ($pending_grn == 1 && $value->grn_no == '')
                                    <tr
                                        @if (@$value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>
                                        <td class="text-center">{{ date('d/m/Y', strtotime(@$value->po_date)) }}</td>
                                        <td><a href="javacript:void(0);" onclick="list_style_new()" class="po-item"
                                                data-id="{{ $value->id }}">{{ @$value->doc_number }}</a></td>
                                        <td>{{ @$value->accountname->account_name }}</td>
                                        <td>{{ @$value->narration }}11</td>

                                        <td class="text-center">
                                            @if (empty($value->grn_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->grn_no) as $grn)
                                                    <a href="javacript:void(0);"onclick="list_style_new()"
                                                        data-id="{{ @App\SysHelper::getGRNID($grn) }}"
                                                        class="grn-item">{{ trim($grn) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty($value->piv_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->piv_no) as $piv)
                                                    <a href="javacript:void(0);"onclick="list_style_new()"
                                                        data-id="{{ @App\SysHelper::getPurchaseIvoiceID($piv) }}"
                                                        class="pi-item">{{ trim($piv) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty($value->prt_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->prt_no) as $prt)
                                                    <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}"
                                                        target="_blank">{{ trim($prt) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty(@$value->code))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', @$value->code) as $code)
                                                    <a href="{{ url('get-url-deal-track/' . trim($code)) }}"
                                                        target="_blank">{{ trim($code) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                         <td class=""> @if(@$value->sales_person != null)
                                        {{ @$value->salesperson->first_name.' '.@$value->salesperson->last_name }}
                                    @elseif(@$value->sales_person_name != null)
                                    {{ @$value->sales_person_name}}
                                    @endif</td>
                                        <td class="text-center">{{ @$value->currency_name->code }}</td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}</td>
                                        <td class="text-center">
                                            @if (empty(@$value->attach))
                                            @else
                                                @foreach (explode(',', @$value->attach) as $att)
                                                    <a href="{{ url(trim($att)) }}" target="_blank"><i
                                                            class="ico icon-bold-paperclip"
                                                            aria-hidden="true"></i></a>&nbsp;
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="">

                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <a href="{{ url('purchase-order/' . $value->id . '?po_action=edit') }}"
                                                    class="btn btn-sm btn-light " title="Comments">
                                                    <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                                </a>

                                                <a href="{{ url('purchase-order/' . $value->id . '/print') }}"
                                                    class="btn btn-sm btn-light" title="Comments">
                                                    <i class="ico icon-bold-download-minimalistic text-white"
                                                        style="font-size: 16px;"></i>
                                                </a>

                                                @if (@$value->status == 2)
                                                    <a class="btn btn-light btn-sm"
                                                        href="{{ url('purchase-order/' . $value->id . '/restore') }}"
                                                        onclick="return confirm('Are you sure you want to restore this item?');">
                                                        <i class="ico icon-bold-restart text-white"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-light btn-sm"
                                                        href="{{ url('purchase-order/' . $value->id . '/delete') }}"
                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>
                                                    </a>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if ($pending_pi == 1 && $value->piv_no == '')
                                    <tr
                                        @if (@$value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>
                                        <td class="text-center">{{ date('d/m/Y', strtotime(@$value->po_date)) }}</td>
                                        <td><a href="javacript:void(0);" onclick="list_style_new()" class="po-item"
                                                data-id="{{ $value->id }}">{{ @$value->doc_number }}</a></td>
                                        <td>{{ @$value->accountname->account_name }}</td>
                                        <td>{{ @$value->narration }}12</td>
                                        <td class="text-center">
                                            @if (empty($value->grn_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->grn_no) as $grn)
                                                    <a href="javacript:void(0);"onclick="list_style_new()"
                                                        data-id="{{ @App\SysHelper::getGRNID($grn) }}"
                                                        class="grn-item">{{ trim($grn) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty($value->piv_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->piv_no) as $piv)
                                                    <a href="javacript:void(0);"onclick="list_style_new()"
                                                        data-id="{{ @App\SysHelper::getPurchaseIvoiceID($piv) }}"
                                                        class="pi-item">{{ trim($piv) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty($value->prt_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->prt_no) as $prt)
                                                    <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}"
                                                        target="_blank">{{ trim($prt) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if (empty(@$value->code))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', @$value->code) as $code)
                                                    <a href="{{ url('get-url-deal-track/' . trim(@$code)) }}"
                                                        target="_blank">{{ trim(@$code) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class=""> @if(@$value->sales_person != null)
                                        {{ @$value->salesperson->first_name.' '.@$value->salesperson->last_name }}
                                    @elseif(@$value->sales_person_name != null)
                                    {{ @$value->sales_person_name}}
                                    @endif</td>
                                        <td class="text-center">{{ @$value->currency_name->code }}</td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}</td>
                                        <td class="text-center">
                                            @if (empty(@$value->attach))
                                            @else
                                                @foreach (explode(',', @$value->attach) as $att)
                                                    <a href="{{ url(trim($att)) }}" target="_blank"><i
                                                            class="ico icon-bold-paperclip"
                                                            aria-hidden="true"></i></a>&nbsp;
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                 <a href="{{ url('purchase-order/' . $value->id . '?po_action=edit') }}"
                                                    class="btn btn-sm btn-light" title="Comments">
                                                    <i class="ico icon-outline-pen-2 text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>

                                                <a href="{{ url('purchase-order/' . $value->id . '/print') }}"
                                                    class="btn btn-sm btn-light" title="Comments">
                                                    <i class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>

                                                @if (@$value->status == 2)
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ url('purchase-order/' . $value->id . '/restore') }}"
                                                        onclick="return confirm('Are you sure you want to restore this item?');">
                                                        <i class="ico icon-bold-restart text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ url('purchase-order/' . $value->id . '/delete') }}"
                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="ico icon-bold-trash-bin-2 text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if ($pending_pr == 1 && $value->prt_no == '')
                                    <tr
                                        @if (@$value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>
                                        <td class="text-center">{{ date('d/m/Y', strtotime(@$value->po_date)) }}</td>
                                        <td><a href="javacript:void(0);" onclick="list_style_new()" class="po-item"
                                                data-id="{{ $value->id }}">{{ @$value->doc_number }}</a></td>
                                        <td>{{ @$value->accountname->account_name }}</td>
                                        <td>{{ @$value->narration }}13</td>

                                        <td class="text-center">
                                            @if (empty($value->grn_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->grn_no) as $grn)
                                                    <a href="javacript:void(0);"onclick="list_style_new()"
                                                        data-id="{{ @App\SysHelper::getGRNID($grn) }}"
                                                        class="grn-item">{{ trim($grn) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty($value->piv_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->piv_no) as $piv)
                                                    <a href="javacript:void(0);"onclick="list_style_new()"
                                                        data-id="{{ @App\SysHelper::getPurchaseIvoiceID($piv) }}"
                                                        class="pi-item">{{ trim($piv) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (empty($value->prt_no))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', $value->prt_no) as $prt)
                                                    <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}"
                                                        target="_blank">{{ trim($prt) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if (empty(@$value->code))
                                                <span class="text-dark">Pending</span>
                                            @else
                                                @foreach (explode(',', @$value->code) as $code)
                                                    <a href="{{ url('get-url-deal-track/' . trim(@$code)) }}"
                                                        target="_blank">{{ trim(@$code) }}</a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                         <td class=""> @if(@$value->sales_person != null)
                                        {{ @$value->salesperson->first_name.' '.@$value->salesperson->last_name }}
                                    @elseif(@$value->sales_person_name != null)
                                    {{ @$value->sales_person_name}}
                                    @endif</td>
                                        <td class="text-center">{{ @$value->currency_name->code }}</td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}</td>
                                        <td class="text-center">
                                            @if (empty(@$value->attach))
                                            @else
                                                @foreach (explode(',', @$value->attach) as $att)
                                                    <a href="{{ url(trim($att)) }}" target="_blank"><i
                                                            class="ico icon-bold-paperclip"
                                                            aria-hidden="true"></i></a>&nbsp;
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <a href="{{ url('purchase-order/' . $value->id . '?po_action=edit') }}"
                                                    class="btn btn-sm btn-light" title="Comments">
                                                    <i class="ico icon-outline-pen-2 text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>

                                                <a href="{{ url('purchase-order/' . $value->id . '/print') }}"
                                                    class="btn btn-sm btn-light" title="Comments">
                                                    <i class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>

                                                @if (@$value->status == 2)
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ url('purchase-order/' . $value->id . '/restore') }}"
                                                        onclick="return confirm('Are you sure you want to restore this item?');">
                                                        <i class="ico icon-bold-restart text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ url('purchase-order/' . $value->id . '/delete') }}"
                                                        onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="ico icon-bold-trash-bin-2 text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @else
                                <tr
                                    @if (@$value->status == 2) style="background-color: rgba(0, 0, 0, 0.19);" @endif>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->po_date)) }}</td>
                                    <td><a href="javacript:void(0);"onclick="list_style_new()" class="po-item"
                                            data-id="{{ $value->id }}">{{ @$value->doc_number }}</a></td>
                                    <td>{{ @$value->accountname->account_name }}</td>
                                    <td>

                                    @php
                                    
                                        $selectedCompanies = $value->ref_company_id
        ? explode(',', $value->ref_company_id)
        : [];
      
                                    @endphp
                                    
                                      @forelse ($selectedCompanies as $companyId)
                                          @php
                                              $company = App\SysCustSuppl::find($companyId);
                                          @endphp
                                          @if ($company)
                                              <span>{{ $company->name }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                              </span>
                                          @endif
                                      @empty
                                          
                                      @endforelse


                                    </td>

                                    <td class="text-center">
                                        @if (empty($value->grn_no))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->grn_no) as $grn)
                                                {{-- <a href="{{ url('get-url-purchase-grn/' . trim($grn)) }}"
                                                    target="_blank">{{ trim($grn) }}</a> --}}
                                                <a href="javacript:void(0);"onclick="list_style_new()"
                                                    data-id="{{ @App\SysHelper::getGRNID($grn) }}"
                                                    class="grn-item">{{ trim($grn) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (empty($value->piv_no))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->piv_no) as $piv)
                                                <a href="javacript:void(0);"onclick="list_style_new()"
                                                    data-id="{{ @App\SysHelper::getPurchaseIvoiceID($piv) }}"
                                                    class="pi-item">{{ trim($piv) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (empty($value->prt_no))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->prt_no) as $prt)
                                                <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}"
                                                    target="_blank">{{ trim($prt) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if (empty(@$value->code))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', @$value->code) as $code)
                                                <a href="{{ url('get-url-deal-track/' . trim(@$code)) }}"
                                                    target="_blank">{{ trim(@$code) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class=""> @if(@$value->sales_person != null)
                                        {{ @$value->salesperson->first_name.' '.@$value->salesperson->last_name }}
                                    @elseif(@$value->sales_person_name != null)
                                    {{ @$value->sales_person_name}}
                                    @endif</td>
                                    <td class="text-center">{{ @$value->currency_name->code }}</td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                    </td>
                                    <td class="text-center">
                                        @if (empty(@$value->attach))
                                        @else
                                            @foreach (explode(',', @$value->attach) as $att)
                                                <a href="{{ url(trim($att)) }}" target="_blank"><i
                                                        class="ico icon-bold-paperclip" aria-hidden="true"></i></a>&nbsp;
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                             <a href="{{ url('purchase-order/' . $value->id . '?po_action=edit') }}"
                                                class="btn btn-sm btn-light " title="Comments">
                                                <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                            </a>

                                            <a href="{{ url('purchase-order/' . $value->id . '/print') }}"
                                                class="btn btn-sm btn-light" title="Comments">
                                                <i class="ico icon-bold-download-minimalistic text-dark"
                                                    style="font-size: 16px;"></i>
                                            </a>

                                            @if (@$value->status == 2)
                                                <a class="btn btn-light btn-sm"
                                                    href="{{ url('purchase-order/' . $value->id . '/restore') }}"
                                                    onclick="return confirm('Are you sure you want to restore this item?');">
                                                    <i class="ico icon-bold-restart text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-light btn-sm"
                                                    href="{{ url('purchase-order/' . $value->id . '/delete') }}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                    <i class="ico icon-bold-trash-bin-2 text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            @endif

                                        </div>
                                        {{-- <a class="btn-sm btn-info" href="{{url('purchase-order/' . $value->id . '/edit')}}"><i
                                                class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-warning" href="{{url('purchase-order/' . $value->id . '/print')}}"><i
                                                class="fa fa-download" aria-hidden="true"></i></a>

                                        @if (@$value->status == 2)
                                        <a class="btn-sm btn-warning" href="{{url('purchase-order/' . $value->id . '/restore')}}"
                                            onclick="return confirm('Are you sure you want to restore this item?');"><i
                                                class="fa fa-undo" aria-hidden="true"></i></a>
                                        @else
                                        <a class="btn-sm btn-danger" href="{{url('purchase-order/' . $value->id . '/delete')}}"
                                            onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif --}}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    </tbody>

                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                 $(document).ready(function() {
                    $(document).on('click', '.po-item', function() {
                        var id = $(this).data('id');
                      
                        $('.po-item').removeClass('active');
                        $('.po-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('purchase-order') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('purchase-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#po-details').html(response);
                            },
                            error: function() {
                                $('#po-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>


            <script>
                $(document).ready(function() {
                    $('.grn-item').on('click', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');


                        $('.grn-item').removeClass('active');
                        $('.grn-item[data-id="' + id + '"]').addClass('active');

                        var action = "{{ URL::to('purchasegrn-details') }}/" + id;
                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#po-details').html(response);
                            },
                            error: function() {
                                $('#po-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            }
                        });

                        $("#loading_bg").css("display", "none");
                    });


                });
            </script>

            <script>
                $(document).ready(function() {
                    $('.pi-item').on('click', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');
                        $('.pi-item').removeClass('active');
                        $('.pi-item[data-id="' + id + '"]').addClass('active');

                        var action = "{{ URL::to('purchase-invoice-details') }}/" + id;
                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#po-details').html(response);
                            },
                            error: function() {
                                $('#po-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            }
                        });

                        $("#loading_bg").css("display", "none");
                    });


                });
            </script>


            <div class="" role="tabpanel" aria-labelledby="po-tab" id="po-details">
                @if ($action === 'add')
                    @include('backEnd.purchaseorder.manage_purchase_order')
                @elseif($action === 'edit')
                    @include('backEnd.purchaseorder.manage_purchase_order_edit', $editData)
                @elseif (!empty($selectedPO) && is_array($selectedPO))
                    @include('backEnd.purchaseorder.po-pdf-html', $selectedPO)
                @else
                    <form id="supplierForm" method="GET" action="{{ url('purchase-order') }}">


                        <input type="hidden" name="po_action" value="add">

                        <div onclick="document.getElementById('supplierForm').submit();"
                            class="container-fluid d-flex flex-column justify-content-center align-items-center"
                            style="min-height: 90vh;">

                            <!-- Icon + Heading -->
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3" style="cursor:pointer"> Purchase Order</h1>
                                {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                            </div>

                        </div>
                    </form>
                @endif
            </div>


        </div>
    </div>


    <script>
        const SHOW_SUPPLIER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};
        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_supp_account_list_ajax') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search_text: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                      let text = "";

                                if (SHOW_SUPPLIER_CODE) {
                                    text = item.account_name + " (" + item.account_code + ")";
                                } else {
                                    text = item.account_name;  // no code
                                }

                                return {
                                    id: item.id,
                                    text: text
                                };
                                })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Select Account',
                    minimumInputLength: 2
                });
            }

            // Initial init
            initAccountSelect2('.js-account-select');

            // Re-initialize on focus (if needed for dynamically added fields)
            $(document).on('focus', '.js-account-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
                }
            });

            // Open dropdown and focus search box on click
            $(document).on('click', '.js-account-select', function() {
                $(this).select2('open');
            });

            // Focus the search input inside the opened Select2 dropdown
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector(
                        '.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 0);
            });
        });
    </script>

       <script>
        $(document).ready(function() {

            $('#search_po').on('keyup', function() {
                var query = $(this).val();
                

                $.ajax({
                    url: "{{ route('purchase.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        

                      



                        $('#short-list').html('');

                        if (data.length > 0) {
                            $.each(data, function(index, purchase) {


                

                             


                                let ims = `<li class="nav-item w-100" role="presentation">
                                <button class="nav-link po-item"
                                    data-id="${purchase.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                    data-bs-target="#purchase-order-1" type="button" role="tab"
                                    aria-controls="purchase-order-1" aria-selected="true">
                                    <div class="row w-100">
                                          <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">
                                                    ${(purchase.accountname ? purchase.accountname.account_name : 'N/A')}</label>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-control-plaintext" style="font-size: 11px">${purchase.doc_number}</div>
                                        </div>
                                        <div class="col-4 text-center">
                                            <div class="form-control-plaintext" style="font-size: 11px">
                                               ${get_format_date(purchase.po_date)}
                                                </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                           
                                              ${(parseFloat(purchase.amount) || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}
                                                ${(purchase.currency_name ? purchase.currency_name.code : '')}
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
$(document).ready(function() {
    $('#exportExcelPurchaseOrders').on('click', function(e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalPO = @json($purchaseorder->count() ?? 0);
        var dateFrom = @json($ctrl_date ?? '');
        var dateTo = @json($ctrl_date2 ?? '');

        var $table = $('#long-list');

        var visibleColIndexes = [];
        var headerLabels = [];
        var lastIndex = $table.find('thead tr th').length - 1;

        $table.find('thead tr th').each(function(i) {
            if (i === lastIndex) return;
            if ($(this).css('display') !== 'none') {
                var label = $(this).text().trim();
                if (['actions', 'action', 'actions '].includes(label.toLowerCase().trim())) {
                    return;
                }
                visibleColIndexes.push(i);
                headerLabels.push(label);
            }
        });

        function formatDMY(value) {
            if (!value) return '-';
            var normalized = value.trim().replace(/\s+/g, '');
            var parts = normalized.split(/[\/\-\.]/);
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                return parts[0] + '/' + parts[1] + '/' + parts[2];
            }
            return value;
        }

        var rows = [];
        rows.push([companyName]);
        rows.push(['Purchase Orders (' + totalPO + ')']);

        if (dateFrom || dateTo) {
            var parts = [];
            if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
            if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
            rows.push([parts.join('  ')]);
        }

        rows.push([]);
        rows.push(headerLabels);

        $table.find('tbody tr').each(function() {
            var $cells = $(this).find('td');
            if ($cells.length === 0) return;
            var rowData = [];
            visibleColIndexes.forEach(function(i) {
                var cellText = $cells.eq(i).text().trim().replace(/\s+/g, ' ');
                rowData.push(cellText);
            });
            rows.push(rowData);
        });

        if (rows.length <= 5) {
            alert('No data available for export');
            return;
        }

        var N = headerLabels.length || 1;
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Purchase Orders');
            var wsCols = [];
            for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
            worksheet.columns = wsCols;

            var hdrIdx = rows.indexOf(headerLabels);
            if (hdrIdx < 0) hdrIdx = rows.length - 1;

            // Meta rows (company name, page title, optional date rows)
            var wsRowNum = 0;
            for (var ri = 0; ri < hdrIdx; ri++) {
                if (!(rows[ri] && rows[ri][0])) continue; // skip blank separators
                wsRowNum++;
                var wsRow = worksheet.addRow([]);
                wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                wsRow.getCell(1).value = rows[ri][0] || '';
                if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            }

            // Blank separator
            wsRowNum++;
            worksheet.addRow([]);

            // Column header row
            wsRowNum++;
            var wsHdrRow = worksheet.addRow(headerLabels);
            wsHdrRow.height = 20;
            wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.border    = {
                    top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                    right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
                };
            });

            // Data rows
            for (var di = hdrIdx + 1; di < rows.length; di++) {
                var wsDataRow = worksheet.addRow(rows[di]);
                wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.border = {
                        top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                        right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                    };
                });
            }

            workbook.xlsx.writeBuffer().then(function (buffer) {
                var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                function pad(n) { return n < 10 ? '0' + n : n; }
                var d = new Date();
                var filename = 'purchase_orders_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>

{{-- Form Validation Script --}}
<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize form validation for crm-deals-form
    FormValidator.init('tender-create-form', {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        toastrPosition: 'toast-top-right',
        toastrTimeout: 6000
    });
});
</script>

@if (!empty($auto_print) && $auto_print)
<script>
$(function() {
    var poId = "{{ $active_id }}";
    if (poId) {
        setTimeout(function() {
            window.location.href = "{{ url('purchase-order') }}/" + poId + "/print";
        }, 600);
    }
});
</script>
@endif

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

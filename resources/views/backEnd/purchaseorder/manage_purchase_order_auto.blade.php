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
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
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

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order', 'method' => 'get', 'id' => 'purchase-order-search']) }}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="{{ $flt_documents_number }}">
                </div>


                <button type="submit" class="btn btn-light">
                    <i class="ico icon-outline-magnifer"></i>
                </button>
                {{ Form::close() }}
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
                                    value="{{ @$flt_customer }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number"
                                    value="{{ @$flt_dealno }}">
                            </div>





                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">GRN Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="grn_number"
                                    value="{{ @$flt_grnno }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Purchase Invoice Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_invoice_number"
                                    value="{{ @$flt_purchase_invoice_no }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Purchase Return Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_return_number"
                                    value="{{ @$flt_purchase_return_no }}">
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
                                <button type="submit" class="btn btn-success mt-4 rounded-0"
                                    id="btnSubmit">Filter</button>
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
                    <li class="nav-item w-100" role="presentation">
                                    <button class="nav-link po-item {{ @$active_id == $value->id ? 'active' : '' }}"
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

                       



                        
                    @endforeach
                @else
                    No Records
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th style="width: 80px;">@lang('PO Date')</th>
                            <th style="width: 60px;">@lang('PO No')</th>

                            <th style="width: 200px;">@lang('Supplier')</th>
                            <th style="width: 190px;">@lang('Customer')</th>
                            <th style="width: 80px;">@lang('GRN No')
                            </th>
                            <th style="width: 80px;">@lang('PIV No')
                            </th>
                            <th style="width: 80px;">@lang('PRT No')
                            </th>
                            <th style="width: 60px;">@lang('Deal No')</th>
                            <th style="width: 60px;">@lang('Currency')</th>
                            <th style="width: 100px;" class="text-end">@lang('Amount')</th>
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
                                        <td>{{ @$value->narration }}</td>

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
                                                <a href="{{ url('purchase-order/' . $value->id . '/edit') }}"
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
                                        <td>{{ @$value->narration }}</td>
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
                                                <a href="{{ url('purchase-order/' . $value->id . '/edit') }}"
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
                                        <td>{{ @$value->narration }}</td>

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
                                                <a href="{{ url('purchase-order/' . $value->id . '/edit') }}"
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
                                    <td>{{ @$value->narration }}</td>

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
                                            <a href="{{ url('purchase-order/' . $value->id . '/edit') }}"
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
                 

                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $('.po-item').on('click', function() {
                        var id = $(this).data('id');
                        console.log(id)
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
                        @include('backEnd.purchaseorder.manage_purchase_order_auto_form')
                </div>
      

          

        </div>
    </div>


    <script>
        $(document).ready(function() {

   const SHOW_SUPPLIER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};


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
                console.log("clicked");
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


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

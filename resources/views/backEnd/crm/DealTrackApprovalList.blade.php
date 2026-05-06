@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
    // dd("WORK if (!isFullList) {
    //             // Switch to FULL LIST VIEW
    //             isFullList = true;

    //             leftNav.classList.remove('col-3');
    //             leftNav.classList.add('col-12');
    //             leftNav.style.width = '100%';

    //             content.classList.add('d-none');

    //             $('#long-list').removeClass('d-none');
    //             $('#short-list').removeClass('d-none');
    //             $('#short-list-items').addClass('d-none');");
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <?php try { ?>



    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Deal Track</h4>

        <div class="search-filter-container mb-4" id="short-list">

            <div class="input-group flex-nowrap">
                <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search"
                    aria-describedby="addon-wrapping">  
            </div>
            <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()"
                style="height: 32px;">
                <i class="ico icon-outline-list-down"></i>
            </button>

        </div>

        <div class="left-nav-list" id="invoice_list">
            <ul id="short-list-items" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($dealtrack) > 0)
                    @foreach ($dealtrack as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$value->customername->name }}    @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                            ({{ @$value->customername->code }})
                                            @endif</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size:11px">{{ @$value->deal_code->code }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                            {{ date('d/m/Y', strtotime(@$value->delivery_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                            {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }}
                                        </div>
                                    </div>
                                   
                                </div>
                                {{-- </button> --}}
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>
            <div id="long-list" style="display: none;">

                <input type="text" id="tableSearch" 
                                    class="form-control" 
                                    style="font-size:13px; width: 350px;
                                    position: absolute;
                                    top: 10px;
                                    right: 231px;" 
                                    placeholder="Search">

                <button type="button" class="btn btn-light list_style_search_btn" id="exportExcelDealTrack" title="Export to Excel" style="margin-right:66px">
                    <i class="ico icon-outline-export text-success"></i> Export
                </button>

                <button type="button" class="btn btn-light list_style_search_btn" onclick="search_box_show_hide()" style="margin-right: 8px;">
                    <i class="ico icon-outline-magnifer"></i>
                </button>

              

                <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button"
                    onclick="list_style_search()">
                    <i class="ico icon-outline-list-down"></i>
                </button>

                <div class="card mt-3" id="search_box" style="display: none;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-list', 'method' => 'get', 'id' => 'crm-deals-search']) }}
                                <div class="row">
                                    <div class="col-1 mb-2">
                                        <label for="" class="form-label">Deal ID</label>
                                        <input class="form-control" id="deal_id" type="text" autocomplete="off"
                                            name="deal_id" value="{{ $ctrl_deal_id }}">
                                    </div>

                                     @if (session('logged_session_data.company_id') == 1)

                                    <div class="col-md-2 mb-2">
                                        <label for="" class="form-label">Company Name</label>
                                        <select class="form-control js-example-basic-single" name="company_id2"
                                            id="company_id2">
                                            <option value="">Select Company</option>

                                            @if (@isset($company_list))
                                                @foreach ($company_list as $co)
                                                    <option @if ($ctrl_company_id2 == $co->id) selected @endif
                                                        value="{{ $co->id }}">{{ $co->company_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    @endif
                                    <div class="col-md-2 mb-2">
                                        <label for="" class="form-label">Customer Name</label>
                                        <select class="form-control js-example-basic-single" name="company_id"
                                            id="company_id">
                                            <option value="">-Select-</option>
                                            @foreach ($vendors as $value)
                                                <option value="{{ @$value->id }}"
                                                    @if ($ctrl_company_id == $value->id) selected @endif>{{ @$value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 27 || Auth::user()->role_id == 2)
                                        <div class="col-1-5 mb-2">
                                            <label for="" class="form-label">Sales Person</label>
                                            <select class="form-control js-example-basic-single" name="owner_id"
                                                id="owner_id">
                                                <option value="">-Select-</option>
                                                @foreach ($staff as $value)
                                                    <option value="{{ @$value->user_id }}"
                                                        @if ($ctrl_owner_id == $value->user_id) selected @endif>
                                                        {{ @$value->full_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                 

                                    <div class="col-1 mb-2">
                                        <label for="" class="form-label">Delivery Date</label>
                                        <input class="form-control date-picker" id="date" type="text"
                                            autocomplete="off" name="date"
                                            value="{{ @App\SysHelper::normalizeToDmy($ctrl_date) }}">
                                    </div>


                                    <div class="col-1 mb-2">
                                        <label for="" class="form-label">From Date</label>
                                        <input class="form-control date-picker" id="from_date" type="text"
                                            autocomplete="off" name="from_date"
                                            value="{{ @App\SysHelper::normalizeToDmy($from_date) }}">
                                    </div>



                                    <div class="col-1 mb-2">
                                        <label for="" class="form-label">To Date</label>
                                        <input class="form-control date-picker" id="to_date" type="text"
                                            autocomplete="off" name="to_date"
                                            value="{{ @App\SysHelper::normalizeToDmy($to_date) }}">
                                    </div>


                                       <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Status</label>
                                        <select class="form-control js-example-basic-single" name="status_id"
                                            id="status_id">
                                            <option value="10" @if ($ctrl_status_id == '10') selected @endif>
                                                -Select-</option>

  @if (session('logged_session_data.designation_id') == 8 || Auth::user()->role_id == 1 || Auth::user()->id == 56)
                                             
                                <option @if($ctrl_status_id == "A1") selected @endif value="A1">Accounts Approved</option>
                                <option @if($ctrl_status_id == "A2") selected @endif value="A2">Accounts Rejected</option>
                                <option @if($ctrl_status_id == "A3") selected @endif value="A3">Accounts Pending</option>
                                @endif
                                            @if (session('logged_session_data.designation_id') == 27 || Auth::user()->role_id == 1 || Auth::user()->id == 56)
                                
                                <option @if($ctrl_status_id == "S1") selected @endif value="S1">Sales Approved</option>
                                <option @if($ctrl_status_id == "S2") selected @endif value="S2">Sales Rejected</option>
                                <option @if($ctrl_status_id == "S3") selected @endif value="S3">Sales Pending</option>
                                @endif
                                            @if (session('logged_session_data.designation_id') == 20 || Auth::user()->role_id == 1 || Auth::user()->id == 56)

                                <option @if($ctrl_status_id == "P1") selected @endif value="P1">Purchase Approved</option>
                                <option @if($ctrl_status_id == "P2") selected @endif value="P2">Purchase Rejected</option>
                                <option @if($ctrl_status_id == "P3") selected @endif value="P3">Purchase Pending</option>
                                <option @if($ctrl_status_id == "P4") selected @endif value="P4">Purchase Partial Delivery</option>
                                @endif
                                
                                   @if (session('logged_session_data.designation_id') == 35 ||
                                                    Auth::user()->role_id == 1 ||
                                                    Auth::user()->id == 56 ||
                                                    Auth::user()->id == 49 ||
                                                    Auth::user()->id == 51)
                                <option @if($ctrl_status_id == "I1") selected @endif value="I1">Invoice Approved</option>
                                <option @if($ctrl_status_id == "I2") selected @endif value="I2">Invoice Rejected</option>
                                <option @if($ctrl_status_id == "I3") selected @endif value="I3">Invoice Pending</option>
                                @endif
                                
                                      @if (session('logged_session_data.designation_id') == 34 ||
                                                    Auth::user()->role_id == 1 ||
                                                    Auth::user()->id == 56 ||
                                                    Auth::user()->id == 49 ||
                                                    Auth::user()->id == 51)
                                <option @if($ctrl_status_id == "D1") selected @endif value="D1">Delivery Completed</option>
                                <option @if($ctrl_status_id == "D2") selected @endif value="D2">Delivery Rejected</option>
                                <option @if($ctrl_status_id == "D3") selected @endif value="D3">Out For Delivery</option>
                                <option @if($ctrl_status_id == "D4") selected @endif value="D4">Pending For Delivery</option>
                                <option @if($ctrl_status_id == "D5") selected @endif value="D5">Ready For Delivery</option>
                                @endif

                                 @if (session('logged_session_data.designation_id') == 2 ||
                                                    Auth::user()->role_id == 1 ||
                                                    Auth::user()->id == 49 ||
                                                    Auth::user()->id == 51)
                                <option @if($ctrl_status_id == "R1") selected @endif value="R1">Payment Received</option>
                                <option @if($ctrl_status_id == "R2") selected @endif value="R2">Receivables Rejected</option>
                                <option @if($ctrl_status_id == "R3") selected @endif value="R3">Payment Pending</option>
                                <option @if($ctrl_status_id == "R4") selected @endif value="R4">Order Cancelled</option>
                                @endif
                  
                                    <option value="Z1">Partial Delivery</option>

                                           
                                        </select>
                                    </div>




                                
                              
                                    <div class="col-1">
                                        <button type="submit" class="btn btn-light add-btn mt-4" id="btnSubmit">
                                            <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                        </button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover mt-2 data-table table-fixed-header" id="long-list" style="table-layout: fixed;width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;" class="text-center">@lang('Deal ID')</th>
                                        @if (session('logged_session_data.company_id') == 1)
                                            <th style="width: 100px;">@lang('Company')</th>
                                        @endif
                                        <th style="width: 200px;" class="mobhd">@lang('Deal Name')</th>
                                        <th style="width: 200px;" class="mobhd">@lang('Customer')</th>
                                        <th style="width: 100px;" class="mobhd">@lang('Sales Person')</th>
                                        <th style="width: 100px;" class="mobhd  text-center">@lang('Delivery Date')</th>
                                        <th style="width: 100px;" class="mobhd  text-center">@lang('Created Date')</th>
                                        <th style="width: 100px;" class="mobhd">@lang('Payment Terms')</th>
                                        <th style="width: 100px;">@lang('Status')</th>
                                        <th style="width: 100px;" class="text-end">@lang('Value')</th>
                                        {{-- <th></th> --}}
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count =1; @endphp
                                    @foreach ($dealtrack as $value)
                                        <tr>
                                            <td class="text-center data-item" data-id="{{ $value->id }}" onclick="list_style_search()"><a>{{ @$value->deal_code->code }}</a>
                                            </td>
                                            @if (session('logged_session_data.company_id') == 1)
                                                <td>{{ $value->companyname->company_name }}</td>
                                            @endif
                                            <td class="mobhd">
                                                {{ @$value->dealid->deal_name }}

                                            </td>
                                            <td class="mobhd">{{ @$value->customername->name }}</td>
                                            <td class="mobhd">{{ @$value->ownername->full_name }}</td>
                                            <td class="mobhd  text-center">
                                                @if (date('d/m/Y', strtotime(@$value->delivery_date)) != '01/01/1970')
                                                    {{ date('d/m/Y', strtotime(@$value->delivery_date)) }}
                                                @endif
                                            </td>
                                            <td class="mobhd  text-center">
                                                @if (date('d/m/Y', strtotime(@$value->created_at)) != '01/01/1970')
                                                    {{ date('d/m/Y', strtotime(@$value->created_at)) }}
                                                @endif
                                            </td>
                                            <td class="mobhd">{{ @$value->paymentterms->title }}</td>
                                            <td>
                                                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->id == 21)
                                                    @if ($value->receivables == 1)
                                                        <span class="badge bg-success ">Payment Received</span>
                                                    @elseif($value->receivables == 2)
                                                        <span class="badge bg-danger ">Rejected</span>
                                                    @elseif($value->receivables == 3)
                                                        <span class="badge bg-primary ">Payment Pending</span>
                                                    @elseif($value->receivables == 4)
                                                        <span class="badge bg-dark ">Order Cancelled</span>
                                                    @elseif($value->delivery == 1)
                                                        <span class="badge bg-success ">Delivery Completed</span>
                                                    @elseif($value->delivery == 2)
                                                        <span class="badge bg-danger ">Delivery Rejected</span>
                                                    @elseif($value->delivery == 3)
                                                        <span class="badge bg-primary ">Out For Delivery</span>
                                                    @elseif($value->delivery == 4)
                                                        <span class="badge bg-primary ">Pending For Delivery</span>
                                                    @elseif($value->delivery == 5)
                                                        <span class="badge bg-primary ">Ready For Delivery</span>
                                                    @elseif($value->invoice == 1)
                                                        <span class="badge bg-success ">Invoice Approved</span>
                                                    @elseif($value->invoice == 2)
                                                        <span class="badge bg-danger ">Invoice Disapproved</span>
                                                    @elseif($value->invoice == 3)
                                                        <span class="badge bg-primary ">Invoice Pending</span>
                                                    @elseif($value->purchease == 1)
                                                        <span class="badge bg-success ">Purchase Approved</span>
                                                    @elseif($value->purchease == 2)
                                                        <span class="badge bg-danger ">Purchase Disapproved</span>
                                                    @elseif($value->purchease == 3)
                                                        <span class="badge bg-primary ">Purchase Pending</span>
                                                    @elseif($value->purchease == 4)
                                                        <span class="badge bg-primary ">Partial Delivery</span>
                                                    @elseif($value->sales == 1)
                                                        <span class="badge bg-success ">Sales Approved</span>
                                                    @elseif($value->sales == 2)
                                                        <span class="badge bg-danger ">Sales Disapproved</span>
                                                    @elseif($value->sales == 3)
                                                        <span class="badge bg-primary ">Sales Pending</span>
                                                    @elseif($value->accounts == 1)
                                                        <span class="badge bg-success ">Accounts Approved</span>
                                                    @elseif($value->accounts == 2)
                                                        <span class="badge bg-danger ">Accounts Disapproved</span>
                                                    @elseif($value->accounts == 3)
                                                        <span class="badge bg-primary ">Accounts Pending</span>
                                                    @else
                                                        <span class="badge bg-warning ">New</span>
                                                    @endif

                                                    {{--  accounts  --}}
                                                @elseif(App\SysHelper::account_approval_access())
                                                    @if ($value->accounts == 1)
                                                        <span class="badge bg-success btn-badge ">Approved</span>
                                                    @elseif($value->accounts == 2)
                                                        <span class="badge bg-danger btn-badge ">Disapproved</span>
                                                    @elseif($value->accounts == 3)
                                                        <span class="badge bg-primary btn-badge ">Pending</span>
                                                    @else
                                                        <span class="badge bg-warning btn-badge ">New</span>
                                                    @endif
                                                    {{--  sales  --}}
                                                @elseif(App\SysHelper::sales_approval_access())
                                                    @if ($value->sales == 1)
                                                        <span class="badge bg-success btn-badge ">Approved</span>
                                                    @elseif($value->sales == 2)
                                                        <span class="badge bg-danger btn-badge ">Disapproved</span>
                                                    @elseif($value->sales == 3)
                                                        <span class="badge bg-primary btn-badge ">Pending</span>
                                                    @else
                                                        <span class="badge bg-warning btn-badge ">New</span>
                                                    @endif
                                                    {{--  purchease  --}}
                                                @elseif(App\SysHelper::purchase_approval_access())
                                                    @if ($value->purchease == 1)
                                                        <span class="badge bg-success btn-badge ">Approved</span>
                                                    @elseif($value->purchease == 2)
                                                        <span class="badge bg-danger btn-badge ">Disapproved</span>
                                                    @elseif($value->purchease == 3)
                                                        <span class="badge bg-primary btn-badge ">Pending</span>
                                                    @else
                                                        <span class="badge bg-warning btn-badge ">New</span>
                                                    @endif
                                                    {{--  invoice  --}}
                                                @elseif(App\SysHelper::invoice_approval_access())
                                                    @if ($value->invoice == '1')
                                                        <span class="badge bg-success btn-badge ">Approved</span>
                                                    @elseif($value->invoice == '2')
                                                        <span class="badge bg-danger btn-badge ">Disapproved</span>
                                                    @elseif($value->invoice == '3')
                                                        <span class="badge bg-primary btn-badge ">Pending</span>
                                                    @else
                                                        <span class="badge bg-warning btn-badge ">New</span>
                                                    @endif
                                                    {{--  delivery  --}}
                                                @elseif(App\SysHelper::delivery_approval_access())
                                                    @if ($value->delivery == 1)
                                                        <span class="badge bg-success btn-badge ">Delivery Completed</span>
                                                    @elseif($value->delivery == 2)
                                                        <span class="badge bg-danger btn-badge ">Rejected</span>
                                                    @elseif($value->delivery == 3)
                                                        <span class="badge bg-primary btn-badge ">Out For Delivery</span>
                                                    @elseif($value->delivery == 4)
                                                        <span class="badge bg-primary btn-badge ">Pending For
                                                            Delivery</span>
                                                    @else
                                                        <span class="badge bg-warning btn-badge ">New</span>
                                                    @endif
                                                    {{--  receivables  --}}
                                                @elseif(App\SysHelper::receivables_approval_access())
                                                    @if ($value->receivables == 1)
                                                        <span class="badge bg-success btn-badge ">Payment Received</span>
                                                    @elseif($value->receivables == 2)
                                                        <span class="badge bg-danger btn-badge ">Rejected</span>
                                                    @elseif($value->receivables == 3)
                                                        <span class="badge bg-primary btn-badge ">Payment Pending</span>
                                                    @elseif($value->receivables == 4)
                                                        <span class="badge bg-dark btn-badge ">Order Cancelled</span>
                                                    @else
                                                        <span class="badge bg-warning btn-badge ">New</span>
                                                    @endif
                                                @endif
                                            </td>


                                            <td class="text-end">
                                                @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                                                {{ @App\SysHelper::com_curr_format($aed, 2, '.', ',') }}
                                                {{ @$value->deal_code->dealcurrency->code }}
                                            </td>
                                            {{-- <td class="text-end">
                                <a class="btn-sm btn-success text-white" href="{{url('crm-deal-track-approval/'.$value->id)}}">View</a>
                            </td> --}}
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </aside>


    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $(this).addClass('active');

                        var newUrl = "{{ url('crm-deal-track-approval-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('crm-deal-track-details') }}/" + id;

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            },
                            complete: function() {
                                $("#loading_bg").css("display", "none");
                            }
                        });
                    });
                });
            </script>

            <script>
                $(document).ready(function() {

                    $('#search_invoice').on('input', function() {
                        var query = $(this).val();

                        $.ajax({
                            url: "{{ route('crm-deals-track.search') }}",
                            type: "GET",
                            data: {
                                query: query
                            },
                            success: function(data) {
                                $('#short-list-items').html('');

                                if (data.length > 0) {
                                    $.each(data, function(index, invoice) {

                                        let ims = `<li class="nav-item w-100" role="presentation">
    <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
        <div class="row w-100">
             <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                     ${invoice.account_name} @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                    (${invoice.account_code})
                                            @endif
                </label>
            </div>
            <div class="col-4">
                <div class="form-control-plaintext" style="font-size: 11px">${invoice.code}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${get_format_date(invoice.date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${Number(invoice.deal_profit).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
           
        </div>
    </button>
</li>`;
                                        $('#short-list-items').append(ims);
                                    });
                                } else {
                                    $('#short-list-items').html(
                                        '<div class="p-2">No results found</div>');
                                }
                            }
                        });
                    });

                });
            </script>
            @if (count($dealtrack) > 0)
                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                    @if (count($trackdata) > 0)
                        @include('backEnd.crm.DealTrackApprovalDetail', $trackdata)
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        const leftNav = document.querySelector('.left-nav');
        const content = document.querySelector('.content-container');
        const state = localStorage.getItem("leftNavState");
        if (state === "expanded") {
            leftNav.classList.remove('col-3');
            leftNav.classList.add('col-12');
            if (content) {
                content.classList.remove('col-9');
                content.classList.add('col-0');
            }
            $('#short-list').hide();
            $('#short-list-items').hide();
            $('#long-list').show();
        } else if (state === "collapsed") {
            leftNav.classList.remove('col-12');
            leftNav.classList.add('col-3');
            if (content) {
                content.classList.remove('col-0');
                content.classList.add('col-9');
            }
            $('#short-list').show();
            $('#short-list-items').show();
            $('#long-list').hide();
        }
    </script>

    <script>
        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_cust_account_list_ajax') }}',
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
                                    return {
                                        id: item.id,
                                        text: item.account_code + ' - ' + item.account_name
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

        $(document).ready(function() {
            $(".list_style_search_btn").on("click", function() {
                $("#search_box").slideToggle(200); // expands/collapses smoothly
            });
        });
    </script>

       <script>
$(document).ready(function() {
    $('#exportExcelDealTrack').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalDeals = @json($dealtrack->count() ?? 0);
        var dateFrom = @json($from_date ?? '');
        var dateTo = @json($to_date ?? '');

        var $table = $('table#long-list.table-fixed-header');

        var visibleColIndexes = [];
        var headerLabels = [];
        var lastIndex = $table.find('thead tr th').length - 1;

        $table.find('thead tr th').each(function (i) {
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
        rows.push(['Deal Track (' + totalDeals + ')']);

        if (dateFrom || dateTo) {
            var parts = [];
            if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
            if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
            rows.push([parts.join('  ')]);
        }

        rows.push([]);
        rows.push(headerLabels);

        $table.find('tbody tr').each(function () {
            var $cells = $(this).find('td');
            var rowData = [];
            visibleColIndexes.forEach(function (i) {
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
        var workbook = new ExcelJS.Workbook();
        var worksheet = workbook.addWorksheet('DealTrack');
        var wsCols = [];
        for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
        worksheet.columns = wsCols;

        var hdrIdx = rows.indexOf(headerLabels);
        if (hdrIdx < 0) hdrIdx = rows.length - 1;

        var wsRowNum = 0;
        for (var ri = 0; ri < hdrIdx; ri++) {
            if (!(rows[ri] && rows[ri][0])) continue;
            wsRowNum++;
            var wsRow = worksheet.addRow([]);
            wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
            if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
            wsRow.getCell(1).value = rows[ri][0] || '';
            if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
            else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
            wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        }

        wsRowNum++;
        worksheet.addRow([]);

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
            var filename = 'dealtrack_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
            saveAs(blob, filename);
        });
    });

    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');

        var columnWidths = [
            @if (session('logged_session_data.company_id') == 1)
                70, 100, 150, 150, 120, 100, 120, 120, 100, 75, 80, 75
            @else
                70, 100, 150, 150, 120, 100, 120, 120, 80, 75, 75
            @endif
        ];

        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>

@if(request()->get('print_po'))

<script>
$(function() {
    var poId = "{{ request()->get('print_po')  }}";
    if (poId) {
        setTimeout(function() {
            window.location.href = "{{ url('purchase-order') }}/" + poId + "/print";
        }, 600);
    }
});
</script>
@endif

@if(request()->get('download_cheque') && request()->get('cheque_id'))
<script>
$(function() {
    var chequeId = "{{ request()->get('cheque_id') }}";
    if (chequeId) {
        var chequePrintUrl = "{{ url('payment-cheque-print') }}/" + chequeId;
        window.open(chequePrintUrl, '_blank');

        var url = new URL(window.location.href);
        url.searchParams.delete('download_cheque');
        url.searchParams.delete('cheque_id');
        window.history.replaceState({}, document.title, url.toString());
    }
});
</script>
@endif

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

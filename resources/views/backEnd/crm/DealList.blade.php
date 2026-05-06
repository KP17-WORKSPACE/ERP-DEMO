@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

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
                $('#short-list-items').addClass('d-none');
                


                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');

                sessionStorage.setItem('listViewDealList', 'long');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;


                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';



                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');


                $('#short-list').removeClass('d-none');
                $('#short-list-items').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');

                sessionStorage.setItem('listViewDealList', 'short');

            }


        }


       




        // Initialize view from sessionStorage (tab-specific)
        document.addEventListener('DOMContentLoaded', () => {
            // Check if we have customer_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('deal_action');
            
            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewDealList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewDealList');
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
                    sessionStorage.setItem('listViewDealList', 'short');
                });
            });



        });

        
       

    </script>

    <style>
        
 .pagination .page-item.active .page-link {
            background-color: #198754 !important;
            /* Bootstrap success green */

            color: #fff !important;
        }
    </style>
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Deal List</h4>

                    <div class="search-filter-container mb-4" id="short-list">
                        
                        <div class="input-group flex-nowrap">
                            <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>                        
                        <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()" style="height: 32px;">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                        
                    </div>

                    <div class="left-nav-list" id="invoice_list">
                        <ul id="short-list-items" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                         @if(count($deals)>0)
                         @foreach($deals as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
                              
                                    <div class="row w-100">

                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text"> {{@$value->customername->customer_name_display }}

                                                  @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                            ({{ @$value->customername->code }})
                                            @endif

                                            </label>
                                        </div>

                                        <div class="col-4">
                                            <div class="form-control-plaintext" style="font-size:11px">{{@$value->code }}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text" style="font-size:11px">{{date('d/m/Y', strtotime(@$value->date))}}</div>
                                        </div>

                                           
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text" style="font-size:11px">  @php @$aed2 = @$value->deal_value; @endphp
                                            {{@App\SysHelper::currancy_format_deal(@$aed2,@$value->company_id)}} {{ @$value->dealcurrency->code }}</div>
                                        </div>
                                        
                                    </div>
                                {{-- </button> --}}
                                </button>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                        <div id="long-list" class="d-none">

<div class="search-filter-container mb-0 d-flex align-items-center justify-content-start flex-nowrap">
 
                                <input type="text" id="tableSearch" 
                                    class="form-control" 
                                    style="font-size:13px; width: 350px;
                                    position: absolute;
                                    top: 10px;
                                    right: 231px;" 
                                    placeholder="Search">

                                <button type="button" class="btn btn-light list_style_search_btn" id="exportExcelDeals" style="margin-right: 66px;">
                                        <i class="ico icon-outline-export text-success"></i> Export
                                </button>

                                <button type="button" class="btn btn-light list_style_search_btn"  onclick="search_box_show_hide()" style="margin-right: 8px;">
                                        <i class="ico icon-outline-magnifer"></i>
                                </button>

                               

                                <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_new()">
                                        <i class="ico icon-outline-list-down"></i>
                                </button>

                                

                                
                            </div>
                               
                                   

                            <div class="card mt-3" id="search_box" style="display: none;">
                                <div class="card-body">
                            <div class="row">
                                <div class="col-12">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/show', 'method' => 'get', 'id' => 'crm-deals-search']) }}

            <div class="row">

                <div class="col-1-5">
                        <label for="" class="form-label">Deal ID</label>                    
                        <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id" value="{{ $ctrl_deal_id }}">
                    </div>
                    <div class="col-3">
                        <label for="" class="form-label">Customer Name</label>
                        <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                            <option value="">-Select-</option>
                            @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_cust_id == $value->id) selected @endif>{{ @$value->name }} - {{ @$value->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 35)
                    <div class="col-1-5">
                        <label for="" class="form-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            @foreach ($staff as $value)
                            <option value="{{ @$value->user_id }}" @if($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(Auth::user()->role_id == 13) {{--  KSA Sales Department Head  --}}
                    <div class="col-1-5">
                        <label for="" class="form-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            @foreach ($staff as $value)
                            <option value="{{ @$value->user_id }}" @if($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(Auth::user()->id == 33)
                    <div class="col-1-5">
                        <label for="" class="form-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            <option value="33" @if($ctrl_owner == 33) selected @endif>Jacob George</option>
                            <option value="31" @if($ctrl_owner == 31) selected @endif>Sheikh Nadeem Akthar</option>
                            <option value="59" @if($ctrl_owner == 59) selected @endif>Trison Thomas</option>
                        </select>
                    </div>
                    @endif

                    @if(Auth::user()->id == 27)
                    <div class="col-1-5">
                        <label for="" class="form-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            <option value="27" @if($ctrl_owner == 27) selected @endif>Monica</option>
                            <option value="28" @if($ctrl_owner == 28) selected @endif>Archana Revi</option>
                            <option value="30" @if($ctrl_owner == 30) selected @endif>Faizaan Aslam Shaikh</option>
                            <option value="54" @if($ctrl_owner == 54) selected @endif>Satyabhan Sikarwar</option>
                        </select>
                    </div>
                    @endif

                    @if(Auth::user()->id == 44)
                    <div class="col-1-5">
                        <label for="" class="form-label">Owner</label>
                        <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                            <option value="">-Select-</option>
                            <option value="44" @if($ctrl_owner == 44) selected @endif>Rajiv R</option>
                            <option value="32" @if($ctrl_owner == 32) selected @endif>Irshaad Aklekar</option>
                            <option value="34" @if($ctrl_owner == 34) selected @endif>Stephen F Mendonsa</option>
                            <option value="45" @if($ctrl_owner == 79) selected @endif>Shamshad Ahmed</option>
                        </select>
                    </div>
                    @endif

                    <div class="col-1-5">
                        <label for="" class="form-label">Brand</label>
                        <select class="form-control js-example-basic-single" name="brand_id" id="brand_id">
                            <option value="">-Select-</option>
                            @foreach ($brand as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_brand == $value->id) selected @endif>{{ @$value->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-1-5">
                        <label for="" class="form-label">From Date</label>
                        <input class="form-control date-picker" id="date" type="text" autocomplete="off" name="date" value="{{ @App\SysHelper::normalizeToDmy($ctrl_date) }}">
                    </div>
                    <div class="col-1-5">
                        <label for="" class="form-label">To Date</label>
                        <input class="form-control date-picker" id="date2" type="text" autocomplete="off" name="date2" value="{{  @App\SysHelper::normalizeToDmy($ctrl_date2)}}">
                    </div>

                     <div class="col-1-5">
                        <label for="" class="form-label">Follow Up</label>
                        <input class="form-control date-picker" id="followup" type="text" autocomplete="off" name="followup" value="{{ @$ctrl_followup}}">
                    </div>

                    <div class="col-1-5">
                        <label for="" class="form-label">Type</label>
                        <select class="form-control" name="isproject_id" id="isproject_id">
                            <option value="">-Select-</option>
                            <option value="1" @if(@$ctrl_isproject == "1") selected @endif >Project</option>
                            <option value="2" @if(@$ctrl_isproject == "2") selected @endif >Channel</option>
                            <option value="3" @if(@$ctrl_isproject == "3") selected @endif >Corporate</option>
                            <option value="4" @if(@$ctrl_isproject == "4") selected @endif >Ecommerce</option>
                            <option value="0" @if(@$ctrl_isproject == "0") selected @endif >Lead</option>
                            <option value="5" @if(@$ctrl_isproject == "5") selected @endif >Marketing</option>
                        </select>
                    </div>
                    <div class="col-1-5">
                        <label for="" class="form-label">Status</label>
                        <select class="form-control" name="stage_id" id="stage_id">
                            <option value="">-Select-</option>
                            <option value="1" @if($ctrl_stage == 1) selected @endif>Prospecting</option>
                            <option value="2" @if($ctrl_stage == 2) selected @endif>Quote</option>
                            <option value="3" @if($ctrl_stage == 3) selected @endif>Closure</option>
                            <option value="4" @if($ctrl_stage == 4) selected @endif>Won</option>
                            <option value="5" @if($ctrl_stage == 5) selected @endif>Lost</option>
                            <option value="6" @if($ctrl_stage == 6) selected @endif>completed</option>
                            <option value="7" @if($ctrl_stage == 7) selected @endif>On Process</option>
                            <option value="8" @if($ctrl_stage == 8) selected @endif>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-1-5">
                        <label for="" class="form-label">Source</label>
                        <select class="form-control" name="source_id" id="source_id">
                            <option value="">-Select-</option>
                            <option value="Gitex 2023" @if($ctrl_source == "Gitex 2023") selected @endif>Gitex 2023</option>
                            <option value="Gitex" @if($ctrl_source == "Gitex") selected @endif>Gitex</option>
                            <option value="Chat" @if($ctrl_source == "Chat") selected @endif>Chat</option>
                            <option value="Call" @if($ctrl_source == "Call") selected @endif>Call</option>
                            <option value="Mail" @if($ctrl_source == "Mail") selected @endif>Mail</option>
                            <option value="Fulfillment" @if($ctrl_source == "Fulfillment") selected @endif >Fulfillment</option>
                            <option value="Ecommerce" @if($ctrl_source == "Ecommerce") selected @endif >Ecommerce</option>
                            <option value="Other" @if($ctrl_source == "Other") selected @endif>Other</option>
                        </select>
                    </div>

                    <div class="col-1-5 mb-2 filter-field">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control js-example-basic-single" name="sort_id" id="sort_id"
                                    onchange="this.form.submit()">
                                    <option value="" >-Select-
                                    </option>
                                    <option value="11" >Latest Deals
                                    </option>
                                     <option value="12" >Expired Deals
                                    </option>
                                     <option value="9">By Deal Value
                                    </option>
                                    <option value="1" >Today
                                    </option>
                                    <option value="2" >This Week
                                    </option>
                                    <option value="3" >Last Week
                                    </option>
                                    <option value="4" >This
                                        Month
                                    </option>
                                    <option value="5" >Last
                                        Month
                                    </option>
                                    <option value="6" >Last
                                        6 Months
                                    </option>
                                    <option value="7" >This Year
                                    </option>
                                    <option value="8">Last Year
                                    </option>
                                   
                                    <option value="10">By Date
                                    </option>
                                </select>
                            </div>

                <div class="col-1"><br />
                    <button type="submit" class="btn btn-light">
                        <i class="ico icon-outline-magnifer"></i> Filter
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
                            <th style="width: 80px;" class="text-center">@lang('Deal No')</th>
                            <th class="text-center" style="width: 70px;">@lang('Date')</th>
                            <th style="width: 150px;">@lang('Customer')</th>
                            <th style="width: 150px;">@lang('Deal Name')</th>
                            <th style="width: 120px;">@lang('Sales Person')</th>

                            <th style="width: 120px" class="text-end">@lang('Deal Value')</th>
                            <th style="width: 120px;" class="text-end">@lang('Deal Profit')</th>

                            <th  style="width: 100px;">@lang('Stage')</th>

                            @if(session('logged_session_data.company_id') == 1)
                            <th style="width: 100px;">@lang('Company')</th>
                            @endif
                            
                           
                            <th class="text-center" style="width: 75px;">@lang('Updated On')</th>
                            <th class="text-center" style="width: 80px;">@lang('Closing Date')</th>
                            <th class="text-center" style="width: 75px;">@lang('Actions')</th>

                                                     
                                </tr>
                            </thead>
                            <tbody style="font-size:12px">
                                @php $count = 1;
        $total_deal = 0;        $total_amount = 0;
        $deal_currency = "AED"; @endphp
                        @foreach($deals as $value)
                                    @php $total_deal += 1; @endphp

                                    @if((@$value->estimated_close_date <= Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}" style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}">
                                    @endif
                                        <td class="text-center no-toggle"><a class="data-item" onclick="list_style_new()" data-id="{{ $value->id }}">{{@$value->code }}</a></td>
                                        <td class="text-center">{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                        <td>{{ $value->customername->code }} - {{@$value->customername->name}}</td>
                                        <td>{{@$value->deal_name}}</td>
                                        <td>{{@$value->ownername->full_name}}</td>
                                          <td class="text-end" >
                                            @php $aed = $value->deal_value; @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp {{ $value->dealcurrency->code }} <?php        $deal_currency = $value->dealcurrency->code; ?>
                                        </td>
                                        <td class="text-end">{{@App\SysHelper::currancy_format_deal($value->deal_profit,$value->company_id)}} {{ $value->dealcurrency->code }}
                                        </td>

                                          <td>
                                                     @php


$followupDate = @$value->followup_date;

// If value exists, convert from DB (UTC or system timezone) to Dubai time
if (!empty($followupDate)) {
    try {
        $followupDate = Carbon\Carbon::parse($followupDate)
           
            ->format('d/m/Y h:i A'); // Match Flatpickr
    } catch (\Exception $e) {
        // Fallback: in case parsing fails
        $followupDate = Carbon\Carbon::now()
            ->addDays(3)
            ->setTime(11, 0)
            ->format('d/m/Y h:i A');
    }
} 
@endphp
                                            @if($value->stage == 1) <span class="badge bg-primary py-1 px-2">Prospecting</span> <span>{{ $followupDate }}</span> @endif
                                            @if($value->stage == 2) <span class="badge bg-warning py-1 px-2">Quote</span> <span>{{ $followupDate }}</span> @endif
                                            @if($value->stage == 3) <span class="badge bg-info py-1 px-2">Closure</span> @endif
                                            @if($value->stage == 4) 
                                            <?php
                                                    $data = App\SysHelper::deal_track_status($value->id);
                                                    $color = "danger";
                                                    if ($data == "Pending") {
                                                        $color = "warning";
                                                    } else if ($data == "completed") {
                                                        $color = "primary";
                                                    } else if ($data == "OnProcess") {
                                                        $color = "info";
                                                    } else {
                                                        $color = "danger";
                                                    }
                                            ?>
                                            @if($data != "completed")
                                            <span class="badge bg-success py-1 px-2">Won</span>@endif

                                            @if(App\SysHelper::set_track($value->id) == 1)
                                                <a class="badge bg-{{ $color }}  py-1 px-2 @if($data == "Fulfill") @else deal-track-sales-person @endif" @if($data == "Fulfill") href="{{ url('crm-deals/'.$value->id.'/edit') }}" @endif onclick="list_style_new()" data-id="{{ $value->id }}"  title="Click to Fullfill">
                                                @if($data == "Fulfill")<span class="spinner-grow spinner-grow-sm text-capatalize" role="status" aria-hidden="true"></span>@endif {{ $data }} </a>
                                            @endif

                                            @endif
                                            @if($value->stage == 5) <span class="badge bg-danger py-1 px-2">Lost</span> @endif
                                            @if($value->stage == 6) <span class="badge bg-secondary py-1 px-2">Cancelled</span> @endif
                                        </td>

                                        @if(session('logged_session_data.company_id') == 1)
                                        <td> &nbsp; {{ $value->companyname->company_name }}</td>
                                        @endif
                                      
                                      
                                        <td class="text-center">&nbsp;&nbsp; {{date('d/m/Y h:i A', strtotime(@$value->updated_at))}}</td>
                                        <td class="text-center">&nbsp; {{date('d/m/Y', strtotime(@$value->estimated_close_date))}}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                      <a class="btn btn-sm btn-light open-comments-modal" style="cursor: pointer;"
                                                                data-deal-id="{{ $value->id }}"><i class="ico icon-outline-chat-round-dots" style="font-size:16px" aria-hidden="true"></i></a>
                                            <a class="btn btn-sm btn-light" href="{{url('crm-quote/'.$value->id.'/download/'.$value->quote_id)}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                                @if(Auth::user()->role_id == 1)
                                                @if ($value->deleted_at)
                                                    <button data-id="{{ $value->id }}" data-bs-toggle="modal" data-bs-target="#restoreModal" type="button"
                                                        class="btn btn-sm btn-light open-restore-modal" title="Restore">
                                                       <i class="ico icon-bold-restart text-dark" style="font-size: 16px;"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light open-delete-modal" data-id="{{ $value->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal">
                                                        <i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i>
                                                    </button>

                                                @endif

                                            @endif

                                            </div>
                                        </td>
                                    </tr>

                        @endforeach
                                {{-- @php $count =1; @endphp
                         @foreach($salesreturn as $value)
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td class="text-center">{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td class="text-center"><a href="{{url('sales-return/'.$value->id.'/view')}}" target="_blank">{{ @$value->doc_number }}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->supplier_name}}</td>                             
                             <td class="text-center">@if (@$value->si_doc_number=="") -- @else <a href="{{url('get-url-sales-invoice/'.$value->si_doc_number)}}" target="_blank">{{@$value->si_doc_number}}</a>@endif</td>
                             <td class="text-center">@if (@$value->dn_doc_number=="") -- @else <a href="{{url('get-url-delivery-note/'.$value->dn_doc_number)}}" target="_blank">{{@$value->dn_doc_number}}</a>@endif</td>
                             <td class="text-center">@if (@$value->code=="") -- @else <a href="{{url('get-url-deal-track/'.$value->code)}}" target="_blank">{{@$value->code}}</a>@endif</td>
                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}</td>
                             <td class="text-center">
                                <a class="btn btn-sm btn-light d-block" href="{{url('sales-return/'.$value->id.'/download')}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                             </td>
                         </tr>
                            @endforeach --}}
                            </tbody>

                             <?php try { ?>
                    <tfoot>
                        <tr>
                            <td colspan="11" class="text-center border-0">
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $deals->appends(request()->input())->links() }}
                                </div>
                            </td>
                        </tr>
                    </tfoot>

                    <?php } catch (\Exception $e) {
                        } ?>
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
                        $(document).ready(function () {
    // Delegated click works for both static + dynamic .data-item
    $(document).on('click', '.data-item', function () {
        
        $("#loading_bg").css("display", "block");

        var id = $(this).data('id');

        // highlight active
        $('.data-item').removeClass('active');
        $(this).addClass('active');

           var newUrl = "{{ url('crm-deals/show') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

        var action = "{{ URL::to('crm-deals-details') }}/" + id;

        $.ajax({            
            url: action,
            method: 'GET',
            success: function (response) {
                $('#data-details').html(response);
            },
            error: function () {
                $('#data-details').html('<p class="text-danger">Error loading details.</p>');
            },
            complete: function () {
                // always hide loading, success or error
                $("#loading_bg").css("display", "none");
            }
        });
    });
});
                        </script>
                        <script>
$(document).ready(function(){

    $('#search_invoice').on('input', function(){
        var query = $(this).val();

        $.ajax({
            url: "{{ route('crm-deals.search') }}",
            type: "GET",
            data: { query: query },
            success: function(data){
                console.log(data)
                $('#short-list-items').html('');

                if(data.length > 0){
                    $.each(data, function(index, invoice){

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
                    ${Number(invoice.deal_value).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
         
        </div>
    </button>
</li>`;
$('#short-list-items').append(ims);
                    });
                } else {
                    $('#short-list-items').html('<div class="p-2">No results found</div>');
                }
            }
        });
    });

});
</script>

<script>
   function loadDealTrackDetails(id) {

    $("#loading_bg").css("display", "block");

    // Update URL
    var newUrl = "{{ url('crm-deals/show') }}/" + id;
    window.history.pushState({ path: newUrl }, '', newUrl);

    // Ajax load
    var action = "{{ URL::to('crm-deal-track') }}/" + id + "/view";

    $.ajax({
        url: action,
        method: 'GET',
        success: function (response) {
            $('#data-details').html(response);
        },
        error: function () {
            $('#data-details').html('<p class="text-danger">Error loading details.</p>');
        },
        complete: function () {
            $("#loading_bg").css("display", "none");
        }
    });

}

</script>


     <script>
                        $(document).ready(function () {
    // Delegated click works for both static + dynamic .data-item
    $(document).on('click', '.deal-track-sales-person', function () {
        
        $("#loading_bg").css("display", "block");

        var id = $(this).data('id');

      var newUrl = "{{ url('crm-deals/show') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

        var action = "{{ URL::to('crm-deal-track') }}/" + id + "/view";

        $.ajax({            
            url: action,
            method: 'GET',
            success: function (response) {
                $('#data-details').html(response);
            },
            error: function () {
                $('#data-details').html('<p class="text-danger">Error loading details.</p>');
            },
            complete: function () {
                // always hide loading, success or error
                $("#loading_bg").css("display", "none");
            }
        });
    });
});
                        </script>

                          <div class="" role="tabpanel" aria-labelledby="po-tab" id="data-details">
                @if ($action === 'add')
           
                    @include('backEnd.crm.DealForm_add', $addData)

                      
                @elseif($action === 'edit')
              
                    @include('backEnd.crm.DealFormEdit_edit', $editData)

                    {{-- @include('backEnd.purchaseorder.manage_purchase_order_edit', $editData) --}}
                @elseif (!empty($selectedDeal) && is_array($selectedDeal))
                    @include('backEnd.crm.DealList_details', $selectedDeal)
                @else
                    {{-- <p class="text-danger">No details available.</p> --}}

                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                                 <a href="{{ url('crm-deals/show?deal_action=add') }}" class="text-decoration-none text-dark">
                        <div class="text-center mb-4">
                            <div data-bs-toggle="modal" data-bs-target="#adddeal"
                                class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" data-bs-toggle="modal"
                                data-bs-target="#adddeal">Add New Deal</h1>
                            <p class="text-muted">Create and track your leads with ease</p>
                        </div>
                    </a>

                    </div>

                @endif
            </div>

                       
                    </div>
                </div>



<div class="modal side-panel fade" id="restoreModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            <form method="POST" action="" id="restoreForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="poexcelimport">Restore Deal</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <p>Please provide a reason for restoring this deal:</p>
                                <textarea name="restore_reason" class="form-control" rows="3" required></textarea>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2 text-success">
                            <i class="ico icon-bold-restart text-success" style="font-size: 16px;"></i> Restore
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>


        <div class="modal side-panel fade" id="deleteModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            <form method="POST" action="" id="deleteForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="poexcelimport">Delete Deal</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-0 p-0">
                        <div class="card mb-0 mt-0">
                            <div class="card-body">

                                <p>Please provide a reason for deleting this deal:</p>
                                <textarea name="delete_reason" class="form-control" rows="3" required></textarea>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-light add-btn ms-2 text-danger">
                            <i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Delete
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>



<div class="modal side-panel fade" id="commentsModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Deal Comments</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div id="commentsScrollContainer"
                                style="flex: 1 1 auto; overflow-y: auto; border: 1px solid #dee2e6; border-radius: .25rem;">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                       
                                            <th width="45%">Comment</th>
                                            <th width="20%">Person</th>
                                            <th width="5%"><i class="ico icon-bold-paperclip" style="font-size:16px"></i></th>
                                            <th width="20%">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="commentsModalBody">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted no-comments-found">No
                                                comments found
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
    </div>







<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
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
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // Open dropdown and focus search box on click
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Focus the search input inside the opened Select2 dropdown
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 0);
    });
});

$(document).ready(function () {
    $(".list_style_search_btn").on("click", function () {
        $("#search_box").slideToggle(200); // expands/collapses smoothly
    });
});
</script>

<script> 
            $(document).on('click', '.open-delete-modal', function () {
            var leadId = $(this).data('id');
            var actionUrl = "{{ url('crm-deals') }}/" + leadId + "/delete";
            $('#deleteForm').attr('action', actionUrl);
        });

        $(document).on('click', '.open-restore-modal', function () {
            var leadId = $(this).data('id');
            var actionUrl = "{{ url('crm-deals') }}/" + leadId + "/restore";
            $('#restoreForm').attr('action', actionUrl);
        });

        $(document).ready(function() {
                $('.open-comments-modal').click(function() {
                $("#loading_bg").css("display", "block");


                    var leadId = $(this).data('deal-id');
                    var $body = $('#commentsModalBody');
                    $body.html('<tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>');

                    $.ajax({
                        url: '/crm-deals/comments/' + leadId,
                        method: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            $body.empty();
                            if (res.data && res.data.length > 0) {
                                $.each(res.data, function(i, comment) {
                                    var row = `
                                        <tr>
                                            <td>${comment.comments}</td>
                                            <td>${comment.createdby.first_name || '-'} ${comment.createdby.last_name || '-'}</td>
                                            <td>
                                           ${comment.commentsdoc ? ` <a class="text-info p-0"
                                                    href="{{asset('public/uploads/crm_deal_doc/')}}/${ comment.commentsdoc }"
                                                    target="_blank"><i class="fa fa-paperclip"
                                                        aria-hidden="true"></i>&nbsp;&nbsp;View File</a>` : '' }

                                            </td>
                                            <td>${formatDateTime(comment.created_at)}</td>
                                        </tr>`;
                                    $body.append(row);
                                });
                            } else {
                                $body.html(
                                    '<tr><td colspan="3" class="text-center text-muted">No comments found</td></tr>'
                                    );
                            }
                $("#loading_bg").css("display", "none");

                            $('#commentsModal').modal('show');
                        },
                        error: function() {
                            $body.html(
                                '<tr><td colspan="3" class="text-danger text-center">Error loading comments</td></tr>'
                                );
                        }
                    });



                });

                      });
 function formatDateTime(datetime) {
                var date = new Date(datetime);
                return date.toLocaleString('en-IN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }
</script>

   <script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
       

        
        var  columnWidths = [

          @if (session('logged_session_data.company_id') == 1)
            80, 100, 150, 150, 120, 100, 120, 110, 70, 75, 80, 75
            @else
            80, 100, 150, 150, 120, 100, 120, 110, 70, 75, 75
            @endif

            
        ];


        // Apply widths to <thead> and <tbody>
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

<script>
$(document).ready(function () {
    $('#exportExcelDeals').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalDeals = @json($deals->count() ?? 0);
        var dateFrom = @json($ctrl_date ?? '');
        var dateTo = @json($ctrl_date2 ?? '');

        // Use the main deal table (unique by class) so modal comment table with same id doesn't interfere
        var $table = $('table#long-list.table-fixed-header');

        var visibleColIndexes = [];
        var headerLabels = [];
        var lastIndex = $table.find('thead tr th').length - 1; // always omit last column (Actions)

        $table.find('thead tr th').each(function (i) {
            if (i === lastIndex) return; // skip actions column regardless of label text
            if ($(this).css('display') !== 'none') {
                var label = $(this).text().trim();
                // Extra safety: skip if label suggests action even if not last due to layout
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
        rows.push(['Deals (' + totalDeals + ')']);

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
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Deals');
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
                var filename = 'deals_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>

<script>
$(document).on('click', '#savePaymentTerm', function () {

    let title = $('#payment_term_title').val().trim();
    let input = $('#payment_term_title');

    input.removeClass('is-invalid');
    input.next('.invalid-feedback').text('');

    if (!title) {
        input.addClass('is-invalid');
        input.next('.invalid-feedback').text('Payment term is required');
        return;
    }

    $.ajax({
        url: "{{  url('payment-terms-store-ajax') }}", // adjust route
        type: "POST",
        data: {
            title: title,
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function () {
            $('#loading_bg').show();

        },
        success: function (res) {

            if (res.status) {

                // ✅ NEW ID AVAILABLE HERE
                console.log('New ID:', res.data.id);

                // Example: append to dropdown
                $('#payment_terms').append(
                    `<option value="${res.data.id}" selected>${res.data.title}</option>`
                );

                $('#paymenttermsModal').modal('hide');
                $('#payment_term_title').val('');

                toastr.success(res.message, 'Success');
            }
        },
        error: function (xhr) {

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors.title) {
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors.title[0]);
                }
            } else {
                toastr.error('Something went wrong', 'Error');
            }
        },
        complete: function () {
            $('#loading_bg').hide();
        }
    });
});
</script>

<div class="modal side-panel  fade" id="paymenttermsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add </h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                       

                          
                            <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                                <input type="text" id="payment_term_title" name="name" class="form-control" required="" autocomplete="off">
            
                        <div class="modal-footer d-flex justify-content-center p-0">
                            <button type="button" id="savePaymentTerm" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- Modal PO --}}

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

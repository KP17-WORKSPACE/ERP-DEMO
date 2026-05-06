@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Deal List</h4>

                    <div class="search-filter-container mb-4" id="short-list">
                        
                        <div class="input-group flex-nowrap">
                            <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>                        
                        <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()" style="height: 32px;">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                        
                    </div>

                    <div class="left-nav-list" id="invoice_list">
                        <ul id="short-list-items" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                         @if(count($deals)>0)
                         @foreach($deals as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item {{ $loop->first ? 'active' : '' }}" data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                    <div class="row w-100">
                                        <div class="col-4">
                                            <div class="form-control-plaintext">{{@$value->deal_code->code }}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text">{{date('d/m/Y', strtotime(@$value->date))}}</div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text">{{@App\SysHelper::currancy_format_deal($value->deal_profit,$value->company_id)}} {{ $value->dealcurrency->code }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">{{@$value->deal_name}} | {{ $value->customername->code }} - {{@$value->customername->name}}</label>
                                        </div>
                                    </div>
                                {{-- </button> --}}
                                </button>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                        <div id="long-list" style="display: none;">
                               
                                    <button type="button" class="btn btn-light list_style_search_btn"  onclick="search_box_show_hide()">
                                        <i class="ico icon-outline-magnifer"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_search()">
                                        <i class="ico icon-outline-list-down"></i>
                                    </button>

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
                        <label for="" class="form-label">Company Name</label>
                        <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                            <option value="">-Select-</option>
                            @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" @if($ctrl_cust_id == $value->id) selected @endif>{{ @$value->code }} - {{ @$value->name }}</option>
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
                                <select class="form-control" name="sort_id" id="sort_id"
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
                        <table class="table table-hover mt-2" id="long-list" style="table-layout: fixed;width:100%">
                            <thead>
                                <tr>
                            <th style="width: 55px;">@lang('Deal No')</th>
                            @if(session('logged_session_data.company_id') == 1)
                            <th style="width: 100px;">@lang('Company')</th>
                            @endif
                            <th style="width: 150px;">@lang('Deal Name')</th>
                            <th style="width: 150px;">@lang('Customer')</th>
                            <th style="width: 120px;">@lang('Created By')</th>
                            <th  style="width: 100px;">@lang('Stage')</th>
                            <th style="width: 120px" class="text-end">@lang('Deal Value')</th>
                            <th style="width: 120px;" class="text-end">@lang('Deal Profit')</th>
                            <th class="text-center" style="width: 70px;">@lang('Date')</th>
                            <th class="text-center" style="width: 75px;">@lang('Updated On')</th>
                            <th class="text-center" style="width: 80px;">@lang('Closing Date')</th>
                            <th class="text-center" style="width: 75px;">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1;
        $total_deal = 0;
        $total_amount = 0;
        $deal_currency = "AED"; @endphp
                        @foreach($deals as $value)
                                    @php $total_deal += 1; @endphp

                                    @if((@$value->estimated_close_date <= Carbon\Carbon::today()) && ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}" style="background-color:#ffebeb !important; color:#ff0000;">
                                    @else
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}">
                                    @endif
                                        <td><a class="data-item" onclick="list_style_search()" data-id="{{ $value->id }}">{{@$value->deal_code->code }}</a></td>
                                        @if(session('logged_session_data.company_id') == 1)
                                        <td>{{ $value->companyname->company_name }}</td>
                                        @endif
                                        <td>{{@$value->deal_name}}</td>
                                        <td>{{ $value->customername->code }} - {{@$value->customername->name}}</td>
                                        <td>{{@$value->ownername->first_name}}</td>
                                        <td >
                                            @if($value->stage == 1) <span class="badge bg-primary py-1 px-2">Prospecting</span> @endif
                                            @if($value->stage == 2) <span class="badge bg-warning py-1 px-2">Quote</span> @endif
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
                                                <a class="badge bg-{{ $color }}  py-1 px-2" href="{{url('crm-deal-track/' . $value->id . '/view')}}" title="Click to Fullfill">
                                                @if($data == "Fulfill")<span class="spinner-grow spinner-grow-sm text-capatalize" role="status" aria-hidden="true"></span>@endif {{ $data }} </a>
                                            @endif

                                            @endif
                                            @if($value->stage == 5) <span class="badge bg-danger py-1 px-2">Lost</span> @endif
                                            @if($value->stage == 6) <span class="badge bg-secondary py-1 px-2">Cancelled</span> @endif
                                        </td>
                                        <td class="text-end" >
                                            @php $aed = $value->deal_value; @endphp
                                            {{@App\SysHelper::currancy_format_deal($aed,$value->company_id)}}
                                            @php $total_amount += $aed; @endphp {{ $value->dealcurrency->code }} <?php        $deal_currency = $value->dealcurrency->code; ?>
                                        </td>
                                        <td class="text-end">{{@App\SysHelper::currancy_format_deal($value->deal_profit,$value->company_id)}} {{ $value->dealcurrency->code }}
                                        </td>
                                        <td class="text-center">{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                        <td class="text-center">{{date('d/m/Y h:i A', strtotime(@$value->updated_at))}}</td>
                                        <td class="text-center">{{date('d/m/Y', strtotime(@$value->estimated_close_date))}}</td>
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
                                                        <i class="ico icon-bold-trash-bin-2 text-dark" style="font-size: 16px;"></i>
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

    $('#search_invoice').on('keyup', function(){
        var query = $(this).val();

        $.ajax({
            url: "{{ route('crm-deals.search') }}",
            type: "GET",
            data: { query: query },
            success: function(data){
                $('#short-list-items').html('');

                if(data.length > 0){
                    $.each(data, function(index, invoice){

                    let ims = `<li class="nav-item w-100" role="presentation">
    <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
        <div class="row w-100">
            <div class="col-4">
                <div class="form-control-plaintext">${invoice.code}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text">
                    ${get_format_date(invoice.date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text">
                    ${Number(invoice.deal_profit).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
            <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.deal_name} | ${invoice.account_code} - ${invoice.account_name}
                </label>
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
                       
                        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                            @if(count($pdfdata) > 0)
                                @include('backEnd.crm.DealList_details',$pdfdata)
                            @else
                    {{-- <p class="text-danger">No details available.</p> --}}

                            <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                                style="min-height: 90vh;">

                                <!-- Icon + Heading -->
                                <div class="text-center mb-4" >
                                    <div onclick="window.location.href='{{url('crm-deals-add')}}'" class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                        style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                        <i class="ico icon-outline-add-square"></i>
                                    </div>
                                    <h1 class="fw-bold mt-3" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#addlead">Add New Deal</h1>
                                    <p class="text-muted">Create and track your deals with ease</p>
                                </div>

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
                                <table class="table table-hover" id="long-list">
                                    <thead class="thead-light">
                                        <tr>
                                       
                                            <th width="40%">Comment</th>
                                            <th width="20%">Person</th>
                                            <th width="10%">Attachment</th>
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
  } 
  else if (state === "collapsed") {
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Sales Invoice</h4>

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
                         @if(count($salesinvoice)>0)
                         @foreach($salesinvoice as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item {{ $id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                    <div class="row w-100">
                                        <div class="col-4">
                                            <div class="form-control-plaintext">{{ $value->doc_number }}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',')}} {{ $value->currency_name->code }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">{{ $value->accountname->account_code }} - {{ $value->accountname->account_name }}</label>
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
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice', 'method' => 'get', 'id' => 'sales-invoice-search']) }}
            <div class="row">

                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Documents Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="documents_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Customer</label>
                        <select class="form-control js-account-select" name="customer" id="customer">
                            <option value=""></option>
                            {{-- @foreach ($customer_list as $value)
                                <option value="{{ @$value->id }}" >{{ @$value->account_name }}
                                </option>
                            @endforeach --}}
                        </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Supplier</label>
                    <input class="form-control" type="text" autocomplete="off" name="supplier" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Delivery Note</label>
                    <input class="form-control" type="text" autocomplete="off" name="delivery_note" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">SRT Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="srt" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Amount</label>
                    <input class="form-control" type="number" autocomplete="off" name="amount" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">From Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="from_date" id="from_date" value="" onchange="set_filter()">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">To Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="to_date" id="to_date" value="" onchange="set_filter()">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Sales Person</label>
                    <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                        <option value=""></option>
                        @foreach ($sales_person_list as $value)
                            <option value="{{ @$value->user_id }}" >{{ @$value->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Attachment</label>
                    <select class="form-control js-example-basic-single" name="attachments" id="attachments">
                        <option value=""></option>
                        <option value="1" >With</option>
                        <option value="2" >Without</option>
                        <option value="3" >All</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Filter By</label>
                    <select class="form-control" name="filter_by" id="filter_by">
                        <option value="" @if($filter_by == "") selected @endif>-Select-</option>
                        <option value="this_month" @if($filter_by == "this_month") selected @endif>This Month</option>
                        <option value="today" @if($filter_by == "today") selected @endif>Today</option>
                        <option value="this_week" @if($filter_by == "this_week") selected @endif>This Week</option>
                        <option value="last_week" @if($filter_by == "last_week") selected @endif>Last Week</option>                                    
                        <option value="last_month" @if($filter_by == "last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if($filter_by == "this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if($filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if($filter_by == "this_year") selected @endif>This Year</option>
                        <option value="last_year" @if($filter_by == "last_year") selected @endif>Last Year</option>
                    </select>
                </div>
                <script>
                    function set_filter(){
                    if($('#from_date').val()!="" || $('#to_date').val() != "")
                    {
                        $('#filter_by').val('')
                    }
                    }
                </script>

                <div class="col-2"><br />
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
                        <table class="table table-hover mt-2">
                            <thead>
                                <tr>
                             <th class="text-center">@lang('SI Date')</th>
                             <th class="text-center" style="width: 50px;">@lang('SI No')</th>
                             <th style="width: 150px;">@lang('Customer')</th>
                             <th style="width: 150px;">@lang('Supplier')</th>
                             
                             <th class="text-end">@lang('Taxable Amount')</th>
                             <th class="text-end">@lang('Tax')</th>
                             <th class="text-end">@lang('Amount')</th>
                             <th class="text-center">@lang('Deal ID')</th>
                             <th>@lang('Salesman')</th>

                             
                             <th class="text-center">@lang('LPO Date')</th>
                             <th class="text-center">@lang('LPO No')</th>
                             <th class="text-center">@lang('DLN No')</th>
                             <th class="text-center">@lang('SRT No')</th>
                             <th class="text-center">@lang('Currency')</th>
                             <th>@lang('Payment')</th>
                             <th class="text-center"><i class="ico icon-bold-paperclip"></i></th>

                             <th class="text-center" style="width: 35px;">@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count =1; $total_taxable_amount=0; $total_tax=0; $total_amount=0; @endphp
                         @foreach($salesinvoice as $value)
                         
                        @if($pending_dn==1)
                        
                        @if (empty($value->dlnno))
                        <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td class="text-center">{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td class="text-center"> <a href="{{url('sales-invoice/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td><div class="truncate-text" style="width: 150px;">{{@$value->accountname->account_name}}</div>
                            </td>
                             <td><div class="truncate-text" style="width: 150px;">
                                {{@$value->supplier_name}}</div>
                                </td>

                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount-@$value->deal_discount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount-@$value->deal_discount; ?></td>
                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount-(@$value->deal_discount*$value->net_vat/100),2,'.',',') }}<?php $total_tax += $value->total_vatamount-(@$value->deal_discount*$value->net_vat/100); ?></td>
                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
                             <td>@if (@$value->code=="") -- @else <a href="{{url('get-url-deal-track/'.$value->code)}}" target="_blank">{{@$value->code}}</a>@endif</td>
                             <td>{{ @$value->salesman->full_name }}</td>

                             
                             <td class="text-center">{{ @$value->lpo_date }}</td>
                             <td class="text-center">{{ @$value->lpo_number }}</td>
                             <!-- Delivery Note Numbers -->
                            <td>
                                <span class="text-dark">Pending</span>
                            </td>

                            <!-- Sales Return Numbers -->
                            <td>
                                @if (empty($value->srtno))
                                    <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->srtno) as $srt)
                                        <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                             <td>{{ @$value->currency_name->code }}</td>
                             <td>
                                <?php $count = $adj_list->where('bi_doc_no',$value->doc_number)->count(); ?>
                                @if($count==1)
                                <span class="text-success">Paid</span>
                                @else
                                <span class="text-danger">Pending</span>
                                @endif
                             </td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>

                             <td class="text-end">
                                @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                    <a class="btn btn-sm btn-light" href="{{ url('sales-invoice/' . $value->id . '/download/t') }}" target="_blank"><i class="ico icon-bold-download-minimalistic text-dark" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn btn-sm btn-light" href="{{ url('sales-invoice/' . $value->id . '/download') }}" target="_blank"><i class="ico icon-bold-download-minimalistic text-dark" aria-hidden="true"></i></a>
                                @endif
                                <a class="btn btn-sm btn-light" href="{{url('sales-invoice/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                    <a class="btn btn-sm btn-light" href="{{url('sales-invoice/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn btn-sm btn-light" href="{{url('sales-invoice/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif

                             </td>
                         </tr>
                         @endif

                        @else
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td class="text-center">{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td  class="text-center"><a href="{{url('sales-invoice/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td><div class="truncate-text" style="width: 150px;">{{@$value->accountname->account_name}}</div>
                            </td>
                             <td><div class="truncate-text" style="width: 150px;">
                                {{@$value->supplier_name}}</div>
                                </td>

                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount-@$value->deal_discount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount-@$value->deal_discount; ?></td>
                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount-(@$value->deal_discount*$value->net_vat/100),2,'.',',') }}<?php $total_tax += $value->total_vatamount-(@$value->deal_discount*$value->net_vat/100); ?></td>
                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
                             <td  class="text-center">@if (@$value->code=="") -- @else <a href="{{url('get-url-deal-track/'.$value->code)}}" target="_blank">{{@$value->code}}</a>@endif</td>
                             <td>{{ @$value->salesman->full_name }}</td>

                             
                             <td  class="text-center">{{ @$value->lpo_date }}</td>
                             <td  class="text-center">{{ @$value->lpo_number }}</td>
                             <!-- Delivery Note Numbers -->
                            <td  class="text-center">
                                @if (empty($value->dlnno))
                                <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->dlnno) as $dln)
                                        <a href="{{ url('get-url-delivery-note/' . trim($dln)) }}" target="_blank">{{ trim($dln) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>

                            <!-- Sales Return Numbers -->
                            <td  class="text-center">
                                @if (empty($value->srtno))
                                <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->srtno) as $srt)
                                        <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                             <td>{{ @$value->currency_name->code }}</td>
                             <td>
                                <?php $count = $adj_list->where('bi_doc_no',$value->doc_number)->count(); ?>
                                @if($count==1)
                                <span class="text-success">Paid</span>
                                @else
                                <span class="text-danger">Pending</span>
                                @endif
                             </td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>

                             <td class="text-end">
                                @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                <a class="btn btn-sm btn-light text-center d-block" href="{{url('sales-invoice/'.$value->id.'/download/t')}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                @else
                                <a class="btn btn-sm btn-light text-center d-block" href="{{url('sales-invoice/'.$value->id.'/download')}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif

                            @endforeach
                            </tbody>
                        </table>
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

        var action = "{{ URL::to('sales-invoice-details') }}/" + id;

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
            url: "{{ route('sales-invoice.search') }}",
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
                <div class="form-control-plaintext">${invoice.doc_number}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text">
                    ${get_format_date(invoice.doc_date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text">
                    ${Number(invoice.amount).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
            <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.account_code} - ${invoice.account_name}
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
                       
                        
                        {{-- <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                            @if(count($purchasegrn) > 0)
                                @include('backEnd.grn.grn_add',$data)
                            @endif
                        </div> --}}


                        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                            @if(count($salesinvoice) > 0)
                                @include('backEnd.salesinvoice.si_details',$data)
                            @else
                              <div onclick="window.location.href='{{ url('sales-invoice/create') }}'" class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4" >
                            <div  class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" >Sales Invoice</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                                @endif
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

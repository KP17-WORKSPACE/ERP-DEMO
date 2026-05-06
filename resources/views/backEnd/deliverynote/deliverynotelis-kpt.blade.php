@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Delivery Note</h4>

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
                         @if(count($deliverynote)>0)
                         @foreach($deliverynote as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item {{ $loop->first ? 'active' : '' }}" data-id="{{ $value->id }}">
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
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note', 'method' => 'get', 'id' => 'delivery-note-search']) }}
                <div class="row">
    
                    <div class="col-md-3 mb-2">
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
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>
                        <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Sales Invoice Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="sales_invoice_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">SRT Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="srt" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Date</label>
                        <input class="form-control" type="date" autocomplete="off" name="date" value="">
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
                        <table class="table table-hover mt-2">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('DN Date')</th>
                                    <th class="text-center">@lang('DN No')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Supplier')</th>
                                    <th class="text-center">@lang('SIV No')</th>
                                    <th class="text-center">@lang('SRT No')</th>
                                    <th class="text-center">@lang('Deal ID')</th>
                                    <th class="text-center">@lang('Currency')</th>
                                    <th class="text-end">@lang('Amount')</th>
                                    <th><i class="ico icon-bold-paperclip"></i></th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                 @php $count =1; @endphp
                             @foreach($deliverynote as $value)
                             
                        @if($pending_si==1)
                        @if (empty($value->invoice_no))
                        
                        <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td class="text-center">{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td class="text-center"><a href="{{url('delivery-note/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->supplier_name}}</td>
                             
                             <!-- Sales Invoice Numbers -->
                            <td class="text-center">
                                <span class="text-dark">Pending</span>
                            </td>

                            <!-- Sales Return Numbers -->
                            <td class="text-center">
                                @if (empty($value->srtno))
                                <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->srtno) as $srt)
                                        <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>

                            <!-- Deal Codes -->
                            <td class="text-center">
                                @if (empty($value->code))
                                <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->code) as $code)
                                        <a href="{{ url('get-url-deal-track/' . trim($code)) }}" target="_blank">{{ trim($code) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td class="text-center">{{ @$value->currency_name->code }}</td>
                             <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-center">
                                @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                    <a class="btn-sm btn-light" href="{{url('delivery-note/'.$value->id.'/download/t')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-light" href="{{url('delivery-note/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @endif
                                <a class="btn-sm btn-light" href="{{url('delivery-note/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                <a class="btn-sm btn-light" href="{{url('delivery-note/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                <a class="btn-sm btn-light" href="{{url('delivery-note/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif

                        @else
                             <tr @if (@$value->status == 2) class="bg-dark" @endif>
                                <td class="text-center">{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                                 <td class="text-center"><a href="{{url('delivery-note/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                                 <td>{{@$value->accountname->account_name}}</td>
                                 <td>{{@$value->supplier_name}}</td>
                                 
                                 <!-- Sales Invoice Numbers -->
                                <td class="text-center">
                                    @if (empty($value->invoice_no))
                                    <span class="text-dark">Pending</span>
                                    @else
                                        @foreach (explode(',', $value->invoice_no) as $inv)
                                            <a href="{{ url('get-url-sales-invoice/' . trim($inv)) }}" target="_blank">{{ trim($inv) }}</a>@if (!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </td>

                                <!-- Sales Return Numbers -->
                                <td class="text-center">
                                    @if (empty($value->srtno))
                                    <span class="text-dark">Pending</span>
                                    @else
                                        @foreach (explode(',', $value->srtno) as $srt)
                                            <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </td>

                                <!-- Deal Codes -->
                                <td class="text-center">
                                    @if (empty($value->code))
                                    <span class="text-dark">Pending</span>
                                    @else
                                        @foreach (explode(',', $value->code) as $code)
                                            <a href="{{ url('get-url-deal-track/' . trim($code)) }}" target="_blank">{{ trim($code) }}</a>@if (!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-center">{{ @$value->currency_name->code }}</td>
                                 <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}</td>
                                 <td>
                                    @if (empty(@$value->attach))
                                        
                                    @else
                                        @foreach (explode(',', @$value->attach) as $att)
                                            <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                        @endforeach
                                    @endif
                                </td>
                                 <td class="text-center">
                                    @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                    <a class="btn btn-light d-block" href="{{url('delivery-note/'.$value->id.'/download/t')}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                    @else
                                    <a class="btn btn-light d-block" href="{{url('delivery-note/'.$value->id.'/download')}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
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

        var action = "{{ URL::to('delivery-note-details') }}/" + id;

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
            url: "{{ route('delivery-note.search') }}",
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
                            @if(count($deliverynote) > 0)
                                @include('backEnd.deliverynote.dn_details',$data)
                            @else
                                <div onclick="window.location.href='{{ url('delivery-note-add') }}'" class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4" >
                            <div  class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" >Delivery Note</h1>
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
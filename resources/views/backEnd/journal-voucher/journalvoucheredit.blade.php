@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Journal Voucher</h4>
        
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
                         @if (count($journalvoucher) > 0)
                    @foreach ($journalvoucher as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)" class="nav-link data-item {{ $loop->first ? 'active' : '' }}"
                                data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext truncate-text">{{ $value->doc_number }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ @App\SysHelper::com_curr_format(abs(@$value->credit_amount), 2, '.', ',') }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">{{ @$value->narration }}
                                        </label>
                                    </div>
                                </div>
                                {{-- </button> --}}
                            </button>
                        </li>
                    @endforeach
                @endif
                        </ul>
                        <div id="long-list" style="display: none;">
                    <input type="text" id="tableSearch" class="form-control w-25 list_style_expand_btn" style="margin: 2px 100px 0 0" placeholder="Search in List">
                               
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
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher', 'method' => 'get', 'id' => 'journalvoucher-search']) }}
            <div class="row">

                
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Search in List</label>
                    <input type="text" id="tableSearch" class="form-control mb-2" placeholder="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Doc Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="documents_number" value="{{ $documents_number }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">From Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="from_date" id="from_date" value="{{ $ctrl_date }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">To Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="to_date" id="to_date" value="{{ $ctrl_date2 }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Filter By</label>
                    <select class="form-control" name="filter_by" id="filter_by">
                        <option value="" >-Select-</option>
                        <option value="this_month" @if($filter_by=="this_month") selected @endif>This Month</option>
                        <option value="today" @if($filter_by=="today") selected @endif>Today</option>
                        <option value="this_week" @if($filter_by=="this_week") selected @endif>This Week</option>
                        <option value="last_week" @if($filter_by=="last_week") selected @endif>Last Week</option>
                        <option value="last_month" @if($filter_by=="last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if($filter_by=="this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if($filter_by=="pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if($filter_by=="this_year") selected @endif>This Year</option>
                        <option value="last_year" @if($filter_by=="last_year") selected @endif>Last Year</option>
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
            <table class="table table-hover data-table mt-2">
                <thead>
                    <tr>
                        <th style="width: 100px;" class="text-center"> @lang('Doc Number')</th>
                        <th class="text-center"> @lang('Doc Date')</th>
                        <th class="text-center"> @lang('Remarks')</th>
                        <th class="text-end"> @lang('Amount')</th>
                        <th class="text-center"> @lang('Created By')</th>
                        <th style="width:100px;"> Attachment</th>
                        <th class="text-center">@lang('lang.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($journalvoucher))
                        @foreach ($journalvoucher as $value)
                            <tr @if ($value->status == 2) class="bg-dark" @endif
                                @if (@$value->credit_amount == '') class="text-danger" @endif>
                                <td class="text-center"><a href="javascript:void(0)" class="data-item {{ $value->id ? 'active' : '' }}"  onclick="list_style_search()"
                                data-id="{{ $value->id }}">{{ @$value->doc_number }}</a>
                                </td>
                                <td class="text-center">
                                    {{ date('d/m/Y', strtotime(@$value->doc_date)) }}
                                </td>
                                <td>
                                    <div class="truncate-text" style="width:500px;">
                                    {{ @$value->narration }}</div>
                                </td>
                                <td class="text-end">
                                    {{ @App\SysHelper::com_curr_format(@$value->credit_amount, '', '', ',') }}
                                    {{--  {{ @$value->debit_amount }}  --}}
                                </td>
                                <td class="pl-3 text-center">
                                    {{ @$value->createdby->full_name }}
                                </td>
                                <td class="text-center">
                                    @if (empty(@$value->attach))
                                    @else
                                        @foreach (explode(',', @$value->attach) as $att)
                                            <a href="{{ url(trim($att)) }}" target="_blank"> <i
                                                    class="ico icon-bold-paperclip"></i></a>&nbsp;
                                        @endforeach
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        @if ((Auth::user()->role_id == 1 || Auth::user()->id == @$value->created_by) && $value->status != 0)
                                            <a class="btn btn=-sm btn-light"
                                                href="{{ url('journalvoucher/' . @$value->id . '/edit') }}" onclick="list_style_search()"><i
                                                    class="ico icon-outline-pen-2 text-dark"
                                                    style="font-size: 16px;"></i></a>
                                            @if (@$value->status == 2)
                                                <a class="btn btn=-sm btn-light"
                                                    href="{{ url('journalvoucher/' . $value->id . '/restore') }}"
                                                    onclick="return confirm('Are you sure you want to restore this item?');"><i
                                                        class="ico icon-bold-restart text-dark"
                                                        style="font-size: 16px;"></i></a>
                                            @else
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ url('journalvoucher/' . $value->id . '/delete') }}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');"><i
                                                        class="ico icon-bold-trash-bin-2 text-dark"
                                                        style="font-size: 16px;"></i></a>
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
                        </div>
                    </div>
    </aside>


    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
<script>
    $(document).ready(function () {
    $(document).on('click', '.data-item', function () {
        
        $("#loading_bg").css("display", "block");

        var id = $(this).data('id');

        $('.data-item').removeClass('active');
        $(this).addClass('active');

        var action = "{{ URL::to('journalvoucher-details') }}/" + id;

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
    });
});
</script>
   


                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                    @if (count($journalvoucher) > 0)
                        @include('backEnd.journal-voucher.j_edit')
                    @else
                       <div onclick="window.location.href='{{ url('journalvoucher-add') }}'" class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4" >
                            <div  class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" >Journal Voucher</h1>
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
$(document).ready(function(){

    $('#search_invoice').on('keyup', function(){
        var query = $(this).val();

        $.ajax({
            url: "{{ route('journalvoucher.search') }}",
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
                    ${Number(invoice.credit_amount).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
            <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.narration}
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


    $(document).ready(function () {
    $(".list_style_search_btn").on("click", function () {
        $("#search_box").slideToggle(200); // expands/collapses smoothly
    });
});


</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

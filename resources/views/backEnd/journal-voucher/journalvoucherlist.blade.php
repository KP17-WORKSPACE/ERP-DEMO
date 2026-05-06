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

                sessionStorage.setItem('listViewJVList', 'long');
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

                sessionStorage.setItem('listViewJVList', 'short');

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
            const hasCustomerAction = urlParams.has('jv_action');

            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewJVList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewJVList');
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
                    sessionStorage.setItem('listViewJVList', 'short');
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
            <h4 class="mb-2">Journal Voucher
            </h4>


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_invoice" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Journal Voucher List
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">


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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher', 'method' => 'get', 'id' => 'journalvoucher-search']) }}
                        <div class="row">


                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">Doc Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="{{ $documents_number }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date"
                                    id="from_date" value="{{ @App\SysHelper::normalizeToDmy($ctrl_date) }}" onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date" id="to_date"
                                    value="{{ @App\SysHelper::normalizeToDmy($ctrl_date2) }}" onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control js-example-basic-single" name="filter_by" id="filter_by">
                                    <option value="">-Select-</option>
                                    <option value="this_month" @if ($filter_by == 'this_month') selected @endif>This Month
                                    </option>
                                    <option value="today" @if ($filter_by == 'today') selected @endif>Today</option>
                                    <option value="this_week" @if ($filter_by == 'this_week') selected @endif>This Week
                                    </option>
                                    <option value="last_week" @if ($filter_by == 'last_week') selected @endif>Last Week
                                    </option>
                                    <option value="last_month" @if ($filter_by == 'last_month') selected @endif>Last Month
                                    </option>
                                    <option value="this_quarter" @if ($filter_by == 'this_quarter') selected @endif>This
                                        Quarter</option>
                                    <option value="pre_quarter" @if ($filter_by == 'pre_quarter') selected @endif>Previous
                                        Quarter</option>
                                    <option value="this_year" @if ($filter_by == 'this_year') selected @endif>This Year
                                    </option>
                                    <option value="last_year" @if ($filter_by == 'last_year') selected @endif>Last Year
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

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($journalvoucher) > 0)
                    @foreach ($journalvoucher as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}">

                                <div class="row w-100">

                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">{{ @$value->narration }}
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">{{ $value->doc_number }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @App\SysHelper::com_curr_format(abs(@$value->credit_amount), 2, '.', ',') }}
                                        </div>
                                    </div>
                                    
                                </div>

                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table table-fixed-header" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width: 100px;" class="text-center"> @lang('Doc Number')</th>
                            <th class="text-center" style="width: 100px;"> @lang('Doc Date')</th>
                            <th class="text-start" style="width: 450px;"> @lang('Remarks')</th>
                            <th class="text-end" style="width: 150px;"> @lang('Amount')</th>
                         
                             <th style="width:30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i></th>
                            <th class="text-start" style="width:100px;"> @lang('Created By')</th>
                           
                            <th class="text-center" style="width: 100px;">@lang('lang.action')</th>
                        </tr>
                    </thead>

                    

                    <tbody>
                        @if (isset($journalvoucher))
                            @foreach ($journalvoucher as $value)
                                <tr @if ($value->status == 2) class="bg-dark" @endif
                                    @if (@$value->credit_amount == '') class="text-danger" @endif>
                                    <td class="text-center"><a href="javascript:void(0)"
                                            class="data-item {{ $value->id ? 'active' : '' }}"
                                            onclick="list_style_new()"
                                            data-id="{{ $value->id }}">{{ @$value->doc_number }}</a>
                                    </td>
                                    <td class="text-center">
                                        {{ date('d/m/Y', strtotime(@$value->doc_date)) }}
                                    </td>
                                    <td>
                                        
                                            {{ @$value->narration }}
                                    </td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format(@$value->credit_amount, '', '', ',') }}
                                        {{--  {{ @$value->debit_amount }}  --}}
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
                                    <td>
                                        {{ @$value->createdby->full_name }}
                                    </td>
                                   

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            @if ((Auth::user()->role_id == 1 || Auth::user()->id == @$value->created_by) && $value->status != 0)
                                                <a class="btn btn=-sm btn-light"
                                                    href="{{ url('journalvoucher/' . @$value->id . '?jv_action=edit') }}"
                                                    onclick="list_style_new()"><i
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
                                                            class="ico ico icon-outline-trash-bin-minimalistic text-dark"
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
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">



            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {
                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('journalvoucher') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('journalvoucher-details') }}/" + id;




                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>


            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                @if ($action === 'add')
                @include('backEnd.journal-voucher.j_add', $addData)

                @elseif($action === 'edit')
                    @include('backEnd.journal-voucher.j_edit', $editData)
                @elseif (!empty($data) && is_array($data))
                    @include('backEnd.journal-voucher.j_details', $data)
                @else
                    <div onclick="window.location.href='{{ url('journalvoucher-add') }}'"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer">Journal Voucher</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                @endif
            </div>


        </div>
    </div>





    <script>
        $(document).ready(function() {

            $('#search_invoice').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('journalvoucher.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        console.log(data)
                        $('#short-list').html('');

                        if (data.length > 0) {
                            $.each(data, function(index, invoice) {

                                let ims = `<li class="nav-item w-100" role="presentation">
    <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
        <div class="row w-100">
             <div class="col-12">
                <label class="form-control-plaintext truncate-text">
                    ${invoice.narration}
                </label>
            </div>
            <div class="col-4">
                <div class="form-control-plaintext" style="font-size: 11px" >${invoice.doc_number}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${get_format_date(invoice.doc_date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${Number(invoice.credit_amount).toLocaleString()} ${invoice.currency_code}
                </div>
            </div>
           
        </div>
    </button>
</li>`;
                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html(
                                '<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
        var columnWidths = [100,100,450,150,30,100,100]; // 👈 define widths in px

        // Apply widths to <thead> and <tbody>
        $theadTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
            $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
        });

        // Apply the same widths to <tfoot>
        $tfootTh.each(function(i) {
            var w = columnWidths[i];
            $(this).css('width', w + 'px');
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>


@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Receipts</h4>

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
                @if (count($receipt) > 0)
                    @foreach ($receipt as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)" class="nav-link data-item {{ $loop->first ? 'active' : '' }}"
                                data-id="{{ $value->id }}">
                                <div class="row w-100">
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ $value->first_account_name }}
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ $value->doc_number }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @App\SysHelper::com_curr_format(abs(@$value->debit_amount - @$value->credit_amount), 2, '.', ',') }}
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>
            <div id="long-list" style="display: none;">

                <div class="d-flex align-items-center justify-content-center">
    <input type="text" id="tableSearch" 
           class="form-control me-2  " 
           style="font-size:13px; width: 350px; position: absolute;
    top: 12px;
    right: 120px;" 
           placeholder="Search">

    <button type="button" class="btn btn-light list_style_search_btn me-2" onclick="search_box_show_hide()">
        <i class="ico icon-outline-magnifer"></i>
    </button>

    <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_search()">
        <i class="ico icon-outline-list-down"></i>
    </button>
</div>

                <div class="card mt-3" id="search_box" style="display: none;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt', 'method' => 'post', 'id' => 'receipt-search']) }}
                                <div class="row">
                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Doc Number</label>
                                        <input class="form-control" id="doc_number" type="text" autocomplete="off"
                                            name="doc_number" value="">
                                    </div>
                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Receipt Mode</label>
                                        <select class="form-control js-example-basic-single" name="receipt_mode"
                                            id="receipt_mode">
                                            <option value="">-Select-</option>
                                            @if (count($receipt_mode_list) > 0)
                                                @foreach ($receipt_mode_list as $li)
                                                    <option value="{{ $li['id'] }}">{{ $li['account_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Receipt Through</label>
                                        <select class="form-control js-example-basic-single" name="receipt_through"
                                            id="receipt_through">
                                            <option value="">-Select-</option>
                                            <option value="0">Cash</option>
                                            <option value="1">Bank Transfer</option>
                                            <option value="2">CDC Cheque</option>
                                            <option value="3">PDC Cheque</option>
                                        </select>
                                    </div>
                                    <div class="col-3 mb-2">
                                        <label for="" class="form-label">Account Name</label>
                                        <select class="form-control js-example-basic-single" name="account_name"
                                            id="account_name">
                                            <option value="">-Select-</option>
                                            @foreach ($accounts as $value)
                                                <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Amount</label>
                                        <input class="form-control " id="amount" type="text"
                                            autocomplete="off" name="amount" value="">
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Doc Date</label>
                                        <input class="form-control date-picker" id="doc_date" type="text"
                                            autocomplete="off" name="doc_date" >
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Receipt Date</label>
                                        <input class="form-control date-picker" id="receipt_date" type="text"
                                            autocomplete="off" name="receipt_date">
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Cheque Date</label>
                                        <input class="form-control date-picker" id="cheque_date" type="text"
                                            autocomplete="off" name="cheque_date">
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Cheque Number</label>
                                        <input class="form-control " id="cheque_number" type="text"
                                            autocomplete="off" name="cheque_number" value="">
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Cheque Bank Name</label>
                                        <input class="form-control " id="cheque_bank_name" type="text"
                                            autocomplete="off" name="cheque_bank_name" value="">
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Deal ID</label>
                                        <input class="form-control " id="" type="text"
                                            autocomplete="off" name="deal_id" value="">
                                    </div>

                                    <div class="col-1-5 mb-2">
                                        <label for="" class="form-label">Created By</label>
                                        <select class="form-control js-example-basic-single" name="created_by"
                                            id="created_by">
                                            <option value="">-Select-</option>
                                            @foreach ($staff_list as $value)
                                                <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-1"><br />
                                        <button type="submit" class="btn btn-light">
                                            <i class="ico icon-outline-magnifer text-success" style="font-size: 16px"></i> Filter
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
                        <div class="table-responsive mt-2">
                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 90px;"> @lang('Doc Number')</th>
                                        <th class="text-center" style="width: 50px;"> @lang('Mode')</th>
                                        <th> @lang('Receipt Mode')</th>
                                        <th> @lang('Receipt Through')</th>
                                        <th style="width: 200px;"> @lang('Account Name')</th>
                                        <th class="text-end"> @lang('Amount')</th>
                                        <th class="text-center"> @lang('Doc Date')</th>
                                        <th class="text-center"> @lang('Receipt Date')</th>
                                        <th class="text-center"> @lang('Cheque Date')</th>
                                        <th> @lang('Cheque Number')</th>
                                        <th> @lang('Cheque Bank Name')</th>
                                        {{-- <th> @lang('Deal ID')</th> --}}
                                        <th> @lang('Created By')</th>
                                        {{-- <th> @lang('Narration')</th> --}}
                                        <th class="text-center" style="width:70px"> @lang('lang.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($receipt))
                                        @foreach ($receipt as $value)
                                            <tr @if ($value->status == 2) class="bg-dark" @endif
                                                @if (@$value->type == 2) class="text-danger" @endif>
                                                <td class="text-center"><a
                                                        href="{{ url('receipt/' . @$value->id . '/view') }}">{{ @$value->doc_number }}</a>
                                                </td>
                                                <td class="text-center">
                                                    @if (@$value->mode == 1)
                                                        Cash
                                                    @else
                                                        Bank
                                                    @endif
                                                </td>
                                                <td>{{ @$value->account->account_name }}</td>
                                                <td>
                                                    @if (@$value->mode == 1)
                                                        Cash
                                                    @else
                                                        @if (@$value->receipt_through == 1)
                                                            Bank Transfer
                                                        @elseif(@$value->receipt_through == 2)
                                                            CDC Cheque
                                                        @else
                                                            PDC Cheque
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ @$value->account_name }}</td>
                                                <td class="text-end">
                                                    {{ @App\SysHelper::com_curr_format(abs(@$value->debit_amount - @$value->credit_amount), 2, '.', ',') }}
                                                </td>
                                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}
                                                </td>
                                                <td class="text-center">
                                                    {{ date('d/m/Y', strtotime(@$value->receipt_date)) }}</td>
                                                <td class="text-center">
                                                    @if (@$value->mode == 2 && @$value->receipt_through != 1)
                                                        {{ date('d/m/Y', strtotime(@$value->cheque_date)) }}
                                                    @endif
                                                </td>
                                                <td>{{ @$value->cheque_number }}</td>
                                                <td>{{ @$value->cheque_bank_name }}</td>


                                                {{-- <td>
                                    @php
                                    $dealid =  explode(',', $value->deal_id);
                                    @endphp
                                    @foreach ($dealid as $d)
                                    @php $deal_code = @App\SysHelper::get_code_from_dealid($d); @endphp
                                        <a href="{{url('get-url-deal-track/'.$deal_code)}}" target="_blank">{{ $deal_code }}</a>
                                    @endforeach
                                    
                                
                                
                                </td> --}}



                                                <td>{{ @$value->full_name }}</td>
                                                {{-- <td>{{ @$value->narration }}</td> --}}
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <button class="btn btn-sm btn-light"
                                                            href="{{ url('receipt/' . $value->id . '/download') }}"><i
                                                                class="ico icon-bold-download-minimalistic text-dark"
                                                                style="font-size: 16px;"></i></button>
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
        </div>
    </aside>


    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <script>
                $(document).ready(function() {
                    // Delegated click works for both static + dynamic .data-item
                    $(document).on('click', '.data-item', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');

                        // highlight active
                        $('.data-item').removeClass('active');
                        $(this).addClass('active');

                           var newUrl = "{{ url('receipt') }}/" + id;
                                        window.history.pushState({
                                            path: newUrl
                                        }, '', newUrl);

                        var action = "{{ URL::to('receipt-details') }}/" + id;

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
                                // always hide loading, success or error
                                $("#loading_bg").css("display", "none");
                            }
                        });
                    });
                });
            </script>
            <script>
                $(document).ready(function() {

                    $('#search_invoice').on('keyup', function() {
                        var query = $(this).val();

                        $.ajax({
                            url: "{{ route('receipt.search') }}",
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
                    ${invoice.first_account_name}
                </label>
            </div>
            <div class="col-4">
                <div class="form-control-plaintext" style="font-size: 11px">${invoice.doc_number}</div>
            </div>
            <div class="col-4 pl-2">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${get_format_date(invoice.doc_date)}
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                    ${Number(invoice.debit_amount - invoice.credit_amount).toLocaleString()} ${invoice.currency_code}
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

                function get_mode(mode, receipt_through) {
                    if (mode == 1) {
                        return "Cash";
                    } else {
                        if (receipt_through == 1) return "Bank Transfer";
                        else if (receipt_through == 2) return "CDC Cheque";
                        else return "PDC Cheque";
                    }
                }
            </script>
            @if (count($receipt) > 0)
                {{-- <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                            @if (count($purchasegrn) > 0)
                                @include('backEnd.grn.grn_add',$data)
                            @endif
                        </div> --}}


                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                    @if (count($receipt) > 0)
                        @include('backEnd.receipt.r_add')
                    @endif
                </div>
            @else
                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        @include('backEnd.receipt.r_add')
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
            $(".list_style_search_btn").on("click", function() {
                $("#search_box").slideToggle(200); // expands/collapses smoothly
            });
        });
    </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

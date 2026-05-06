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

                localStorage.setItem('listViewPaymentList', 'long');
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

                localStorage.setItem('listViewPaymentList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewPaymentList');
            if (savedView === 'long') {
                isFullList = false; // so that toggling once activates full view
                list_style_new();
            } else {
                // Default to short view
                isFullList = true; // so that toggling once activates short view
                list_style_new();
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewPaymentList', 'short');
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
            <h4 class="mb-2">Payments
            </h4>



            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_payment_id" id="search_payment_id" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Payments
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>

                        <ul class="dropdown-menu">

                    <li><a class="dropdown-item" href="{{url('stl')}}"><i class="ico icon-outline-add-square text-success"></i> STL</a></li>
                    <li><a class="dropdown-item" href="{{url('payment-cheque-list')}}"><i class="ico icon-outline-add-square text-success"></i> Cheque</a></li>
                    <!-- export  -->
                    <li>
                        <a href="#" id="exportExcelPayments"
                            class="dropdown-item d-flex align-items-center"><i
                                class="ico icon-outline-export text-success title-15 me-2"></i> Export</a>
                    </li>
                </ul>
                    </div>





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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment', 'method' => 'post', 'id' => 'payment-search']) }}
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Doc Number</label>
                                <input class="form-control" id="doc_number" type="text" autocomplete="off"
                                    name="doc_number" value="{{ $ctrl_doc_number }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Payment Mode</label>
                                <select class="form-control js-example-basic-single" name="payment_mode" >
                                    <option value="">-Select-</option>
                                    @if (count($payment_mode_list) > 0)
                                        @foreach ($payment_mode_list as $li)
                                            <option value="{{ $li['id'] }}"
                                                @if ($ctrl_payment_mode == $li['id']) selected @endif>{{ $li['account_name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Payment Through</label>
                                <select class="form-control js-example-basic-single" name="payment_through">
                                    <option value="" @if ((string)($ctrl_payment_through ?? '') === '') selected @endif>-Select-</option>
                                    <option value="0" @if ((string)($ctrl_payment_through ?? '') === '0') selected @endif>Cash</option>
                                    <option value="1" @if ((string)($ctrl_payment_through ?? '') === '1') selected @endif>Bank Transfer</option>
                                    <option value="2" @if ((string)($ctrl_payment_through ?? '') === '2') selected @endif>CDC Cheque</option>
                                    <option value="3" @if ((string)($ctrl_payment_through ?? '') === '3') selected @endif>PDC Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Account Name</label>
                                <select class="form-control js-example-basic-single" name="account_name" >
                                    <option value="">-Select-</option>
                                    @foreach ($accounts as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_account_name == $value->id) selected @endif>{{ @$value->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Amount</label>
                                <input class="form-control datepicker" id="amount4" type="text" autocomplete="off"
                                    name="amount" value="{{ $ctrl_amount }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Doc Date</label>
                                <input class="form-control date-picker"  type="text" autocomplete="off"
                                    name="doc_date" value="{{ $ctrl_doc_date }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Payment Date</label>
                                <input class="form-control date-picker"  type="text"
                                    autocomplete="off" name="payment_date" value="{{ $ctrl_payment_date }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Cheque Date</label>
                                <input class="form-control date-picker"type="text"
                                    autocomplete="off" name="cheque_date" value="{{ $ctrl_cheque_date }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Cheque Number</label>
                                <input class="form-control datepicker"  type="text"
                                    autocomplete="off" name="cheque_number" value="{{ $ctrl_cheque_number }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Deal ID</label>
                                <input class="form-control datepicker"  type="text" autocomplete="off"
                                    name="deal_id" value="{{ $ctrl_deal_id }}">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Created By</label>
                                <select class="form-control js-example-basic-single" name="created_by" id="created_by">
                                    <option value="">-Select-</option>
                                    @foreach ($staff_list as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if ($ctrl_created_by == $value->user_id) selected @endif>{{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1">
                                <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
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
                @if (count($payment) > 0)
                    @foreach ($payment as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                <div class="row w-100">
                                      <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">{{ @$value->account->account_code }}
                                            - {{ @$value->account->account_name }} -
                                            @if (@$value->mode == 1)
                                                Cash
                                            @else
                                                @if (@$value->payment_through == 1)
                                                    Bank Transfer
                                                @elseif(@$value->payment_through == 2)
                                                    CDC Cheque
                                                @else
                                                    PDC Cheque
                                                @endif
                                            @endif
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
                                            {{ @App\SysHelper::com_curr_format(abs(@$value->debit_amount - @$value->credit_amount), 2, '.', ',') }}
                                        </div>
                                    </div>
                                  
                                </div>
                                {{-- </button> --}}
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th style="width: 70px" class="text-center"> @lang('Doc No')</th>
                            <th style="width: 100px" class="text-center"> @lang('Mode')</th>
                            <th style="width: 100px"> @lang('Payment Mode')</th>
                            <th style="width: 100px"> @lang('Payment Through')</th>
                            <th style="width: 200px"> @lang('Account Name')</th>
                            <th style="width: 100px" class="text-end"> @lang('Amount')</th>
                            <th style="width: 100px" class="text-center"> @lang('Doc Date')</th>
                            <th style="width: 100px" class="text-center"> @lang('Payment Date')</th>
                            <th style="width: 100px" class="text-center"> @lang('Cheque Date')</th>
                            <th style="width: 100px"> @lang('Cheque No')</th>
                            <th style="width: 100px" class="text-center"> @lang('Deal ID')</th>
                            <th style="width: 100px"> @lang('Created By')</th>
                            <th style="width: 100px"> @lang('Narration')</th>
                            <th class="text-center" style="width: 70px">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($payment))
                            @foreach ($payment as $value)
                                <tr @if ($value->status == 2) class="bg-dark" @endif
                                    @if (@$value->type == 2) class="text-danger" @endif>
                                    <td class="text-center"><a
                                            href="{{ url('payment/' . @$value->id . '/view') }}">{{ @$value->doc_number }}</a>
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
                                            @if (@$value->payment_through == 1)
                                                Bank Transfer
                                            @elseif(@$value->payment_through == 2)
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
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->payment_date)) }}</td>
                                    <td class="text-center">
                                        @if (@$value->mode == 2 && @$value->payment_through != 1)
                                            {{ date('d/m/Y', strtotime(@$value->cheque_date)) }}
                                        @endif
                                    </td>
                                    <td>{{ @$value->cheque_number }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('get-url-deal-track/' . @$value->deal_code->code) }}"
                                            target="_blank">{{ @$value->deal_code->code }}</a>
                                    </td>
                                    <td>{{ @$value->full_name }}</td>
                                    <td>{{ @$value->narration }}</td>
                                    <td class="text-center">
                                        <a class=" btn-sm btn-light"
                                            href="{{ url('payment/' . $value->id . '/download') }}"><i
                                                class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>
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

                        var newUrl = "{{ url('payment') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('payment-details') }}/" + id;
                        $('#loading_bg').show();

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
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });


                    });


                });
            </script>



            <div class="" role="tabpanel" aria-labelledby="po-tab" id="data-details">

                @if ($action === 'add')
                    @include('backEnd.payment.p_add_2', $addData)
                @elseif($action === 'edit')
                  
                    @include('backEnd.payment.p_edit', $editData)
                @elseif($action === 'dealtrack')
                    @include('backEnd.payment.p_add_from_dealtrack', $addData)
                @elseif (isset($data) && !empty($data))
                    @include('backEnd.payment.p_details', $data)
                @else
                    <div onclick="window.location.href='{{ url('payment?pr_action=add') }}'"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer"> Payment</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                @endif
            </div>




        </div>
    </div>






    <script>
        $(document).ready(function() {

            $('#search_payment_id').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-payment.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                                console.log(amc_list);



                                let ims = `  <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item"
                                data-id="${amc_list.id}">
                           
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">
                                            ${amc_list.account?.account_code}
                                            - ${amc_list.account?.account_name}
                                                ${
                                                    amc_list.mode == 1
                                                        ? 'Cash'
                                                        : amc_list.payment_through == 1
                                                            ? 'Bank Transfer'
                                                            : amc_list.payment_through == 2
                                                                ? 'CDC Cheque'
                                                                : 'PDC Cheque'
                                                }
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${get_format_date(amc_list.doc_date)}
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${amc_list.formatted_amount}
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
    $('#exportExcelPayments').on('click', function(e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalPayment = @json($payment->count() ?? 0);
        var dateFrom = @json($ctrl_doc_date ?? '');
        var dateTo = @json($ctrl_payment_date ?? '');

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
        rows.push(['Payments (' + totalPayment + ')']);

        if (dateFrom || dateTo) {
            rows.push(['From: ' + formatDMY(dateFrom) + '  To: ' + formatDMY(dateTo)]);
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
            var worksheet = workbook.addWorksheet('Payments');
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
                var filename = 'payments_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>

    <?php } catch (\Exception $e) { ?> {{ $e }}
    <?php  } ?>
@endsection

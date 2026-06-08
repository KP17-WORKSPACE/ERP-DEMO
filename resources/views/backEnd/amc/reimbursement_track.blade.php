@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp


    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Reimbursement Track</h4>

        <div class="search-filter-container mb-4" id="short-list">
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control" id="search_invoice" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
            </div>                        
            <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_search()" style="height: 32px;">
                <i class="ico icon-outline-list-down"></i>
            </button>
        </div>

        <div class="left-nav-list" id="invoice_list">
            <ul id="short-list-items" class="nav flex-column nav-pills" role="tablist">
                @if(count($data)>0)
                    @foreach($data as $value)
                    <li class="nav-item w-100" role="presentation">
                        <button href="javascript:void(0)" class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
                            <div class="row w-100">
                                <div class="col-12">
                                    <label class="form-control-plaintext truncate-text">{{ $value->deal_code->customername->name ?? 'N/A' }}</label>
                                </div>
                                <div class="col-4">
                                    <div class="form-control-plaintext" style="font-size:11px">{{ $value->reimbursement_no }}</div>
                                </div>
                                <div class="col-4 pl-2">
                                    <div class="form-control-plaintext truncate-text" style="font-size:11px">{{ date('d/m/Y', strtotime($value->date)) }}</div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                        {{ number_format($value->amount, 2) }} {{ $value->currencycode->code ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </button>
                    </li>
                    @endforeach
                @endif
            </ul>

            <div id="long-list" style="display: none;">
                <input type="text" id="tableSearch" class="form-control" style="font-size:13px; width: 350px; position: absolute; top: 10px; right: 231px;" placeholder="Search">

                <button type="button" class="btn btn-light list_style_search_btn" id="exportExcelDealTrack" title="Export to Excel" style="margin-right:66px">
                    <i class="ico icon-outline-export text-success"></i> Export
                </button>

                <button type="button" class="btn btn-light list_style_search_btn" onclick="search_box_show_hide()" style="margin-right: 8px;">
                    <i class="ico icon-outline-magnifer"></i>
                </button>

                <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_search()">
                    <i class="ico icon-outline-list-down"></i>
                </button>

                <div class="card mt-3" id="search_box" style="display: {{ request()->has('reimbursement_no') || request()->has('vendor_name') || request()->has('from_date') || request()->has('to_date') ? 'block' : 'none' }};">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                {{ Form::open(['class' => 'form-horizontal', 'url' => 'crm-reimbursement-track', 'method' => 'get', 'id' => 'crm-reimbursement-search']) }}
                                <div class="row">
                                    <div class="col-md-2 mb-2">
                                        <label for="reimbursement_no" class="form-label">Reimbursement No</label>
                                        <input class="form-control" id="reimbursement_no" type="text" autocomplete="off" name="reimbursement_no" value="{{ $ctrl_reimbursement_no ?? '' }}">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="vendor_name" class="form-label">Vendor Name</label>
                                        <input class="form-control" id="vendor_name" type="text" autocomplete="off" name="vendor_name" value="{{ $ctrl_vendor_name ?? '' }}">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label for="from_date" class="form-label">From Date</label>
                                        <div class="input-group">
                                            <input class="form-control date form-control-sm" id="from_date" type="text" autocomplete="off" name="from_date" value="{{ $ctrl_from_date ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label for="to_date" class="form-label">To Date</label>
                                        <div class="input-group">
                                            <input class="form-control date form-control-sm" id="to_date" type="text" autocomplete="off" name="to_date" value="{{ $ctrl_to_date ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-light add-btn w-100" id="btnSubmit">
                                            <i class="ico icon-outline-magnifer text-success"></i> Filter
                                        </button>
                                        <a href="{{ url('crm-reimbursement-track') }}" class="btn btn-light add-btn ms-2" title="Reset">
                                            <i class="ico icon-outline-rotate-left"></i>
                                        </a>
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
                            <table class="table table-hover mt-2 data-table table-fixed-header" style="table-layout: fixed;width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 120px;" class="text-center">Reimbursement No</th>
                                        <th class="text-center" style="width: 100px;">Date</th>
                                        <th style="width: 150px;">Employee</th>
                                        <th style="width: 150px;">Customer Name</th>
                                        <th style="width: 150px;">Vendor Name</th>
                                        <th style="width: 120px;" class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:12px">
                                    @foreach($data as $value)
                                        <tr class="{{ $value->deleted_at ? 'bg-dark' : '' }}">
                                            <td class="text-center data-item" data-id="{{ $value->id }}" onclick="list_style_search()"><a>{{ $value->reimbursement_no }}</a></td>
                                            <td class="text-center">{{ date('d/m/Y', strtotime($value->date)) }}</td>
                                            <td>{{ $value->employee->full_name ?? '' }}</td>
                                            <td>{{ $value->deal_code->customername->name ?? '' }}</td>
                                            <td>{{ $value->vendor_name }}</td>
                                            <td class="text-end">
                                                {{ number_format($value->amount, 2) }} {{ $value->currencycode->code ?? '' }}
                                            </td>
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
                $(document).ready(function () {
                    $(document).on('click', '.data-item', function () {
                        $("#loading_bg").css("display", "block");
                        var id = $(this).data('id');

                        $('.data-item').removeClass('active');
                        $(this).addClass('active');

                        var newUrl = "{{ url('crm-reimbursement-track') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ url('crm-reimbursement-track-details') }}/" + id;

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

                    // Trigger initial load if active_id exists
                    var initialId = "{{ $active_id ?? '' }}";
                    if(initialId) {
                        setTimeout(function(){
                            // If initial load, don't trigger click immediately if it's already rendered server side,
                            // but since it's an AJAX container, we might need to click it. Wait, we can render it serverside!
                        }, 200);
                    }
                });
            </script>

            <script>
                $(document).ready(function(){
                    $('#search_invoice').on('input', function(){
                        var query = $(this).val();

                        $.ajax({
                            url: "{{ route('crm-reimbursement.search') }}",
                            type: "GET",
                            data: { query: query },
                            success: function(data){
                                $('#short-list-items').html('');

                                if(data.length > 0){
                                    $.each(data, function(index, invoice){
                                        let amount = parseFloat(invoice.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        let ims = `<li class="nav-item w-100" role="presentation">
                                            <button href="javascript:void(0)" class="nav-link data-item" data-id="${invoice.id}">
                                                <div class="row w-100">
                                                    <div class="col-12">
                                                        <label class="form-control-plaintext truncate-text">
                                                            ${invoice.customer_name ?? 'N/A'}
                                                        </label>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="form-control-plaintext" style="font-size: 11px">${invoice.reimbursement_no}</div>
                                                    </div>
                                                    <div class="col-4 pl-2">
                                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                            ${invoice.date ? invoice.date.split('-').reverse().join('/') : ''}
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                                            ${amount} ${invoice.currency_code}
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

            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                @if (isset($selectedReimbursement))
                    @include('backEnd.amc.reimbursement_track_detail', ['selectedReimbursement' => $selectedReimbursement, 'staff' => $staff])
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
                $("#search_box").slideToggle(200); 
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#exportExcelDealTrack').on('click', function (e) {
                e.preventDefault();

                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var totalDeals = @json($data->count() ?? 0);

                var $table = $('#long-list table');

                var visibleColIndexes = [];
                var headerLabels = [];

                $table.find('thead tr th').each(function (i) {
                    if ($(this).css('display') !== 'none') {
                        var label = $(this).text().trim();
                        if (['actions', 'action', 'actions '].includes(label.toLowerCase().trim())) {
                            return;
                        }
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                });

                var rows = [];
                rows.push([companyName]);
                rows.push(['Reimbursement Track (' + totalDeals + ')']);

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

                if (rows.length <= 4) {
                    alert('No data available for export');
                    return;
                }

                var N = headerLabels.length || 1;
                var workbook = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('ReimbursementTrack');
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
                        top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'}
                    };
                });

                for (var ri = hdrIdx + 1; ri < rows.length; ri++) {
                    wsRowNum++;
                    var dataRow = worksheet.addRow(rows[ri]);
                    dataRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.alignment = { vertical: 'middle', wrapText: true };
                        cell.border = {
                            top: {style:'thin'}, left: {style:'thin'}, bottom: {style:'thin'}, right: {style:'thin'}
                        };
                    });
                }

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'Reimbursement_Track.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    setTimeout(function() {
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    }, 0);
                });
            });
        });
    </script>
@endsection

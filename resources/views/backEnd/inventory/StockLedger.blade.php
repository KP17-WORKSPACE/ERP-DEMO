@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#exportStockLedger').on('click', function () {
                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var dateFrom = $('#from_date').val().trim();
                var dateTo = $('#to_date').val().trim();
                var headerLabels = [
                    'Part Number', 'Description', 'Doc Date', 'Doc No', 'Ref No', 'Deal Id',
                    'Account Name', 'Reference Name', 'In Qty', 'In Rate', 'Out Qty', 'Out Rate',
                    'Bal Qty', 'Avg Rate', 'Serial No'
                ];

                function formatDMY(value) {
                    if (!value) return '';
                    var text = value.trim();
                    if (/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/.test(text)) {
                        return text.replace(/-/g, '/');
                    }
                    var normalized = text.replace(/-/g, '/');
                    var parts = normalized.split('/');
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                        return normalized;
                    }
                    return text;
                }

                var rows = [];
                rows.push([companyName]);
                rows.push(['Stock Ledger']);
                if (dateFrom || dateTo) {
                    var parts = [];
                    if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
                    if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
                    rows.push([parts.join('  ')]);
                }
                rows.push([]);
                rows.push(headerLabels);

                var totalRows = 0;

                $('table.data-table:visible').each(function () {
                    var $table = $(this);
                    var $partHeader = $table.prevAll('div.border.bg-success').first();
                    var partNumber = $partHeader.clone().children('span').remove().end().text().trim() || '';
                    var partDesc = $partHeader.find('span').text().trim() || '';

                    $table.find('tr').each(function () {
                        var $cells = $(this).find('td').filter(function () {
                            return $(this).css('display') !== 'none';
                        });
                        if ($cells.length === 0) return;

                        var rowData = [partNumber, partDesc];
                        $cells.each(function () {
                            var $cell = $(this);
                            var cellText = $cell.text().trim().replace(/\s+/g, ' ');
                            var $srlDiv = $cell.find('div[class^="all_srl_no_"]');
                            if ($srlDiv.length > 0) {
                                var serials = $srlDiv.text().trim();
                                if (serials.length > 0) {
                                    cellText = serials;
                                }
                            }
                            cellText = cellText.replace(/View\s+SrlNo/gi, '').trim();
                            rowData.push(cellText);
                        });

                        while (rowData.length < headerLabels.length) {
                            rowData.push('');
                        }

                        rows.push(rowData);
                        totalRows++;
                    });
                });

                if (totalRows === 0) {
                    alert('No data available for export');
                    return;
                }

                var N = headerLabels.length || 1;
                var workbook = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('Stock Ledger');
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
                    else wsRow.getCell(1).font = { bold: true, size: 11 };
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
                    function pad(n){ return n<10 ? ('0'+n) : n; }
                    var d = new Date();
                    var filename = 'stock_ledger_' + pad(d.getDate()) + '-' + pad(d.getMonth()+1) + '-' + d.getFullYear() + '.xlsx';
                    saveAs(blob, filename);
                });
            });
        });
    </script>

    <aside class="left-nav col-12" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Stock Ledger
                </h4>
                <div class="search-filter-container mb-0">

                




                    <a type="button" id="exportStockLedger" class="btn btn-light text-dark ">
                        <i class="ico icon-outline-export text-success title-15"></i> Export
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><a href="{{ url('list-price') }}" class="dropdown-item">
                                    List Price</a></li>
                            <li><a href="{{ url('license-key-report') }}" class="dropdown-item">
                                    License Key Report</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            

            <div class="search-filter-container mt-1 mb-4 border">

                <div class="card" style="width:100%">
                    <div class="card-body">

                        {{ Form::open([
                            'class' => 'form-horizontal',
                            'files' => true,
                            'url' => 'stock-ledger',
                            'method' => 'POST',
                            'enctype' => 'multipart/form-data',
                        ]) }}
                        <div class="row">

                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                            <div class="col-1-5 mb-2 ">
                                @php

                                    if (!empty($from_date)) {
                                        try {
                                            $from_date = \Carbon\Carbon::parse($from_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $from_date = '';
                                        }
                                    }

                                    if (!empty($to_date)) {
                                        try {
                                            $to_date = \Carbon\Carbon::parse($to_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $to_date = '';
                                        }
                                    }
                                @endphp
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date"
                                    id="from_date" value="{{ $from_date }}">
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date"
                                    id="to_date" value="{{ $to_date }}">
                            </div>



                            <style>
                                #part_number_list ul {
                                    width: 380px
                                }
                            </style>

                            <div class="col-3 mb-2 ">
                                <label class="form-label">Part Number</label>

                                <input class="form-control" type="hidden" id="part_number_array">
                                <input class="form-control" type="text" id="part_number" name="part_number"
                                    value="{{ $str_partno }}" autocomplete="off">
                                <div id="part_number_list">
                                </div>
                                <script>
                                    $(document).ready(function() {

                                        // When typing in input
                                        $('#part_number').keyup(function() {
                                            var query = $(this).val().split(',').pop().trim(); // get last part

                                            if (query != '') {
                                                var _token = $('input[name="_token"]').val();
                                                $.ajax({
                                                    url: "{{ route('autocomplete.fetch_product_partnumber_withcoma') }}",
                                                    method: "POST",
                                                    data: {
                                                        query: query,
                                                        _token: _token
                                                    },
                                                    success: function(data) {
                                                        $('#part_number_list').fadeIn();
                                                        $('#part_number_list').html(data);
                                                    }
                                                });
                                            } else {
                                                $('#part_number_list').fadeOut();
                                            }
                                        });

                                        // When clicking a suggestion
                                        $('#part_number_list').on('click', 'li', function() {
                                            var current = $('#part_number').val(); // existing input value
                                            var parts = current.split(','); // split into array
                                            parts[parts.length - 1] = $(this).text().trim(); // replace last typed part
                                            var finalVal = parts.join(',').replace(/^,|,$/g, ''); // clean commas

                                            $('#part_number').val(finalVal); // update visible input
                                            $('#part_number_array').val(finalVal); // update hidden field

                                            $('#part_number_list').fadeOut();
                                        });

                                        // Hide suggestion box on outside click
                                        $(document).click(function(e) {
                                            if (!$(e.target).closest('#part_number, #part_number_list').length) {
                                                $('#part_number_list').fadeOut();
                                            }
                                        });

                                    });
                                </script>



                                
                                

                            </div>


                            <div class="col-md-3">
                                <button type="submit" class="btn btn-light mt-4 ">
                                    <i class="ico icon-outline-magnifer"></i> Filter
                                </button>
                            </div>

                            <div class="col mb-2">
                                <label for="" class="form-check-label">Search in List</label>
                                <input type="text" id="tableSearch" class="form-control mb-2" placeholder="" autocomplete="off">
                            </div>

                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <div class="table-responsive">

                <script>
                    function show_hide_srl_no(id) {
                        if ($('.all_srl_no_' + id).css('display') == 'none') {
                            $('.all_srl_no_' + id).css("display", "block");
                            $('#atag_srl_no' + id).text("Hide All SrlNo");
                        } else {
                            $('.all_srl_no_' + id).css("display", "none");
                            $('#atag_srl_no' + id).text("View All SrlNo");
                        }
                    }
                </script>

                <?php $i = 0; ?>
                @if (count($stocklist) > 0)
                    @foreach ($stocklist as $list)
                    
                        <div class="border bg-success text-left fw-bold text-white d-block w-100" style="box-sizing: border-box;">&nbsp;&nbsp;{{ $partnolist[$i] }}
                            <?php try{ ?>
                            {{-- <span style="padding-left: 50px;">{{ $stocklist[$i][0]->productdet->description }}</span> --}}
                            <span class=" text-white" style="padding-left: 50px;">{{ @App\SysHelper::get_product_description(null,$partnolist[$i]) }}</span>
                            <?php }catch (\Exception $e) { } ?>
                        </div>
                        <table id="long-list" class="table table-hover data-table" width="100%" cellspacing="0" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width:100px;">@lang('Doc Date')</th>
                                    <th style="width:100px;">@lang('Doc No')</th>
                                    <th style="width:100px;">@lang('Ref No')</th>
                                    <th style="width:70px;">@lang('Deal Id')</th>
                                    <th>@lang('Account Name')</th>
                                    <th>@lang('Reference Name')</th>
                                    <th style="width:70px;" class="text-center">@lang('In Qty')</th>
                                    <th style="width:100px;" class="text-end">@lang('In Rate')</th>
                                    <th style="width:70px;" class="text-center">@lang('Out Qty')</th>
                                    <th style="width:100px;" class="text-end">@lang('Out Rate')</th>
                                    <th style="width:70px;" class="text-center">@lang('Bal Qty')</th>
                                    <th style="width:90px;" class="text-end">@lang('Avg Rate')</th>
                                    <th style="width:110px;" class="text-end"><a
                                            style="padding: 2px 6px; font-size: 12px; line-height: 1.2; white-space: normal; display: block; width: 100%;"
                                            class="btn btn-light rounded-0" id="atag_srl_no{{ $i }}"
                                            onclick="show_hide_srl_no({{ $i }})">View All SrlNo</a></th>
                                </tr>


                                @php
                                    $opb = @App\SysHelper::get_stock_ledger_opening_stock(
                                        $partnolist[$i],
                                        $opb_date,
                                        $company_id,
                                    );
                                @endphp
                                 @if (count($list) == 0)
                              <tr >
                                        <td colspan="14" class="text-center text-danger"> &nbsp;</td>
                                </tr>
                                
                            @endif
                            @if ($opb[0] > 0 || $opb[1] > 0)
                                      <tr>
                                    <td>{{ date('d-M-Y', strtotime(@$opb_date)) }}</td>
                                    <td colspan="3"></td>
                                    <td>Opening Balance</td>
                                    <td colspan="5"></td>
                                    <td class="text-center">{{ $opb[0] }}</td>
                                    <td class="text-end">{{ $opb[1] }}</td>
                                    <td></td>
                                </tr>
                                
                            @endif                      
                                 @if (count($list) == 0)
                              <tr >
                                        <td colspan="14" class="text-center text-danger"> &nbsp;</td>
                                </tr>
                                
                            @endif


                            </thead>
                           
                            @if (count($list) > 0)
                                <tbody>
                                    @php
                                        $count = 1;
                                        $total_qty_in = 0;
                                        $total_price_in = 0;
                                        $total_qty_out = 0;
                                        $total_price_out = 0;
                                        $total_value = 0;
                                        $price_in_qty_in = 0;
                                        $qty_in = 0;
                                        $bal_qty = 0;
                                        $avg_qty = 0;
                                        $stockLedgerParseAmount = function ($v) {
                                            if ($v === null || $v === '') {
                                                return 0.0;
                                            }
                                            if (is_int($v) || is_float($v)) {
                                                return (float) $v;
                                            }
                                            if (is_numeric($v)) {
                                                return (float) $v;
                                            }
                                            $s = trim((string) $v);
                                            $s = str_replace([',', ' '], '', $s);

                                            return $s === '' ? 0.0 : (float) $s;
                                        };
                                        $bal_qty = $stockLedgerParseAmount($opb[0] ?? 0);
                                        $avg_rate_value = $stockLedgerParseAmount($opb[1] ?? 0);
                                        $running_stock_value = (float) $bal_qty * $avg_rate_value;
                                        $avg_rate = @App\SysHelper::com_curr_format($avg_rate_value, 2, '.', ',');
                                    @endphp



                                    </tr>
                                    @foreach ($list as $value)
                                    {{-- {{ dd($value) }} --}}
                                        @php
                                            $serialText = trim((string) ($value->slno ?? ''));
                                            $serialItems = $serialText !== '' ? array_values(array_filter(array_map('trim', explode(',', $serialText)))) : [];
                                            $serialDisplay = implode(', ', $serialItems);
                                            $serialModalDisplay = implode(' | ', $serialItems);
                                            $serialModalId = 'exampleModalCenter' . $i . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', (string) ($value->doc_number ?? '')) . '_' . ($value->item_id ?? '0');
                                        @endphp
                                        <tr>
                                            <td>{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                            <td>
                                                @php
                                                    $docNumbers = array_filter(array_map('trim', explode(',', @$value->doc_number)));
                                                    $docLinks = [];
                                                    foreach ($docNumbers as $docNumber) {
                                                        $prefix = strtoupper(substr($docNumber, 0, 2));
                                                        switch ($prefix) {
                                                            case 'PO':
                                                                $url = url('get-url-purchase-order/' . $docNumber);
                                                                break;
                                                            case 'GR':
                                                                $url = url('get-url-purchase-grn/' . $docNumber);
                                                                break;
                                                            case 'PI':
                                                                $url = url('get-url-purchase-invoice/' . $docNumber);
                                                                break;
                                                            case 'PR':
                                                                $url = url('get-url-purchase-return/' . $docNumber);
                                                                break;
                                                            case 'SI':
                                                                $url = url('get-url-sales-invoice/' . $docNumber);
                                                                break;
                                                            case 'DL':
                                                            case 'DN':
                                                                $url = url('get-url-delivery-note/' . $docNumber);
                                                                break;
                                                            case 'SR':
                                                                $url = url('get-url-sales-return/' . $docNumber);
                                                                break;
                                                            case 'SH':
                                                                $url = url('get-url-stock-out/' . $docNumber);
                                                                break;
                                                            case 'EX':
                                                                $url = url('get-url-stock-in/' . $docNumber);
                                                                break;
                                                            default:
                                                                $url = null;
                                                        }

                                                        if ($url) {
                                                            $docLinks[] = '<a href="' . $url . '" target="_blank">' . $docNumber . '</a>';
                                                        } else {
                                                            $docLinks[] = $docNumber;
                                                        }
                                                    }
                                                @endphp

                                                {!! implode(', ', $docLinks) !!}
                                            </td>
                                            <td>
                                                @php
                                                    $refNumbers = array_filter(array_map('trim', explode(',', @$value->refno)));
                                                    $refLinks = [];
                                                    foreach ($refNumbers as $refNo) {
                                                        $prefix = strtoupper(substr($refNo, 0, 2));
                                                        switch ($prefix) {
                                                            case 'PO':
                                                                $url = url('get-url-purchase-order/' . $refNo);
                                                                break;
                                                            case 'GR':
                                                                $url = url('get-url-purchase-grn/' . $refNo);
                                                                break;
                                                            case 'PI':
                                                                $url = url('get-url-purchase-invoice/' . $refNo);
                                                                break;
                                                            case 'PR':
                                                                $url = url('get-url-purchase-return/' . $refNo);
                                                                break;
                                                            case 'SI':
                                                                $url = url('get-url-sales-invoice/' . $refNo);
                                                                break;
                                                            case 'DL':
                                                            case 'DN':
                                                                $url = url('get-url-delivery-note/' . $refNo);
                                                                break;
                                                            case 'SR':
                                                                $url = url('get-url-sales-return/' . $refNo);
                                                                break;
                                                            case 'SH':
                                                                $url = url('get-url-stock-out/' . $refNo);
                                                                break;
                                                            case 'EX':
                                                                $url = url('get-url-stock-in/' . $refNo);
                                                                break;
                                                            default:
                                                                $url = null;
                                                        }

                                                        if ($url) {
                                                            $refLinks[] = '<a href="' . $url . '" target="_blank">' . $refNo . '</a>';
                                                        } else {
                                                            $refLinks[] = $refNo;
                                                        }
                                                    }
                                                @endphp

                                                {!! implode(', ', $refLinks) !!}
                                            </td>


                                            <td>
                                                @if ($value->deal_id != 0)
                                                    @php
                                                        $deal_code = @App\SysHelper::get_code_from_dealid(
                                                            $value->deal_id
                                                        );
                                                    @endphp
                                                    <a href="{{ url('get-url-deal-track/' . $deal_code) }}"
                                                        target="_blank">{{ $deal_code }}</a>
                                                @else
                                                    Without
                                                @endif
                                            </td>
                                            <td>
                                                @if (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'SH')
                                                    Shortage Stock
                                                @elseif (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'EX')
                                                    Excess Stock
                                                @elseif (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'DI')
                                                    Demo In
                                                @elseif (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'DO')
                                                    Demo Out
                                                @elseif (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'RI')
                                                    {{-- RMA In --}}
                                                    {{ $value->description }}
                                                @elseif (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'RO')
                                                    {{-- RMA In --}}
                                                    {{ $value->description }}
                                                @else
                                                    {{ @$value->accountname->account_name }}
                                                @endif
                                            </td>
                                            <td>

                                               
                                                @if(@$value->grn_reference)
                                                    @php
                                                        $selectedCompanies = @$value->grn_reference
                                                                        ? explode(',', $value->grn_reference)
                                                                        : [];
                                                        $selectedCompanyNames = [];
                                                        foreach ($selectedCompanies as $sc) {
                                                            $sc_company = App\SysCustSuppl::find($sc);
                                                            if ($sc_company) {
                                                                $selectedCompanyNames[] = $sc_company->name;
                                                            }
                                                        }
                                                    @endphp
                                                    {{ implode(', ', $selectedCompanyNames) }}
                                                @endif
                                                {{ @$value->dln_reference }}
                                                {{ @$value->srt_reference }}
                                                @if(@$value->prt_reference)
                                                    @php
                                                        $prtCompanies = @$value->prt_reference
                                                                        ? explode(',', $value->prt_reference)
                                                                        : [];
                                                        $prtCompanyNames = [];
                                                        foreach ($prtCompanies as $prt) {
                                                            $prt_company = App\SysCustSuppl::find($prt);
                                                            if ($prt_company) {
                                                                $prtCompanyNames[] = $prt_company->name;
                                                            }
                                                        }
                                                    @endphp
                                                    {{ implode(', ', $prtCompanyNames) }}
                                                @endif

                                                

                                                @if (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'RI')
                                                    RMA In
                                                @endif

                                                @if (@$value->accountname->account_name == '' && substr($value->doc_number, 0, 2) == 'RO')
                                                    RMA Out
                                                @endif

                                            </td>
                                            <td class="text-center">{{ $value->qty_in }}</td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($value->price_in, 2, '.', ',') }}</td>

                                            @php
                                                // Moving-average stock value: GR/PI in at cost; DN/SI out at avg;
                                                // PR out at return line rate (parsed numeric); SR in at prior avg (unchanged).
                                                $previousAvgRateValue = $avg_rate_value;
                                                $docRaw = strtoupper(trim((string) ($value->doc_number ?? '')));
                                                $docFirst = trim(explode(',', $docRaw)[0]);
                                                $docPrefix2 = substr($docFirst, 0, 2);

                                                $lineQtyIn = $stockLedgerParseAmount($value->qty_in ?? 0);
                                                $lineQtyOut = $stockLedgerParseAmount($value->qty_out ?? 0);
                                                $linePriceIn = $stockLedgerParseAmount($value->price_in ?? 0);
                                                $linePriceOut = $stockLedgerParseAmount($value->price_out ?? 0);

                                                $hasPrtRef = isset($value->prt_reference)
                                                    && $value->prt_reference !== null
                                                    && trim((string) $value->prt_reference) !== '';

                                                // Use first doc segment only (comma lists); avoid matching SRT on a later segment.
                                                $isSalesReturn = ($docPrefix2 === 'SR') || str_contains($docFirst, 'SRT');
                                                $isPurchaseReturn = ($docPrefix2 === 'PR')
                                                    || ($hasPrtRef && $lineQtyOut > 0);

                                                if ($isSalesReturn) {
                                                    $running_stock_value += $lineQtyIn * $previousAvgRateValue;
                                                    $running_stock_value -= $lineQtyOut * $previousAvgRateValue;
                                                } elseif ($isPurchaseReturn) {
                                                    $returnCostOut = $linePriceOut > 0 ? $linePriceOut : $previousAvgRateValue;
                                                    $running_stock_value += $lineQtyIn * $linePriceIn;
                                                    $running_stock_value -= $lineQtyOut * $returnCostOut;
                                                } else {
                                                    $running_stock_value += $lineQtyIn * $linePriceIn;
                                                    $running_stock_value -= $lineQtyOut * $previousAvgRateValue;
                                                }

                                                $qty_in += $lineQtyIn;
                                                $bal_qty += $lineQtyIn;
                                                $bal_qty -= $lineQtyOut;

                                                if ($bal_qty > 0) {
                                                    $avg_rate_value = $running_stock_value / $bal_qty;
                                                    $displayAvgRateValue = $avg_rate_value;
                                                } elseif ($bal_qty == 0.0) {
                                                    $displayAvgRateValue = $previousAvgRateValue;
                                                    $avg_rate_value = 0;
                                                    $running_stock_value = 0;
                                                } else {
                                                    $displayAvgRateValue = $previousAvgRateValue;
                                                    $avg_rate_value = 0;
                                                    $running_stock_value = 0;
                                                }

                                                $avg_rate = @App\SysHelper::com_curr_format($displayAvgRateValue, 2, '.', ',');
                                            @endphp
                                            <td class="text-center">{{ $value->qty_out }}</td>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($value->price_out, 2, '.', ',') }}</td>
                                            <td class="text-center">{{ $bal_qty }}</td>
                                            <td class="text-end">{{ $avg_rate }}</td>
                                            <td class="text-end">
                                                @if (!empty($serialItems))
                                                    <a style="padding: 2px 2px; font-size: 12px; line-height: 1.2; white-space: normal;"
                                                        class="btn btn-sm btn-success text-white text-center rounded-0 d-block"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#{{ $serialModalId }}">View
                                                        SrlNo</a>
                                                @endif
                                                <div class="all_srl_no_{{ $i }}" style="display: none;">
                                                    {{ $serialDisplay }}</div>
                                            </td>

                                            <div class="modal fade" id="{{ $serialModalId }}"
                                                data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">
                                                                {{ $partnolist[$i] }} | {{ $value->doc_number }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body" style="line-height: 25px;">
                                                            {{ $serialModalDisplay }}
                                                        </div>
                                                        <div class="modal-footer"><button type="button"
                                                                class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button></div>
                                                    </div>
                                                </div>
                                            </div>

                                        </tr>
                                        @php
                                            $total_qty_in += $value->qty_in;
                                            $total_price_in += $value->price_in;
                                            $total_qty_out += $value->qty_out;
                                            $total_price_out += $value->price_out;
                                            $total_value += $value->price_in * $value->qty_in;
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-center">{{ $total_qty_in }}</th>
                                        <th class="text-end"></th>
                                        <th class="text-center">{{ $total_qty_out }}</th>
                                        <th class="text-end"></th>
                                        <th class="text-center">{{ $bal_qty }}</th>
                                        <th class="text-end">{{ $avg_rate }}</th>

                                           @php
                                                $reserved_qty = @App\SysHelper::get_reserved_qty(
                                                    $value->stockid,
                                                    $value->part_number
                                                );
                                               
                                            @endphp

                                            @if ($reserved_qty > 0)

                                                <th class="text-center" data-stock="{{ json_encode($value) }}" onclick="openReservedStockListModal(this)">
                                                    Reserve: 
                                                    <span class="text-danger fw-bold">{{ $reserved_qty }}</span>                                                
                                                </th>
                                            @else
                                                <th></th>

                                            @endif
                                    </tr>
                                </tfoot>
                            @else
                                <tbody>
                                    <tr class="bg-light">
                                        <th colspan="14" class="text-center text-danger"> No Data Found! </th>
                                    </tr>
                                   
                                </tbody>
                            @endif
                        </table>
                        <br>
                        <?php $i++; ?>
                    @endforeach
                @endif

            </div>
        </div>
    </aside>




<script>
    function openReservedStockListModal(el) {
    // Show loading indicator
    $('#loading_bg').show();

    // data-stock is ALREADY an object
    var value = $(el).data('stock');


    console.log('Opening reserved stock list for:', value);

    $('#reservedStockListModalLabel').text('Reserved Stock - ' + value.part_number);
    $('#reserved_stock_partno').val(value.stockid);
    $('#reserved_stock_part_number').val(value.part_number);

    // Load reserved stock data via AJAX
    loadReservedStockData(value.stockid, value.part_number);

    $('#reservedStockListModal').modal('show');
}
  function loadReservedStockData(stockId, partNumber) {
            $('#reservedStockTableBody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');

            $('#reservedStockListTitle').text('Reserved Stock - ' + partNumber);

            $.ajax({
                url: "{{ URL::to('get-reserved-stock-list') }}",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    stock_id: stockId,
                    part_number: partNumber
                },
                success: function(response) {
                    console.log("response", response);
                    if (response.success && response.data.length > 0) {
                        let tableBody = '';
                        response.data.forEach(function(item) {
                            tableBody += `
                                <tr>
                                    <td class="text-center" style="padding: 1px 3px;">${item.doc_number}</td>
                                    <td class="text-center" style="padding: 1px 3px;">${item.deal_id || '-'}</td>
                                    <td style="padding: 1px 3px;">${item.customer_name}</td>
                                    <td style="padding: 1px 3px;">${item.sales_person || 'N/A'}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserved_qty}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserve_date}</td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.created_by} ${item.created_at} </td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.updated_by} ${item.updated_at}</td>
                                    
                                </tr>
                            `;
                        });
                        $('#reservedStockTableBody').html(tableBody);
                    } else {
                        $('#reservedStockTableBody').html(
                            '<tr><td colspan="9" class="text-center text-muted">No reserved stock found</td></tr>'
                        );
                    }
                    $('#loading_bg').hide(); // Hide loader after data is loaded
                },
                error: function() {
                    $('#reservedStockTableBody').html(
                        '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                    $('#loading_bg').hide(); // Hide loader even on error
                }
            });
        }

</script>

   <div class="modal fade" id="reservedStockListModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="top:10%;left:10%;max-width:100rem">
            {{ Form::open(['class' => 'form-horizontal', 'files' => false, 'url' => 'store-reserve-qty', 'id' => 'reserve_stock_form', 'method' => 'POST']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="reservedStockListTitle"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body p-0">
                            <input type="hidden" id="reserved_stock_partno" value="">
                            <input type="hidden" id="reserved_stock_balance_qty" value="">
                            <input type="hidden" id="reserved_stock_part_number" value="">
                            <div class="table-responsive">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead>
                                        <tr>
                                            <th width="7%" class="text-center">Doc Number</th>
                                            <th width="7%" class="text-center">Deal Code</th>
                                            <th width="19%">Customer Name</th>
                                            <th width="15%">Sales Person</th>
                                            <th width="5%" class="text-center">Res. Qty</th>
                                            <th width="8%" class="text-center">Res. Date</th>
                                            <th width="15%" class="text-start">Created By</th>
                                            <th width="15%" class="text-start">Updated By</th>
                                      

                                        </tr>
                                    </thead>
                                    <tbody id="reservedStockTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="row" id="noDataRow" style="display: none;">
                                <div class="col-md-12 text-center">
                                    <p class="text-muted">No reserved stock found for this item.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
            {{ Form::close() }}

        </div>
    </div>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

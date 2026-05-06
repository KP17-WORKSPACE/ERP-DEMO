@extends('backEnd.newmasterpage')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush
@section('mainContent')
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    General Ledger
                </h4>
                <div class="purchase-order-content-header-right">
                    <button type="button" class="btn btn-light" id="exportGeneralLedgerReport" title="Export to Excel">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>
                {{-- <a class="btn btn-light" href="{{ url('chartofaccounts-import') }}"> Account Import</a> --}}

                    {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'generalledger', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="row">
                                    <div class="col-md-5 mb-20">
                                        <div class="input-effect">
                                            <label>@lang('Account')</label>
                                            
                                            <select class="form-control js-example-basic-single" name="account_id[]" id="account_id" multiple required>
                                                <option value="all" @if($ctrl_account_id=="all") selected @endif>View All</option>
                                                <option value="c" @if($ctrl_account_id=="c") selected @endif>View All Customer</option>
                                                <option value="s" @if($ctrl_account_id=="s") selected @endif>View All Supplier</option>
                                                <option value="a" @if($ctrl_account_id=="a") selected @endif>View All Account</option>
                                                @foreach ($accounts_list as $val)
                                                    <option value="{{ @$val->id }}"
                                                        @if($ctrl_account_id != "" && $ctrl_account_id != "all" && $ctrl_account_id != "c" && $ctrl_account_id != "s" && $ctrl_account_id != "a")
                                                        @foreach ($ctrl_account_id as $id)
                                                        @if(@$id == @$val->id) selected @endif
                                                        @endforeach
                                                        @endif
                                                        
                                                        >{{ @$val->account_code }} - {{ @$val->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    @php
                                                    $value = date('01/01/Y');
                                                    if($from_date != ""){ @$value = \Carbon\Carbon::parse($from_date)->format('d/m/Y'); }
                                                   
                                                    @endphp
                                                    <label>@lang('From Date')</label>
                                                    <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-20">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    @php
                                                    $value = date('d/m/Y');
                                                    if($to_date != "" ){ @$value =  \Carbon\Carbon::parse($to_date)->format('d/m/Y'); }
                                                    
                                                    @endphp
                                                    <label>@lang('To Date')</label>
                                                    <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{ @$value }}" autocomplete="off" onchange="set_filter()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Filter By</label>
                    <select class="form-control" name="filter_by" id="filter_by" onchange="set_filter2()">
                        <option value="" >-Select-</option>
                        <option value="this_month" @if($filter_by=="this_month") selected @endif>This Month</option>
                        <option value="today" @if($filter_by=="today") selected @endif>Today</option>
                        <option value="this_week" @if($filter_by=="this_week") selected @endif>This Week</option>
                        <option value="last_week" @if($filter_by=="last_week") selected @endif>Last Week</option>
                        <option value="last_month" @if($filter_by=="last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if($filter_by=="this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if($filter_by=="pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if($filter_by=="this_year") selected @endif @if($filter_by=="") selected @endif>This Year</option>
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
                    function set_filter2(){
                        if($('#filter_by').val()!="")
                        {
                            $('#from_date').val('');
                            $('#to_date').val('');
                        }
                    }
                </script>
                                    {{--  <div class="col-md-3 mb-20">
                                        <div class="input-effect">
                                            <label>@lang('Duration')</label>
                                            <select class="form-control" name="period" id="period">
                                                <option value="">@lang('')</option>
                                                <option value="1">@lang('All')</option>
                                                <option value="2">@lang('Today')</option>
                                                <option value="3">@lang('This Month')</option>
                                                <option value="4">@lang('This Quarter')</option>
                                                <option value="5">@lang('This Financial Year')</option>
                                                <option value="6">@lang('Yesterday')</option>
                                                <option value="7">@lang('Previous Month')</option>
                                                <option value="8">@lang('Previous Quarter')</option>
                                                <option value="9">@lang('Previous Financial Year')</option>
                                                <option value="10">@lang('Previous Financial Year to Date')</option>
                                                <option value="11">@lang('Month Start (to Date)')</option>
                                                <option value="12">@lang('Month End (from Date)')</option>
                                                <option value="13">@lang('Year Start (to Date)')</option>
                                                <option value="14">@lang('Year End (from Date)')</option>
                                            </select>
                                        </div>
                                    </div>  --}}
                                    <div class="col-1"><br />
                                        <button type="submit" class="btn btn-light" id="btnSubmit">
                                            <i class="ico icon-outline-magnifer"></i> Search
                                        </button>
                                    </div>
                                </div>

                                {{ Form::close() }}
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-body">
                    @if (isset($data_all))
            @for($j=0; $j<count($data_all); $j++)
            
            <?php $data = $data_all[$j]; ?>
            <?php $is_merge = App\SysHelper::ledger_merge_account($account_id_all[$j]); ?>
            <?php $is_merge_notvat = App\SysHelper::ledger_merge_account_notvat($account_id_all[$j]); ?>
            <?php $is_merge_vat = App\SysHelper::ledger_merge_account_vat($account_id_all[$j]); ?>
            
            <table class="table table-hover" id="long-list" style=": solid 1px #e3e6f0;">
                <thead>
                    <tr>
                        <th colspan="7" class=" text-left" width="500px" style="color: #000000; font-size: 13px; padding-bottom: 0px;">{{ $account_name[$j]["account_code"] }} - {{ $account_name[$j]["account_name"] }}
                            <hr style="height: 1px; margin: 3px 0px 0px 0px; background: #499258;"/>
                        </th>
                    </tr>
                  <tr>
                      <th class=" text-center" width="100px">Date</th>
                      <th class=" text-center" width="120px">Doc No</th>
                      <th class=" text-start" width="250px">Account</th>
                      <th class=" text-end" width="100px">Debit</th>
                      <th class=" text-end" width="100px">Credit</th>
                      <th class=" text-end" width="100px">Balance</th>
                      <th class=" text-start">Narration</th>
                  </tr>
                </thead>
                <tbody>
                  
                    <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; $deb=0; $cre=0; ?>
                  @if (isset($data))
                      @for ($i=0; $i < count($data); $i++)
                      <?php $ac_id = strtolower($data[$i]["account_name"]); ?>

                      @if(($ac_id == "purchase" || $ac_id == "sales" || $ac_id == "purchase return" || $ac_id == "sales return") && $is_merge == true)
                      
                      <?php
                        $date = date('d/m/Y', strtotime($data[$i]["transaction_date"]));
                        $trn_no = $data[$i]["transaction_no"];
                        $acc_name = $data[$i]["account_name"];
                        $rem = $data[$i]["remarks"];
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        
                        if($i+1 < count($data)){
                        if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat on sales') && $ac_id == "sales"){
                        $i++;
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        }
                        else if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat on sales') && $ac_id == "sales return"){
                        $i++;
                        $deb += $data[$i]["debit_amount"];
                        $cre += $data[$i]["credit_amount"];
                        }
                        else if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat on purchase')){
                       $i++;
                                if($ac_id != "purchase return"){
                                $deb += $data[$i]["debit_amount"];
                                $cre += $data[$i]["credit_amount"];
                                }
                        }
                        else if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat')){
                        $i++;
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        }
                        }
                      ?>

                      <tr>
                        <td class=" text-center">{{ $date }}</td>
                        <td class="text-center">
                            @if(substr($trn_no, 0, 2)=="JV")
                                <a href="{{url('get-url-journalvoucher/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="CR" || substr($trn_no, 0, 2)=="BR")
                                <a href="{{url('get-url-receipt/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="CP" || substr($trn_no, 0, 2)=="BP")
                                <a href="{{url('get-url-payment/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>                                
                            @elseif(substr($trn_no, 0, 2)=="PI")
                                <a href="{{url('get-url-purchase-invoice/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="PR")
                                <a href="{{url('get-url-purchase-return/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="SR")
                                <a href="{{url('get-url-sales-return/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(in_array(substr($trn_no, 0, 2),$sales_code))
                                <a href="{{url('get-url-sales-invoice/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @else
                                {{ $trn_no }}
                            @endif
                        </td>
                        <td class="">{{ $acc_name }}</td>
                        <td class=" text-end ">{{ @App\SysHelper::com_curr_format($deb,2,'.',',') }} @php $total_dr += $deb; @endphp </td>
                        <td class="text-end ">{{ @App\SysHelper::com_curr_format($cre,2,'.',',') }} @php $total_cr += $cre; @endphp </td>
                        <td class=" text-end ">
                        @if ($group == 1 || $group == 3)
                            @php $tot += ($deb); @endphp
                            @php $tot -= ($cre); @endphp
                        @endif
                        @if ($group == 2 || $group == 4 || $group == 5)
                            @php $tot += ($cre); @endphp
                            @php $tot -= ($deb); @endphp
                        @endif
                        @if ($group == 0)
                            @php $tot += ($cre); @endphp
                            @php $tot -= ($deb); @endphp
                        @endif
                            {{ @App\SysHelper::com_curr_format(($tot), 2, '.', ',') }}
                        </td>
                        <td class="">{{ $rem }}</td>
                      </tr>
                      
                      @else

                      <tr>
                        <td class="text-center">{{ date('d/m/Y', strtotime($data[$i]["transaction_date"])) }}</td>
                        <td class="text-center">
                            @if(substr($data[$i]["transaction_no"], 0, 2)=="JV")
                                <a href="{{url('get-url-journalvoucher/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="CR" || substr($data[$i]["transaction_no"], 0, 2)=="BR")
                                <a href="{{url('get-url-receipt/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="CP" || substr($data[$i]["transaction_no"], 0, 2)=="BP")
                                <a href="{{url('get-url-payment/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>                                
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="PI")
                                <a href="{{url('get-url-purchase-invoice/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="PR")
                                <a href="{{url('get-url-purchase-return/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="SR")
                                <a href="{{url('get-url-sales-return/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(in_array(substr($data[$i]["transaction_no"], 0, 2),$sales_code))
                                <a href="{{url('get-url-sales-invoice/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @else
                                {{ $data[$i]["transaction_no"] }}
                            @endif
                        </td>
                        <td class="">{{ $data[$i]["account_name"] }}</td>
                        <td class="text-end">{{ @App\SysHelper::com_curr_format($data[$i]["debit_amount"], 2, '.', ',') }} @php $total_dr += $data[$i]["debit_amount"]; @endphp </td>
                        <td class="text-end ">{{ @App\SysHelper::com_curr_format($data[$i]["credit_amount"], 2, '.', ',') }} @php $total_cr += $data[$i]["credit_amount"]; @endphp </td>
                        <td class="text-end ">
                        @if ($group == 1 || $group == 3)
                            @php $tot += ($data[$i]["debit_amount"]); @endphp
                            @php $tot -= ($data[$i]["credit_amount"]); @endphp
                        @endif
                        @if ($group == 2 || $group == 4 || $group == 5)
                            @php $tot += ($data[$i]["credit_amount"]); @endphp
                            @php $tot -= ($data[$i]["debit_amount"]); @endphp
                        @endif
                        @if ($group == 0)
                            @php $tot += ($data[$i]["credit_amount"]); @endphp
                            @php $tot -= ($data[$i]["debit_amount"]); @endphp
                        @endif
                            {{ @App\SysHelper::com_curr_format($tot, 2, '.', ',') }}
                        </td>
                        <td class="text-start">{{ $data[$i]["remarks"] }}</td>
                      </tr>
                      @endif

                      @endfor
                  @endif
                </tbody>
                <thead>
                  <tr>
                      <th class=""></th>
                      <th class=""></th>
                      <th class=""></th>
                      <th class=" text-center "></th>
                      <th class=" text-center "></th>
                      @if ($group == 1 || $group == 3)
                        <th class="text-end ">{{ @App\SysHelper::com_curr_format(($total_dr - $total_cr), 2, '.', ',') }}</th>
                      @endif
                      @if ($group == 2 || $group == 4 || $group == 5)
                        <th class="text-end ">{{ @App\SysHelper::com_curr_format(($total_cr - $total_dr), 2, '.', ',') }}</th>
                      @endif
                      <th class=""></th>
                  </tr>
                </thead>
              </table>
              @endfor
              @endif

              @if(count($data_all)==0)
              <table class="table table-hover" id="long-list" style=": solid 1px #e3e6f0;">
                <thead>
                  <tr>
                      <th class=" text-center" width="100px">Date</th>
                      <th class=" text-center" width="120px">Doc No</th>
                      <th class=" text-start" width="250px">Account</th>
                      <th class=" text-end" width="100px">Debit</th>
                      <th class=" text-end" width="100px">Credit</th>
                      <th class=" text-end" width="100px">Balance</th>
                      <th class=" text-start">Narration</th>
                  </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center"><br /><br /> No Data Found! <br /><br /></td>
                    </tr>
                </tbody>
              </table>

              @endif
                </div>
            </div>


        </div>
    </div>
</div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<script>
$(document).ready(function () {
    $('#exportGeneralLedgerReport').on('click', function (e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate = @json($from_date ?? '');
        var toDate = @json($to_date ?? '');

        function formatDMY(value) {
            if (!value) return '';
            var normalized = String(value).trim().replace(/\s+/g, '');
            var parts = normalized.split(/[\/\-\.]/);
            if (parts.length === 3) {
                if (parts[0].length === 4) {
                    return parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                return parts[0] + '/' + parts[1] + '/' + parts[2];
            }
            return value;
        }

        var $tables = $('.card-body table.table-hover');
        if ($tables.length === 0) {
            alert('No data available for export.');
            return;
        }

        var workbook = new ExcelJS.Workbook();
        var worksheet = workbook.addWorksheet('General Ledger');
        worksheet.columns = [
            { width: 14 },
            { width: 16 },
            { width: 30 },
            { width: 14 },
            { width: 14 },
            { width: 14 },
            { width: 40 }
        ];

        var rowNum = 0;
        function addMetaRow(text, fontSize, bold) {
            rowNum++;
            var r = worksheet.addRow([text]);
            worksheet.mergeCells(rowNum, 1, rowNum, 7);
            r.getCell(1).value = text;
            r.getCell(1).font = { bold: bold || false, size: fontSize || 11 };
            r.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        }

        addMetaRow(companyName, 14, true);
        addMetaRow('General Ledger', 12, true);
        if (fromDate || toDate) {
            var parts = [];
            if (fromDate) parts.push('From: ' + formatDMY(fromDate));
            if (toDate) parts.push('To: ' + formatDMY(toDate));
            addMetaRow(parts.join('   '), 10, false);
        }
        rowNum++; worksheet.addRow([]);

        $tables.each(function (tableIndex) {
            var $table = $(this);
            var $thead = $table.find('thead');
            var title = '';
            var headerRowIndex = 0;

            if ($thead.length > 0) {
                var $firstTr = $thead.first().find('tr').first();
                if ($firstTr.find('th[colspan]').length) {
                    title = $firstTr.find('th[colspan]').text().trim();
                    headerRowIndex = 1;
                }
            }

            if (title) {
                rowNum++;
                var titleRow = worksheet.addRow([title]);
                worksheet.mergeCells(rowNum, 1, rowNum, 7);
                titleRow.getCell(1).font = { bold: true, size: 11 };
                titleRow.getCell(1).alignment = { horizontal: 'left', vertical: 'middle' };
            }

            var $headerTr = $thead.find('tr').eq(headerRowIndex);
            var headers = [];
            $headerTr.find('th').each(function () {
                headers.push($(this).text().trim());
            });

            if (headers.length) {
                rowNum++;
                var headerRow = worksheet.addRow(headers);
                headerRow.height = 20;
                headerRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.font = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        left: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        right: { style: 'thin', color: { argb: 'FFB8C4D8' } }
                    };
                });
            }

            $table.find('tbody tr').each(function () {
                var cells = [];
                $(this).find('td').each(function () {
                    cells.push($(this).text().trim().replace(/\s+/g, ' '));
                });
                if (cells.length) {
                    rowNum++;
                    var dataRow = worksheet.addRow(cells);
                    dataRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.font = { size: 10 };
                        cell.alignment = { vertical: 'middle' };
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'FFE0E0E0' } },
                            left: { style: 'thin', color: { argb: 'FFE0E0E0' } },
                            bottom: { style: 'thin', color: { argb: 'FFE0E0E0' } },
                            right: { style: 'thin', color: { argb: 'FFE0E0E0' } }
                        };
                    });
                }
            });

            var $footer = $table.find('thead').last();
            if ($footer.length > 1) {
                var $footerRow = $footer.find('tr').last();
                var footCells = [];
                $footerRow.find('th').each(function () {
                    footCells.push($(this).text().trim().replace(/\s+/g, ' '));
                });
                if (footCells.length) {
                    rowNum++;
                    worksheet.addRow([]);
                    rowNum++;
                    var totalRow = worksheet.addRow(footCells);
                    totalRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.font = { bold: true, size: 10 };
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF0F4FA' } };
                        cell.alignment = { vertical: 'middle' };
                        cell.border = {
                            top: { style: 'thin', color: { argb: 'FFBDBDBD' } },
                            left: { style: 'thin', color: { argb: 'FFBDBDBD' } },
                            bottom: { style: 'thin', color: { argb: 'FFBDBDBD' } },
                            right: { style: 'thin', color: { argb: 'FFBDBDBD' } }
                        };
                    });
                }
            }

            rowNum++;
            worksheet.addRow([]);
        });

        workbook.xlsx.writeBuffer().then(function (buffer) {
            var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            function pad(n) { return n < 10 ? '0' + n : '' + n; }
            var d = new Date();
            saveAs(blob, 'general_ledger_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx');
        });
    });
});
</script>
@endsection
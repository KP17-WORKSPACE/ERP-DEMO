@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <style>
        .ageing-grn-popover { max-width: 320px; text-align: left; }
        .ageing-grn-popover .popover-body { padding: 0.5rem 0.65rem; }
        .ageing-grn-tip {
            cursor: help;
            border-bottom: 1px dotted #adb5bd;
        }
        .sub_table .recv-sched-col { overflow: visible; }
        .recv-sched-col { font-size: 11px; line-height: 1.35; overflow: visible; }
        .recv-sched-list { display: inline; word-break: break-word; }
        .recv-sched-item {
            cursor: help;
            border-bottom: 1px dotted #adb5bd;
            white-space: nowrap;
        }
        .recv-sched-sep { color: #868e96; }
        .recv-sched-od-late { color: #c92a2a; font-weight: 600; }
        .recv-sched-od-soon { color: #2b8a3e; font-weight: 600; }
        .recv-sched-od-today { color: #495057; font-weight: 600; }
    </style>

     <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script>
            // Export visible Payable Outstanding rows to Excel (ExcelJS styled)
            $(document).ready(function () {
                $('#exportExcelPayable').on('click', function () {
                    var hideBasicCols = @json(!empty($ctrl_basic_search));
                    var companyName   = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                    var asOfDate      = @json(@App\SysHelper::normalizeToDmy($till_date ?? ''));

                    // Build header label array
                    var headerLabels = [
                        'Account Code', 'Supplier', 'Deal ID',
                        'Inv Date', 'Inv No', 'LPO No', 'Bill No', 'Bill Date',
                        'Amount', 'Adjustments', 'Balance', 'Total Balance',
                        'Due Date', 'Over Due Days', 'Due Amount'
                    ];
                    if (!hideBasicCols) {
                        headerLabels = headerLabels.concat(['0-30', '31-60', '61-90', '>90', 'Finance Cost']);
                    }
                    headerLabels.push('Payment Terms');
                    if (!hideBasicCols) {
                        headerLabels = headerLabels.concat(['Receipt Date', 'Doc Number']);
                    }

                    var N = headerLabels.length;

                    // Collect data rows
                    var dataRows = [];
                    $('.main_table:visible').each(function () {
                        var mainId      = $(this).attr('id');
                        if (!mainId) return;
                        var aid         = mainId.replace('account_table', '');
                        var accountCode = $(this).data('acccode') || '';
                        var supplierName = $(this).find('th a').first().text().trim();
                        var $subRows    = $('#collapse' + aid).find('.sub_table tbody tr');

                        $subRows.each(function () {
                            if ($(this).find('td[colspan]').length > 0) return;
                            var cells = $(this).find('td').filter(function () {
                                return $(this).css('display') !== 'none';
                            }).map(function () {
                                return $(this).text().trim().replace(/\s+/g, ' ');
                            }).get();
                            if (cells.length === 0) return;
                            dataRows.push([accountCode, supplierName].concat(cells));
                        });
                    });

                    if (dataRows.length === 0) {
                        alert('No data available for export');
                        return;
                    }

                    var workbook  = new ExcelJS.Workbook();
                    var worksheet = workbook.addWorksheet('Payable Outstanding');

                    var wsCols = [];
                    for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 18 }); }
                    worksheet.columns = wsCols;

                    // Row 1 — Company name
                    var r1 = worksheet.addRow([]);
                    r1.getCell(1).value     = companyName;
                    r1.getCell(1).font      = { bold: true, size: 14 };
                    r1.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                    r1.height = 26;
                    worksheet.mergeCells(1, 1, 1, N);

                    // Row 2 — Page title
                    var r2 = worksheet.addRow([]);
                    r2.getCell(1).value     = 'Payable Outstanding';
                    r2.getCell(1).font      = { bold: true, size: 12 };
                    r2.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                    r2.height = 20;
                    worksheet.mergeCells(2, 1, 2, N);

                    // Row 3 — As of date
                    if (asOfDate) {
                        var r3 = worksheet.addRow([]);
                        r3.getCell(1).value     = 'As of Date: ' + asOfDate;
                        r3.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                        r3.height = 16;
                        worksheet.mergeCells(3, 1, 3, N);
                    }

                    // Blank separator
                    worksheet.addRow([]);

                    // Header row
                    var headerRow = worksheet.addRow(headerLabels);
                    headerRow.height = 20;
                    headerRow.eachCell({ includeEmpty: true }, function (cell) {
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
                    dataRows.forEach(function (rowData) {
                        var dr = worksheet.addRow(rowData);
                        dr.eachCell({ includeEmpty: true }, function (cell) {
                            cell.border = {
                                top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                                left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                                bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                                right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                            };
                        });
                    });

                    workbook.xlsx.writeBuffer().then(function (buffer) {
                        var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        function pad(n) { return n < 10 ? '0' + n : n; }
                        var d = new Date();
                        var filename = 'payable_outstanding_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                        saveAs(blob, filename);
                    });
                });
            });
        </script>
        <script>
            function row_det_fun(id,docs){
                $('#row_det_table_'+id+' tbody').empty();
                var doc = docs.split(',');
                for (var i = 0; i < doc.length; i++) {
                    doc[i] = doc[i].trim();
                    var inv_e_doc_date = $('#inv_e_doc_date_'+doc[i]).val();
                    var inv_e_doc_no = $('#inv_e_doc_no_'+doc[i]).val();
                    var inv_e_lpo_no = $('#inv_e_lpo_no_'+doc[i]).val();
                    var inv_e_deal_code = $('#inv_e_deal_code_'+doc[i]).val();
                    var inv_e_amount = $('#inv_e_amount_'+doc[i]).val();
                    var inv_e_adjustment = $('#inv_e_adjustment_'+doc[i]).val();

                    var htm = "<tr>\
                        <td class='border'>"+inv_e_doc_date+"</td>\
                        <td class='border'>"+inv_e_doc_no+"</td>\
                        <td class='border'>"+inv_e_lpo_no+"</td>\
                        <td class='border'>"+inv_e_deal_code+"</td>\
                        <td class='text-right'>"+inv_e_amount+"</td>\
                        <td class='text-right'>"+inv_e_adjustment+"</td>\
                        </tr>"
                        $('#row_det_table_'+id+' tbody').append(htm);

                }
                var row = $('#row_det_'+id);
                if (row.is(':visible')) {
                    row.hide();
                } else {
                    row.show();
                }
            }
        </script>

        <style>
    .form-group .form-control {
    min-height: 0px;
    font-size: 13px;
    font-weight: 500;
}

.form-control {
    border: 1px solid var(--color-border-1);
    border-radius: 0px;
    padding: 2px 5px;
}
 .btn-light {
    color: var(--color-btn-light);
    border: 1px solid var(--color-btn-light-border);
    background-color: var(--color-btn-light-bg);
}

 .btn {
    display: flex
;
    align-items: center;
    font-size: 12px;
    padding: 3px 10px;
    gap: 5px;
    border-radius: 8px;
    min-height: 25px;
}
.sub_table {
    width: 100% !important;
    table-layout: fixed;
    font-size: 11px;
}
.sub_table th, .sub_table td {
    padding: 3px 4px !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}


    </style>
   

        
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Payable Outstanding
                </h4>
                <div class="purchase-order-content-header-right">
                    <div class="me-2" style="min-width:250px;">
                        <input id="payableOutstandingSearch" class="form-control" type="text" placeholder="Search...">
                    </div>
                    <a class="btn btn-light text-dark" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a>

                           
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                          
                            <li>
                                <a
                                    href="{{ url('supplier-ageing-report') }}"class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Supplier Ageing Report</a>
                            </li>

                             <li>
                                <a
                                    type="button" id="exportExcelPayable" class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-export text-success title-15 me-2"></i> Export</a>
                            </li>


                        </ul>
                    </div>
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>
            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payables-outstanding', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                            <div class="row">
                                                <div class="col-1-5 mb-20">
                                                       {{-- prepare a normalized array of selected account ids --}}
                                    @php
                                        $selectedAccounts = [];
                                        if (is_array($account_id)) {
                                            $selectedAccounts = $account_id;
                                        } elseif ($account_id instanceof \Illuminate\Support\Collection) {
                                            $selectedAccounts = $account_id->toArray();
                                        } elseif ($account_id && $account_id != 0) {
                                            $selectedAccounts = [$account_id];
                                        }

                                        if($is_view_all_supp){
                                           $selectedAccounts = ['view_all_supp'];
                                        
                                        }


                                    @endphp
                                               @php
    $filters = request()->except(['account_id']); // exclude current field if needed
    $isFirstLoad = count(array_filter($filters)) == 0;
@endphp

<div class="input-effect">
    <label>@lang('Account')</label>
    <select class="form-control js-example-basic-single" 
            name="account_id[]" 
            id="account_id" 
            multiple>

        <option value="view_all_supp"
            @if(($isFirstLoad && empty($selectedAccounts)) || in_array('view_all_supp', $selectedAccounts))
                selected
            @endif>
            @lang('View All Suppliers')
        </option>

        @foreach ($accounts_select as $val)
            <option value="{{ $val->id }}"
                @if(in_array($val->id, $selectedAccounts))
                    selected
                @endif>
                {{ $val->account_name }}
                @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                    ({{ $val->account_code }})
                @endif
            </option>
        @endforeach
    </select>
</div>
                                                </div>
                                                <div class="col-md-1 mb-20">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="form-check-label">
                                                                <label>@lang('As of Date')</label>
                                                                <input class="form-control date-picker" id="till_date" type="text" name="till_date" value="{{ @App\SysHelper::normalizeToDmy($till_date) }}" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                       
                            
                                                <!-- <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Doc No</label>
                                                    <input class="form-control" id="transaction_no" type="text" autocomplete="off" name="transaction_no" >
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Deal ID</label>
                                                    <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id" >
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Amount</label>
                                                    <input class="form-control" name="amount" id="amount" />
                                                </div> -->
        
                                                
                                                <div class="col-1-5 mb-2">
                                                    <label for="" class="form-check-label">Sales Person</label>
                                                    <select class="form-control js-example-basic-single" name="sales_person[]" id="sales_person" multiple>
                                                        <option value="">-Select-</option>
                                                        @foreach ($sales_person_list as $sp)
                                                            <option value="{{ $sp->user_id }}"  @if(in_array($sp->user_id, (array) @$ctrl_sales_person)) selected @endif> {{ $sp->full_name }} </option>                                                    
                                                        @endforeach
                                                    </select>
        
                                                </div>
        
                               
                           
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Over Due</label>
                                                    <select class="form-control js-example-basic-single" name="overdue" id="overdue">
                                                        <option value="" @if(@$ctrl_overdue == "") selected @endif>-Select-</option>
                                                        <option value="0" @if(@$ctrl_overdue == "0") selected @endif> >0 </option>
                                                        <option value="30" @if(@$ctrl_overdue == "30") selected @endif> 0-30 </option>
                                                        <option value="60" @if(@$ctrl_overdue == "60") selected @endif> 31-60</option>
                                                        <option value="90" @if(@$ctrl_overdue == "90") selected @endif> 61-90 </option>
                                                        <option value="90+" @if(@$ctrl_overdue == "90+") selected @endif> >90 </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Ageing</label>
                                                    <select class="form-control js-example-basic-single" name="ageing" id="ageing">
                                                        <option value=""  @if(@$ctrl_ageing == "") selected @endif>-Select-</option>
                                                        <option value="0" @if(@$ctrl_ageing == "0") selected @endif>0-30</option>
                                                        <option value="30" @if(@$ctrl_ageing == "30") selected @endif>31-60</option>
                                                        <option value="60" @if(@$ctrl_ageing == "60") selected @endif>61-90</option>
                                                        <option value="90+" @if(@$ctrl_ageing == "90+") selected @endif > >90 </option>
                                                    </select>
                                                </div>
                                                 <div class="col-1  mb-2">
                                            <label for="" class="form-check-label">List Option</label>
                                            <select class="form-control js-example-basic-single" name="list_option" id="list_option">
                                                <option value="" @if(@$ctrl_list_option == "") selected @endif>Normal</option>
                                                <option value="unadjusted_balance" @if(@$ctrl_list_option == 'unadjusted_balance') selected @endif>Unadjusted Bal</option>
                                                <option value="unmatched_balance" @if(@$ctrl_list_option == 'unmatched_balance') selected @endif>Unmatched Bal</option>
                                                <option value="overdue_balance" @if(@$ctrl_list_option == 'overdue_balance') selected @endif>Overdue Bal</option>
                                                 <option value="pdc" @if(@$ctrl_list_option == 'pdc') selected @endif>PDC</option>
                                                <option value="consolidated" @if(@$ctrl_list_option == 'consolidated') selected @endif>Consolidated</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-1  mb-2">
                                           
                                            <label for="" class="form-check-label">Internal/External</label>
                                            <select class="form-control js-example-basic-single" name="list_in_ex" id="list_in_ex">
                                                <option value="" @if(@$ctrl_intext == "") selected @endif>-Select-</option>
                                                <option value="1" @if(@$ctrl_intext == "1") selected @endif>Internal</option>
                                                <option value="0" @if(@$ctrl_intext == "0") selected @endif>External</option>
                                            </select>
                                        </div>
                                         <div class="col-1  mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                   <label for="" class="form-check-label">Basic/Detail</label>
                                            <select class="form-control js-example-basic-single" name="list_in_basic" id="list_in_basic">
                                                <option value="" @if(@$ctrl_basic_search == "") selected @endif>-Select-</option>
                                                <option value="1" @if(@$ctrl_basic_search == "1") selected @endif>Basic</option>
                                                <option value="0" @if(@$ctrl_basic_search == "0") selected @endif>Details</option>
                                            </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- follow-up date range filter -->
                                        <div class="col-1  mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="form-check-label">
                                                        <label>@lang('Follow-up From')</label>
                                                        <input class="form-control date-picker" type="text" name="followup_from" value="{{ @$ctrl_followup_from }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1  mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="form-check-label">
                                                        <label>@lang('Follow-up To')</label>
                                                        <input class="form-control date-picker" type="text" name="followup_to" value="{{ @$ctrl_followup_to }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                <div class="col-md-1 mt-4" >
                                                    <div class="input-effect">
                                                        <button class="btn btn-light" type="submit">
                                                            <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Search
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
            
                                            {{ Form::close() }}
                                    </div>
                                </div>
                            </div>

                             <div class="card mb-3">
                                <div class="card-body">
                                      




            <div class="card mb-3 card-min-height">
                <div class="card-body">
                    <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                        <div class="row">
                            <div class="col-12 mb-2" >
            <input type="hidden" id="base_url" value="{{ url('/') }}" />
                                

            
            <script>
    function download_outstanding(id){
        var date = $('#till_date').val();                                                                        
        var url = $("#base_url").val()+"/payables-outstanding-download/"+id+"/"+date;
        window.location.href = url;
    }
</script>


<script>
        $(document).ready(function() {

            function renderComments(dataResult) {
                $('#mydiv').empty();
                dataResult.forEach(function(re) {
                    var isDeleted = re.is_deleted == 1;
                    var textClass = isDeleted ? 'text-decoration-line-through text-muted' : '';
                    var commentHtml = re.comment ? re.comment.split("\n").map(function(l){ return l; }).join('<br>') : '';
                    var attachmentHtml = '';
                    if (re.file) {
                        attachmentHtml = '<a href="{{ asset("public/uploads/outstand_comments_doc/") }}/' + re.file + '" target="_blank" class="btn btn-sm btn-light me-1" style="min-height:17px">' +
                            '<i class="ico icon-bold-paperclip" style="font-size:11px"></i></a>';
                    }
                    var deleteHtml = '';
                    if (!isDeleted && re.created_by == {{ Auth::user()->id }}) {
                        deleteHtml = '<button type="button" class="btn btn-sm btn-light btn-delete-outstand-comment" data-id="' + re.id + '" style="min-height:17px"><i class="ico icon-outline-trash-bin-minimalistic" style="font-size:11px"></i></button>';
                    }
                    var followupHtml = '';
                    if (re.followup_date) {
                        // format to dd/mm/yyyy
                        var fd = new Date(re.followup_date);
                        var formatted = fd.toLocaleDateString('en-GB');
                        followupHtml = ' <span class="ms-1 text-primary" style="font-size:10px"><i class="ico icon-bold-clock me-1"></i>Follow-up: ' + formatted + '</span>';
                    }
                    var deletedHtml = '';
                    if (isDeleted) {
                        deletedHtml = ' <span class="text-danger" style="font-size:10px">• Deleted</span>';
                    }
                    var card = '<div class="card rounded-3 mb-2"><div class="card-body py-1 px-2"><div class="d-flex justify-content-between mb-0">' +
                        '<p class="mb-0 text-break fw-semibold ' + textClass + '" style="font-size:11px">' + commentHtml + '</p>' +
                        '<div class="d-flex align-items-baseline gap-1">' + attachmentHtml + deleteHtml + '</div>' +
                        '</div>' +
                        '<div class="text-end text-muted" style="font-size:10px">' +
                        '<span>' + (re.username || '') + '</span> <span>•</span> <span><i class="ico icon-bold-clock me-1"></i>' +
                        new Date(re.created_at).toLocaleDateString('en-GB') + ' ' +
                        new Date(re.created_at).toLocaleTimeString('en-US', {hour:'2-digit',minute:'2-digit'}) +
                        '</span>' + followupHtml + deletedHtml +
                        '</div></div></div>';
                    $('#mydiv').append(card);
                });
                if (dataResult.length === 0) {
                    $('#mydiv').html('<p class="text-muted text-center" style="font-size:11px">No remarks yet.</p>');
                }
            }

            // delete handler
            $(document).on('click', '.btn-delete-outstand-comment', function() {
                if (!confirm('Are you sure you want to delete this remark?')) return;
                var commentId = $(this).data('id');
                var accountId = $('#iddetail').val();
                $.ajax({
                    url: "outstanding_comment_delete_payable",
                    type: "post",
                    data: { _token: '{{ csrf_token() }}', comment_id: commentId },
                    success: function(dataResult) {
                        view(accountId);
                    }
                });
            });

           id=''

              $('.btn-badge').click(function() {
                var accountId = $(this).data('id');
                var custInfo = $(this).data('cust') || '';
                id = accountId; // keep global if other code relies on it
                $('#iddetail').val(accountId);
                $('#customerInfoDisplay').html(custInfo);
                $('#mydiv').html('<p class="text-muted text-center" style="font-size:11px">Loading...</p>');
                view(accountId);
            });

            

            $('.btn-badge').click(function() {
             //   alert('with btn')
                id = $(this).data('id')
             //   alert(id)
                $('#iddetail').val(id)
               // alert(id)
                comment=$('#comment').val()
              //  alert(comment)

              var action = "outstanding_comment_payable";  
              $.ajax({
                    url: action,
                    type: "post",
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_deal: id,
                    },
                    success: function(dataResult) {
                        // ensure we have an array before iterating
                        if (typeof dataResult === 'string') {
                            try { dataResult = JSON.parse(dataResult); } catch(e) {}
                        }
                        renderComments(dataResult);
                    }
                });


 
               
            });


            $('#btnSubmit1').click(function() {
                var action = "outstanding_comment_save_payable";
                var accountId = $('#iddetail').val();
                var fd = new FormData();
                fd.append('_token', '{{ csrf_token() }}');
                fd.append('id_deal', accountId);
                fd.append('comment', $('#comment').val());
                fd.append('remark_date', $('#remark_date').val());
                var fileInput = document.getElementById('remark_file');
                if (fileInput && fileInput.files.length > 0) {
                    fd.append('remark_file', fileInput.files[0]);
                }

                $.ajax({
                    url: action,
                    type: "post",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(dataResult) {
                        // expect object {status:'SUCCESS'|'ERROR',...}
                        if (dataResult.status === 'SUCCESS') {
                            $('#message').append("<div class='alert alert-success'><i class='fa fa-check'></i> Note successfully added!</div>").delay(3000).fadeOut(300);
                            view(accountId);
                            // close the add-remark inputs modal after successful save
                            $('#ModalTrackCommentInputs').modal('hide');
                        } else {
                            alert('Could not save remark');
                        }
                    }
                });
            });

            function view(id){
                var action = "outstanding_comment_payable";  
              $.ajax({
                    url: action,
                    type: "post",
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',

                        id_deal: id,
                     //   comment: comment,

                    },

                    success: function(dataResult) {
                        if (typeof dataResult === 'string') {
                            try { dataResult = JSON.parse(dataResult); } catch(e) {}
                        }
                        renderComments(dataResult); return;
                    
//                      var vv="<table border='1'><tr><td>name</td></tr>";
//                      dataResult.forEach((re) => {
//                         vv+="<tr><td>"+re.comment+"</td></tr>"
                        

// });
//                     vv+="</table>";
vv="<div>";
dataResult.forEach((re) => {
    vv+="<div class='notes py-2 px-3 p-0 mt-3'><p class='mb-0 p-0 m-0'>"+re.comment+"</p></div>";
     vv+="<p class='text-muted text-end p-0 m-0'>"+re.username+" Created on {{date('d/m/Y H:i:s')}}</p>";
});
vv+="</div>";
//                         vv+="<tr><td>"+re.comment+"</td></tr>"
                        

// });
                    document.getElementById("mydiv").innerHTML=vv;
                    $('#comment').val('');
                    }
                });
            } 
        });
    </script>       


                                    

                                
                                
                                <div class="accordion" id="accordionExample">
                  @if(count($data_all)>0)
                  <?php $no=1; $all_total=0;   $k=0;?>
                  @foreach($data_all as $data)
                  
                  <?php
                  if(count($data)>0){
                        $aname = $accounts->where('id',$data[0]->account_id)->first();
                        $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code);
                        
                        $data_adjestment = @App\SysPurchaseReturnAdjestment::select('piv_no',DB::raw('sum(paid_amount) as paid_amount'))->wherein('piv_no',$data->pluck("transaction_no"))->groupby('piv_no')->get();
        
                        $data_payment = DB::table('sys_payment as p')->select('pa.bi_doc_no','p.doc_number','pa.bi_amount','p.payment_through','p.payment_date','p.cheque_number','p.cheque_bank_name')
                        ->join('sys_payment_adjustments as pa','pa.bi_doc_number','p.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('p.status',1)->get();
                        
                        $data_payment2 = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date')
                        ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();

                        $data_payment3 = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no','j.doc_number','ra.bi_amount','j.doc_date')
                        ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','j.doc_number')->where('ra.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();

                        $data_return = DB::table('sys_purchase_return as r')->select('ra.piv_no','r.doc_number','ra.paid_amount','r.doc_date')
                        ->join('sys_purchase_return_adjestment as ra','ra.pri_no','r.doc_number')->where('r.vendors',$data[0]->account_id)->wherein('pri_no',$data->pluck("transaction_no"))->where('r.status',1)->get();
                  ?>
                  @php
                      $hideBasicColumns = !empty($ctrl_basic_search);
                      $max_installments = $max_installments ?? 1;
                      $payable_finance_rate = $payable_finance_rate ?? 0;
                      $purchase_invoice_map = $purchase_invoice_map ?? collect([]);
                      $sales_invoice_map = $sales_invoice_map ?? collect([]);
                      $payment_terms_map = $payment_terms_map ?? collect([]);
                      $opbinvoice_map = $opbinvoice_map ?? collect([]);
                      $scheduleColCount = 3;
                      $asOfDateCalc = App\SysHelper::normalizeToYmd($till_date) ?: $till_date;
                  @endphp

                  @if($ctrl_list_option == 'pdc')
                  <?php $pdc_1 = !empty($list_of_unadjusted_pdc) ? $list_of_unadjusted_pdc->where('account_id',$aname->id) : []; ?>
                  <?php $pdc_2 = !empty($list_of_adjusted_pdc) ? $list_of_adjusted_pdc->where('account_id',$aname->id) : []; ?>

                  
                  @if(count($pdc_1)>0 || count($pdc_2)>0)
                   
                  @else
                     @continue
                  @endif
                  @endif

                <script>
                    function set_total(id,at){
                        $('#sum_'+id).text(at.toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                        $('#collapse'+id).css('display','');
                        $('#account_table'+id).css('display','');
                    }

                    function formatAmountToNumber(input) {
    if (!input) return 0;

    let inputStr = String(input).replace(/,/g, '').trim();
    let number = parseFloat(inputStr);

    return isNaN(number) ? 0 : number;
}


function set_total_addmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal + additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function set_total_lessmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal - additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function check_total(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    if(currentTotal != additionalAmount){
        $('#sum_' + id).css('color', 'red');
    }

}
                </script>



                <table id="account_table{{ $aname->id }}" class="table main_table" data-acccode="{{ $aname->account_code }}" style="border: solid 1px #e3e6f0; margin-bottom: -1px !important; display: none;">
                    <thead>
                      <tr>


                                   <th class="text-center">

<div style="display:flex; justify-content:space-between; align-items:center; width:100%;">

    <!-- LEFT SIDE -->
    <div>
        <a type="button"
           data-bs-toggle="collapse"
           data-bs-target="#collapse{{ $aname->id }}"
           aria-expanded="false"
           aria-controls="collapse{{ $aname->id }}"
        
           >

          <b style="font-size:13px">  {{ $aname->account_name }} </b>
            
        </a>
    </div>

    <!-- RIGHT SIDE BUTTONS -->
    <div style="display:flex; align-items:center; gap:8px; white-space:nowrap;">

    {{ Form::open(['class'=>'m-0','url'=>'generalledger','target'=>'_blank','method'=>'POST']) }}
            <input type="hidden" name="account_id[]" value="{{ $aname->id }}" />
            <input type="hidden" name="from_date" value="{{ date('Y-01-01') }}" />
            <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}" /> 

            <button type="submit" class="p-0 border-0">
                <i class="ico icon-outline-notebook text-success" style="font-size:16px" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="View General Ledger for {{ $aname->account_name }}"
                            data-bs-placement="bottom"></i> 
</button>
        {{ Form::close() }}

        <a class="btn-badge p-0"
           data-id="{{@$aname->id}}"
           data-cust="{{ @$cust_det }}"
           href="#"
           id="crmajax"
           data-bs-toggle="modal"
           data-bs-target="#ModalTrackComment"
           
           data-bs-popover="popover"
                            data-bs-trigger="hover" 
                            data-bs-delay="500"
                            data-bs-content="View/Add Comments for {{ $aname->account_name }}"
                            data-bs-placement="bottom">
            <i class="ico icon-outline-chat-round-line text-primary" style="font-size:16px"></i>
        </a>

        <a href="#"
           title="Download"
           class="p-0"
           data-bs-popover="popover"
                            data-bs-trigger="hover" 
                            data-bs-delay="500"
                            data-bs-content="Download Outstanding for {{ $aname->account_name }}"
                            data-bs-placement="bottom"
           onclick="download_outstanding({{ $aname->id }})">
            <i class="ico icon-outline-download-square text-danger" style="font-size:16px"></i>
        </a>

        

    </div>

</div>

</th>


                        
                          
                          <th class="text-end" width="100px"><label class="main_sum" id="sum_{{ $aname->id }}"></label></th>
                      </tr>
                    </thead>
                </table>

                <div id="collapse{{ $aname->id }}" class="collapse" data-bs-parent="#accordionExample">  {{-- display: none; --}}
                <table class="table sub_table table-hover" style="border: solid 1px #e3e6f0; width:100%; table-layout:fixed;" id="long-list">
                    <thead>


                    <!-- <tr>
                        <td colspan="10">&nbsp;</td>
                        <td colspan="2">
                        <a data-id="{{@$aname->id}}"      id="crmajax" class="btn-badge btn btn-info  py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalTrackComment" title="Click to Fullfill">
                                    Comments</a>
                                   

                        </td>

                        </tr> -->
                      <tr>
                        <th class="text-center" style="width:5%">Deal ID</th>
                          <th class="text-center" style="width:6%">Inv Date</th>
                          <th class="text-center" style="width:6%">Inv No</th>
                          <th class="text-center" style="width:7%">LPO No</th>                          
                          <th class="text-center" style="width:5%">Bill No</th>
                          <th class="text-center" style="width:6%">Bill Date</th>
                          <th class="text-center" style="width:6%">Amount</th>
                          <th class="text-center" style="width:7%">Adjustments</th>
                          <th class="text-center" style="width:6%">Balance</th>
                          <th class="text-center" style="width:6%">Total Balance</th>
                          <th class="text-start" style="width:7%">Due Date</th>
                          <th class="text-start" style="width:6%">Over Due Days</th>
                          <th class="text-start" style="width:7%">Due Amount</th>
                        @if(!$hideBasicColumns)
                        <th class="text-center" style="width:6%">0-30</th>
                        <th class="text-center" style="width:6%">31-60</th>
                        <th class="text-center" style="width:6%">61-90</th>
                        <th class="text-center" style="width:6%">>90</th>
                        <th class="text-end" style="width:6%">Finance Cost</th>
                        @endif
                           <th class="text-start" style="width:11%">Payment Terms</th>
                        @if(!$hideBasicColumns)
                        <th class="text-center hidecol_{{ $aname->id }}" style="width:10%">Receipt Date</th>
                          <th class="text-center hidecol_{{ $aname->id }}" style="width:10%">Doc Number</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>

                    <?php
                         $ats=Array();   
                         $k=0;
                          $row_count_1 = 0;  // count rows for this account
                         foreach ($data as $dt){
                            if (($dt->transaction_type ?? '') == 'unadjusted_placeholder') {
                                continue;
                            }
                            if (isset($dt->transaction_type) && $dt->transaction_type == 'opbinvoice') {
                                $opbFilterDet = $opbinvoice_map->get($dt->transaction_no);
                                $DueData = @App\SysHelper::get_due_date_invoice_opbinvoice(
                                    $dt->transaction_no,
                                    $opbFilterDet->due_date ?? '',
                                    $opbFilterDet->payment_terms ?? '',
                                    $asOfDateCalc
                                );
                            } elseif (Illuminate\Support\Str::contains($dt->transaction_no, ['SI'])) {
                                $DueData = App\SysHelper::get_due_date_sales_invoice($dt->transaction_no, $dt->transaction_date, $asOfDateCalc);
                            } else {
                                $DueData = App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no, $dt->transaction_date, $asOfDateCalc);
                            }
                       
                            // if( $DueData[1] < $overdue && $DueData[1] < $ageing ){
                            //     $ats[$k]=$dt;
                            //     $k++;
                            // }

                            // if($doc_date != null){  
                            //     if($dt->transaction_date== Date($doc_date))
                            //     $ats[$k]=$dt;
                            // }    

                           

                            if($overdue != 999999){    
                                if(  $DueData[1] >= $overdue ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                            }

                            if($ageing != 99999){
                                if($ageing <0 && $DueData[1] <0 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=0 && $ageing <31 && $DueData[1] >=0 && $DueData[1] <=30 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=30 && $ageing <61 &&  $DueData[1] >=31 && $DueData[1] <=60 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=60 && $ageing <=90 &&  $DueData[1] >=61 && $DueData[1] <=90  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing > 90 && $DueData[1] >90 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                            }  

                         }
                         //if($overdue != 999999 ||  $ageing != 99999 || $doc_date != null)
                         //   $data=$ats;
                        
                    ?>
                        @php $adjustments = 0;
                        $b=0;
                        $grand_credit_amount=0; 
                        $grand_paid=0;
                        $grand_balance=0;
                        $grand_total_balance=0;
                        $gtot1=0;$gtot2=0;$gtot3=0;$gtot4=0;
                        $gtot_finance=0;
                        $payment = collect();
                        $realData = collect($data)->reject(function ($row) {
                            return ($row->transaction_type ?? '') == 'unadjusted_placeholder';
                        })->values();
                        @endphp
                        @if (count($realData)>0)
                        @php $sum_b=0; @endphp
                        @foreach ($realData as $dt)
                        
                        @php
                        $adjustments = 0; $receipt_date=''; $doc_number=''; $cheque_number=''; $bank_name=''; $bi_amount=0; $bi_amount2=0; $bi_amount3=0; $bi_amount4=0; $paid=0;
                       
                        @endphp
                        @php
                            $adjustments = $data_adjestment->where('piv_no',$dt->transaction_no)->max('paid_amount');
                            $payment = $data_payment->where('bi_doc_no',$dt->transaction_no);
                            if(count($payment)>0){
                                foreach($payment as $p){
                                    $receipt_date .= date('d/m/Y', strtotime($p->payment_date)).',';
                                    $doc_number .= $p->doc_number.',';
                                    if ($p->cheque_number != ""){
                                        $cheque_number .= $p->cheque_number.',';
                                    }                                
                                    if ($p->cheque_bank_name != ""){
                                        $bank_name .= $p->cheque_bank_name.',';
                                    }
                                    $bi_amount += $p->bi_amount;
                                }
                            }

                            $payment2 = $data_payment2->where('bi_doc_no',$dt->transaction_no);
                            if(count($payment2)>0){
                                foreach($payment2 as $p){
                                    $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                    $doc_number .= $p->doc_number.',';

                                    $bi_amount2 += $p->bi_amount;
                                }
                            }
                            $payment3 = $data_payment3->where('bi_doc_no',$dt->transaction_no);
                            if(count($payment3)>0){
                                foreach($payment3 as $p){
                                    $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                    $doc_number .= $p->doc_number.',';

                                    $bi_amount3 += $p->bi_amount;
                                }
                            }
                            $payment4 = $data_return->where('piv_no',$dt->transaction_no);
                            if(count($payment4)>0){
                                foreach($payment4 as $p){
                                    $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                    $doc_number .= $p->doc_number.',';

                                    $bi_amount4 += $p->paid_amount;
                                }
                            }
                            

                            $opb_import_paid = 0;
                            if (isset($dt->transaction_type) && $dt->transaction_type == 'opbinvoice') {
                                $opb_import_paid = (float) ($dt->debit_amount ?? 0);
                            }
                            $paid += ($adjustments + $bi_amount + $bi_amount2 + $opb_import_paid) - ($bi_amount3 - $bi_amount4);

                            $deal_id="";
                            $deal_code="";
                            $lpo_no="";
                            $bill_no="";
                            $bill_date="";
                            $sales_person="";
                            $payment_terms="";
                            $duedate="";
                            if(Illuminate\Support\Str::contains($dt->transaction_no, ['SI'])){
                                 $lpono = @App\SysHelper::get_sales_invoice_details($dt->transaction_no);
                                 $deal = @App\SysHelper::get_deal_detail_for_receivable_outstanding($dt->transaction_no);

                                 
                              
                            }else{
                                 $lpono = @App\SysHelper::get_purchase_invoice_details($dt->transaction_no);
                                 $deal = @App\SysHelper::get_deal_detail_for_payable_outstanding($dt->transaction_no);

                            }
                            
                            if(isset($deal) && $deal != ""){
                                $deal_id=$deal->id;
                                $deal_code=$deal->code;
                                $sales_person=$deal->full_name;
                            }
                            
                            if ($dt->transaction_type=="opbinvoice"){
                                $opbDet = $opbinvoice_map->get($dt->transaction_no);
                                if ($opbDet) {
                                    $lpo_no = $opbDet->po_no ?? '';
                                    $deal_code = $opbDet->deal_id ?? '';
                                    $payment_terms = $opbDet->payment_terms ?? '';
                                    $duedate = $opbDet->due_date ?? '';
                                    $bill_no = $opbDet->bill_no ?? '';
                                    $bill_date = $opbDet->bill_date ?? '';
                                }
                            }else{
                                if(isset($lpono) && $lpono != ""){
                                    $lpo_no=$lpono->lpo_number;
                                    $bill_no=@$lpono->bill_number;
                                    $bill_date=@$lpono->bill_date;
                                }
                            }
                           
                        @endphp
                        <?php 
                        if($dt->credit_amount != $paid){
                            $grand_credit_amount+=$dt->credit_amount;
                            $grand_paid+=$paid;
                            $grand_balance+=$dt->credit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }                        
                        if(($dt->debit_amount)>0){
                            $grand_credit_amount-=$dt->debit_amount;
                        }

                        ?>
                        <?php $is_hide=0; 
                        if(str_contains($dt->transaction_no,'PR')){
                        if($dt->debit_amount >= $paid){

                        $is_hide=1;
                        }} ?>

                         @if(((@App\SysHelper::com_curr_format($dt->credit_amount,2,'.','') != @App\SysHelper::com_curr_format($paid,2,'.','')) || (@App\SysHelper::com_curr_format($dt->debit_amount,2,'.',''))>0) && $is_hide == 0)
                        @php
                            $row_count_1++;
                            $rowAmount = (float) ($dt->credit_amount ?? 0);
                            if (isset($dt->transaction_type) && $dt->transaction_type == 'opbinvoice' && ($dt->debit_amount ?? 0) > 0) {
                                $rowAmount = (float) $dt->credit_amount - (float) $dt->debit_amount;
                            }
                            $paidExOpb = $paid - $opb_import_paid;
                            $paidDisplayParts = [];
                            if (abs($paidExOpb) >= 0.005) {
                                $paidDisplayParts[] = App\SysHelper::com_curr_format($paidExOpb, 2, '.', ',');
                            }
                            if ($opb_import_paid > 0) {
                                $paidDisplayParts[] = '- ' . App\SysHelper::com_curr_format($opb_import_paid, 2, '.', ',');
                            }
                            $paidDisplay = count($paidDisplayParts) > 0 ? implode(' ', $paidDisplayParts) : App\SysHelper::com_curr_format(0, 2, '.', ',');
                        @endphp
                        <tr>
                            <td class="text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $deal_code }}
                                @else
                                <a href="{{url('get-url-deal-track/'.$deal_code)}}" target="_blank">{{ $deal_code }}</a><input type="hidden" id="inv_e_deal_code_{{ $dt->transaction_no }}" value="{{ $deal_code }}" />
                                @endif
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}<input type="hidden" id="inv_e_doc_date_{{ $dt->transaction_no }}" value="{{ date('d/m/Y', strtotime($dt->transaction_date)) }}" /></td>
                            <td class="text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $dt->transaction_no }}
                                @else
                                 @if(Illuminate\Support\Str::contains($dt->transaction_no, ['SI']))
                                    <a href="{{ url('get-url-sales-invoice/' . $dt->transaction_no) }}" target="_blank">{{ $dt->transaction_no }}</a>
                                @else
                                  <a href="{{url('get-url-purchase-invoice/'.$dt->transaction_no)}}" target="_blank">{{ $dt->transaction_no }}</a>
                                @endif
                                <input type="hidden" id="inv_e_doc_no_{{ $dt->transaction_no }}" value="{{ $dt->transaction_no }}" />
                                @endif
                            </td>
                            <td class="text-center">{{ $lpo_no }}<input type="hidden" id="inv_e_lpo_no_{{ $dt->transaction_no }}" value="{{ $lpo_no }}" /></td>
                            <td class="text-center">{{ $bill_no }}</td>
                            <td class="text-center">
                                @if($bill_date !="" && $bill_date !=null)
                                {{ date('d/m/Y', strtotime($bill_date)) }}
                                @endif
                            </td>
                            <td class="text-end">
                                @if(str_contains($dt->transaction_no,'PR'))
                                    - {{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }}
                                    <input type="hidden" id="inv_e_amount_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }}" />
                                @else
                                    {{ @App\SysHelper::com_curr_format($rowAmount,2,'.',',') }}
                                    <input type="hidden" id="inv_e_amount_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($rowAmount,2,'.',',') }}" />
                                @endif
                            </td>
                            <td class="text-end">{{ $paidDisplay }}<input type="hidden" id="inv_e_adjustment_{{ $dt->transaction_no }}" value="{{ $paidDisplay }}" /></td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}
                                @php
                                if(str_contains($dt->transaction_no,'PR')){
                                    if($dt->debit_amount >= $paid){
                                        $b -= $dt->debit_amount;
                                    }
                                } else {
                                    $b += $dt->credit_amount-abs($paid);
                                }
                                @endphp
                            </td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($b,2,'.',',') }}</td>
                            @php $sum_b += $dt->credit_amount-abs($paid); $all_total += $dt->credit_amount-abs($paid); @endphp
                            <input type="hidden" class="inv_e_total" value="{{ $dt->credit_amount-abs($paid) }}" />
                            <script>
                                set_total({{ $aname->id }},{{ $sum_b }});
                            </script>

                            @php
                                $rowBalance = $dt->credit_amount - abs($paid);
                                if (str_contains($dt->transaction_no, 'PR')) {
                                    $rowBalance = $dt->debit_amount - abs($paid);
                                }
                                $invoiceDate = $dt->transaction_date;
                                $paymentTermRow = null;
                                $effectivePaymentTerm = null;
                                if ($dt->transaction_type == 'opbinvoice') {
                                    $effectivePaymentTerm = App\SysPaymentTerms::resolveOpbPaymentTerm(
                                        $payment_terms,
                                        $invoiceDate,
                                        $duedate,
                                        $payment_terms_map
                                    );
                                    $breakdown = App\SysPaymentTerms::buildOutstandingBreakdown(
                                        $invoiceDate,
                                        $rowBalance,
                                        $effectivePaymentTerm,
                                        $payable_finance_rate ?? 0,
                                        $asOfDateCalc
                                    );
                                } elseif (Illuminate\Support\Str::contains($dt->transaction_no, ['SI'])) {
                                    $siRow = $sales_invoice_map->get($dt->transaction_no);
                                    if ($siRow) {
                                        $invoiceDate = $siRow->doc_date;
                                        $paymentTermRow = $payment_terms_map->get($siRow->payment_terms);
                                    }
                                    $effectivePaymentTerm = $paymentTermRow;
                                    $breakdown = App\SysPaymentTerms::buildOutstandingBreakdown(
                                        $invoiceDate,
                                        $rowBalance,
                                        $effectivePaymentTerm,
                                        $payable_finance_rate ?? 0,
                                        $asOfDateCalc
                                    );
                                } else {
                                    $piRow = $purchase_invoice_map->get($dt->transaction_no);
                                    if ($piRow) {
                                        $invoiceDate = $piRow->pi_date ?? $dt->transaction_date;
                                        $paymentTermRow = $payment_terms_map->get($piRow->payment_terms);
                                    }
                                    $effectivePaymentTerm = $paymentTermRow;
                                    $breakdown = App\SysPaymentTerms::buildOutstandingBreakdown(
                                        $invoiceDate,
                                        $rowBalance,
                                        $effectivePaymentTerm,
                                        $payable_finance_rate ?? 0,
                                        $asOfDateCalc
                                    );
                                }
                                $ageingRow = App\SysPaymentTerms::buildOsListAgeingBuckets(
                                    $invoiceDate,
                                    $rowBalance,
                                    $effectivePaymentTerm,
                                    $asOfDateCalc,
                                    $breakdown['max_overdue_days'] ?? null
                                );
                                $gtot1 += $ageingRow['0_30'];
                                $gtot2 += $ageingRow['31_60'];
                                $gtot3 += $ageingRow['61_90'];
                                $gtot4 += $ageingRow['90_plus'];
                                $gtot_finance += $breakdown['total_finance_cost'];
                            @endphp

                            @if(($breakdown['max_overdue_days'] ?? 0) > 0)
                            <script>
                                $('#sum_{{ $aname->id }}').css('color', 'red');
                            </script>
                            @endif

                            @include('backEnd.outstanding.partials.receivable_due_columns', ['breakdown' => $breakdown])

@if(!$hideBasicColumns)
                            <td class="text-end">{{ abs($ageingRow['0_30']) >= 0.01 ? App\SysHelper::com_curr_format($ageingRow['0_30'], 2, '.', ',') : '' }}<input type="hidden" class="inv_all_0_30" value="{{ $ageingRow['0_30'] }}" /></td>
                            <td class="text-end">{{ abs($ageingRow['31_60']) >= 0.01 ? App\SysHelper::com_curr_format($ageingRow['31_60'], 2, '.', ',') : '' }}<input type="hidden" class="inv_all_31_60" value="{{ $ageingRow['31_60'] }}" /></td>
                            <td class="text-end">{{ abs($ageingRow['61_90']) >= 0.01 ? App\SysHelper::com_curr_format($ageingRow['61_90'], 2, '.', ',') : '' }}<input type="hidden" class="inv_all_61_90" value="{{ $ageingRow['61_90'] }}" /></td>
                            <td class="text-end">{{ abs($ageingRow['90_plus']) >= 0.01 ? App\SysHelper::com_curr_format($ageingRow['90_plus'], 2, '.', ',') : '' }}<input type="hidden" class="inv_all_90_above" value="{{ $ageingRow['90_plus'] }}" /></td>
                            <td class="text-end">
                                @if (!empty($breakdown['finance_cost_popover_content_attr']) && ($breakdown['total_finance_cost'] ?? 0) != 0)
                                    <span class="ageing-grn-pop ageing-grn-tip d-inline-block" tabindex="0" role="button" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover focus" data-bs-placement="auto" data-bs-content="{!! $breakdown['finance_cost_popover_content_attr'] !!}">{{ App\SysHelper::com_curr_format($breakdown['total_finance_cost'], 2, '.', ',') }}</span>
                                @else
                                    {{ ($breakdown['total_finance_cost'] ?? 0) != 0 ? App\SysHelper::com_curr_format($breakdown['total_finance_cost'], 2, '.', ',') : '' }}
                                @endif
                            </td>
                            <td class="text-start">{{ $breakdown['payment_terms_title'] }}</td>
                            <td class="text-start hidecol_{{ $aname->id }}">{{ rtrim($receipt_date, ',') }}</td>
                            <td class="text-start hidecol_{{ $aname->id }}">{{ rtrim($doc_number, ',') }}</td>
@else
                            <td class="text-start">{{ $breakdown['payment_terms_title'] ?? '' }}</td>
@endif
                        </tr>
                       
                        @endif
                            @if(count($payment)==0)
                            <script>
                                $('.hidecol_'+{{ $aname->id }}).css('display','none');
                            </script>
                            @endif

                        
                        <?php // } ?>    
                            
                        @endforeach
                        @endif
                    @if (count($realData) > 0)
                    <tr>
                        <td colspan="6"></td>
                        <td class="text-end"><b><?php echo @App\SysHelper::com_curr_format($grand_credit_amount,2,'.',',') ?></b></td>
                        <td class="text-end"><b><?php echo @App\SysHelper::com_curr_format($grand_paid,2,'.',',') ?></b></td>
                        <td class="text-end"><b><?php echo @App\SysHelper::com_curr_format($grand_balance,2,'.',',') ?></b></td>
                        <td class="text-end"><b><?php echo @App\SysHelper::com_curr_format($b,2,'.',',') ?></b></td>
                        <td colspan="{{ $scheduleColCount }}"></td>
                        @if(!$hideBasicColumns)
                            <td class="text-end"><b>{{ App\SysHelper::com_curr_format($gtot1, 2, '.', ',') }}</b></td>
                            <td class="text-end"><b>{{ App\SysHelper::com_curr_format($gtot2, 2, '.', ',') }}</b></td>
                            <td class="text-end"><b>{{ App\SysHelper::com_curr_format($gtot3, 2, '.', ',') }}</b></td>
                            <td class="text-end"><b>{{ App\SysHelper::com_curr_format($gtot4, 2, '.', ',') }}</b></td>
                            <td class="text-end"><b>{{ App\SysHelper::com_curr_format($gtot_finance, 2, '.', ',') }}</b></td>
                            <td colspan="1"></td>
                            <td class="text-center hidecol_{{ $aname->id }}">&nbsp;</td>
                            <td class="text-center hidecol_{{ $aname->id }}">&nbsp;</td>
                        @else
                            <td colspan="1"></td>
                        @endif
                    </tr>
                    @endif
                        <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>    
                    </tbody>
                  </table>








                  
                  
                  <?php $pdc = $list_of_adjusted_pdc->where('account_id',$aname->id); ?>
                 <?php $pdc2 = $list_of_unadjusted_pdc->where('account_id',$aname->id); ?>
                  @if (count($pdc)>0)
                  <br>
                  <b>List of PDC:-</b>
                  <table class="table sub_table table-hover" id="long-list" style="border: solid 1px #e3e6f0; width:100%; table-layout:fixed;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5%">Deal ID</th>
                            <th class="text-center" style="width:6%">Doc Date</th>
                            <th class="text-center" style="width:6%">Payment No</th>
                            <th class="text-end" style="width:7%">Amount</th>
                             <th class="text-end" style="width:5%">Adjusted</th>
                            <th class="text-center" style="width:6%">Cheque Date</th>
                            <th class="text-center" style="width:7%">Cheque No</th>
                            <th class="text-center" style="width:7%">Payment Date</th>
                            <th class="text-center" style="width:6%">Invoice Adjusted</th>
                            <th class="text-start" style="width:45%">Remarks</th>
                            <th class="text-center" style="width:4%"></th>
                        </tr>
                    </thead>
                    <tbody>
                         @php
                            $row_count_2 = 0;
                        @endphp
                        @foreach ($pdc as $p)
                        @php
                            $row_count_2++;
                        @endphp
                         @php
                            if($p->doc_number){
                               $deal_id = @App\SysReceipt::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                            }
                        @endphp
                        <tr id="row_pdc_paid_{{ $p->doc_number }}">
                             <td class="text-center"> @if (@App\SysHelper::get_code_from_dealid($deal_id)!= 'Without Deal')
                                 
                                    <a href="{{url('get-url-deal-track/'.@App\SysHelper::get_code_from_dealid($deal_id))}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                   {{@App\SysHelper::get_code_from_dealid($deal_id)}}
                                @endif</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="text-center"><a href="{{url('get-url-payment/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                              <td class="text-end">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="text-center">{{ $p->cheque_number }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="text-center">
                                <a style="cursor: pointer;" onclick="row_det_fun('{{ $p->doc_number }}','{{ $p->bi_doc_no }}')">{{ $p->bi_doc_no }}</a>
                            </td>
                          
                            <td class="text-start">{{ $p->remarks }}</td>
                            <td class="text-center"><a class="text-danger text-center" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ @App\SysHelper::normalizeToDmy($p->payment_date) }}',3)"><i class="ico icon-outline-pen-new-square" style="font-size: 16px" aria-hidden="true"></i></a></td>
                            
                            <script>
                                set_total_addmore({{ $aname->id }},{{ $p->adj_amount }})
                            </script>
                        </tr>
                        <tr style="display: none;" id="row_det_{{ $p->doc_number }}">
                            <td></td>
                            <td colspan="9">
                                    <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;" id="row_det_table_{{ $p->doc_number }}">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Doc Date</th>
                                                <th class="text-center">Doc No</th>
                                                <th class="text-center">LPO No</th>
                                                <th class="text-center">Deal ID</th>
                                                <th class="text-center">Amount</th>
                                                <th class="text-center">Adjustments</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                            </td>
                        </tr>
                        @endforeach
                        @php
                            $row_count_2 = 0;
                        @endphp
                         @foreach ($pdc2 as $p)
                           @php
                            $row_count_2++;
                        @endphp
                          @php
                            if($p->doc_number){
                               $deal_id = @App\SysReceipt::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                            }
                        @endphp
                        <tr id="row_pdc_paid_{{ $p->doc_number }}">
                             <td class="text-center"> 
                                @if (@App\SysHelper::get_code_from_dealid($deal_id) != 'Without Deal')
                                    <a href="{{url('get-url-deal-track/'.@App\SysHelper::get_code_from_dealid($deal_id))}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                    {{ @App\SysHelper::get_code_from_dealid($deal_id) }}
                                @endif
                                
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="text-center"><a href="{{url('get-url-payment/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="text-end">0.00</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="text-center">{{ $p->cheque_number }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="">-</td>
                            <td class="">{{ $p->remarks }}</td>
                            <td class="text-center"><a class="text-danger text-center" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ @App\SysHelper::normalizeToDmy($p->payment_date) }}',2)"><i class="ico icon-outline-pen-new-square" style="font-size: 16px" aria-hidden="true"></i></a></td>
                        </tr>
                        @endforeach

                         {{-- add totals row for PDC --}}
                  @php
                      $pdcCount = count($pdc) + count($pdc2);
                      $pdcSumAmount = $pdc->sum('amount') + $pdc2->sum('amount');
                      $pdcSumAdjusted = $pdc->sum('adj_amount') + $pdc2->sum('adj_amount');
                  @endphp
                 
                      <tr>
                        <td class="text-center font-weight-bold"></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class="text-end"><b>{{ @App\SysHelper::com_curr_format($pdcSumAmount,2,'.',',') }}</b></td>
                        <td class="text-end"><b>{{ @App\SysHelper::com_curr_format($pdcSumAdjusted,2,'.',',') }}</b></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                      </tr>
                    
                

                    </tbody>
                  </table>
  @endif
                 

              


                  
                  <?php $unadj_list = $list_of_unadjusted->where('account_id',$aname->id); ?>
                  <?php $unadj_list_jv_to_jv = $list_of_unadjusted_jv_to_jv->where('account_id',$aname->id); ?>
                  
                  @if (count($unadj_list)>0 || count($unadj_list_jv_to_jv)>0)
                  <br>
                  <b>List of Unadjusted balance:-</b>
                  <table class="table sub_table table-hover" id="long-list" style="border: solid 1px #e3e6f0;  width:100%; table-layout:fixed;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5%">Deal ID</th>
                            <th class="text-center" style="width:6%">Doc Date</th>
                            <th class="text-center" style="width:6%">Payment No</th>
                            <th class="text-end" style="width:7%">Amount</th>
                            <th class="text-start" style="width:80%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($unadj_list)>0)
                        @foreach ($unadj_list as $p)
                           @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BP', 'CP']))
                               @php
                                    if($p->doc_number){
                                    $deal_id = @App\SysPayment::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                    }
                                @endphp
                               
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                             @php
                                    if($p->doc_number){
                                    $deal_id = @App\SysJournalVoucher::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                    }
                                @endphp
                          
                                
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['PR']))
                                @php
                                    if($p->doc_number){
                                    $deal_id = @App\SysPurchaseReturn::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                    }
                                @endphp
                            @endif
                        <tr>
                             <td class="text-start"> @if (@App\SysHelper::get_code_from_dealid($deal_id)!= 'Without Deal')
                                    <a href="{{url('get-url-deal-track/'.@App\SysHelper::get_code_from_dealid($deal_id))}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                   {{@App\SysHelper::get_code_from_dealid($deal_id)}}
                                @endif
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BP', 'CP']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-payment/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['PR']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-purchase-return/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="text-center">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="">{{ $p->remarks }}</td>
                            <script>
                                set_total_lessmore({{ $aname->id }},{{ $p->amount - $p->adj_amount }})
                            </script>
                        </tr>
                        @endforeach
                        @endif
                        
                        @if (count($unadj_list_jv_to_jv)>0)
                        @foreach ($unadj_list_jv_to_jv as $p)
                                @php
                                    $docNumber = $p->doc_number;
                             
                                        if($p->doc_number){
                                        $deal_id = @App\SysJournalVoucher::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                        }
                                        @endphp
                                   
                        <tr>
                            <td class="text-start"> @if (@App\SysHelper::get_code_from_dealid($deal_id)!= 'Without Deal')
                                    <a href="{{url('get-url-deal-track/'.@App\SysHelper::get_code_from_dealid($deal_id))}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                   {{@App\SysHelper::get_code_from_dealid($deal_id)}}
                                @endif
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="text-center">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->amount2,2,'.',',') }}</td>
                            <td class="">{{ $p->remarks }}</td>
                            <script>
                                set_total_lessmore({{ $aname->id }},{{ $p->amount - $p->amount2 }})
                            </script>
                        </tr>
                        @endforeach
                        @endif                       

                    @php
                        $unadjAll = collect($unadj_list)->merge($unadj_list_jv_to_jv);
                        $unadjSum = $unadjAll->sum(function($p){
                            $amt = $p->amount;
                            if(isset($p->adj_amount)){
                                $amt -= $p->adj_amount;
                            }
                            if(isset($p->amount2)){
                                $amt -= $p->amount2;
                            }
                            return $amt;
                        });
                    @endphp
                    <tr class="">
                        <td class="text-center font-weight-bold"></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class="text-end"><b>{{ @App\SysHelper::com_curr_format($unadjSum,2,'.',',') }}</b></td>
                        <td class=""></td>
                    </tr>
  </tbody>
                  </table>
                  @endif

                 

                  </div>
                  
                  <?php
                    $record = $opb_balance_amount->where('account_id', $aname->id)->first();
                    $opb = $record ? $record->opb_amount : 0;
                    $opb = @App\SysHelper::com_curr_format($opb,2,'.','')
                  ?>
                <script>
                    check_total({{ $aname->id }},{{ $opb }})
                </script>
                
                  <?php } ?>

                  @endforeach
                   @if(@$ctrl_list_option == 'consolidated')
                    <script>
                        // expand all accordion panels when consolidated view selected
                        $('#accordionExample .collapse').addClass('show');
                    </script>
                  @endif
                  
                  <style>
                    .no-last-border > :not(:last-child) > :last-child > * {
    border-bottom-color: transparent !important;
}
                  </style>
                  <table class="table no-last-border" style="border: solid 1px #e3e6f0;">
                    <thead>
                        <tr>
                            <th class="text-center" width="168px"></th>
                            <th class="text-center" width="70px"></th>
                            <th class="text-center" width="384px"></th>
                            <th class="text-end" width="85px"></th>
                            <th class="text-center" width="338px"></th>
                            <th class="text-center" width="105px"></th>
                            <th class="text-center" width="114px"></th>
                            <th class="text-end" width="103px"><b>Total</b></th>
                            <th class="text-center" width="103px"><b><label class="fw-bold" id="lbl_all_sivno_count"></label></b></th>
                            <th class="text-end" width="102px"><b><label class="fw-bold" id="lbl_main_sum_total"></label></b></th>
                        </tr>
                    </thead>
                  </table>
                  @else
                  <table class="table no-last-border" style="border: solid 1px #e3e6f0;">
                    <tbody>
                        <tr>
                            <td colspan="14" class="text-center">No data found</td>
                        </tr>
                        <tr>
                            <td colspan="14">&nbsp;</td>
                        </tr>
                      
                       
                    </tbody>
                  </table>
                  @endif
                  </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ************** --}}


        
    
<script>
    $(document).ready(function () {
        // when one accordion panel opens, close any other that is showing
        $('#accordionExample').on('show.bs.collapse', function (e) {
            $('#accordionExample .collapse.show').not(e.target).collapse('hide');
        });

        $('#payableOutstandingSearch').on('input', function () {
            var term = $(this).val().trim().toLowerCase();

            $('.sub_table').each(function () {
                var $table = $(this);
                var $rows = $table.find('tbody tr').not('.no-search-result');
                var visibleRows = 0;

                if (!term) {
                    $rows.show();
                    $table.find('.no-search-result').remove();
                    return;
                }

                $rows.each(function () {
                    var $row = $(this);
                    if ($row.find('td[colspan]').length) {
                        $row.hide();
                        return;
                    }
                    var text = $row.text().toLowerCase();
                    var match = text.indexOf(term) !== -1;
                    $row.toggle(match);
                    if (match) {
                        visibleRows++;
                    }
                });

                $table.find('.no-search-result').remove();
                if (visibleRows === 0) {
                    var colspan = $table.find('thead th').length || 1;
                    $table.find('tbody').append('<tr class="no-search-result"><td colspan="' + colspan + '" class="text-center text-muted">No matching records found</td></tr>');
                }

                var $collapse = $table.closest('.collapse');
                if (visibleRows > 0) {
                    $collapse.collapse('show');
                } else {
                    $collapse.collapse('hide');
                }
            });
        });

        let visibleCount = 0;
        let totalInv = 0;
        let totalall_0_30 = 0;
        let totalall_31_60 = 0;
        let totalall_61_90 = 0;
        let totalall_90_above = 0;
        // accumulator for header .main_sum values (the visible totals)
        let totalMainSum = 0;

        var ctrlOption = '{{ @$ctrl_list_option }}';
    

        $('label.main_sum').each(function () {
            var value = $(this).text().trim();
            var $mainTable = $(this).closest('.main_table');
            var color = $(this).css('color');
            var mainTableId = $mainTable.attr('id') || '';
            var anameId = mainTableId.replace('account_table', '');
            var headerTotal = formatAmountToNumber(value);

             // if unadjusted_balance filter is active, require at least one unadjusted row
            if (ctrlOption === 'unadjusted_balance') {
                // look for any <b> tag containing "unadjusted" inside this account's collapse
                var $section = $('#collapse' + anameId);
                var hasUnadj = $section.find('b').filter(function() {
                    return $(this).text().trim().toLowerCase().indexOf('unadjusted balance') !== -1;
                }).length > 0;
                if (!hasUnadj) {
                    $mainTable.hide();
                    $('#collapse' + anameId).hide();
                    return;
                }
            }

            // if unmatched_balance filter is active, only keep red totals
            if (ctrlOption === 'unmatched_balance') {
                if (color.indexOf('255, 0, 0') === -1) { // red rgb
                    $mainTable.hide();
                    return;
                }
            }

            // if overdue_balance filter is active, only keep blue totals
            if (ctrlOption === 'overdue_balance') {
                // computed color likely in rgb format; match on blue component
                if (color.indexOf('0, 0, 255') === -1) {
                    $mainTable.hide();
                    return; // skip to next iteration
                }
            }

            if (!value || Math.abs(headerTotal) <= 0.01) {
                $mainTable.hide();
                $('#collapse' + anameId).hide();
            } else {
                $mainTable.show(); // optional if hidden by default
                visibleCount++;

                // Extract ID from main table to locate sub_table
                // Now find the corresponding .sub_table inside the collapse div
                var $subTable = $('#collapse' + anameId).find('.sub_table');

                $subTable.find('.inv_e_total').each(function () {
                    totalInv += parseFloat($(this).val()) || 0;
                });

                $subTable.find('.inv_all_0_30').each(function () {
                    totalall_0_30 += parseFloat($(this).val()) || 0;
                });

                $subTable.find('.inv_all_31_60').each(function () {
                    totalall_31_60 += parseFloat($(this).val()) || 0;
                });

                $subTable.find('.inv_all_61_90').each(function () {
                    totalall_61_90 += parseFloat($(this).val()) || 0;
                });

                $subTable.find('.inv_all_90_above').each(function () {
                    totalall_90_above += parseFloat($(this).val()) || 0;
                });
                // also grab this account header total (main_sum)
                totalMainSum += headerTotal;
            }
        });

        $('#lbl_all_sivno_count').text(visibleCount);
        $('#lbl_all_total').text(formatAmount(totalInv.toFixed(2)));
        $('#lbl_all_total_0_30').text(formatAmount(totalall_0_30.toFixed(2)));
        $('#lbl_all_total_31_60').text(formatAmount(totalall_31_60.toFixed(2)));
        $('#lbl_all_total_61_90').text(formatAmount(totalall_61_90.toFixed(2)));
        // footer shows sum of visible main_sum headers, matching supplier ageing net balance.
        $('#lbl_main_sum_total').text(formatAmount(totalMainSum.toFixed(2)));
        
    });
</script>






        <script>
            function row_det_fun(id,docs){
                $('#row_det_table_'+id+' tbody').empty();
                var doc = docs.split(',');
                for (var i = 0; i < doc.length; i++) {
                    doc[i] = doc[i].trim();
                    var inv_e_doc_date = $('#inv_e_doc_date_'+doc[i]).val();
                    var inv_e_doc_no = $('#inv_e_doc_no_'+doc[i]).val();
                    var inv_e_lpo_no = $('#inv_e_lpo_no_'+doc[i]).val();
                    var inv_e_deal_code = $('#inv_e_deal_code_'+doc[i]).val();
                    var inv_e_amount = $('#inv_e_amount_'+doc[i]).val();
                    var inv_e_adjustment = $('#inv_e_adjustment_'+doc[i]).val();

                    var htm = "<tr>\
                        <td class='border'>"+inv_e_doc_date+"</td>\
                        <td class='border'>"+inv_e_doc_no+"</td>\
                        <td class='border'>"+inv_e_lpo_no+"</td>\
                        <td class='border'>"+inv_e_deal_code+"</td>\
                        <td class='text-end'>"+inv_e_amount+"</td>\
                        <td class='text-end'>"+inv_e_adjustment+"</td>\
                        </tr>"
                        $('#row_det_table_'+id+' tbody').append(htm);

                }
                var row = $('#row_det_'+id);
                if (row.is(':visible')) {
                    row.hide();
                } else {
                    row.show();
                }
            }
        </script>
    


@push('scripts')
<script>
(function () {
    function initAgeingGrnPopovers(root) {
        var scope = root && root.querySelectorAll ? root : document;
        var nodes = scope.querySelectorAll ? scope.querySelectorAll('.ageing-grn-pop') : document.querySelectorAll('.ageing-grn-pop');
        nodes.forEach(function (el) {
            if (typeof bootstrap === 'undefined' || !bootstrap.Popover) {
                return;
            }
            if (bootstrap.Popover.getInstance(el)) {
                return;
            }
            if (!el.getAttribute('data-bs-content')) {
                return;
            }
            new bootstrap.Popover(el, {
                container: 'body',
                html: true,
                sanitize: false,
                trigger: 'hover focus',
                placement: 'auto',
                delay: { show: 120, hide: 60 }
            });
        });
    }

    $(document).on('click', '.ageing-grn-pop', function (e) {
        e.stopPropagation();
    });

    $(function () {
        initAgeingGrnPopovers(document);
        $(document).on('shown.bs.collapse', '.collapse', function () {
            initAgeingGrnPopovers(this);
        });
        setTimeout(function () {
            initAgeingGrnPopovers(document);
        }, 600);
    });
})();
</script>
@endpush

@endsection




<div class="modal side-panel fade" id="ModalTrackComment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Payment Follow-up Remark</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'outstanding_comment_save','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-edit']) }}

            <div class="modal-body">

             <div class="" id="customerInfoDisplay">
                        <!-- customer details will be injected here -->
                    </div>
                
                <div class="row">
                    <div id="message"></div>
<!-- 
                    <div class="col-lg-12 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <input type="hidden" id="iddetail" name="id_detail">
                                  
                                    <textarea   id="comment" name="comment" class="form-control"  cols="10" rows="3" ></textarea>
                                   
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- next follow-up date -->
                    <!-- <div class="col-lg-4 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Next Follow-up')<span></span></label>
                                    <input type="text" id="remark_date" name="remark_date" class="form-control date-picker">
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- attachment -->
                    <!-- <div class="col-lg-4 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Attachment')<span></span></label>
                                    <input type="file" id="remark_file" name="remark_file" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- <div class="col-4" style="margin-top:1.9rem">
                         <button type="button" class="btn btn-light add-btn ms-2" name="btnSubmit1" id="btnSubmit1" >
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Add Remark
                        </button>
                    </div> -->
                </div>
                <div class="d-flex align-items-center mt-2 mb-1">
    <b class="me-2">Previous Remarks</b>
    <i class="ico icon-outline-add-square text-success" data-bs-toggle="modal" data-bs-target="#ModalTrackCommentInputs" style="font-size:14px"></i>
</div>
<div class="row">
    <div class="col-lg-12" id="mydiv" style="height: auto; max-height: 300px; overflow-y: scroll;">
    </div>
</div>

            </div>

            

            {{ Form::close() }}

        </div>
    </div>
</div>
<!-- Modal Deal Track-->



<!-- Modal Payment Follow-up Remark -->
<div class="modal side-panel fade" id="ModalTrackCommentInputs" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel" style="font-size: 14px">Add Remark</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'outstanding_comment_save','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-edit']) }}

            <div class="modal-body">

             
               
                <div class="row">
                    <div id="message"></div>

                    <div class="col-lg-12 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <input type="hidden" id="iddetail" name="id_detail">
                                    <!-- <label class="form-label">@lang('Internal Note')<span></span></label> -->
                                    <textarea   id="comment" name="comment" class="form-control"  cols="10" rows="3" ></textarea>
                                     <!-- <input class="form-control" width="60" id="comment" type="text" required name="comment"> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- added date and attachment fields -->
                    <div class="col-lg-4 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Next Follow-up')<span></span></label>
                                    <input type="text" id="remark_date" name="remark_date" class="form-control date-picker">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="form-label">@lang('Attachment')<span></span></label>
                                    <input type="file" id="remark_file" name="remark_file" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-4" style="margin-top:1.9rem">
                         <button type="button" class="btn btn-light add-btn ms-2" name="btnSubmit1" id="btnSubmit1" >
							<i class="ico icon-outline-bookmark-opened text-success" style="font-size:16px"></i> Add Remark
						</button>
                    </div>

                </div>
            </div>
                 

            

            {{ Form::close() }}

        </div>
    </div>
</div>
<!-- Modal Payment Follow-up Remark -->



<!-- Modal PDC Update -->
<div class="modal fade" id="ModalPDCUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">PDC Update</h5>
                    						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        </div>
        <div class="modal-body">                
            <div class="row">
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <input type="hidden" id="pdc_payment_doc_no">
                                <label class="txtlbl">@lang('Payment Date')<span></span></label>
                                <input class="form-control date-picker" id="pdc_payment_doc_date" type="text" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Status')<span></span></label>
                                <select class="form-control" id="pdc_payment_status">
                                    <option value="2">Paid & Removed</option>
                                    <option value="1">Paid</option>
                                    <option value="3">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <input type="hidden" id="pdc_status">
                       <button type="button" class="btn btn-light btn-small" id="btnSubmitPDC" onclick="pdc_update_save()"><i class="ico icon-outline-bookmark-opened text-success" style="font-size:16px"></i> PDC Received</button>

        </div>
      </div>
    </div>
  </div>
<!-- Modal PDC Update -->

<script>
    function pdc_update(id,dat,status){
        $('#pdc_payment_doc_no').val(id);
        $('#pdc_payment_doc_date').val(dat);
        $('#pdc_status').val(status);
        // use bootstrap 5 modal show method instead of hidden link
        $('#ModalPDCUpdate').modal('show');
    }

    function pdc_update_save() {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('update-payable-pdc') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id: $('#pdc_payment_doc_no').val(),
                doc_date: $('#pdc_payment_doc_date').val(),
                status: $('#pdc_payment_status').val(),
                pdc_status: $('#pdc_status').val(),
            },
            cache: false,
            success: function(dataResult) {
                // controller returns JSON string
                try { dataResult = JSON.parse(dataResult); } catch(e) {}
                if(dataResult['data']=="SUCCESS"){
                    var a = $('#pdc_payment_doc_no').val();
                    $('#btn_pdc_received_'+a).css("background-color", "#f6c23e");
                    $('#btn_pdc_received_'+a).text("Updated");
                    if($('#pdc_payment_status').val()==2){
                        $('#row_pdc_paid_'+a).css("display", "none");
                    }
                    $('#btnSubmitPDC_close').click();
                    location.reload();
                } else { alert("Error!!"); }
                $("#loading_bg").css("display", "none");
            }
        });
    }

</script>

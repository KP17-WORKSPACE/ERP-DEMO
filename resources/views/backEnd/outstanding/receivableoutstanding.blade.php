@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

  <script>
            // Export visible Receivable Outstanding rows to Excel (ExcelJS styled)
            $(document).ready(function () {
                $('#exportExcelReceivable').on('click', function () {
                    var hideBasicCols = @json(!empty($ctrl_basic_search));
                    var companyName   = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                    var asOfDate      = $('#till_date').val() || '';

                    // Build header label array
                    var headerLabels = [
                        'Account Code', 'Customer', 'Deal ID',
                        'Inv Date', 'Inv No', 'LPO No',
                        'Amount', 'Adjustments', 'Balance', 'Total Balance',
                        'Due Date', 'Over Due'
                    ];
                    if (!hideBasicCols) {
                        headerLabels = headerLabels.concat(['0-30', '31-60', '61-90', '>90']);
                    }
                    headerLabels = headerLabels.concat(['Sales Person', 'Payment Terms']);
                    if (!hideBasicCols) {
                        headerLabels = headerLabels.concat(['Receipt Date', 'Receipt No']);
                    }

                    var N = headerLabels.length;

                    // Collect data rows
                    var dataRows = [];
                    $('.main_table:visible').each(function () {
                        var mainId       = $(this).attr('id');
                        if (!mainId) return;
                        var aid          = mainId.replace('account_table', '');
                        var accountCode  = $(this).data('acccode') || '';
                        var customerName = $(this).find('th a').first().text().trim();
                        var $subRows     = $('#collapse' + aid).find('.sub_table tbody tr');

                        $subRows.each(function () {
                            if ($(this).find('td[colspan]').length > 0) return;
                            var cells = $(this).find('td').filter(function () {
                                return $(this).css('display') !== 'none';
                            }).map(function () {
                                return $(this).text().trim().replace(/\s+/g, ' ');
                            }).get();
                            if (cells.length === 0) return;
                            dataRows.push([accountCode, customerName].concat(cells));
                        });
                    });

                    if (dataRows.length === 0) {
                        alert('No data available for export');
                        return;
                    }

                    var workbook  = new ExcelJS.Workbook();
                    var worksheet = workbook.addWorksheet('Receivable Outstanding');

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
                    r2.getCell(1).value     = 'Receivable Outstanding';
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
                        var filename = 'receivable_outstanding_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                        saveAs(blob, filename);
                    });
                });
            });
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

        
<div class="content-container col-12 purchase-order">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Receivable Outstanding
                </h4>
                <div class="purchase-order-content-header-right">
                    <div class="me-2" style="min-width:250px;">
                        <input id="receivableOutstandingSearch" class="form-control form-control-sm" type="text" placeholder="Search...">
                    </div>
                    <a class="btn btn-light text-dark" href="{{url('receipt-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Receipt
                    </a>

                    
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                          
                            <li>
                                <a
                                    href="{{ url('customer-ageing-report') }}"class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Customer Ageing Report</a>
                            </li>

                            <li>
                                <a
                                    type="button" id="exportExcelReceivable" class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-export text-success title-15 me-2"></i> Export</a>
                            </li>


                            


                        </ul>
                    </div>

                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

                   
<script>
    function download_outstanding(id){
        var date = $('#till_date').val();    
             // Replace all slashes with hyphens
    date = date.replaceAll('/', '-');                                                    

        var url = $("#base_url").val()+"/receivable-outstanding-download/"+id+"/"+date;
        window.location.href = url;
    }
</script>                                   
<script>
        $(document).ready(function() {


          var id=''
            

            $('.btn-badge').click(function() {
                var accountId = $(this).data('id');
                var custInfo = $(this).data('cust') || '';
                id = accountId; // keep global if other code relies on it
                $('#iddetail').val(accountId);
                $('#customerInfoDisplay').html(custInfo);
                $('#mydiv').html('<p class="text-muted text-center" style="font-size:11px">Loading...</p>');
                view(accountId);
            });

            // open remark modal when plus button is clicked
            $('#btnAddRemark').on('click', function() {
                // clear previous fields
                $('#comment').val('');
                $('#remark_date').val('');
                $('#remark_file').val('');
                $('#message').empty();
                $('#ModalTrackComment').modal('show');
            });


 
               
           

            var currentUserId = {{ Auth::user()->id }};

            $('#btnSubmit1').click(function() {
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
                    url: "outstanding_comment_save",
                    type: "post",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(dataResult) {
                        var res = JSON.parse(dataResult);
                        if (res.data === 'SUCCESS') {
                            $('#comment').val('');
                            $('#remark_date').val('');
                            if (fileInput) fileInput.value = '';
                            $('#message').html("<div class='alert alert-success py-1'><i class='fa fa-check'></i> Remark added!</div>");
                            setTimeout(function(){ $('#message').html(''); }, 3000);
                            view(accountId);
                            // close inputs modal
                            $('#ModalTrackCommentInputs').modal('hide');
                        } else {
                            $('#message').html("<div class='alert alert-danger py-1'>Error: " + (res.message || 'Failed') + "</div>");
                        }
                    },
                });
            });

            $(document).on('click', '.btn-delete-outstand-comment', function() {
                if (!confirm('Are you sure you want to delete this remark?')) return;
                var commentId = $(this).data('id');
                var accountId = $('#iddetail').val();
                $.ajax({
                    url: "outstanding_comment_delete",
                    type: "post",
                    data: { _token: '{{ csrf_token() }}', comment_id: commentId },
                    success: function(dataResult) {
                        view(accountId);
                    },
                });
            });

          function view(accountId){
            $.ajax({
                url: "outstanding_comment",
                type: "post",
                data: { _token: '{{ csrf_token() }}', id_deal: accountId },
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    $('#mydiv').empty();
                    dataResult.forEach(function(re) {
                        var isDeleted = re.is_deleted == 1;
                        var textClass = isDeleted ? 'text-decoration-line-through text-muted' : '';
                        var commentHtml = re.comment
                            ? re.comment.split("\n").map(function(l){ return l; }).join('<br>')
                            : '';
                        var attachmentHtml = '';
                        if (re.file) {
                            attachmentHtml = '<a href="{{ asset("public/uploads/outstand_comments_doc/") }}/' + re.file +
                                '" target="_blank" class="btn btn-sm btn-light me-1" style="min-height:17px">' +
                                '<i class="ico icon-bold-paperclip" style="font-size:11px"></i></a>';
                        }
                        var deleteHtml = '';
                        if (!isDeleted && re.created_by == currentUserId) {
                            deleteHtml = '<button type="button" class="btn btn-sm btn-light btn-delete-outstand-comment" data-id="' + re.id + '" style="min-height:17px">' +
                                '<i class="ico icon-outline-trash-bin-minimalistic" style="font-size:11px"></i></button>';
                        }
                        var followupHtml = '';
                        if (re.followup_date) {
                            // convert date to d/m/Y for display
                            var fd = new Date(re.followup_date);
                            var formatted = fd.toLocaleDateString('en-GB');
                            followupHtml = ' <span class="ms-1 text-primary" style="font-size:10px"><i class="ico icon-bold-clock me-1"></i>Follow-up: ' + formatted + '</span>';
                        }
                        var deletedHtml = '';
                        if (isDeleted) {
                            deletedHtml = ' <span class="text-danger" style="font-size:10px">• Deleted</span>';
                        }
                        var card = '<div class="card rounded-3 mb-2">' +
                            '<div class="card-body py-1 px-2">' +
                            '<div class="d-flex justify-content-between mb-0">' +
                            '<p class="mb-0 text-break fw-semibold ' + textClass + '" style="font-size:11px">' + commentHtml + '</p>' +
                            '<div class="d-flex align-items-baseline gap-1">' + attachmentHtml + deleteHtml + '</div>' +
                            '</div>' +
                            '<div class="text-end text-muted" style="font-size:10px">' +
                            '<span>' + (re.username || '') + '</span>' +
                            ' <span>•</span> <span><i class="ico icon-bold-clock me-1"></i>' +
                            new Date(re.created_at).toLocaleDateString('en-GB') + ' ' +
                            new Date(re.created_at).toLocaleTimeString('en-US', {hour:'2-digit',minute:'2-digit'}) +
                            '</span>' + followupHtml + deletedHtml +                            '</div>' +
                            '</div></div>';
                        $('#mydiv').append(card);
                    });
                    if (dataResult.length === 0) {
                        $('#mydiv').html('<p class="text-muted text-center" style="font-size:11px">No remarks yet.</p>');
                    }
                }
            });
            }
        }); // close document.ready
    </script>
            
                    
          
                            <div class="card mb-3">
                                <div class="card-body">

                                        <input type="hidden" id="base_url" value="{{ url('/') }}" />


            @if(Auth::user()->role_id==1 || Auth::user()->role_id==2)
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receivable-outstanding', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    
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

    if ($is_view_all_cust) {
        $selectedAccounts = ['view_all_cust'];
    }

    // Default select "View All Customers" on first load
    if (empty($selectedAccounts)) {
        $selectedAccounts = ['view_all_cust'];
    }
@endphp

                                    <div class="row gap-rows">
                                        <div class="col-1-5 mb-20">
                                            <div class="input-effect">
                                                <label>@lang('Account')</label>
                                                <select class="form-control js-example-basic-single" name="account_id[]" id="account_id" multiple>
                                                    <option value="view_all_cust" @if(in_array('view_all_cust', $selectedAccounts)) selected @endif>@lang('View All Customers')</option>
                                                    @foreach ($accounts_select as $val)
                                                        <option value="{{ @$val->id }}" @if(in_array($val->id, $selectedAccounts)) selected @endif>{{ @$val->account_name }}
                                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                                                ({{   @$val->account_code }})
                                                            @endif
                                                            
                                                            </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1  mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="form-check-label">
                                                        <label>@lang('As of Date')</label>
                                                        <input class="form-control date-picker" id="till_date" type="text" name="till_date" value="{{ @App\SysHelper::normalizeToDmy($till_date) }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                                             
<!--                     
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Doc No</label>
                                            <input class="form-control" id="transaction_no" type="text" value="{{ @$ctrl_doc_no }}" autocomplete="off" name="transaction_no" >
                                        </div>
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Deal ID</label>
                                            <input class="form-control" id="deal_id" type="text" value="{{ @$ctrl_deal_id }}" autocomplete="off" name="deal_id" >
                                        </div>
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Amount</label>
                                            <input class="form-control" name="amount" id="amount" value="{{ @$ctrl_amount }}" />
                                        </div> -->

                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Sales Person</label>
                                            <select class="form-control js-example-basic-single" name="sales_person[]" id="sales_person" multiple>
                                                <option value="">-Select-</option>
                                                @foreach ($sales_person_list as $sp)
                                                    <option value="{{ $sp->user_id }}" @if(in_array($sp->user_id, (array) @$ctrl_sales_person)) selected @endif> {{ $sp->full_name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-1  mb-2">
                                            <label for="" class="form-check-label">Over Due</label>
                                            <select class="form-control js-example-basic-single" name="overdue" id="overdue">
                                                <option value="" @if(@$ctrl_overdue == "") selected @endif>-Select-</option>
                                                <option value="0" @if(@$ctrl_overdue == "0") selected @endif> >0 </option>
                                                <option value="30" @if(@$ctrl_overdue == "30") selected @endif> 0-30 </option>
                                                <option value="60" @if(@$ctrl_overdue == "60") selected @endif> 31-60</option>
                                                <option value="90" @if(@$ctrl_overdue == "90") selected @endif> 61-90 </option>
                                                <option value="90+" @if(@$ctrl_overdue == '90+') selected @endif> >90 </option>
                                            </select>
                                        </div>
                                    
                                        <div class="col-1  mb-2">
                                            <label for="" class="form-check-label">Ageing</label>
                                            <select class="form-control js-example-basic-single" name="ageing" id="ageing">
                                                <option value="" @if(@$ctrl_ageing == "") selected @endif>-Select-</option>
                                                <option value="0" @if(@$ctrl_ageing == "0") selected @endif>0-30</option>
                                                <option value="30" @if(@$ctrl_ageing == "30") selected @endif>31-60</option>
                                                <option value="60" @if(@$ctrl_ageing == "60") selected @endif>61-90</option>
                                                <option value="90+" @if(@$ctrl_ageing == '90+') selected @endif> >90 </option>
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
                                                <option value="grn" @if(@$ctrl_list_option == 'grn') selected @endif>GRN</option>
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

                                        <div class="col-1 mt-4" >
                                            <button class="btn btn-light" type="submit">
                                                <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                            </button>
                                        </div>
                                    </div>
    
                                    {{ Form::close() }}
            @endif

                                </div>
                            </div>

                            
                            <div class="card mb-3">
                                <div class="card-body">



                  
            <div class="accordion" id="accordionExample">
                  @if(count($data_all)>0)
                  <?php $no=1; $all_total=0; $all_sivno_count=0; $all_overdue=0; $all_0_30=0; $all_31_60=0; $all_61_90=0; $all_90_above=0; ?>
                  @foreach($data_all as $data)

 

                  <?php $aname = $accounts->where('id',$data[0]->account_id)->first();                  
                  $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code); ?>
                  
                  @php
                      $hideBasicColumns = !empty($ctrl_basic_search);
                  @endphp

                  @if($ctrl_list_option == 'pdc')
                  <?php $pdc_1 = !empty($list_of_unadjusted_pdc) ? $list_of_unadjusted_pdc->where('account_id',$aname->id) : []; ?>
                  <?php $pdc_2 = !empty($list_of_adjusted_pdc) ? $list_of_adjusted_pdc->where('account_id',$aname->id) : []; ?>

         

                  @if(count($pdc_1)>0 || count($pdc_2)>0)
                   
                  @else
                     @continue
                  @endif
                  @endif



                  <?php
                  if(count($data)>0){
                    $a1 = clone $data_adjestment_all;
                    $a2 = clone $data_receipt_all;
                    $a3 = clone $data_receipt2_all;
                    $a4 = clone $data_receipt3_all;
                    $a5 = clone $data_return_all;
                    $a6 = clone $data_receipt_opb;

                    $data_adjestment = $a1->wherein('srn_no',$data->pluck("transaction_no"));

                    $data_receipt = $a2->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                    
                    $data_receipt2 = $a3->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();

                    $data_receipt3 = $a4->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                    
                    $data_receipt6 = $a6->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();

                    $data_return = $a5->where('customer',$data[0]->account_id)->wherein('srn_no',$data->pluck("transaction_no"))->get();

                  ?>
                
                  
                  
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
                          
                         <th class="">

<div style="display:flex; justify-content:space-between; align-items:center; width:100%;">

    <!-- LEFT SIDE -->
    <div>
        <a type="button"
           data-bs-toggle="collapse"
           data-bs-target="#collapse{{ $aname->id }}"
           aria-expanded="false"
           aria-controls="collapse{{ $aname->id }}"
        
           >

           <b style="font-size:13px"> {{ $aname->account_name }} </b>
            
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
<style>
     .table.table-hover tbody tr td {
    border-bottom-color: #f0f1f3;
   
}

    </style>

                          <th class="text-end" width="100px"><label class="main_sum" id="sum_{{ $aname->id }}"></label></th>
                      </tr>
                    </thead>
                </table>
                
                <div id="collapse{{ $aname->id }}" class="collapse" data-parent="#accordionExample">  {{-- display: none; --}}
                <table class="table sub_table table-hover" id="long-list" style="border: solid 1px #e3e6f0; width:100%; table-layout:fixed;">
                    
            
                    <thead>
                        
                      <tr>
                        <th class="text-center" style="width:6%">Deal ID</th>
                          <th class="text-center" style="width:6%">Inv Date</th>
                          <th class="text-center" style="width:7%">Inv No</th>
                          <th class="text-center" style="width:5%">LPO No</th>
                          <th class="text-end" style="width:6%">Amount</th>
                          <th class="text-end" style="width:5%">Adjustments</th>
                          <th class="text-end" style="width:5%">Balance</th>
                          <th class="text-end" style="width:5%">Total Balance</th>
                          <th class="text-center" style="width:6%">Due Date</th>
                          <th class="text-center" style="width:4%">Over Due</th>
                          @if(!$hideBasicColumns)
                            <th class="text-center" style="width:5%">0-30</th>
                            <th class="text-center" style="width:5%">31-60</th>
                            <th class="text-center" style="width:5%">61-90</th>
                            <th class="text-center" style="width:5%">>90</th>
                          @endif
                            <th class="text-start" style="width:8%">Sales Person</th>
                            <th class="text-start" style="width:6%">Payment Terms</th>
                          @if(!$hideBasicColumns)
                          <th class="text-center hidecol_{{ $aname->id }}" style="width:6%">Receipt Date</th>
                          <th class="text-start hidecol_{{ $aname->id }}" style="width:6%">Receipt No</th>
                          @endif

                      </tr>
                    </thead>
                
                    
                    <tbody>





                    <?php
                         $ats=Array();   
                         $k=0;
                         $row_count_1 = 0;
                         foreach ($data as $dt){
                           
                            $DueData =  App\SysHelper::get_due_date_sales_invoice($dt->transaction_no,$dt->transaction_date); 
                       
                           

                            if($overdue != 999999 && $ageing != 99999){    
                                if($ageing <0 && $DueData[1] <0 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=0 && $ageing <31 && $DueData[1] <0  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >30 && $ageing <61 &&  $DueData[1] >=0 && $DueData[1] <31 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=60 && $ageing <=90 &&  $DueData[1] >30 && $DueData[1] <61  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                                if($ageing >=90 &&   $DueData[1] >60 && $DueData[1] <90  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                                
                                
                            }

                            if($overdue != 999999 && $ageing == 99999){    
                                if(  $DueData[1] < $overdue ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                            }

                            if($ageing != 99999 && $overdue == 999999){
                             
                                if($ageing <0 && $DueData[1] <0 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=0 && $ageing <31 && $DueData[1] <0  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=30 && $ageing <61 &&  $DueData[1] >=0 && $DueData[1] <31 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=60 && $ageing <=90 &&  $DueData[1] >30 && $DueData[1] <61  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                                if($ageing >=90 &&   $DueData[1] >60 && $DueData[1] <90  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                              
                            }  

                         }
                         //if(  $ageing != 99999 ||  $overdue != 999999 )
                         //   $data=$ats;
                  
                    ?>
                        @php
                            $adjustments = 0;
                            $b=0;
                            $grand_debit_amount=0; 
                        $grand_paid=0;
                        $grand_balance=0;
                        $grand_total_balance=0;
                        $gtot1=0;$gtot2=0;$gtot3=0;$gtot4=0;
                        @endphp
                        
                        @if (count($data)>0)
                        @php $sum_b=0; @endphp
                        @foreach ($data as $dt)
                        @php
                        $adjustments = 0;
                        $receipt_date='';
                        $doc_number='';
                        $cheque_number='';
                        $bank_name='';
                        $bi_amount=0;
                        $bi_amount2=0;
                        $bi_amount3=0;
                        $bi_amount4=0;
                        $bi_amount6=0;
                        $paid=0;
                        @endphp
                        @php
                            $adjustments = $data_adjestment->where('srn_no',$dt->transaction_no)->max('paid_amount');
                            $receipt = $data_receipt->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt)>0){
                                foreach($receipt as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->receipt_date)).',';
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
                            
                            $receipt2 = $data_receipt2->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt2)>0){
                                foreach($receipt2 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount2 += $p->bi_amount;
                                }
                            }
                            
                            $receipt3 = $data_receipt3->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt3)>0){
                                foreach($receipt3 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount3 += $p->bi_amount;
                                }
                            }

                            $receipt4 = $data_return->where('siv_no',$dt->transaction_no);
                            if(count($receipt4)>0){
                                foreach($receipt4 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount4 += $p->paid_amount;
                                }
                            }

                            $receipt6 = $data_receipt6->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt6)>0){
                                foreach($receipt6 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->receipt_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount6 += $p->bi_amount;
                                }
                            }

                            $paid += ($adjustments+$bi_amount+$bi_amount2+$bi_amount6)-($bi_amount3+$bi_amount4);
                            
                            
                            $deal_id="";
                            $deal_code="";
                            $lpo_no="";
                            $sales_person="";
                            $deal_track_id=0;
                            $payment_terms="";
                            $duedate="";
                            //$deal = @App\SysHelper::get_deal_detail_for_receivable_outstanding($dt->transaction_no);
                            $deal = @App\SysHelper::get_deal_track_detail_for_receivable_outstanding($dt->transaction_no);
                            $lpono = @App\SysHelper::get_sales_invoice_details($dt->transaction_no);
                            if(isset($deal) && $deal != ""){
                                $deal_id=$deal->id;
                                $deal_code=$deal->code;
                                $sales_person=$deal->full_name;
                                $deal_track_id=$deal->track_id;
                            }
                            if ($dt->transaction_type=="opbinvoice"){
                                if(count($opbinvoice)>0){
                                $lpo_no = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('po_no')->first();
                                $deal_code = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('deal_id')->first();
                                $payment_terms = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('payment_terms')->first();
                                $duedate = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('due_date')->first();
                                }
                            }else{
                                if(isset($lpono) && $lpono != ""){
                                    $lpo_no=$lpono->lpo_number;
                                }
                            }
                        @endphp


                        <?php 
                        if($dt->debit_amount != $paid){
                            $grand_debit_amount+=$dt->debit_amount;
                            $grand_paid+=$paid;
                            $grand_balance+=$dt->debit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }
                        if(($dt->credit_amount)>0){
                            //if(!str_contains($dt->transaction_no,'SR')){
                            $grand_debit_amount-=$dt->credit_amount;
                            $grand_paid+=$dt->credit_amount;
                            //}
                            //$grand_paid+=$paid;
                            //$grand_balance+=$dt->debit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }
                        
                        ?>  
                        

                        <?php $is_hide=0;  $is_hide2=0; 
                        if(str_contains($dt->transaction_no,'SR')){
                        if($dt->credit_amount >= $paid){

                        $is_hide2=1;
                        }} 
                        
                        if(str_contains($dt->transaction_no,'SI')){
                            if(abs($dt->debit_amount) == abs($paid)){
                                $is_hide2=1;
                            }
                        }

                        ?>

                        
                        

                        @if(((@App\SysHelper::com_curr_format($dt->debit_amount,2,'.','') != @App\SysHelper::com_curr_format($paid,2,'.','')) || (@App\SysHelper::com_curr_format($dt->credit_amount,2,'.',''))>0) && $is_hide2 == 0)
                        
                        <tr>
                        <td class="text-center">
                            @php
                                 $row_count_1++;
                            @endphp
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $deal_code }}
                                
                                @else
                                <a href="{{url('crm-deal-track-approval-list/'.$deal_track_id)}}" target="_blank">{{ $deal_code }}</a><input type="hidden" id="inv_e_deal_code_{{ $dt->transaction_no }}" value="{{ $deal_code }}" /></td>
                                @endif
                            <td class="text-center">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}<input type="hidden" id="inv_e_doc_date_{{ $dt->transaction_no }}" value="{{ date('d/m/Y', strtotime($dt->transaction_date)) }}" /></td>
                            <td class="text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $dt->transaction_no }}
                                @else
                                <a href="{{url('get-url-sales-invoice/'.$dt->transaction_no)}}" target="_blank">{{ $dt->transaction_no }}</a><input type="hidden" id="inv_e_doc_no_{{ $dt->transaction_no }}" value="{{ $dt->transaction_no }}" /></td>
                                @endif <?php $all_sivno_count++ ?>
                            <td class="text-center">{{ $lpo_no }}<input type="hidden" id="inv_e_lpo_no_{{ $dt->transaction_no }}" value="{{ $lpo_no }}" /></td>
                            

                            
                            <td class="text-end">@if(str_contains($dt->transaction_no,'SR')) - {{ @App\SysHelper::com_curr_format($dt->credit_amount,2,'.',',') }}
                                <input type="hidden" id="inv_e_amount_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($dt->credit_amount,2,'.',',') }}" />
                                @else  {{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }} 
                                <input type="hidden" id="inv_e_amount_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }}" />
                                @endif</td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($paid,2,'.',',') }}<input type="hidden" id="inv_e_adjustment_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($paid,2,'.',',') }}" /></td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}
                                
                                @php 
                                if(str_contains($dt->transaction_no,'SR')){
                                    if($dt->credit_amount >= $paid){
                                    $b -= $dt->credit_amount;
                                    }
                                } else{ $b += $dt->debit_amount-abs($paid); } @endphp
                            
                            </td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($b,2,'.',',') }}</td>

                            @php $sum_b += $dt->debit_amount-abs($paid); $all_total += $dt->debit_amount-abs($paid); @endphp
                            <input type="hidden" class="inv_e_total" value="{{ $dt->debit_amount-abs($paid) }}" />
                            <script>
                                set_total({{ $aname->id }},{{ $sum_b }});
                            </script>

                            
                           
                           
                            @php                            
                            if ($dt->transaction_type=="opbinvoice"){
                                $DueData =  @App\SysHelper::get_due_date_invoice_opbinvoice($dt->transaction_no,$duedate,$payment_terms);
                            } else {
                               $DueData =  @App\SysHelper::get_due_date_sales_invoice($dt->transaction_no,$dt->transaction_date);
                            }                            
                            @endphp

                            
                            <td class="text-center">{{ $DueData[0] }} </td>
                            <?php 
                            if($DueData[1] >0){ ?>
                            <td class="text-center" style="color:red">{{ $DueData[1] }}</td>
                            <script>
                                if ($('#sum_{{ $aname->id }}').css('color') === 'red') { // red
                                    $('#sum_{{ $aname->id }}').css('color', 'red');
                                } else {
                                    $('#sum_{{ $aname->id }}').css('color', 'blue');
                                }
                            </script>
                            <?php } else { ?>

                            <td class="text-center">{{ $DueData[1] }} <?php $all_overdue += $DueData[1]; ?></td>
                            <?php }  ?>
                            <?php 
                            if($DueData[3] ==1)	  {
                                $gtot1+=$dt->debit_amount-abs($paid);
                                $all_0_30 += $dt->debit_amount-abs($paid);
                            ?><input type="hidden" class="inv_all_0_30" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }
                            if($DueData[3] ==2)	  {
                                $gtot2+=$dt->debit_amount-abs($paid);
                                $all_31_60 += $dt->debit_amount-abs($paid);                                
                            ?><input type="hidden" class="inv_all_31_60" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }
                            if($DueData[3] ==3)	  {
                                $gtot3+=$dt->debit_amount-abs($paid);
                                $all_61_90 += $dt->debit_amount-abs($paid);
                            ?><input type="hidden" class="inv_all_61_90" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }
                            if($DueData[3] ==4)	  {
                                $gtot4+=$dt->debit_amount-abs($paid);
                                $all_90_above += $dt->debit_amount-abs($paid);
                            ?><input type="hidden" class="inv_all_90_above" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }                                   

                            ?>
                            

@if(!$hideBasicColumns)
                            @if($DueData[3] ==1)                            
                            <td class="text-end" >{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="text-end">&nbsp;</td>
                            @endif
                            @if($DueData[3] ==2)	                            
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="text-end">&nbsp;</td>
                            @endif
                            @if($DueData[3] ==3)	                            
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="text-end">&nbsp;</td>
                            @endif	    
                            @if($DueData[3] ==4)	                            
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="text-end">&nbsp;</td>
                            @endif

                             <td class="text-start">{{ $sales_person }}</td>
                            <td class="text-start">{{ $DueData[2] }}</td>
                            <td class="text-center hidecol_{{ $aname->id }}">{{ rtrim($receipt_date, ',') }} </td>
                             <td class="text-center hidecol_{{ $aname->id }}">@foreach(explode(',', rtrim($doc_number, ',')) as $doc)
    <a href="{{ url('get-url-receipt/' . trim($doc)) }}" target="_blank">
        {{ trim($doc) }}
    </a>@if(!$loop->last), @endif
@endforeach</td>
                          @else
                            <td class="text-start">{{ $sales_person }}</td>
                            <td class="text-start">{{ $DueData[2] }}</td>
                          @endif


                            {{--  <td class="text-center">{{ rtrim($cheque_number, ',') }}</td>
                            <td class="text-center">{{ rtrim($bank_name, ',') }}</td>  --}}
                        </tr>
                        @endif

                        @endforeach
                        @endif

                    {{--  @if($dt->debit_amount == $paid || count($receipt)==0)
                    <tr><td colspan="14" class="text-danger text-center">No Ouitstanding Found!</td></tr>
                    @endif  --}}


                    @if(($dt->sum('debit_amount') != $paid || ($dt->sum('credit_amount'))>0)  && $is_hide == 0)
                    
                    <tr>
                    <!-- <td class="text-center"><b>{{ $row_count_1 }}</b></td> -->
                    <td colspan="4"></td>
                        <td class="text-end"><b><?php echo   @App\SysHelper::com_curr_format($grand_debit_amount,2,'.',',')    ?> </b></td>
                        <td class="text-end"><b><?php echo  @App\SysHelper::com_curr_format($grand_paid,2,'.',',')   ?> </b></td>
                        <td class="text-end"><b><?php echo  @App\SysHelper::com_curr_format($grand_balance,2,'.',',')   ?> </b></td>
                        <td class="text-end"><b><?php echo  @App\SysHelper::com_curr_format($b,2,'.',',')   ?> </td>
         
                        @if(!$hideBasicColumns)
                            <td class="text-center" colspan="2">&nbsp </td>

                            <td class="text-end" ><b><?php echo  @App\SysHelper::com_curr_format($gtot1,2,'.',',')   ?></b> </td>
                            <td class="text-end" ><b><?php echo  @App\SysHelper::com_curr_format($gtot2,2,'.',',')   ?></b> </td>
                            <td class="text-end" ><b><?php echo  @App\SysHelper::com_curr_format($gtot3,2,'.',',')   ?> </b></td>
                            <td class="text-end" ><b><?php echo  @App\SysHelper::com_curr_format($gtot4,2,'.',',')   ?> </b></td>
                            <td class="text-center" colspan="2">&nbsp </td>
                            <td class="text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                            <td class="text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                        @endif
                       
                       
                    </tr>
                    @else
                
                    @if($list_option != "show")
                    <script>                        
                        {{--  $('#account_table'+{{ $aname->id }}).css('display','none');  --}}
                    </script>
                    @endif
                    <tr><td colspan="4"></td>
                        <td class="text-center"><b>0.00</b></td>
                        <td class="text-center"><b>0.00</b></td>
                        <td class="text-center"><b>0.00</b></td>
                        <td class="text-center"><b>0.00</b></td>
                     
                        <td class="text-center" colspan="4">&nbsp </td>
                       
                        <td class="text-center"><b>0.00</b></td>
                        <td class="text-center"><b>0.00</b></td>
                        <td class="text-center"><b>0.00</b></td>
                        <td class="text-center"><b>0.00</b></td>
                           <td class="text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                        <td class="text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                    </tr>
                    @endif
                    @if(count($receipt)==0)
                    <script>
                        $('.hidecol_'+{{ $aname->id }}).css('display','none');
                    </script>
                    @else
                    <script>
                        $('.hidecol_'+{{ $aname->id }}).css('display','');
                    </script>
                    @endif
                    
                    <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>
    
                    </tbody>
                  </table>


                  <?php $unadj_list = !empty($list_of_unadjusted) ? $list_of_unadjusted->where('account_id',$aname->id) : []; ?>

                  <?php $unadj_list_jv_to_jv = !empty($list_of_unadjusted_jv_to_jv) ? $list_of_unadjusted_jv_to_jv->where('account_id',$aname->id) : []; ?>

               
                 
                  <!-- no automatic expansion; panels open only in consolidated view -->
                  <br>

 <?php $pdc = !empty($list_of_adjusted_pdc) ? $list_of_adjusted_pdc->where('account_id',$aname->id) : []; ?>
 <?php $pdc2 = !empty($list_of_unadjusted_pdc) ? $list_of_unadjusted_pdc->where('account_id',$aname->id) : []; ?></tr>

                  @if (count($pdc)>0 || count($pdc2)>0)
                  <b>List of PDC:-</b>
                  <table class="table sub_table table-hover" id="long-list" style="border: solid 1px #e3e6f0; width:100%; table-layout:fixed;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5.5%">Deal ID</th>
                            <th class="text-center" style="width:6%">Doc Date</th>
                            <th class="text-center" style="width:6.5%">Receipt No</th>
                            <th class="text-end" style="width:9.5%">Amount</th>
                            <th class="text-end" style="width:5%">Adjusted</th>
                            <th class="text-center" style="width:8%">Cheque Date</th>
                            <th class="text-center" style="width:9%">Cheque No</th>
                            <th class="text-center" style="width:8%">Receipt Date</th>
                            <th class="text-center" style="width:11%">Invoice Adjusted</th>
                            <th class="text-start" style="width:24%">Remarks</th>
                            <th class="text-center" style="width:2%"></th>
                        </tr>
                    </thead>
                    <tbody>
                         @php
                            $row_count_2 = 0;
                        @endphp
                        @if (count($pdc)>0)
                       
                        @foreach ($pdc as $p)
                        @php
                            $row_count_2++;
                        @endphp
                        @php
                            if($p->doc_number){
                               $deal_id = @App\SysReceipt::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                            }
                        @endphp
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="text-center"> 
                                @if (@App\SysHelper::get_code_from_dealid($deal_id) != 'Without Deal')
                                    <a href="{{url('crm-deal-track-approval-list/'.$deal_id)}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                    {{ @App\SysHelper::get_code_from_dealid($deal_id) }}
                                @endif
                                
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="text-center"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                            <td class="text-end">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="text-center">{{ $p->cheque_number }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="text-center">
                                <a style="cursor: pointer;" onclick="row_det_fun('{{ $p->doc_number }}','{{ $p->bi_doc_no }}')">{{ $p->bi_doc_no }}</a>
                            </td>
                            
                            <td class="">{{ $p->remarks }}</td>
                            <td class="text-center"><a class="text-danger text-center" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ @App\SysHelper::normalizeToDmy($p->receipt_date) }}',3)"><i class="ico icon-outline-pen-new-square" style="font-size: 16px" aria-hidden="true"></i></a></td>
                            
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
                                                <th class="">Doc Date</th>
                                                <th class="">Doc No</th>
                                                <th class="">LPO No</th>
                                                <th class="">Deal ID</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-end">Adjustments</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                         
                        @if (count($pdc2)>0)
                         @foreach ($pdc2 as $p)
                          @php
                            $row_count_2++;
                        @endphp
                          @php
                            if($p->doc_number){
                               $deal_id = @App\SysReceipt::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                            }
                        @endphp
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="text-center">
                                 @if (@App\SysHelper::get_code_from_dealid($deal_id)!= 'Without Deal')
                                 
                                    <a href="{{url('crm-deal-track-approval-list/'.$deal_id)}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                   {{@App\SysHelper::get_code_from_dealid($deal_id)}}
                                @endif
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="text-center"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="text-end">0.00</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="text-center">{{ $p->cheque_number }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="" colspan="2">{{ $p->remarks }}</td>
                            <td class="text-center"><a class="text-danger text-center" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ @App\SysHelper::normalizeToDmy($p->receipt_date) }}',2)"><i class="ico icon-outline-pen-new-square" style="font-size: 16px" aria-hidden="true"></i></a></td>
                        </tr>
                        @endforeach
                        @endif

                        @php
                        // also count pdc2 records in total count and sum
                        $pdcCount = count($pdc) + count($pdc2);
                        $pdcSumAmount = $pdc->sum('amount') + $pdc2->sum('amount');
                        $pdcSumAdjusted = $pdc->sum('adj_amount') + $pdc2->sum('adj_amount');
                    
                        @endphp
                        <tr class="">
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

                  <br>
   @if (count($unadj_list)>0 || count($unadj_list_jv_to_jv)>0)
                  <b>List of Unadjusted balance:-</b>
                  <table class="table sub_table table-hover" id="long-list" style="border: solid 1px #e3e6f0; width:100%; table-layout:fixed;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:6%">Deal ID</th>
                            <th class="text-center" style="width:6%">Doc Date</th>
                            <th class="text-center" style="width:7%">Receipt No</th>
                            <th class="text-end" style="width:11%">Amount</th>
                            <th class="text-start" style="width:73%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($unadj_list)>0)
                        @foreach ($unadj_list as $p)
                          @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BR', 'CR']))
                               @php
                                    if($p->doc_number){
                                    $deal_id = @App\SysReceipt::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                    }
                                @endphp
                               
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                             @php
                                    if($p->doc_number){
                                    $deal_id = @App\SysJournalVoucher::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                    }
                                @endphp
                                
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['SR']))
                                @php
                                    if($p->doc_number){
                                    $deal_id = @App\SysSalesReturn::where('doc_number',$p->doc_number)->pluck('deal_id')->first();
                                    }
                                @endphp
                            @endif
                        <tr>
                            <td class="text-center"> @if (@App\SysHelper::get_code_from_dealid($deal_id)!= 'Without Deal')
                                 
                                    <a href="{{url('crm-deal-track-approval-list/'.$deal_id)}}" target="_blank">{{ @App\SysHelper::get_code_from_dealid($deal_id) }}</a>
                                @else
                                   {{@App\SysHelper::get_code_from_dealid($deal_id)}}
                                @endif</td>
                            <td class="">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BR', 'CR']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-receipt/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['SR']))
                                <td class="text-center">
                                    <a href="{{ url('get-url-sales-return/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
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
                        <tr>
                            <td class="">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="">
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
                            $unadjCount = $unadjAll->count();
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
                  <br>

                  <!-- <?php $pdc = !empty($list_of_unadjusted_pdc) ? $list_of_unadjusted_pdc->where('account_id',$aname->id) : []; ?>
                 
                  @if (count($pdc)>0)
                  <b>List of Unadjusted PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="">Deal ID</th>
                            <th class="">Doc Date</th>
                            <th class="">Receipt No</th>
                            <th class="text-end">Amount</th>
                            <th class="">Cheque Date</th>
                            <th class="">Cheque No</th>
                            <th class="">Receipt Date</th>
                            <th class="">Remarks</th>
                            <th class=""></th>
                        </tr>
                    </thead>:
                    <tbody>
                       
                    </tbody>
                  </table>
                  @endif -->

                 

                </div>
                

                
                  
                  <?php } ?>

                  <?php
                    $record = $opb_balance_amount->where('account_id', $aname->id)->first();
                    $opb = $record ? $record->opb_amount : 0;
                    $opb = @App\SysHelper::com_curr_format($opb,2,'.','')
                  ?>
                <script>
                    check_total({{ $aname->id }},{{ $opb }})
                </script>
                  @endforeach
                  @if(@$ctrl_list_option == 'consolidated')
                    <script>
                        // expand all accordion panels when consolidated view selected
                        $('#accordionExample .collapse').addClass('show');
                    </script>
                  @endif
                 
                  <table class="table" style="border: solid 1px #e3e6f0;">
                    <thead>
                        <tr>
                            <th class="text-center" width="168px"></th>
                            <th class="text-center" width="70px"></th>
                            <th class="text-center" width="384px"></th>
                            <th class="text-end" width="85px"></th>
                            <th class="text-center" width="338px"></th>
                            <th class="text-center" width="105px"></th>
                            <th class="text-center" width="114px"></th>
                            <th class="text-center" width="103px"><b>Total</b></th>
                            <th class="text-center" width="103px"><b><label class="fw-bold" id="lbl_all_sivno_count"></label></b></th>
                            <th class="text-end" width="102px"><b><label class="fw-bold" id="lbl_main_sum_total"></label></b></th>
                        </tr>
                    </thead>
                  </table>
                  @else
                  <table class="table" style="border: solid 1px #e3e6f0;">
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
                                    {{-- ******************** --}}

                                </div>
                            </div>

            

        </div>
    </div>
</div>


      
<script>
    $(document).ready(function () {

      // when one accordion panel opens, close any other that is showing
        $('#accordionExample').on('show.bs.collapse', function (e) {
            $('#accordionExample .collapse.show').not(e.target).collapse('hide');
        });

        $('#receivableOutstandingSearch').on('input', function () {
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

        var ctrlOption = '{{ @$ctrl_list_option }}';
        $('label.main_sum').each(function () {
            var value = $(this).text().trim();
            var $mainTable = $(this).closest('.main_table');
            var color = $(this).css('color');
            var mainTableId = $mainTable.attr('id') || '';
            var anameId = mainTableId.replace('account_table', '');

            // if unadjusted_balance filter is active, require at least one unadjusted row
            if (ctrlOption === 'unadjusted_balance') {
                // look for the unadjusted list section inside this account's collapse
                var $section = $('#collapse' + anameId);
                var hasUnadj = $section.find('> table').filter(function() {
                    // tables with "Unadjusted balance" header are above
                    return $(this).prev('b').text().trim().toLowerCase().startsWith('list of unadjusted');
                }).find('tbody tr').length > 0;
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

            if (!value || value === '0') {
                $mainTable.hide();
                $('#collapse' + anameId).hide();
            } else {
                $mainTable.show(); // optional if hidden by default
                // do not unconditionally show collapse; leave it collapsed unless consolidated
                visibleCount++;

                // Extract ID from main table to locate sub_table
                var mainTableId = $mainTable.attr('id'); // e.g., "account_table23"
                var anameId = mainTableId.replace('account_table', ''); // get "23"

                // Now find the corresponding .sub_table inside the collapse div
                var $subTable = $('#collapse' + anameId).find('.sub_table');

                // Get the .inv_e_total value
                var invValue = $subTable.find('.inv_e_total').val();
                var numericValue = parseFloat(invValue) || 0;
                totalInv += numericValue;
                
                var all_0_30 = $subTable.find('.inv_all_0_30').val();
                var all_0_30 = parseFloat(all_0_30) || 0;
                totalall_0_30 += all_0_30;
                
                var all_31_60 = $subTable.find('.inv_all_31_60').val();
                var all_31_60 = parseFloat(all_31_60) || 0;
                totalall_31_60 += all_31_60;
                
                var all_61_90 = $subTable.find('.inv_all_61_90').val();
                var all_61_90 = parseFloat(all_61_90) || 0;
                totalall_61_90 += all_61_90;
                
                // the .main_sum label sits in the header row (this element), not inside the subtable
                var all_90_above = formatAmountToNumber($(this).text());
                totalall_90_above += all_90_above;
            }
        });

        $('#lbl_all_sivno_count').text(visibleCount);
        $('#lbl_all_total').text(formatAmount(totalInv.toFixed(2)));
        $('#lbl_all_total_0_30').text(formatAmount(totalall_0_30.toFixed(2)));
        $('#lbl_all_total_31_60').text(formatAmount(totalall_31_60.toFixed(2)));
        $('#lbl_all_total_61_90').text(formatAmount(totalall_61_90.toFixed(2)));
        $('#lbl_all_total_90_above').text(formatAmount(totalall_90_above.toFixed(2)));
        // display the sum of all visible .main_sum totals in summary row
        $('#lbl_main_sum_total').text(formatAmount(totalall_90_above.toFixed(2)));

        // After all data is processed, expand accordions for consolidated view
        // but only those whose header row is still visible (non-empty)
        if (ctrlOption === 'consolidated') {
            document.querySelectorAll('#accordionExample .collapse').forEach(function (el) {
                var $mainTbl = $(el).prev('table.main_table');
                if ($mainTbl.length && $mainTbl.is(':visible')) {
                    var bsCollapse = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                    bsCollapse.show();
                }
            });
        }
        
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

    

    
        <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

        
@endsection





<!-- Modal Payment Follow-up Remark -->
<div class="modal side-panel fade" id="ModalTrackComment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel" style="font-size: 14px">Payment Follow-up Remark</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'outstanding_comment_save','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-edit']) }}

            <div class="modal-body">

               
                    <div class="" id="customerInfoDisplay">
                        <!-- customer details will be injected here -->
                    </div>
             
               
                <div class="row">
                    <div id="message"></div>

                    <!-- <div class="col-lg-12 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <input type="hidden" id="iddetail" name="id_detail">
                                  
                                    <textarea   id="comment" name="comment" class="form-control"  cols="10" rows="3" ></textarea>
                               
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- added date and attachment fields -->
                    <!-- <div class="col-lg-4 mb-12">
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
                    </div> -->

                </div>

                 <div class="row mt-2">
    <div class="col-12 d-flex align-items-center">
        <b class="me-2">Previous Remarks</b>
     
            <i class="ico icon-outline-add-square text-success" data-bs-toggle="modal" data-bs-target="#ModalTrackCommentInputs" style="font-size:14px"></i>
       
    </div>
    <div class="col-lg-12" id="mydiv" style="height: auto; max-height: 300px; overflow-y: scroll;">

    </div>
</div>
                

            </div>

     
            

            {{ Form::close() }}

        </div>
    </div>
</div>
<!-- Modal Payment Follow-up Remark -->




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
<div class="modal side-panel fade" id="ModalPDCUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">PDC Update</h5>
          						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        </div>
        <div class="modal-body mt-1">                
            <div class="row">
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <input type="hidden" id="pdc_receipt_doc_no">
                                <label class="form-label">@lang('Receipt Date')<span></span></label>
                                <input class="form-control date-picker" id="pdc_receipt_doc_date" type="text" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="form-label">@lang('Status')<span></span></label>
                                <select class="form-control js-example-basic-single" id="pdc_receipt_status">
                                    <option value="2">Received & Removed</option>
                                    <option value="1">Received</option>
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
    function pdc_update(id,dat,status) {
        $('#pdc_receipt_doc_no').val(id);
        $('#pdc_receipt_doc_date').val(dat);
        $('#pdc_status').val(status);
        console.log(status);
        $('#ModalPDCUpdate').modal('show');
    }

    function pdc_update_save() {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('update-receivable-pdc') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id: $('#pdc_receipt_doc_no').val(),
                doc_date: $('#pdc_receipt_doc_date').val(),
                status: $('#pdc_receipt_status').val(),
                pdc_status: $('#pdc_status').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);

                if(dataResult['data']=="SUCCESS"){
                  
                    var a = $('#pdc_receipt_doc_no').val();
                    $('#btn_pdc_received_'+a).css("background-color", "#f6c23e");
                    $('#btn_pdc_received_'+a).text("Updated");
                    if($('#pdc_receipt_status').val()==2){
                        $('#row_pdc_received_'+a).css("display", "none");
                    }
                    $('#btnSubmitPDC_close').click();   
                    location.reload()                 
                } else { alert("Error!!"); }

                $("#loading_bg").css("display", "none");
            }
        });
    }





</script>


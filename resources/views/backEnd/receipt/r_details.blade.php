<style>
    .pdfarea header {
        position: fixed;
        left: 20px;
        top: -50px;
        right: 20px;
        height: 80px;
        background-color: white;
        text-align: center;
        border-bottom: solid 0px #808080;
        color: #555555;
    }

   

    .pdfarea footer .page:after {
        content: counter(page, upper-roman);
    }

    .pdfarea {
        font-family: Verdana, sans-serif;
        font-size: 12px;
        color: #555555;
        background-image: url('{!! asset("public/" . $company->pdf_watermark . "") !!}');
    }

    .pdfarea th,
    .pdfarea td {
        padding: 5px 5px;
    }

    .tdd {
        border: dashed 1px #9e9e9e;
        border-width: 0 0 1px 0;
    }

    b {
        font-size: 14px;
    }

    .m1 table {
        border: 0px solid #9e9e9e;
    }

    .m1 td {
        border: 1px solid #9e9e9e;
    }

    .tmc ol {
        padding: 0px;
        margin: 0px;
    }

    .bottom_b {
        font-size: 12px;
    }

    .page-break {
        page-break-after: always;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .item-head-row {
        background: #2c2b6d;
        color: #ffffff;
    }

    .item-row {
        border-bottom: solid 1px #2c2b6d;
    }
</style>
    <?php try { ?>

        



    <div class="purchase-order-content-header sticky-top d-flex align-items-center justify-content-between gap-2" style="background-color: #f7f8fd">
        <div class="d-flex align-items-center gap-2">
            <h4 class="purchase-order-content-header-left">
                {{ $receipt->doc_number }}
            </h4>
            @if(isset($receipt->deal_id))
                {!! App\SysHelper::deal_pipeline($receipt->deal_id) !!}
            @endif
        </div>
        <div class="purchase-order-content-header-right">
          <a class="btn btn-light text-dark" href="{{url('receipt/'.$receipt->id.'/edit')}}">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </a>
            <a class="btn btn-light text-dark" href="{{url('receipt-add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('receipt/'.$receipt->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel</a></li>
                    <li><a class="dropdown-item" href="{{url('receipt/'.$receipt->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#" id="receiptAttachmentsDropdownBtn"><i class="ico icon-outline-document-medicine text-success"></i> Attachments</a></li>
                </ul>
            </div>
        </div>
        <input type="hidden" id="receipt_id" value="{{ $receipt->id }}">
    </div>
    <div class="card mb-3 card-min-height">
        <div class="card-body">
            <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                {{-- <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">heading</h5>
                </div> --}}
                <div class="row">
                    <div class="col-2 mb-2">&nbsp;</div>
                    <div class="col-8 mb-2 pdfarea" >
                        
                        {{-- ************* --}}
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
          <td align="left"><img style="margin-left: -12px;"  src="{{asset('public/'.@$company->company_logo)}}" width="200px"/></td>
          <td align="right"><b style="font-size: 30px; font-weight: 400;">Receipt Voucher</b></td>
      </tr>
  </table>
  <br />

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="55%" valign="top" style="line-height: 18px;">
            <b>{{@$company->company_name}}</b>
            <div>{!! nl2br($company->company_address) !!}</div>
            P: {{@$company->telephone}}, M: {{ @$company->mobile }}<br />
            E: {{@$company->email}}<br />
            TRN No: {{@$company->vat_number}}
          </td>
          <td>
          </td>
        </tr>
    </table>
    <br />
      @if($receipt->mode==1)
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Mode</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Date</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->doc_date)) }}</td>
        <td style="line-height: 18px;">{{ $receipt->doc_number }}</td>
        <td style="line-height: 18px;">Cash</td>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->receipt_date)) }}</td>
      </tr>
    </table>
      @else
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Mode</td>
        <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Through</td>
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->doc_date)) }}</td>
        <td style="line-height: 18px;">{{ $receipt->doc_number }}</td>
        <td style="line-height: 18px;">Bank</td>
        <td style="line-height: 18px;">
          @if($receipt->receipt_through == 1) Bank Transfer @endif
          @if($receipt->receipt_through == 2) CDC Cheque @endif
          @if($receipt->receipt_through == 3) PDC Cheque @endif
        </td>
      </tr>
      </table><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1" style="text-align: center;">
      <tr>
        <td style="line-height: 18px; font-weight:bold;">Receipt Date</td>
        <td style="line-height: 18px; font-weight:bold;">Cheque Date</td>
        @if($receipt->cheque_number !="")
        <td style="line-height: 18px; font-weight:bold;">Cheque Number</td>@endif
        @if($receipt->cheque_bank_name !="")
        <td style="line-height: 18px; font-weight:bold;">Bank Name</td>@endif
      </tr>
      <tr>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->receipt_date)) }}</td>
        <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->cheque_date)) }}</td>
        @if($receipt->cheque_number !="")
        <td style="line-height: 18px;">{{ $receipt->cheque_number }}</td>@endif
        @if($receipt->cheque_bank_name !="")
        <td style="line-height: 18px;">{{ $receipt->cheque_bank_name }}</td>@endif
      </tr>
      </table>
      @endif
    <br />
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr style="background: #2c2b6d; color: #ffffff;">
          <td style="width: 20px;">S.No</td>
          <td style="width: 530px;">Particulars</td>
          <td style="width: 50px; text-align: center;">Amount</td>
        </tr>
    </table>
        <?php
            $i=1;
            $sum=0;
        ?>
        @if(count($receipt_item)>0)
        @foreach ($receipt_item as $item)
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td style="width: 20px; border-bottom: solid 1px #2c2b6d;">{{$i}}. <?php $i++;?></td>
        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">{{ $item->accounts->account_name }} 
          @if($item->remarks !="")
            <br />{{ $item->remarks }}
          @endif

        
        </td>
          <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center;">{{ @App\SysHelper::com_curr_format(abs($item->debit_amount - $item->credit_amount),2,'.',',') }}</td>
            <?php            
            $sum += abs($item->debit_amount - $item->credit_amount);
            ?>
        </tr>
        </table>
        @endforeach
        @endif
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: left; width: 550px; font-weight: bold;">
            <?php echo ucwords(@App\SysHelper::convertAmountToWords($sum,$receipt->currency_name->r_code,$receipt->currency_name->p_code));?></td>
          <td style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold; width: 50px;">{{ @App\SysHelper::com_curr_format($sum,2,'.',',') }}</td>
        </tr>
      </table>
      <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom"></td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">Authorised Signature<br /><br /><br />{{@$company->company_name}}
          <br /><br />Printed on {{ $print }}</td>
        </tr>
      </table>  <footer>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        
        <tr>
          <td colspan="3" style="border: none; font-size: 10px;" align="right" valign="top">
            {{-- Page No <span style="" class="pagenum"></span> of {{@$po->doc_number}}</td> --}}
        </tr>
    </table>
    <img  src="{!! asset('public/'.$company->pdf_footer.'') !!}"  width="100%"/></td>
  </footer>
                        {{-- ************* --}}
                    </div>
                    <div class="col-2 mb-2">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>

    <?php /*
    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
        </h4>
        <div class="purchase-order-content-header-right">&nbsp;
            {{-- <button class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
            <button class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#dealcancelModal"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel Deal</a></li>
                    <li><a class="dropdown-item" href="quote.html"><i class="ico icon-outline-document-medicine text-success"></i> Generate Quote</a></li>
                    <li><a class="dropdown-item" href="#"><i class="ico icon-outline-pen-2 text-warning"></i> Add Pre-Sales Request</a></li>
                    <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-pen-2 text-warning"></i> Add Collaboration</a></li>
                    <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-pen-2 text-warning"></i> End User Details</a></li>
                </ul>
            </div> --}}
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">No details found</h5>
                </div>
                <div class="row">
                    <div class="col-12 mb-2">
                        No details found
                    </div>
                </div>
            </div>
        </div>
    </div> */ ?>
<!-- Receipt Attachments Modal -->
<div class="modal fade" id="receiptAttachmentsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="receiptAttachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptAttachmentsModalLabel">Receipt Attachments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row align-items-end" id="attachmentsUploadSection">
                    <div class="col-md-9">
                        <label class="form-label">Upload files</label>
                        <input type="file" id="receiptAttachmentsFiles" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" />
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="uploadReceiptAttachmentsBtn" class="btn btn-light">
                            Upload
                        </button>
                    </div>
                </div>

                <div id="receiptAttachmentsMessage" class="mb-2"></div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>File Name</th>
                                <th>Uploaded On</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="receiptAttachmentsList">
                            <tr><td colspan="4" class="text-center">No attachments yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function formatDMY(dateString) {
        var d = new Date(dateString);
        if (isNaN(d.getTime())) return '';
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var yyyy = d.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    function renderReceiptAttachments(attachments) {
        var $tbody = $('#receiptAttachmentsList').empty();
        if (!attachments || attachments.length === 0) {
            $tbody.html('<tr><td colspan="4" class="text-center">No attachments found.</td></tr>');
            return;
        }
        attachments.forEach(function (att, index) {
            var viewUrl = '{{ url("receipt/attachments") }}/' + att.id + '/download';
            var attachedDate = att.created_at ? formatDMY(att.created_at) : '';
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + $('<div>').text(att.file_name).html() + '</td>' +
                '<td>' + attachedDate + '</td>' +
                '<td class="text-center"><div class="d-flex justify-content-center align-items-center gap-1">' +
                    '<a href="' + viewUrl + '" target="_blank" class="btn btn-sm btn-light" title="View">' +
                        '<i class="ico icon-outline-eye" style="font-size:16px;"></i>' +
                    '</a>' +
                    '<button type="button" class="btn btn-sm btn-light text-danger delete-receipt-attachment-btn" data-id="' + att.id + '" title="Delete"><i class="ico icon-outline-trash-bin-trash" style="font-size:16px;"></i></button>' +
                '</div></td>' +
                '</tr>';
            $tbody.append(row);
        });
    }

    function fetchAndRenderReceiptAttachments(receiptId) {
        if (!receiptId || receiptId <= 0) {
            $('#receiptAttachmentsMessage').html('<div class="text-danger">Receipt not found.</div>');
            renderReceiptAttachments([]);
            return;
        }
        $('#receiptAttachmentsMessage').html('');
        $.get('{{ url("receipt") }}/' + receiptId + '/attachments', function (response) {
            if (response.success) {
                renderReceiptAttachments(response.attachments);
            } else {
                $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to load attachments.</div>');
            }
        }).fail(function () {
            $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to fetch attachments.</div>');
        });
    }

    $('#receiptAttachmentsDropdownBtn').on('click', function (e) {
        e.preventDefault();
        var receiptId = parseInt($('#receipt_id').val() || 0, 10);
        $('#receiptAttachmentsMessage').html('');
        $('#receiptAttachmentsFiles').val('');
        $('#receiptAttachmentsModal').modal('show');
        fetchAndRenderReceiptAttachments(receiptId);
    });

    $('#uploadReceiptAttachmentsBtn').on('click', function () {
        var receiptId = parseInt($('#receipt_id').val() || 0, 10);
        if (!receiptId || receiptId <= 0) {
            $('#receiptAttachmentsMessage').html('<div class="text-danger">Receipt not found.</div>');
            return;
        }

        var files = $('#receiptAttachmentsFiles')[0].files;
        if (!files.length) {
            $('#receiptAttachmentsMessage').html('<div class="text-warning">Please choose at least one file.</div>');
            return;
        }

        var formData = new FormData();
        formData.append('sys_receipt_id', receiptId);
        for (var i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
        $.ajax({
            url: '{{ url("receipt/attachments/upload") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                if (response.success) {
                    toastr.success('Attachments uploaded successfully.');
                    $('#receiptAttachmentsFiles').val('');
                    fetchAndRenderReceiptAttachments(receiptId);
                } else {
                    toastr.error(response.message || 'Upload failed.');
                }
            },
            error: function (xhr) {
                var err = 'Upload failed.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    err = Object.values(xhr.responseJSON.errors).map(function (v) { return v.join(', '); }).join(' | ');
                } else if (xhr.responseText) {
                    err = xhr.status + ' ' + xhr.statusText + ': ' + xhr.responseText;
                }
                $('#receiptAttachmentsMessage').html('<div class="text-danger">' + err + '</div>');
            }
        });
    });

    $(document).on('click', '.delete-receipt-attachment-btn', function () {
        var id = $(this).data('id');
        var receiptId = parseInt($('#receipt_id').val() || 0, 10);
        $('#receiptAttachmentsMessage').html('');
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
        $.ajax({
            url: '{{ url("receipt/attachments") }}/' + id + '/delete',
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                if (response.success) {
                    toastr.success('Attachment deleted.');
                    fetchAndRenderReceiptAttachments(receiptId);
                } else {
                    toastr.error('Unable to delete attachment.');
                    $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to delete attachment.</div>');
                }
            },
            error: function () {
                toastr.error('Unable to delete attachment.');
                $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to delete attachment.</div>');
            }
        });
    });
</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
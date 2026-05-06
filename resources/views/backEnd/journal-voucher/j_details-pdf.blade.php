<div class="modal-header">
    <h4 class="modal-title" id="po-doc-number">{{ $editData->doc_number }}</h4>

    <div class="d-flex align-items-center gap-2">

        <a href="{{ url('journalvoucher/' . $editData->id . '?jv_action=edit') }}" class="btn btn-light text-dark"
            style="font-size:12px;padding:0px 7px !important;min-height:19px">
            <i class="ico icon-outline-pen-2 text-success" style="font-size:15px"></i> Edit
        </a>

        <a href="{{ url('journalvoucher/' . $editData->id . '?jv_action=add') }}" class="btn btn-light text-dark"
            style="font-size:12px;padding:0px 7px !important;min-height:19px">
            <i class="ico icon-outline-add-square text-success" style="font-size:15px"></i> Add
        </a>




        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                style="font-size:12px;padding:0px 7px !important;min-height:19px" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu" style="font-size:15px"></i>
            </button>
            <ul class="dropdown-menu" style="">




                </li>
                <li><a class="dropdown-item d-flex align-items-center"
                        href="{{ url('journalvoucher/' . $editData->id . '/delete') }}"><i
                            class="ico icon-outline-close-square title-15 me-2"></i> Cancel</a></li>
                <li><a class="dropdown-item d-flex align-items-center"
                        href="{{ url('journalvoucher/' . $editData->id . '/download') }}"><i
                            class="ico  icon-bold-download-minimalistic title-15 me-2"></i> Download</a></li>
                <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal"
                        data-bs-target="#attachment_popup_win" onclick="view_attachment()"><i
                            class="ico icon-bold-paperclip title-15 me-2"></i> Attachment</a></li>


            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
</div>

<div class="modal-body m-0 p-0">
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
            background-image: url('{!! asset('public/backEnd/img/' . $company->pdf_watermark . '') !!}');
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


    <input type="hidden" id="doc_number" value="{{ $editData->doc_number }}">
    <input type="hidden" id="jv_id" value="{{ $editData->id }}">



    <div class="card mb-3 card-min-height">
        <div class="card-body">
            <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                {{-- <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">heading</h5>
                </div> --}}
                <div class="row">

                    <div class="col-12 mb-2 pdfarea">

                        {{-- ************* --}}
                        <div style="min-height: 800px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left"><img src="{{ asset(@$company->company_logo) }}" width="200px" />
                                    </td>
                                    <td align="right"><b style="font-size: 30px; font-weight: 400;">Journal Voucher</b>
                                    </td>
                                </tr>
                            </table>
                            <br />

                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="55%" valign="top" style="line-height: 18px;">
                                        <b>{{ @$company->company_name }}</b>
                                        <div>{!! nl2br($company->company_address) !!}</div>
                                        Phone: {{ @$company->telephone }}<br />
                                        Email: {{ @$company->email }}<br />
                                        TRN No: {{ @$company->vat_number }}
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1"
                                style="text-align: center;">
                                <tr>
                                    <td width="20%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
                                    <td width="20%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
                                    <td width="20%" style="line-height: 18px; font-weight:bold;">Amount</td>
                                    <td width="20%" style="line-height: 18px; font-weight:bold;">Currency</td>
                                    <td width="20%" style="line-height: 18px; font-weight:bold;">Created By</td>
                                </tr>
                                <tr>

                                    <td style="line-height: 18px;">
                                        {{ date('d/m/Y', strtotime(@$editData->doc_date)) }}</td>
                                    <td style="line-height: 18px;">{{ $editData->doc_number }}</td>
                                    <td style="line-height: 18px;">
                                        {{ @App\SysHelper::com_curr_format($editDataList->sum('debit_amount'), 2, '.', ',') }}
                                    </td>
                                    <td style="line-height: 18px;">{{ @$editData->currency_name->code }}</td>
                                    <td style="line-height: 18px;">{{ @$editData->createdby->full_name }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height: 18px; text-align: left;"><span
                                            style="line-height: 18px; font-weight:bold;">Remarks :</span>
                                        {{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}
                                    </td>
                                </tr>
                            </table>
                            <br />

                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr style="background: #2c2b6d; color: #ffffff;">
                                    <td style="width: 30px;">S.No</td>
                                    <td style="width: 200px;" class="text-center">Account</td>
                                    <td style="width: 100px; text-align: right;">Debit</td>
                                    <td style="width: 100px; text-align: right;">Credit</td>
                                    <td class="text-center">Narration</td>
                                    <td style="width: 100px;" class="text-center">Deal ID</td>
                                </tr>
                            </table>
                            <?php
                            $i = 1;
                            $sum_d = 0;
                            $sum_c = 0;
                            ?>
                            @if (count($editDataList) > 0)
                                @foreach ($editDataList as $item)
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="width: 30px; border-bottom: solid 1px #2c2b6d;">
                                                {{ $i }}. <?php $i++; ?></td>
                                            <td style="width: 200px; border-bottom: solid 1px #2c2b6d; font-size: 11px;"
                                                class="">{{ $item->accounts->account_name }}</td>
                                            <td
                                                style="width: 100px; border-bottom: solid 1px #2c2b6d; text-align: right;">
                                                {{ @App\SysHelper::com_curr_format(abs($item->debit_amount), 2, '.', ',') }}
                                            </td>
                                            <td
                                                style="width: 100px; border-bottom: solid 1px #2c2b6d; text-align: right;">
                                                {{ @App\SysHelper::com_curr_format(abs($item->credit_amount), 2, '.', ',') }}
                                            </td>
                                            <td style="border-bottom: solid 1px #2c2b6d; font-size: 10px; padding-left: 10px;"
                                                class="">
                                                @if ($item->remarks != '')
                                                    {{ $item->remarks }}
                                                @endif
                                            </td>

                                            <td style="width: 100px; border-bottom: solid 1px #2c2b6d;"
                                                class="text-center">
                                                @if ($item->transaction_ref === 0)
                                                    --
                                                @else
                                                    {{ $item->transaction_ref }}
                                                @endif
                                            </td>
                                            <?php
                                            $sum_d += abs($item->debit_amount);
                                            $sum_c += abs($item->credit_amount);
                                            ?>
                                        </tr>
                                    </table>
                                @endforeach
                            @endif
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td
                                        style="border-bottom: solid 1px #2c2b6d; text-align: left; font-weight: bold; width: 230px;">
                                    </td>
                                    <td
                                        style="border-bottom: solid 1px #2c2b6d; text-align: right; font-weight: bold; width: 100px;">
                                        {{ @App\SysHelper::com_curr_format($sum_d, 2, '.', ',') }}</td>
                                    <td
                                        style="border-bottom: solid 1px #2c2b6d; text-align: right; font-weight: bold; width: 100px;">
                                        {{ @App\SysHelper::com_curr_format($sum_c, 2, '.', ',') }}</td>
                                    <td
                                        style="border-bottom: solid 1px #2c2b6d; text-align: right; font-weight: bold;">
                                    </td>
                                    <td
                                        style="border-bottom: solid 1px #2c2b6d; text-align: right; font-weight: bold; width: 100px;">
                                    </td>
                                </tr>
                            </table>
                            <br><br><br><br />
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="border: none; font-weight: bold;" align="left" valign="bottom">

                                    </td>
                                    <td style="border: none; font-weight: bold;" align="center" valign="bottom"></td>
                                    <td style="border: none; font-weight: bold;" align="right" valign="bottom">
                                        Authorised Signature<br /><br /><br />{{ @$company->company_name }}
                                </tr>
                            </table>
                        </div>
                        <footer style="position: relative; bottom: 0px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">

                                <tr>
                                    <td colspan="3" style="border: none; font-size: 10px;" align="right"
                                        valign="top">
                                        {{-- Page No <span style="" class="pagenum"></span> of {{@$po->doc_number}}</td> --}}
                                </tr>
                            </table>
                            <img src="{!! asset('public/uploads/crm_pdf_img/new-' . $company->pdf_footer . '') !!}" width="100%" /></td>
                        </footer>
                        {{-- ************* --}}
                    </div>

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
    </div> */
    ?>


    <div class="modal  fade"  id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Attachments - <label class="font-weight-600"
                            id="att_cust_name"></label></h4>
                    <button type="button" class="btn-close" id="closeattachbtn"  aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-20">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('Attach File') <span>*</span> </label>
                                        <input class="form-control" type="file" id="att_file" name="att_file"
                                            onchange="updateDocName()" />
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-20">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('Date') <span>*</span> </label>
                                        <input class="form-control date-picker" type="text" id="att_date"
                                            name="att_date" value="{{ date('d/m/Y') }}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-20">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('File Name') <span>*</span> </label>
                                        <input class="form-control" type="text" id="doc_name" name="doc_name"
                                            value="" />
                                    </div>
                                </div>
                                <script>
                                    function updateDocName() {
                                        var fileInput = document.getElementById('att_file');
                                        var fileName = fileInput.files[0] ? fileInput.files[0].name : '';
                                        var fileNameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                                        document.getElementById('doc_name').value = fileNameWithoutExtension;
                                    }
                                </script>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">No</th>
                                                <th style="width: 30%;">Date</th>
                                                <th style="width: 50%;">Attachment</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <br />


                        </div>

                    </div>
                </div>
                <div class="modal-footer">

                    <input type="hidden" id="srl_id" />

                    <button type="button" onclick="add_attachment()" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>

$('#closeattachbtn').on('click', function () {
    $('#attachment_popup_win').modal('hide');
});


        function add_attachment() {
            console.log('add_attachment');

            if ($('#att_file').val() == "") {
                alert('Please select a file to upload');
                $('#att_file').focus();
                return false;
            }
            if ($('#att_date').val() == "") {
                alert('Please select a date');
                $('#att_date').focus();
                return false;
            }
            if ($('#doc_name').val() == "") {
                alert('Please enter a file name');
                $('#doc_name').focus();
                return false;
            }

            $("#loading_bg").css("display", "block");

            var action = "{{ URL::to('add-journal-voucher-attachment') }}";

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('doc_id', $('#jv_id').val());
            formData.append('att_date', $('#att_date').val());
            formData.append('att_file', $('#att_file')[0].files[0]);
            formData.append('doc_name', $('#doc_name').val());

            console.log('Sending data:', {
                doc_id: $('#jv_id').val(),
                att_date: $('#att_date').val(),
                doc_name: $('#doc_name').val()
            });

            $.ajax({
                url: action,
                type: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(dataResult) {
                    console.log('Response:', dataResult);
                    var data = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                    var len = 0;
                    var getSelectedRows = "";

                    if (data['data'] != null) {
                        len = data['data'].length;
                    }

                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>" + Number(i + 1) + "</td>\
                                    <td>" + get_format_date(data['data'][i].doc_date) + "</td>\
                                    <td><a href='../../" + data['data'][i].doc_file + "' target='_blank'>" + data['data'][
                                    i]
                                .doc_name + "</a></td>\
                                    <td><a onclick='delete_attachment(" + data['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic title-15 text-dark' aria-hidden='true'></i></a></td>\
                                    </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                        console.log('Attachment added successfully');
                        toastr.success('Attachment added successfully', 'Success');
                    } else {
                        $('#att-table tbody').empty();
                    }
                    $("#loading_bg").css("display", "none");
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:");
                    console.error("Status: " + status);
                    console.error("Error: " + error);
                    console.error("Response Text: " + xhr.responseText);
                    $("#loading_bg").css("display", "none");
                    alert("Something went wrong while processing your request. Please try again.");
                }
            });
        }

        function view_attachment() {
            $("#loading_bg").css("display", "block");
            console.log($('#doc_number').val())
            $('#att_cust_name').text(" " + $('#doc_number').val());

            var action = "{{ URL::to('view-journal-voucher-attachment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    doc_id: $('#jv_id').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                        <td>" + Number(i + 1) + "</td>\
                                        <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                        <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                                dataResult['data'][i].doc_name + "</a></td>\
                                        <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico  title-15 icon-outline-trash-bin-minimalistic text-dark' aria-hidden='true'></i></a></td>\
                                        </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                    } else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function delete_attachment(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-journal-voucher-attachment') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    doc_id: $('#jv_id').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                        <td>" + Number(i + 1) + "</td>\
                                        <td>" + get_format_date(dataResult['data'][i].doc_date) + "</td>\
                                        <td><a href='../../" + dataResult['data'][i].doc_file + "' target='_blank'>" +
                                dataResult['data'][i].doc_name + "</a></td>\
                                        <td><a onclick='delete_attachment(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico  title-15 icon-outline-trash-bin-minimalistic text-dark' aria-hidden='true'></i></a></td>\
                                        </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                        toastr.success('Attachment deleted successfully', 'Success');

                    } else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
    </script>



    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

</div>

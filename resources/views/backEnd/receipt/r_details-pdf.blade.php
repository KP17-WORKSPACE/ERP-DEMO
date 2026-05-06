<div class="modal-header">
    <h4 class="modal-title" id="po-doc-number">{{ $receipt->doc_number }}</h4>

    <div class="d-flex align-items-center gap-2">

        <a href="{{ url('receipt/' . $receipt->id . '/edit') }}" class="btn btn-light text-dark"
            style="font-size:12px;padding:0px 7px !important;min-height:19px">
            <i class="ico icon-outline-pen-2 text-success" style="font-size:15px"></i> Edit
        </a>

        <a href="{{ url('receipt-add') }}" class="btn btn-light text-dark"
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



                <li><a class="dropdown-item d-flex align-items-center"
                        href="{{ url('receipt/' . $receipt->id . '/delete') }}"><i
                            class="ico icon-outline-trash-bin-minimalistic text-danger title-15 me-2"></i> Cancel</a>
                </li>
                <li><a class="dropdown-item d-flex align-items-center"
                        href="{{ url('receipt/' . $receipt->id . '/download') }}"><i
                            class="ico icon-outline-document-medicine text-success title-15 me-2"></i> Download</a></li>


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






    <div class="card mb-3 card-min-height">
        <div class="card-body">
            <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                {{-- <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">heading</h5>
                </div> --}}
                <div class="row">
                   
                    <div class="col-12 mb-2 pdfarea">

                        {{-- ************* --}}
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="left"><img src="{{ asset(@$company->company_logo) }}" width="200px" />
                                </td>
                                <td align="right"><b style="font-size: 30px; font-weight: 400;">Receipt Voucher</b>
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
                        @if ($receipt->mode == 1)
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1"
                                style="text-align: center;">
                                <tr>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Mode</td>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Date</td>
                                </tr>
                                <tr>
                                    <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->doc_date)) }}
                                    </td>
                                    <td style="line-height: 18px;">{{ $receipt->doc_number }}</td>
                                    <td style="line-height: 18px;">Cash</td>
                                    <td style="line-height: 18px;">
                                        {{ date('d/m/Y', strtotime(@$receipt->receipt_date)) }}</td>
                                </tr>
                            </table>
                        @else
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1"
                                style="text-align: center;">
                                <tr>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Date</td>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Doc Number</td>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Mode</td>
                                    <td width="25%" style="line-height: 18px; font-weight:bold;">Receipt Through
                                    </td>
                                </tr>
                                <tr>
                                    <td style="line-height: 18px;">{{ date('d/m/Y', strtotime(@$receipt->doc_date)) }}
                                    </td>
                                    <td style="line-height: 18px;">{{ $receipt->doc_number }}</td>
                                    <td style="line-height: 18px;">Bank</td>
                                    <td style="line-height: 18px;">
                                        @if ($receipt->receipt_through == 1)
                                            Bank Transfer
                                        @endif
                                        @if ($receipt->receipt_through == 2)
                                            CDC Cheque
                                        @endif
                                        @if ($receipt->receipt_through == 3)
                                            PDC Cheque
                                        @endif
                                    </td>
                                </tr>
                            </table><br />
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="m1"
                                style="text-align: center;">
                                <tr>
                                    <td style="line-height: 18px; font-weight:bold;">Receipt Date</td>
                                    <td style="line-height: 18px; font-weight:bold;">Cheque Date</td>
                                    @if ($receipt->cheque_number != '')
                                        <td style="line-height: 18px; font-weight:bold;">Cheque Number</td>
                                    @endif
                                    @if ($receipt->cheque_bank_name != '')
                                        <td style="line-height: 18px; font-weight:bold;">Bank Name</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td style="line-height: 18px;">
                                        {{ date('d/m/Y', strtotime(@$receipt->receipt_date)) }}</td>
                                    <td style="line-height: 18px;">
                                        {{ date('d/m/Y', strtotime(@$receipt->cheque_date)) }}</td>
                                    @if ($receipt->cheque_number != '')
                                        <td style="line-height: 18px;">{{ $receipt->cheque_number }}</td>
                                    @endif
                                    @if ($receipt->cheque_bank_name != '')
                                        <td style="line-height: 18px;">{{ $receipt->cheque_bank_name }}</td>
                                    @endif
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
                        $i = 1;
                        $sum = 0;
                        ?>
                        @if (count($receipt_item) > 0)
                            @foreach ($receipt_item as $item)
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="width: 20px; border-bottom: solid 1px #2c2b6d;">
                                            {{ $i }}. <?php $i++; ?></td>
                                        <td style="width: 530px; border-bottom: solid 1px #2c2b6d; font-size: 10px;">
                                            {{ $item->accounts->account_name }}
                                            @if ($item->remarks != '')
                                                <br />{{ $item->remarks }}
                                            @endif


                                        </td>
                                        <td style="width: 50px; border-bottom: solid 1px #2c2b6d; text-align: center;">
                                            {{ @App\SysHelper::com_curr_format(abs($item->debit_amount - $item->credit_amount), 2, '.', ',') }}
                                        </td>
                                        <?php
                                        $sum += abs($item->debit_amount - $item->credit_amount);
                                        ?>
                                    </tr>
                                </table>
                            @endforeach
                        @endif
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td
                                    style="border-bottom: solid 1px #2c2b6d; text-align: left; width: 550px; font-weight: bold;">
                                    <?php echo ucwords(@App\SysHelper::convertAmountToWords($sum, $receipt->currency_name->r_code, $receipt->currency_name->p_code)); ?></td>
                                <td
                                    style="border-bottom: solid 1px #2c2b6d; text-align: center; font-weight: bold; width: 50px;">
                                    {{ @App\SysHelper::com_curr_format($sum, 2, '.', ',') }}</td>
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
                                    <br /><br />Printed on {{ $print }}
                                </td>
                            </tr>
                        </table>
                        <footer>
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

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

</div>

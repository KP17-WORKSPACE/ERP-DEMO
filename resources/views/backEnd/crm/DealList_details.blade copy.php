<style>
    th,
    td {
        padding: 5px 0px;
    }

    .tdd {
        border: solid 0px #f5f5f5;
        border-width: 0 0 0px 0;
    }

    .main2 {
        margin: 0px 0px;
    }

    .dttable {}

    .dttable th,
    .dttable td {
        padding: 3px 5px;
        text-align: left;
        border: solid 0px #e7e6e6;
        border-width: 0px;
        font-size: 11px;
    }

    .dttable th {
        background: #d9d9d9;
    }

    .pdfarea {
        font-family: Verdana, sans-serif;
        font-size: 12px;
        color: #555555;
    }

    .dttable2 {}

    .dttable2 th,
    .dttable2 td {
        padding: 3px 5px;
        text-align: left;
        border: solid 1px #e7e6e6;
        border-width: 0px 1px 1px 1px;
        font-size: 11px;
    }

    .dttable2 th {
        background: #d9d9d9;
    }

    .algc {
        text-align: left !important;
        font-weight: bold;
        background: #f2f2f2;
        color: #ffffff;
    }

    .subhd {
        font-weight: bold;
        background: #f2f2f2;
        color: #808080;
    }

    .cathd {
        font-weight: bold;
        background: #f2f2f2;
        color: #808080;
        text-align: center;
    }

    ol li {
        color: #808080;
    }

    hr {
        border: solid 0px #f5f5f5;
        background: #f5f5f5;
        height: 1px;
    }

    .page-break {
        page-break-after: always;
    }

    body {
        background-image: url('{!! asset('public/uploads/crm_pdf_img/' . $pdfwatermark . '') !!}');
    }

    .item-head-row {
        background: #2c2b6d;
        color: #ffffff;
    }

    .item-head-row td {
        border: solid 2px #2c2b6d !important;
        padding: 5px !important;
        margin: 0px !important;
    }

    .item-row {
        border-bottom: solid 1px #2c2b6d !important;
        border-top: solid 0px #2c2b6d !important;
    }
</style>
<?php try { ?>





<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        {{ $quotation->code }} 
           @if($quotation->stage == 4) 

         <?php
                                                    $data = App\SysHelper::deal_track_status($quotation->id);
                                                    $color = "danger";
                                                    if ($data == "Pending") {
                                                        $color = "warning";
                                                    } else if ($data == "completed") {
                                                        $color = "primary";
                                                    } else if ($data == "OnProcess") {
                                                        $color = "info";
                                                    } else {
                                                        $color = "danger";
                                                    }
                                            ?>
                                            

                                            @if(App\SysHelper::set_track($quotation->id) == 1)
                                                <a class="badge bg-{{ $color }}  py-1 px-2 @if($data == "Fulfill") @else deal-track-sales-person @endif" @if($data == "Fulfill") href="{{ url('crm-deals/'.$quotation->id.'/edit') }}" @endif  data-id="{{ $quotation->id }}"  title="Click to Fullfill">
                                                 {{ $data }} </a>
                                            @endif
                                            @endif
    </h4>
    <div class="purchase-order-content-header-right">
        <a class="btn btn-light text-dark btn-edit-deal" href="{{ url('crm-deals/show/' . $quotation->id . '?deal_action=edit') }}">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
        <script>
$(document).on("click", ".btn-edit-deal", function () {
    console.log("c;dd")
    localStorage.removeItem("active-dealedit-tab");
});
</script>
        <a class="btn btn-light text-dark" href="{{ url('crm-deals/show?deal_action=add') }}">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                {{-- <li><a class="dropdown-item" href="{{url('delivery-note/'.$quotation->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel Deal</a></li> --}}
                <li><a class="dropdown-item d-flex align-items-center text-dark"
                        href="{{ url('crm-quote/' . $quotation->id . '/download/' . $quotation->quote_id) }}"><i
                            class="ico icon-bold-download-minimalistic text-success  title-15 me-2"></i> Download</a>
                </li>

                {{-- @if ($quotation->stage == 4 || $quotation->stage == 1)
                    @if (count($support) == 0)
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalSupport" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center  text-dark"><i class="ico icon-outline-add-square text-success title-15 me-2"></i> Add Pre-Sales Request</a></li>
                    @else
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalSupportCmt" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> Add Pre-Sales Request Comments</a></li>
                    @endif
                @endif
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalCollaboration" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> Add Collaboration</a></li>
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalEndUserDetails" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center  text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> End User Details</a></li> --}}


                @if (!empty($quotation->track) && !empty($quotation->track->id))
                    <li>
                        <a target="__blank" href="{{ url('crm-deal-track-approval-list/' . $quotation->track->id) }}"
                            class="dropdown-item d-flex align-items-center text-dark">
                            <i class="ico icon-outline-document-text text-success title-15 me-2"></i>
                            Deal Track
                        </a>
                    </li>
                @endif


            </ul>
        </div>
    </div>
</div>
<div class="card mb-3 card-min-height">
    <div class="card-body">
        <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
            {{-- <div class="d-flex align-items-center gap-3 mt-3 mb-2">
                    <h5 class="m-0 text-green">heading</h5>
                </div> --}}
            <div class="row">
                <div class="col-2 mb-2">&nbsp;</div>
                <div class="col-8 mb-2 pdfarea">

                    {{-- ************* --}}
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left"><img src="{{ asset(@$company->company_logo) }}" width="200px" />
                            </td>
                            <td align="right"><b style="font-size: 30px; font-weight: 400;">Quotation</b>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <div class="main2">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="60%" style="line-height: 18px; vertical-align: top;width:70% !important;">
                                    <b style="font-size: 14px;">To: <br />{{ $quotation->customername->name }}</b><br />
                                    <span style="font-size: 12px;">Attn: {{ $quotation->cust_name }}</span><br />
                                    <p style="padding: 0px; margin: 0px; width: 250px;">
                                        {{ @$quotation->customername->addresses->first()->address }}</p>
                                    <p style="padding: 0px; margin: 0px; width: 250px;">
                                        {{ @$quotation->customername->addresses->first()->address2 }},
                                        {{ @$quotation->customername->addresses->first()->city }}</p>
                                    <p style="padding: 0px; margin: 0px; width: 250px;">
                                        {{ @$quotation->customername->addresses->first()->statename->name }},
                                        {{ @$quotation->customername->addresses->first()->countryname->name }}</p>
                                    @if ($quotation->cust_no != '')<b>Tel :</b>
                                        {{ @$quotation->cust_no }}<br />@endif
                                    @if ($quotation->cust_email != '')<b>Email :</b>
                                        {{ @$quotation->cust_email }}@endif
                                    <br /><br />
                                    {{-- <b style="font-size: 12px;">By</b>
                                    <br />
                                    <b>{{ $quotation->ownername->full_name }}</b>
                                    <br />
                                        <b>{{ @$quotationitems[0]->company->company_name }}</b>     --}}
                                </td>

                                <td style="line-height: 18px; vertical-align: top;float: right;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                        style="margin-top: 27px;">
                                        <tr>
                                            <td style="padding: 0px; margin: 0px; width: 90px;">Quote No</td>

                                            <td style="padding: 0px; margin: 0px">: {{ $quotation->code }} @if (@$quotationitems[0]->quote_id != 1 && @$quotationitems[0]->quote_id != null) -
                                                    {{ @$quotationitems[0]->quote_id - 1 }} @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0px; margin: 0px;">Quote Date</td>
                                            <td style="padding: 0px; margin: 0px">:
                                                {{ !empty($quotationitems[0]->created_at) ? date('d/m/Y', strtotime($quotationitems[0]->created_at)) : date('d/m/Y', strtotime($quotation->created_at)) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 0px; margin: 0px;">Quote Validity</td>
                                            <td style="padding: 0px; margin: 0px">:
                                                {{ @$quotationitems[0]->quote_validity }}</td>
                                        </tr>



                                        @if ($deliverytime != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> Delivery Time</td>
                                                <td style="padding: 0px; margin: 0px">: {{ $deliverytime }}</td>
                                            </tr>

                                        @endif

                                        <tr>
                                            <td
                                                style="padding: 0px; margin: 0px;vertical-align: top; white-space: nowrap;">
                                                Payment Terms</td>
                                           <td style="padding:0; margin:0; white-space:nowrap; max-width:250px; overflow:hidden; text-overflow:ellipsis;">
    : {{ $paymentterms }}
</td>

                                        </tr>

                                        @if (@$quotationitems[0]->nooflocation != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> No of Locations</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    {{ @$quotationitems[0]->nooflocation }}</td>
                                            </tr>

                                        @endif

                                        @if (@$quotationitems[0]->connectivity != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> Connectivity Required</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    {{ @$quotationitems[0]->connectivity }}</td>
                                            </tr>

                                        @endif

                                        @if (@$quotationitems[0]->telephonetype != '')
                                            <tr>
                                                <td style="padding: 0px; margin: 0px;"> ISP Telephone Type</td>
                                                <td style="padding: 0px; margin: 0px">:
                                                    <?php $string = @$quotationitems[0]->telephonetype;
                                    $str_arr = explode (",", $string);
                                    $string_val = @$quotationitems[0]->nolines;
                                    $str_arr2 = explode (",", $string_val);
                                    for($i=0; $i< count($str_arr); $i++) { ?>
                                                    {{ $str_arr[$i] }} - {{ $str_arr2[$i] }} Line,
                                                    <?php } ?></td>
                                            </tr>

                                        @endif

                                    </table>

                                </td>
                            </tr>
                        </table>
                    </div>



                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="3" align="center"><u><b style="font-size: 15px;">
                                        @if (count($quotationitems) > 0)
                                            {{-- Quotation --}}
                                        @else
                                            Quotation Not Yet Generated
                                        @endif

                                    </b></u></td>
                        </tr>
                    </table>



                    @if (count($quotationitems) > 0)

                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="item-head-row">
                                <td width="5%" style="text-align: center;">No</td>
                                <td width="70%">Description</td>
                                <td style="width: 70px; text-align:right !important;">Cost</td>
                                <td width="5%" style="text-align: center;">Qty</td>
                                <td width="10%" style="text-align: right;">Unit Price</td>
                                <td width="10%" style="text-align: right;">Total</td>
                            </tr>
                        </table>
                        <?php
                        $subtotal = 0;
                        $vat = 0;
                        $discount = 0;
                        $c1 = 0;
                        $c2 = 0;
                        $c3 = 0;
                        $c4 = 0;
                        $c5 = 0;
                        $c6 = 0;
                        $Hardware = 0;
                        $HardwareT = 0;
                        $License = 0;
                        $LicenseT = 0;
                        $srl = 1;
                        ?>
                        <?php foreach($quotationitems as $val){?>
                        @if ($val->status != 0)
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="item-row" style="width: 30px;text-align: center;">{{ $srl++ }}
                                    </td>
                                    <td class="item-row" style="width: 300px;font-size: 11px;">

                                        <b style="font-size: 11px;"> {{ $val->productname->part_number }} </b> <br>
                                        {!! nl2br($val->description) !!}
                                    </td>
                                    <td class="item-row" width="70px" style="text-align:right !important;">
                                        {{ App\SysHelper::currancy_format($val->cost, $val->currency_id) }}</td>

                                    <td class="item-row" width="5%" style="text-align: center;">
                                        {{ $val->qty }}</td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        {{ App\SysHelper::currancy_format($val->price, $val->currency_id) }}</td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        <?php $subtotal += $val->price * $val->qty; ?>
                                        <?php $discount += $val->discount; //$discount += $val->discount * $val->qty; ?>
                                        {{ App\SysHelper::currancy_format($val->qty * $val->price, $val->currency_id) }}
                                    </td>
                                </tr>
                            </table>
                        @endif


                        <?php 
        if($wv==1){ $vat = 0; }
        else { $vat += ((( $val->price * $val->qty) * $val->vat/100) - ($val->discount * $val->vat/100)); }
        
        $currency_id = $val->currency_id;
        }
            $discount += $quotation->deal_discount;
            if($wv==1){
                $deal_discount_vat=0;
            } else { $deal_discount_vat=$quotationitems->max('vat'); }
            $deal_discount_vat_amount= $quotation->deal_discount * $deal_discount_vat/100;
        ?>
                        </table>
                        @if ($wt != 1)


                            <div class="row">
                                <div class="col-8">





                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="font-weight-600">{{ @$currency_modal->code }}
                                                <?php echo ucwords(@App\SysHelper::convertAmountToWords($subtotal - $discount - $deal_discount_vat_amount + $vat, @$currency_modal->r_code, @$currency_modal->p_code)); ?></td>

                                            </td>

                                        <tr>
                                            <td>
                                                <b>Terms & Conditions</b><br />

                                                @if ($quotation->terms_and_condition == '')
                                                    <ol style="padding-left: 15px; font-size: 11px">
                                                        <li>Quote/Order will be subject to approval of payment/credit
                                                            terms by {{ $quotationitems[0]->company->company_name }}.
                                                        </li>
                                                        <li>Please mention our Quotation No.in your Purchase Order</li>
                                                        <li>In case of non-availability of quote products
                                                            {{ $quotationitems[0]->company->company_name }} reserved
                                                            the rights to supply a functionally similar or better
                                                            product.</li>
                                                    </ol>
                                                @else
                                                    <div style="padding-top: 2px; font-size: 11px">
                                                        {!! nl2br($quotation->terms_and_condition) !!}


                                                    </div>
                                                @endif
                                            </td>

                                        </tr>
                                    </table>

                                </div>
                                <div class="col-4">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Total {{ $currency }}</td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                {{ App\SysHelper::currancy_format($subtotal, $currency_id) }}</td>
                                        </tr>
                                    </table>
                                    @if ($discount > 0)
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td></td>
                                                <td
                                                    style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    Discount {{ $currency }}</td>
                                                <td
                                                    style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    {{ App\SysHelper::currancy_format($discount, $currency_id) }}</td>
                                            </tr>
                                        </table>
                                    @endif
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Sub Total {{ $currency }}</td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                {{ App\SysHelper::currancy_format($subtotal - $discount, $currency_id) }}
                                            </td>
                                        </tr>
                                    </table>
                                    @if ($wv != 1)
                                        @if ($vat - $deal_discount_vat_amount != 0)
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td></td>
                                                    @if ($currency == 'INR')
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            GST {{ $currency }}</td>
                                                    @elseif($currency == 'USD')
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            TAX {{ $currency }}</td>
                                                    @else
                                                        <td
                                                            style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                            VAT {{ $currency }}</td>
                                                    @endif
                                                    <td
                                                        style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                        {{ App\SysHelper::currancy_format($vat - $deal_discount_vat_amount, $currency_id) }}</b>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif
                                    @endif
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td></td>
                                            <td
                                                style="width: 160px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                Net Amount {{ $currency }}</td>
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                {{ App\SysHelper::currancy_format($subtotal - $discount - $deal_discount_vat_amount + $vat, $currency_id) }}
                                            </td>
                                        </tr>
                                    </table>
                        @endif

                        <?php if($enduser != "") { ?>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="line-height: 18px;">
                                    <b style="font-size: 12px;">End User Details:</b><br />
                                    <b>{{ $enduser->end_user_company_name }}</b><br />
                                    @if ($enduser->address_line_a != '')
                                        {{ $enduser->address_line_a }}, @endif
                                    @if ($enduser->address_line_b != '')
                                        {{ $enduser->address_line_b }}, @endif
                                    @if ($enduser->city != '')
                                        {{ $enduser->city }}, @endif
                                    @if ($enduser->po_box != '') PB.No :
                                        {{ $enduser->po_box }} @endif <br />
                                    @if ($enduser->end_user_contact_person != '') Contact Person :
                                        {{ $enduser->end_user_contact_person }}<br /> @endif
                                    @if ($enduser->job_title != '') Job Title :
                                        {{ $enduser->job_title }}<br /> @endif
                                    @if ($enduser->mobile_no != '') Mobile No :
                                        {{ $enduser->mobile_no }}, Email : {{ $enduser->email }}<br /> @endif
                                    @if ($enduser->project_name != '') Project Name :
                                        {{ $enduser->project_name }}<br /> @endif
                                    @if ($enduser->project_description != '') Project Brief :
                                        {{ $enduser->project_description }}<br /> @endif
                                    @if ($enduser->expected_close_date != '') Expected to
                                        Close : {{ date('d-M-Y', strtotime($enduser->expected_close_date)) }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <?php } ?>
                </div>

            </div>




            @endif

            <br><br><br><br><br>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:14%; border: none;" align="left" valign="bottom">
                        {{ @$quotation->ownername->full_name }}<br /><br /><br /><b class="bottom_b">Prepared
                            By</b></td>
                    <td style="width:60%; border: none;" align="center" valign="bottom">This document
                        is computer generated Signature is not required</td>
                    <td style="width:20%; border: none;" align="right" valign="bottom">
                        {{ @$company->company_name }}<br /><br /><br /><b class="bottom_b">Authorised
                            Signature</b></td>
                </tr>
            </table>
            <footer>

                <img src="{!! asset('public/uploads/crm_pdf_img/new-' . $company->pdf_footer . '') !!}" width="100%" /></td>
            </footer>


            {{-- ************* --}}
        </div>
        <div class="col-2 mb-2">&nbsp;</div>
    </div>
</div>
</div>
</div>




<!-- Modal Support-->
<div class="modal side-panel fade" id="ModalSupport" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="support_id" value="0" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="mb-3">
                            <label for="" class="form-label">Customer</label>
                            <input type="text" class="form-control" value="{{ $edit->customername->name }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="" class="form-label">Deal Id</label>
                            <input type="number" class="form-control" value="{{ $edit->deal_code->code }}"
                                readonly>
                            <input type="hidden" name="deal_id" id="deal_id" value="{{ $edit->id }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Site Name</label>
                            <input type="text" class="form-control" name="site_name" id="site_name"
                                value="{{ $edit->address }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Date</label>
                            <input type="date" class="form-control" name="support_date" id="support_date"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">From</label>
                            <input type="time" class="form-control" name="time_from" id="time_from" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">To</label>
                            <input type="time" class="form-control" name="time_to" id="time_to" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Scope of Work</label>
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                        <button type="button" class="btn btn-sm btn-light border"
                                            onclick="add_scope_of_work()">
                                            <i class="ico icon-outline-add-square me-1"></i> Add
                                        </button>
                                    </div>

                                    <table class="table table-sm table-borderless align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" width="5%">1.</td>
                                                <td>
                                                    <input type="text" class="form-control" name="scope_of_work[]"
                                                        id="scope_of_work_1" required>
                                                </td>
                                                <td width="5%"></td>
                                            </tr>

                                            @for ($i = 2; $i <= 20; $i++)
                                                <tr id="row_{{ $i }}" style="display: none;">
                                                    <td class="text-muted">{{ $i }}.</td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            name="scope_of_work[]"
                                                            id="scope_of_work_{{ $i }}">
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="delete_work({{ $i }})">
                                                            <i class="ico icon-outline-trash-bin-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="scope_of_work_row_id" value="1" />
                                </div>

                                <script>
                                    function add_scope_of_work() {
                                        let scope = parseInt($('#scope_of_work_row_id').val());
                                        let currentInput = $('#scope_of_work_' + scope);

                                        if (currentInput.val().trim() !== "") {
                                            scope++;
                                            $('#row_' + scope).fadeIn();
                                            $('#scope_of_work_row_id').val(scope);
                                            $('#scope_of_work_' + scope).prop("required", true);
                                        } else {
                                            currentInput.focus();
                                        }
                                    }

                                    function delete_work(id) {
                                        $('#row_' + id).fadeOut(() => {
                                            $('#scope_of_work_' + id).val('').prop("required", false);
                                        });
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="customer_id" id="customer_id" required value="{{ $edit->cust_id }}" />
                <input type="hidden" name="sales_person_id" id="sales_person_id" required
                    value="{{ $edit->owner }}" />
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add Service
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Support-->
<!-- Modal Support Cmt-->
<div class="modal side-panel fade" id="ModalSupportCmt" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Service Comments</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-comments', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @if (count($support) != 0)
                <input type="hidden" name="support_id" value="{{ $support[0]->id }}" />
            @endif
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Comments</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add Comments
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Support Cmt-->

<!-- Modal Collaboration-->
<div class="modal side-panel fade" id="ModalCollaboration" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Collaboration</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-collaboration', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="collaboration_deal_id" value="{{ $edit->id }}" />
            <input type="hidden" name="collaboration_cust_id" value="{{ $edit->cust_id }}" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Select Users</label>
                            <select class="form-control js-example-basic-single" name="user_id[]" multiple>
                                @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}"
                                        @if (isset($collaboration)) @foreach ($collaboration as $coll)
                                        @if ($coll->user_id == $value->user_id) selected @endif
                                        @endforeach
                                @endif >{{ @$value->full_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{--  <div class="row">
                        <div class="col-md-12">
                            @if (count($collaboration) > 0)
                            <hr />
                            <h5 class="sub-head m-0">Collaboration Users</h5><br/>
                            @foreach ($collaboration as $val)
                            <span class="border border-primary rounded py-1 px-3 font-weight-normal">{{ $val->userid->full_name }}</span>
                            @endforeach
                            @endif
                        </div>
                    </div>  --}}
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add to Collaboration
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Collaboration-->
<!-- Modal Service-->
<div class="modal side-panel fade" id="ModalService" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                @if (count($service) == 0)
                    <h4 class="modal-title" id="exampleModalLabel">Add Pre-Sales</h4>
                @else
                    <h4 class="modal-title" id="exampleModalLabel">Add Pre-Sales Comments</h4>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if (count($service) == 0)
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service-comments-additional', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="service_id" value="{{ $service[0]->id }}" />
                <input type="hidden" name="status" value="5" />
            @endif

            <input type="hidden" name="service_deal_id" value="{{ $edit->id }}" />
            <input type="hidden" name="service_cust_id" value="{{ $edit->cust_id }}" />
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Description</label>
                            <textarea class="form-control" name="comments" id="comments" rows="5"></textarea>
                        </div>
                    </div>

                    <?php /*
                        @if (count($service)==0) 
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Part Number</label>
                                <select class="form-control js-example-basic-single" name="part_number[]" id="part_number" multiple>
                                    @foreach ($product_list as $value)
                                        <option value="{{ @$value->part_number }}">{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" id="user_id" multiple>
                                    @foreach ($support_person as $value)
                                        <option value="{{ @$value->user_id }}" >{{ @$value->full_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        */
                    ?>

                </div>
            </div>
            <div class="modal-footer">
                @if (count($service) == 0)
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add to Pre-Sales
                    </button>
                    <button type="submit" class="btn btn-primary">Add to Pre-Sales</button>
                @else
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Comment
                    </button>
                    <button type="submit" class="btn btn-primary"></button>
                @endif
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>
<!-- Modal Service-->

<!-- Modal End User -->
<div class="modal side-panel fade" id="ModalEndUserDetails" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">End User Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if ($enduser == '')
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-add-end-user', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="end_user_deal_id" value="{{ $edit->id }}" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Ultimate End User Company Name *</label>
                                <input type="text" class="form-control" name="end_user_company_name"
                                    id="end_user_company_name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address *</label>
                                <input type="text" class="form-control" name="address_line_a" id="address_line_a"
                                    required />
                            </div>
                        </div>
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" name="address_line_b" id="address_line_b" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">City</label>
                                <input type="text" class="form-control" name="city" id="city" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">PO. Box</label>
                                <input type="text" class="form-control" name="po_box" id="po_box" />
                            </div>
                        </div>  --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">End User Contact Person *</label>
                                <input type="text" class="form-control" name="end_user_contact_person"
                                    id="end_user_contact_person" required />
                            </div>
                        </div>
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="job_title" id="job_title" />
                            </div>
                        </div>  --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Project Name</label>
                                <input type="text" class="form-control" name="project_name" id="project_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Brief description about this project</label>
                                <textarea class="form-control" name="project_description" id="project_description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">When it is expected to Close</label>
                                <input type="date" class="form-control" name="expected_close_date"
                                    id="expected_close_date" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                    </button>
                </div>
                {{ Form::close() }}
            @else
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="" class="form-label">Ultimate End User Company Name </label> :
                            {{ $enduser->end_user_company_name }}
                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Address</label> : {{ $enduser->address_line_a }}
                            <hr class="m-0 p-0 mb-1" />
                            {{--  <label for="" class="form-label">Address Line 2</label> : {{ $enduser->address_line_b }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">City</label> : {{ $enduser->city }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">PO. Box</label> : {{ $enduser->po_box }}<hr class="m-0 p-0 mb-1" />  --}}
                            <label for="" class="form-label">End User Contact Person</label> :
                            {{ $enduser->end_user_contact_person }}
                            <hr class="m-0 p-0 mb-1" />
                            {{--  <label for="" class="form-label">Job Title</label> : {{ $enduser->job_title }}<hr class="m-0 p-0 mb-1" />  --}}
                            <label for="" class="form-label">Mobile No</label> : {{ $enduser->mobile_no }}
                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Email</label> : {{ $enduser->email }}
                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Project Name</label> :
                            {{ $enduser->project_name }}
                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Brief description about this project</label> :
                            {{ $enduser->project_description }}
                            <hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">When it is expected to Close</label> :
                            {{ date('d-M-Y', strtotime($enduser->expected_close_date)) }}
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
<!-- Modal End User -->

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

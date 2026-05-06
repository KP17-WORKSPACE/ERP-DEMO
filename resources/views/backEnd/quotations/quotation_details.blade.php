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
        background-image: url('{!! asset('public/backEnd/img/syscom-watermark-sm.png') !!}')
    }

    /* Prevent images and tables from forcing overflow inside the narrow .col-8 */
    .pdfarea img {
        max-width: 100%;
        height: auto;
        display: block;
    }

    .main2 table {
        width: 100%;
        table-layout: fixed; /* keep columns within container */
    }

    .main2 td, .main2 th {
        word-break: break-word; /* break long words/strings */
        overflow-wrap: anywhere;
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
        font-size: 12px;
        font-weight: bold
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





<div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
    <h4 class="purchase-order-content-header-left">
        {{-- {{ $po->doc_number }} --}} 
        {{ $quotation->code }} ({{ $document_number }})
    </h4>
    <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">

        
       

    @php
        $is_deal_track_submited = App\SysCrmDealTrack::where('deal_id', $quotation->id)->count();
    @endphp
    @if ($is_deal_track_submited == 0)
        <a class="btn btn-light text-dark btn-edit-deal" href="{{ url('quotations/' . $quotation->id . '?qn_action=edit') }}">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
    @endif
     
       
        <a class="btn btn-light text-dark" href="{{ url('quotations?qn_action=add') }}">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item d-flex align-items-center text-dark" name="po_action" value="add" 
                        href="{{ url('crm-quote/' . $quotation->id . '/download/' . $quotation->quote_id) }}"><i
                            class="ico icon-bold-download-minimalistic text-success  title-15 me-2"></i> Download</a>
                </li>
            </ul>
        </div>

        
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">

            <div class="row">
                <div class="col-2 mb-2">&nbsp;</div>
                <div class="col-8 mb-2 pdfarea">














                    <div style="position: relative;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td colspan="2">
                                    <img src="{!! asset('public/' . $pdffirstpage . '') !!}" />
                                </td>
                            </tr>
                            <?php $enduser = App\SysCrmEndUser::where('deal_id', $quotation->id)->first(); ?>
                            <tr>
                                <td colspan="2">
                                    <br /><br />
                                    <br /><br />
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <div class="main2">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="40%" style="line-height: 18px; vertical-align: top;">
                                                    <b style="font-size: 14px;">To:
                                                        <br />{{ $quotation->customername->name }}</b><br />
                                                    <b style="font-size: 12px;">Attn:
                                                        {{ $quotation->cust_name }}</b><br />
                                                    @if ($quotation->customername->address != '')
                                                        <p style="padding: 0px; margin: 0px; width: 250px;">
                                                            {{ $quotation->address }}</p>
                                                    @endif
                                                    @if ($quotation->cust_no != '')
                                                        <b>Tel :</b> {{ $quotation->cust_no }}<br />
                                                    @endif
                                                    @if ($quotation->cust_email != '')
                                                        <b>Email :</b> {{ $quotation->cust_email }}
                                                    @endif
                                                    <br /><br /><br /><br /><br /><br />
                                                    <b style="font-size: 12px;">By</b>
                                                    <br />
                                                    <b>{{ $quotation->ownername->full_name }}</b>
                                                    <br />
                                                    <b>{{ @$quotationitems[0]->company->company_name }}</b>
                                                </td>
                                                <td width="35%" style="line-height: 18px; vertical-align: top;">
                                                    &nbsp;</td>
                                                <td width="25%" style="line-height: 18px; vertical-align: top;">
                                                    <b style="font-size: 14px;">&nbsp;<br />Quote No :
                                                        {{ $quotation->code }} -
                                                        {{ $quotationitems[0]->quote_id }}</b><br />
                                                    Quote Date :
                                                    {{ date('d/m/Y', strtotime($quotationitems[0]->created_at)) }}<br />
                                                    Quote Validity : {{ $quotationitems[0]->quote_validity }}<br />
                                                    Payment Terms : {{ $paymentterms }}<br />
                                                    @if ($deliverytime != '')
                                                        Delivery Time : {{ $deliverytime }}<br />
                                                    @endif

                                                    @if ($quotationitems[0]->nooflocation != '')
                                                        No of Locations : {{ $quotationitems[0]->nooflocation }}
                                                    @endif
                                                    @if ($quotationitems[0]->connectivity != '')
                                                        <br />Connectivity Required :
                                                        {{ $quotationitems[0]->connectivity }}
                                                    @endif
                                                    @if ($quotationitems[0]->telephonetype != '')
                                                        <br />ISP Telephone Type :

                                                        <?php $string = $quotationitems[0]->telephonetype;
                                    $str_arr = explode (",", $string);
                                    $string_val = $quotationitems[0]->nolines;
                                    $str_arr2 = explode (",", $string_val);
                                    for($i=0; $i< count($str_arr); $i++) { ?>
                                                        {{ $str_arr[$i] }} - {{ $str_arr2[$i] }} Line,
                                                        <?php } ?>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- <footer style="margin-top:30px">
                        <div style="padding: 0px 15px; color: #858796;"><b>Disclaimer:</b> This quotation is valid
                            only for "{{ $quotation->customername->name }}". Any unauthorized replication or sharing
                            of this information will be against our company policy and may result in legal
                            formalities.</div>
                        <img src="{!! asset('public/uploads/crm_pdf_img/' . $pdffooter . '') !!}" width="100%">
                    </footer> --}}

                    <div class="page-break"></div>





                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="3" align="center"><u><b style="font-size: 15px;">Quotation</b></u>
                            </td>
                        </tr>
                    </table>
                    <br /><br />

                    @if (count($quotationitems) > 0)
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr class="item-head-row">
                                <td width="5%" style="text-align: center;">No</td>
                                <td width="70%">Description</td>
                                <td width="5%" style="text-align: center;">Qty</td>
                                <td width="10%" style="text-align: right;">Unit Price</td>
                                <td width="10%" style="text-align: right;">Total&nbsp;&nbsp;</td>
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
                                    <td class="item-row" width="5%" style="text-align: center;">
                                        {{ $srl++ }}</td>
                                    <td class="item-row" width="70%" style="font-size: 11px;">
                                        @if ($wp == 1)
                                            <b style="font-size: 11px;">{{ $val->productname->part_number }}</b><br />
                                        @endif
                                        {!! nl2br($val->description) !!}
                                    </td>
                                    <td class="item-row" width="5%" style="text-align: center;">
                                        {{ $val->qty }}</td>
                                    <td class="item-row" width="10%" style="text-align: right;">
                                        {{ App\SysHelper::currancy_format($val->price, $val->currency_id) }}
                                    </td>
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
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td></td>
                                    <td
                                        style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                        <b>Total {{ $currency }}</b>
                                    </td>
                                    <td
                                        style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                        <b>{{ App\SysHelper::currancy_format($subtotal, $currency_id) }}</b>
                                    </td>
                                </tr>
                            </table>
                            @if ($discount > 0)
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td></td>
                                        <td
                                            style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                            <b>Discount {{ $currency }}</b>
                                        </td>
                                        <td
                                            style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                            <b>{{ App\SysHelper::currancy_format($discount, $currency_id) }}</b>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td></td>
                                    <td
                                        style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                        <b>Sub Total {{ $currency }}</b>
                                    </td>
                                    <td
                                        style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                        <b>{{ App\SysHelper::currancy_format($subtotal - $discount, $currency_id) }}</b>
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
                                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    <b>GST {{ $currency }}</b>
                                                </td>
                                            @elseif($currency == 'USD')
                                                <td
                                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    <b>TAX {{ $currency }}</b>
                                                </td>
                                            @else
                                                <td
                                                    style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                    <b>VAT {{ $currency }}</b>
                                                </td>
                                            @endif
                                            <td
                                                style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                                <b>{{ App\SysHelper::currancy_format($vat - $deal_discount_vat_amount, $currency_id) }}</b>
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                            @endif
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td></td>
                                    <td
                                        style="width: 130px; text-align: left; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                        <b>Net Amount {{ $currency }}</b>
                                    </td>
                                    <td
                                        style="width: 80px; text-align: right; font-weight: bold; border-bottom: solid 1px #2c2b6d;">
                                        <b>{{ App\SysHelper::currancy_format($subtotal - $discount - $deal_discount_vat_amount + $vat, $currency_id) }}</b>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <?php if($enduser != "") { ?>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="line-height: 18px;">
                                    <b style="font-size: 13px;">End User Details:</b><br />
                                    <b>{{ @$enduser->end_user_company_name }}</b><br />
                                    @if ($enduser->address_line_a != '')
                                        {{ $enduser->address_line_a }},
                                    @endif
                                    @if ($enduser->address_line_b != '')
                                        {{ $enduser->address_line_b }},
                                    @endif
                                    @if ($enduser->city != '')
                                        {{ $enduser->city }},
                                    @endif
                                    @if ($enduser->po_box != '')
                                        PB.No : {{ $enduser->po_box }}
                                    @endif <br />
                                    @if ($enduser->end_user_contact_person != '')
                                        Contact Person : {{ $enduser->end_user_contact_person }}<br />
                                    @endif
                                    @if ($enduser->job_title != '')
                                        Job Title : {{ $enduser->job_title }}<br />
                                    @endif
                                    @if ($enduser->mobile_no != '')
                                        Mobile No : {{ $enduser->mobile_no }}, Email :
                                        {{ $enduser->email }}<br />
                                    @endif
                                    @if ($enduser->project_name != '')
                                        Project Name : {{ $enduser->project_name }}<br />
                                    @endif
                                    @if ($enduser->project_description != '')
                                        Project Brief : {{ $enduser->project_description }}<br />
                                    @endif
                                    @if ($enduser->expected_close_date != '')
                                        Expected to Close :
                                        {{ date('d-M-Y', strtotime($enduser->expected_close_date)) }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <?php } ?>

                        <br /><br /><br />
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="65%">
                                    <b>Terms & Conditions</b><br />

                                    @if ($quotation->terms_and_condition == '')
                                        <ol style="padding-left: 15px; font-size: 11px">
                                            <li>Quote/Order will be subject to approval of payment/credit terms
                                                by {{ @$quotationitems[0]->company->company_name }}.</li>
                                            <li>Please mention our Quotation No.in your Purchase Order</li>
                                            <li>In case of non-availability of quote products
                                                {{ @$quotationitems[0]->company->company_name }} reserved the
                                                rights to supply a functionally similar or better product.</li>
                                        </ol>
                                    @else
                                        <div style="padding: 5px; font-size: 11px">
                                            <?php
                                            $string = $quotation->terms_and_condition;
                                            
                                            $bits = explode("\n", $string);
                                            
                                            $newstring = "<ol style='padding:0px; margin:0px 0px 0px 10px;'>";
                                            foreach ($bits as $bit) {
                                                $newstring .= '<li>' . substr($bit, 2) . '</li>';
                                            }
                                            $newstring .= '</ol>';
                                            ?>
                                            {!! $newstring !!}
                                        </div>
                                    @endif
                                </td>
                                <td width="35%" style="text-align: right; vertical-align: bottom;">
                                    Authorized Signature<br /><br /><br />
                                    <b>{{ @$quotationitems[0]->company->company_name }}</b>
                                </td>
                            </tr>
                        </table>

                    @endif





                    <footer style="margin-top:200px">

                        <div style="padding: 0px 15px; color: #858796;"><b>Disclaimer:</b> This quotation is valid
                            only for "{{ $quotation->customername->name }}". Any unauthorized replication or sharing
                            of this information will be against our company policy and may result in legal
                            formalities.</div>
                        <img src="{!! asset('public/' . $pdffooter . '') !!}" width="100%">
                    </footer>













                </div>
                <div class="col-2 mb-2">&nbsp;</div>
            </div>
        </div>
    </div>
</div>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

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
        background-image: url('{!! asset('public/' . $company->pdf_watermark . '') !!}');
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





<div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
    <h4 class="purchase-order-content-header-left">
        {{ @$pk->doc_number }}
    </h4>
    <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">


        <form method="GET" action="{{ url('packing-list', @$pk->id) }}">
            {{-- <input hidden type="text" value="{{@$po->id}}" name="id"> --}}
            <button type="submit" name="packing_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>

        <form method="GET" action="{{ url('packing-list') }}">
            <button type="submit" name="packing_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>


 <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">
               


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




                    <style>
                        .pagenum:before {
                            content: counter(page);
                        }
                    </style>



                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="left"><img src="{{ asset('public/'.@$company->company_logo) }}" width="200px" /></td>
                            <td align="right"><b style="font-size: 30px; font-weight: 400;">Packing List</b></td>
                        </tr>
                    </table>
                    <br />

                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%" valign="top" style="line-height: 18px;">
                                <b>{{ @$company->company_name }}</b>
                                <div>{!! nl2br($company->company_address) !!}</div>
                                P: {{ @$company->telephone }}, M: {{ @$company->mobile }}<br />
                                E: {{ @$company->email }}<br />
                                TRN No: {{ @$company->vat_number }}
                            </td>
                            <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                    style="line-height: 18px;">
                                    <tr>
                                        <td style="padding: 0px; margin: 0px; width: 150px;">Document No</td>
                                        <td style="padding: 0px; margin: 0px;">:{{ @$pk->doc_number }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px;">Document Date</td>
                                        <td style="padding: 0px; margin: 0px;">
                                            :{{ date('d/m/Y', strtotime(@$pk->date)) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px;">Ref No</td>
                                        <td style="padding: 0px; margin: 0px;">:{{ @$pk->refno }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 0px; margin: 0px;">Ref Date</td>
                                        <td style="padding: 0px; margin: 0px;">
                                            :{{ date('d/m/Y', strtotime(@$pk->refdate)) }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%" valign="top" style="line-height: 18px;">Bill To,<br />
                                <b style="font-size: 90%;">{{ @$pk->account->account_name }}</b><br>
                                {{ @$contact_name }}<br />
                                {{ @$address }}<br>
                                {{ @$address2 }}, {{ @$city }}<br>
                                {{ @$state }}, {{ @$country }}<br>
                                T: {{ @$tel }}<br />
                                E: {{ @$email }}
                            </td>
                            <td valign="top" style="line-height: 18px;">Ship To,<br />
                                <b style="font-size: 90%;">{{ @$pk->account->account_name }}</b><br>
                                {{ @$contact_name }}<br />
                                {{ @$address }}<br>
                                {{ @$address2 }}, {{ @$city }}<br>
                                {{ @$state }}, {{ @$country }}<br>
                                T: {{ @$tel }}<br />
                                E: {{ @$email }}
                                {{--  {{@$ship_contact_name}}<br />
          {{@$ship_address1}}<br>
          {{@$ship_address2}}, {{@$delivery_city}}<br>
          {{@$delivery_state}}, {{@$delivery_country}}<br>
          T: {{@$ship_tel}}<br/>
          E: {{@$ship_email}}  --}}
                            </td>
                        </tr>
                    </table>
                    <br />


                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr style="background: #2c2b6d; color: #ffffff;">
                            <td style="width: 50px; border: solid 1px #2c2b6d;">Box/Pallet No</td>
                            <td style="width: 150px; border: solid 1px #2c2b6d;">Part No</td>
                            <td style="width: 50px; border: solid 1px #2c2b6d; text-align: center;">Qty</td>
                            <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">COO</td>
                            <td style="width: 80px; border: solid 1px #2c2b6d; text-align: center;">HS Code</td>
                            <td style="width: 70px; border: solid 1px #2c2b6d; text-align: center;">Weight</td>
                            <td style="width: 102px; border: solid 1px #2c2b6d; text-align: center;">Dimension (L * W *
                                H)</td>
                        </tr>

                     
                        @if (count($items) > 0)


                            <?php
                            $qty = 0;
                            $weight = 0;
                            $rowspanMap = [];
                            
                            // Detect consecutive groups and store rowspan counts
                            $count = 1;
                            for ($i = 0; $i < count($items); $i++) {
                                if ($i > 0 && $items[$i]->boxno === $items[$i - 1]->boxno) {
                                    $count++;
                                } else {
                                    // Store count for the start of the previous group
                                    if ($i > 0) {
                                        $rowspanMap[$i - $count] = $count;
                                    }
                                    $count = 1;
                                }
                            }
                            // Store last group
                            $rowspanMap[count($items) - $count] = $count;
                            ?>

                            @foreach ($items as $index => $item)
                                <tr>
                                    {{-- Print box number only at the first row of a consecutive group --}}
                                    @if (isset($rowspanMap[$index]))
                                        <td style="border: solid 1px #2c2b6d; text-align: center;"
                                            rowspan="{{ $rowspanMap[$index] }}">
                                            {{ $item->boxno }}
                                        </td>
                                    @endif

                                    <td style="border: solid 1px #2c2b6d; text-align: center;">
                                        {{ @$item->product->part_number }}
                                    </td>

                                    <td style="border: solid 1px #2c2b6d; text-align: center;">
                                        {{ $item->qty }}<?php $qty += $item->qty; ?>
                                    </td>

                                    <td style="border: solid 1px #2c2b6d; text-align: center;">
                                        {{ @$item->coo }}
                                    </td>

                                    <td style="border: solid 1px #2c2b6d; text-align: center;">
                                        {{ @$item->hscode }}
                                    </td>

                                    <td style="border: solid 1px #2c2b6d; text-align: center;">
                                        {{ @$item->weight }}<?php $weight += $item->weight; ?>
                                    </td>

                                    <td style="border: solid 1px #2c2b6d; text-align: center;">
                                        {{ @$item->dimension }}
                                    </td>
                                </tr>
                            @endforeach



                        @endif

                        <tr>
                            <td colspan="2" style="border: solid 1px #2c2b6d; text-align: right;font-weight:bold">
                                Total</td>
                            <td style="border: solid 1px #2c2b6d; text-align: center;font-weight:bold">
                                {{ $qty }}</td>
                            <td style="border: solid 1px #2c2b6d; text-align: center;">&nbsp;</td>
                            <td style="border: solid 1px #2c2b6d; text-align: center;">&nbsp;</td>
                            <td style="border: solid 1px #2c2b6d; text-align: center;font-weight:bold">
                                {{ @App\SysHelper::com_curr_format(@$weight, 3, '.', '') }}</td>
                            <td style="border: solid 1px #2c2b6d; text-align: center;">&nbsp;</td>
                        </tr>
                    </table>
                    {{--  <br ><br ><br ><br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="border: none; font-weight: bold;" align="left" valign="bottom">
                Received By: <br /><br />
                Name: <br /><br />
                Phone: <br /><br /><br /><br />
                Signature & Stamp
        
            </td>
          <td style="border: none; font-weight: bold;" align="center" valign="bottom">Approved By</td>
          <td style="border: none; font-weight: bold;" align="right" valign="bottom">For {{@$company->company_name}}</td>
        </tr>
      </table>  --}}

                    <div style="bottom: 0px; height:200px;margin-top:20px">
                        <table width="70%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <b>Terms & Conditions</b>
                                    <ul
                                        style="padding: 10px 0px 0px 15px; margin: 0px; font-size: 9px; text-align: justify;">
                                        <li>Kindly mention the LPO number on all correspondence, invoice and delivery
                                            notes.</li>
                                        <li>In the event of your failing to deliver or execute the said order on or
                                            before the stiputated date or such extended time as permitted by us,
                                            {{ @$company->company_name }}. reserves the full right and authority to
                                            cancel such order.</li>
                                        <li>The supplier shall, at its own cost, replace and/or rectify the goods
                                            supplied in the event of any defects in the material.</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <footer>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="border: none; width:200px;" align="left" valign="top"><b
                                        class="bottom_b">Received By:</b><br></td>
                                <td rowspan="4" style="border: none; width:200px;" align="center"
                                    valign="bottom">{{ @$pk->createdby->full_name }}<br /><b class="bottom_b"
                                        style="font-size: 10px;">Prepared By</b></td>
                                <td rowspan="4" style="border: none; width:200px;" align="right"
                                    valign="bottom"><b class="bottom_b" style="font-size: 10px;">For
                                        {!! str_replace(
                                            'SYSCOM DISTRIBUTIONS LLC BRANCH ABU DHABI 1',
                                            'SYSCOM DISTRIBUTIONS LLC<br />BRANCH ABU DHABI 1',
                                            $company->company_name,
                                        ) !!}</b></td>
                            </tr>
                            <tr>
                                <td style="border: none;" align="left" valign="top"><b
                                        class="bottom_b">Name:</b><br></td>
                            </tr>
                            <tr>
                                <td style="border: none;" align="left" valign="top"><b
                                        class="bottom_b">Phone:</b><br></td>
                            </tr>
                            <tr>
                                <td style="border: none;" align="left" valign="top"><b
                                        class="bottom_b">Signature and stamp:</b></td>
                            </tr>

                        </table>

                    </footer>
                    <footer>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">

                            <tr>
                                <td colspan="3" style="border: none; font-size: 10px;" align="right"
                                    valign="top">
                                    {{-- Page No <span style="" class="pagenum"></span> of {{@$po->doc_number}}</td> --}}
                            </tr>
                        </table>
                        <img src="{!! asset('public/' . $company->pdf_footer . '') !!}" width="100%" /></td>
                    </footer>

                    <?php
                    function getIndianCurrency(float $number, string $r1, string $r2)
                    {
                        $decimal = round($number - ($no = floor($number)), 2) * 100;
                        $hundred = null;
                        $digits_length = strlen($no);
                        $i = 0;
                        $str = [];
                        $words = [0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'];
                        $digits = ['', 'Hundred', 'Thousand', '', 'Crore'];
                        while ($i < $digits_length) {
                            $divider = $i == 2 ? 10 : 100;
                            $number = floor($no % $divider);
                            $no = floor($no / $divider);
                            $i += $divider == 10 ? 1 : 2;
                            if ($number) {
                                $plural = ($counter = count($str)) && $number > 9 ? 's' : null;
                                $hundred = $counter == 1 && $str[0] ? ' and ' : null;
                                $str[] = $number < 21 ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                            } else {
                                $str[] = null;
                            }
                        }
                        $Rupees = implode('', array_reverse($str));
                        $paise = $decimal > 0 ? '.' . ($words[$decimal / 10] . ' ' . $words[$decimal % 10]) . ' ' . $r2 : '';
                        return ($Rupees ? $Rupees . $r1 : ' ') . $paise;
                    }
                    ?>



                </div>
                <div class="col-2 mb-2">&nbsp;</div>
            </div>
        </div>
    </div>
</div>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

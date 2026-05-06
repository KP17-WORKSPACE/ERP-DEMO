@extends('backEnd.master')
@section('mainContent')
    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach ($permissions as $permission) {
        @$module_links[] = @$permission->module_link_id;
        @$modules[] = @$permission->moduleLink->module_id;
    }
    $modules = array_unique(@$modules);
    $generalSetting = App\SmGeneralSettings::where('id', 1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    if (isset($generalSetting->logo)) {
        @$logo = @$generalSetting->logo;
    } else {
        $logo = 'public/uploads/settings/logo.png';
    }
    $sm_staff = App\SmStaff::where('user_id', Auth::user()->id)->first();
    if (!empty(@$sm_staff)) {
        @$profile_image = @$sm_staff->staff_photo;
        if (empty(@$profile_image)) {
            @$profile_image = 'public/uploads/staff/staff1.jpg';
        }
    }
    @endphp
    <section class="sms-breadcrumb mb-20 white-box">
        <div class="container-fluid">
            <div class="row" style="float: left;">
                <h1>@lang('Proforma Invoice')</h1>
            </div>
            <div class="row" style="float: right;">
                <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home"
                        aria-hidden="true"></i> Home</a>
                <a href="{{ url('proforma-invoice/create') }}" class="top-btn-r"><i class="far fa fa-plus"
                        aria-hidden="true"></i> New</a>
                <a href="{{ url('proforma-invoice') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i>
                    View</a>
                <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh"
                        aria-hidden="true"></i> Refresh</a>
            </div>
        </div>
    </section>
    <hr style="margin-top: 33px;" />
    <div style="clear: both;"></div>

    <section class="sms-breadcrumb mb-20 white-box top-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">

                </div>
                <div class="col-8" style="text-align: right;">
                    <div class="top-2-text top-2-text-last"><span>{{ Auth::user()->full_name }}</span><br />Owner</div>
                    <div class="top-2-text"><b>{{ date('m/d/Y') }}</b><br />Doc Date</div>
                    <div class="top-2-text">
                        <b>{{ isset($editData) ? (!empty(@$editData->doc_number) ? @$editData->doc_number : old('vat_number')) : old('vat_number') }}</b><br />Doc
                        Number
                    </div>
                </div>
                {{-- <div class="bc-pages">
                <a href="{{ url('dashboard') }}">@lang('lang.dashboard')</a>
                <a href="{{ url('purchase-invoice') }}">@lang('Purchase Invoice')</a>
                <a href="{{ url('purchase-invoice/create') }}" class="active">@lang('lang.create')</a>
            </div> --}}
            </div>
        </div>
    </section>



    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12 text-right">
                    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                        @if (session()->has('message-success'))
                            <p class="text-success">
                                {{ session()->get('message-success') }}
                            </p>
                        @elseif(session()->has('message-danger'))
                            <p class="text-danger">
                                {{ session()->get('message-danger') }}
                            </p>
                        @endif
                    @endif
                </div>

                <div class="col-lg-12">
                    @if (isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'quotations/' . $editData->id, 'method' => 'PUT', 'id' => 'quotations-form']) }}
                    @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'quotations', 'method' => 'POST', 'id' => 'quotations-form']) }}
                    @endif
                    <input type="hidden" name="id" value="{{ isset($editData) ? $editData->id : '' }}">
                    <div class="add-visitor">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="boxed-formctrl">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Customer Name<span>*</span></label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('vendors') ? ' is-invalid' : '' }}"
                                            name="customer" id="customer_with_vat">
                                            <option value=""></option>
                                            @foreach ($custsuppl as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($editData) ? (!empty($editData->customer) ? (@$editData->customer == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Narration<span>*</span></label>
                                        <input
                                            class="dynamicstxt primary-input w-100 form-control{{ $errors->has('number') ? ' is-invalid' : '' }}"
                                            type="text" name="narration" autocomplete="off" id="narration"
                                            value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('number')) : '' }}">
                                        @if ($errors->has('number'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('number') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Currency')</label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('currency') ? ' is-invalid' : '' }}"
                                            name="currency" id="currency">
                                            @foreach ($currency as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($editData) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('currency'))
                                            <span class="invalid-feedback invalid-select" role="alert">
                                                <strong>{{ $errors->first('currency') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Sales Man<span>*</span></label>
                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('sales_man') ? ' is-invalid' : '' }}"
                                            name="sales_man" id="sales_man">
                                            <option value="0"></option>
                                            @foreach ($sales_man as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($editData) ? (!empty(@$editData->sales_man) ? (@$editData->sales_man == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="boxed-formctrl">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Customer Ref No<span>*</span></label>
                                        <input
                                            class="dynamicstxt primary-input w-100 form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                            type="text" name="customer_ref_no" autocomplete="off" id="customer_ref_no"
                                            value="{{ isset($editData) ? (!empty(@$editData->customer_ref_no) ? @$editData->customer_ref_no : old('customer_ref_no')) : old('customer_ref_no') }}">
                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Customer Ref Date</label>
                                        @php
                                            $value = date('m/d/Y');
                                            if (isset($editData) && !empty($editData->customer_ref_date)) {
                                                @$value = date('m/d/Y', strtotime(@$editData->customer_ref_date));
                                            } else {
                                                if (!empty(old('date'))) {
                                                    @$value = old('date');
                                                } else {
                                                    @$value = date('m/d/Y');
                                                }
                                            }
                                        @endphp
                                        <input class="dynamicstxt primary-input w-100 date" id="customer_ref_date"
                                            type="text" name="customer_ref_date" value="{{ @$value }}">
                                        @if ($errors->has('customer_ref_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('customer_ref_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Delivery Terms<span>*</span></label>
                                        <input
                                            class="dynamicstxt primary-input w-100 form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                            type="text" name="delivery_terms" autocomplete="off" id="delivery_terms"
                                            value="{{ isset($editData) ? (!empty(@$editData->delivery_terms) ? @$editData->delivery_terms : old('delivery_terms')) : old('delivery_terms') }}">
                                        @if ($errors->has('delivery_terms'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('delivery_terms') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Payment Terms<span>*</span></label>

                                        <select
                                            class="dynamicstxt niceSelect w-100 bb form-control {{ $errors->has('payment_terms') ? ' is-invalid' : '' }}"
                                            name="payment_terms" id="payment_terms">
                                            <option value=""></option>
                                            @foreach ($paymentterms as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($editData) ? (!empty(@$editData->payment_terms) ? (@$editData->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl">Quote Validity<span>*</span></label>
                                        <input
                                            class="dynamicstxt primary-input w-100 form-control{{ $errors->has('quote_validity') ? ' is-invalid' : '' }}"
                                            type="text" name="quote_validity" autocomplete="off" id="quote_validity"
                                            value="{{ isset($editData) ? (!empty(@$editData->quote_validity) ? @$editData->quote_validity : old('quote_validity')) : old('quote_validity') }}">
                                        @if ($errors->has('quote_validity'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('quote_validity') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>



                            <div class="col-lg-4">
                                <div class="boxed-formctrl">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('VAT Type') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input w-100 form-control" type="text"
                                            id="vat_type" name="vat_type" readonly
                                            value="{{ isset($editData) ? (!empty(@$editData->vat_type) ? @$editData->vat_type : old('vat_type')) : old('vat_type') }}">
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('VAT Country') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input w-100 form-control" type="text"
                                            id="vat_country" name="vat_country" readonly
                                            value="{{ isset($editData) ? (!empty(@$editData->vat_country) ? @$editData->vat_country : old('vat_country')) : old('vat_country') }}">
                                    </div>
                                    <div class="input-effect">
                                        <div class="input-effect" id="sectionStateDiv">
                                            <label class="dynamicslbl"> @lang('VAT State') <span>*</span> </label>
                                            <input class="dynamicstxt primary-input w-100 form-control" type="text"
                                                id="vat_state" name="vat_state" readonly
                                                value="{{ isset($editData) ? (!empty(@$editData->vat_state) ? @$editData->vat_state : old('vat_state')) : old('vat_state') }}">
                                        </div>
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('VAT %') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input w-100 form-control" type="text"
                                            id="vat_percentage" name="vat_percentage" readonly
                                            value="{{ isset($editData) ? (!empty(@$editData->vat_percentage) ? @$editData->vat_percentage : old('vat_percentage')) : old('vat_percentage') }}">
                                    </div>
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('VAT Number') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input w-100 form-control" type="text"
                                            id="vat_number" name="vat_number" readonly
                                            value="{{ isset($editData) ? (!empty(@$editData->vat_number) ? @$editData->vat_number : old('vat_number')) : old('vat_number') }}">
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>


                    <div class="row">
                        <div class="col-lg-12">
                            <hr />
                        </div>
                    </div>
                    <div class="equipment comon-status row mt-20 d-block mr-2 ml-2">
                        <table class="sstable" cellspacing="0" width="100%" id="po-table">
                            <thead>
                                <tr>
                                    <th style="width:100px;">@lang('Part No')</th>
                                    <th style="width:150px;">@lang('Description')</th>
                                    {{-- <th style="width:200px;">@lang('Sl. No')</th> --}}
                                    <th style="width:70px;">@lang('Tax')</th>
                                    <th style="width:70px;">@lang('Qty')</th>
                                    <th style="width:80px;">@lang('Unit Price')</th>
                                    <th style="width:70px;">@lang('Value')</th>
                                    <th style="width:70px;">@lang('Discount')</th>
                                    <th style="width:120px;">@lang('Taxable Amount')</th>
                                    <th style="width:100px;">@lang('VAT Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $setroid = 8;
                                if (isset($editDataList)) {
                                    if (count($editDataList) > 0) {
                                        $setroid = count($editDataList) + 1;
                                    }
                                }
                                ?>
                                @for ($roid = 1; $roid < $setroid; $roid++)
                                    <tr id="rowone{{ $roid }}" onclick="fn_addRow({{ $roid }})">
                                        <td><select class="w-100 sstxtbx" name="part_number[]"
                                                id="part_number_{{ $roid }}"
                                                onchange="ddl_part_change({{ $roid }})">
                                                <option value="none"></option>
                                                @foreach ($items as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        @if (@$editDataList[$roid - 1]->part_number == @$value->id) selected @endif>
                                                        {{ @$value->part_number }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="w-100 sstxtbx" name="part_number_txt[]"
                                                id="part_number_txt_{{ $roid }}" readonly="true" hidden>
                                                <option value="none"></option>
                                                @foreach ($items as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <?php
                                            $des = @App\SmItem::select('description')
                                                ->where('id', $editDataList[$roid - 1]->part_number)
                                                ->first();
                                            ?>
                                            <input class="w-100 sstxtbx" type="text" id="description_{{ $roid }}"
                                                name="description[]" autocomplete="off" readonly="true"
                                                value="{{ $des->description }}">
                                        </td>
                                        {{-- <td>
                                            <input class="w-100 sstxtbx" type="text" id="slno_{{$roid}}" name="slno[]" autocomplete="off">
                                        </td> --}}
                                        <td>
                                            <select class="w-100 sstxtbx" name="tax[]" id="tax_{{ $roid }}"
                                                readonly="true" onchange="calc_change({{ $roid }})">
                                                <option value="{{ round($company->net_vat) }}">VAT
                                                    {{ round($company->net_vat) }}%</option>
                                                <option value="">None</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="qty_{{ $roid }}"
                                                name="qty[]" autocomplete="off" min="0"
                                                onchange="calc_change({{ $roid }})"
                                                value="{{ @$editDataList[$roid - 1]->qty }}">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="unitprice_{{ $roid }}"
                                                name="unitprice[]" autocomplete="off" min="0"
                                                onchange="calc_change({{ $roid }})"
                                                value="{{ @$editDataList[$roid - 1]->unitprice }}">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="value_{{ $roid }}"
                                                name="value[]" autocomplete="off" min="0" readonly
                                                value="{{ @$editDataList[$roid - 1]->value }}">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="discount_{{ $roid }}"
                                                name="discount[]" autocomplete="off" min="0"
                                                onchange="calc_change({{ $roid }})"
                                                value="{{ @$editDataList[$roid - 1]->discount }}">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number"
                                                id="taxableamount_{{ $roid }}" name="taxableamount[]"
                                                autocomplete="off" min="0" readonly
                                                value="{{ @$editDataList[$roid - 1]->taxableamount }}">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="vatamount_{{ $roid }}"
                                                name="vatamount[]" autocomplete="off" min="0" readonly
                                                value="{{ @$editDataList[$roid - 1]->vatamount }}">
                                        </td>
                                    </tr>
                                @endfor
                                <?php $roid--; ?>
                                <input type="hidden" id="qt-row-count" value="{{ $roid }}">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="sstablefoot"></td>
                                    <td class="sstablefoot"><label id="qty_total">0</label></td>
                                    <td class="sstablefoot"><label id="unitprice_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="value_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="discount_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="taxableamount_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="vatamount_total">0.00</label></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div style="display: none;">
                            <button type="button" class="primary-btn small fix-gr-bg" id="addRowQT"><span
                                    class="ti-plus pr-2"></span>@lang('lang.item')</button>
                        </div>


                        <script>
                            function fn_addRow(id) {
                                var rownum = document.getElementById('qt-row-count').value;
                                if (id == rownum) {
                                    document.getElementById('qt-row-count').value = (Number(rownum) + Number(1));
                                    document.getElementById('addRowQT').click();
                                }
                            }

                            function ddl_part_change(id) {
                                var selOpt = $('#part_number_' + id + ' :selected').val();
                                $('#part_number_txt_' + id + ' option[value=' + selOpt + ']').attr('selected', 'selected');
                                var selOpt2 = $('#part_number_txt_' + id + ' :selected').text();
                                $('#description_' + id + '').val(selOpt2);
                                $('#description_' + id + '').focus();
                            }

                            function calc_change(id) {
                                //var net_vat = $('#net_vat').val();
                                var net_vat = $('#tax_' + id + '').val();

                                var qty = $('#qty_' + id + '').val();
                                var unitprice = $('#unitprice_' + id + '').val();
                                var value = $('#value_' + id + '').val();
                                var discount = $('#discount_' + id + '').val();
                                var customcharges = $('#customcharges_' + id + '').val();

                                qty = (qty === '') ? '0' : qty;
                                unitprice = (unitprice === '') ? '0' : unitprice;
                                var fin_value = (unitprice * qty);
                                $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


                                value = (value === '') ? '0' : value;
                                discount = (discount === '') ? '0' : discount;
                                customcharges = (customcharges === '') ? '0' : customcharges;
                                var fin_taxableamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat) +
                                    100) / 100);
                                $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                var fin_vatamount = ((unitprice * qty) + Number(customcharges) - Number(discount)) * ((Number(net_vat)) / 100);
                                var vatamount = $('#vatamount_' + id + '').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                calc_total();
                            }

                            function calc_total() {
                                var countrow = document.getElementById('qt-row-count').value;
                                var t1 = 0,
                                    t2 = 0,
                                    t3 = 0,
                                    t4 = 0,
                                    t5 = 0,
                                    t6 = 0,
                                    t7 = 0;
                                for (var i = 1; i <= countrow; i++) {
                                    t1 += Number($('#qty_' + i).val());
                                    t2 += Number($('#unitprice_' + i).val());
                                    t3 += Number($('#value_' + i).val());
                                    t4 += Number($('#discount_' + i).val());
                                    t5 += Number($('#customcharges_' + i).val());
                                    t6 += Number($('#taxableamount_' + i).val());
                                    t7 += Number($('#vatamount_' + i).val());
                                }
                                $('#qty_total').text(t1);
                                $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#customcharges_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#taxableamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#vatamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
                            }

                            function fn_payment_terms() {
                                var val_payment_terms = $('#payment_terms').val();
                                if (val_payment_terms == 150) {
                                    $('#div_payment_terms').css('display', 'block');
                                } else {
                                    $('#div_payment_terms').css('display', 'none');
                                }
                            }

                            function fn_shipping_name() {
                                var shipping_id = $('#shipping_name').val();
                                var shipping_data = $('#ship_' + shipping_id).val();
                                var ret = shipping_data.split("#");
                                $('#shipping_address_1').val(ret[0]);
                                $('#shipping_address_1').focus();
                                $('#shipping_address_2').val(ret[1]);
                                $('#shipping_address_2').focus();
                                $('#shipping_contact_no').val(ret[2]);
                                $('#shipping_contact_no').focus();
                            }
                        </script>



                    </div>







                    <div class="row mt-40">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="primary-btn fix-gr-bg">
                                <span class="ti-check"></span>
                                @if (isset($editData))
                                    @lang('lang.update')
                                @else
                                    @lang('lang.save')
                                @endif
                                @lang('lang.quotation')

                            </button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        </div>
    </section>
@endsection

@section('script')
    <script>

$(window).ready(function() {
        $("#quotations-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});

        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>
@endsection
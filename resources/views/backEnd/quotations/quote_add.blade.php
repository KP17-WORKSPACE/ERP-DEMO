<?php try { ?>


    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'quotation-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'quotation-store-form', 'novalidate' => true]) }}

<input type="hidden" name="url" id="url" value="{{URL::to('/')}}">


<style>
    .form-item-table .select2-container--default .select2-selection--single {
        border: none !important;
    }

    .form-item-table .select2-container--default .select2-selection--single .select2-selection__arrow b {
        display: none !important;
    }
</style>
<input type="hidden" name="net_vat" id="net_vat" value="0">


<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">

        New (<span class="font-weight-600"
            id="new_code">{{ App\SysHelper::get_new_code_lead('sys_crm_deals', 'DL', 'code', session('logged_session_data.company_id')) }}</span>)
    </h4>
    <div class="purchase-order-content-header-right">
        <button type="submit" class="btn btn-light">
            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
        </button>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">
                <li><button class="dropdown-item d-flex align-items-center text-success"><i
                            class="ico icon-bold-download-minimalistic  text-success title-15 me-2"></i> Save &
                        Download</button></li>

            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Auto-focus Customer dropdown on page load
        setTimeout(function () {
            $('#cust_id').select2('open');
        }, 300);

        // Enter key navigation for #top-row inputs
        $('#top-row').on('keydown', 'input, select, textarea', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();

                // Get the current field's ID
                var currentId = $(this).attr('id');
                var $nextElement = null;

                // Define custom navigation flow
                switch (currentId) {
                    case 'cust_id':
                        $nextElement = $('#deal_name');
                        break;
                    case 'deal_name':
                        $nextElement = $('#estimated_close_date');
                        break;
                    case 'estimated_close_date':
                        $nextElement = $('#owner');
                        break;
                    case 'owner':
                        $nextElement = $('#company');
                        break;
                    case 'company':
                        // Last field in top-row, blur or move to next section
                        $(this).blur();
                        return;
                    default:
                        // Fallback to sequential navigation
                        var $focusableElements = $('#top-row').find('input:visible:not([disabled]):not([readonly]), select:visible:not([disabled]), textarea:visible:not([disabled]):not([readonly])');
                        var currentIndex = $focusableElements.index(this);
                        if (currentIndex > -1 && currentIndex < $focusableElements.length - 1) {
                            $nextElement = $focusableElements.eq(currentIndex + 1);
                        }
                }

                // Focus or open the next element
                if ($nextElement && $nextElement.length > 0) {
                    if ($nextElement.hasClass('js-example-basic-single') || $nextElement.hasClass('select2-hidden-accessible')) {
                        // Open Select2 dropdown
                        $nextElement.select2('open');
                    } else {
                        // Focus regular input/textarea
                        $nextElement.focus();
                    }
                }
            }
        });

        // Auto-fill Lead Name when Customer is selected and focus on Lead Name
        $('#cust_id').on('select2:select', function (e) {
            var selectedText = $(this).find('option:selected').text().trim();
            if (selectedText && selectedText !== 'Select') {
                // $('#lead_name').val(selectedText);
            }
            // Focus on Lead Name field
            setTimeout(function () {
                $('#deal_name').focus();
            }, 100);
        });

        // When Lead Name loses focus or Enter is pressed, open Brand dropdown
        $('#deal_name').on('blur', function () {
            if ($(this).val().trim() !== '') {
                setTimeout(function () {
                    $('#estimated_close_date').focus();
                }, 100);
            }
        });

        // Brand is multiselect - do not auto-jump to next field
        // User can select multiple brands before manually moving to Sales Person

        // When Sales Person is selected, open Company dropdown
        $('#owner').on('select2:select', function (e) {
            setTimeout(function () {
                $('#cust_id').select2('open');
            }, 100);
        });

        // Handle Enter key on select2 dropdowns to close and move to next field
        $(document).on('keydown', '.select2-search__field', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                var $select2Container = $(this).closest('.select2-container');
                var $originalSelect = $('#' + $select2Container.attr('aria-owns').replace('-results', '').replace('select2-', '').replace('-container', ''));

                if ($originalSelect.length) {
                    // Close the dropdown
                    $originalSelect.select2('close');

                    // Trigger the navigation based on which field it is
                    setTimeout(function () {
                        var selectId = $originalSelect.attr('id');

                        if (selectId === 'cust_id') {
                            $('#deal_name').focus();
                        } else if (selectId === 'deal_name') {
                            $('#estimated_close_date').focus();
                        } else if (selectId === 'estimated_close_date') {
                            $('#owner').select2('open');
                        }
                    }, 50);
                }
            }
        });
    });
</script>

<div class="card mb-3" id="top-row">
    <div class="card-body">
        <div class="row gap-rows">

            <div class="col-4">
                <label class="form-label">Customer
                    <a style="float: right; cursor: pointer;" class="text-success" data-bs-toggle="modal"
                        data-bs-target="#addcompany"><i class="ico icon-bold-buildings-2" aria-hidden="true"></i> Add
                    </a>

                </label>

                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required
                        onchange="change_cust_id()">
                        <option value=""></option>
                        @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}" @if(@$ctrl_cust_id == $value->id) selected @endif>
                                {{ trim(@$value->name) }}@if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] == 1)
                                ({{ trim(@$value->code) }})@endif</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-3">
                <label class="form-label">Deal Name</label>
                <div class="form-group">
                    <input class="form-control capitalize-title" type="text" name="deal_name" autocomplete="off"
                        id="deal_name"
                        value="{{ isset($edit) ? (!empty(@$edit->deal_name) ? @$edit->deal_name : old('deal_name')) : old('deal_name') }}"
                        required>
                </div>
            </div>





            <div class="col-2">
                <label class="form-label">Est. Closing Date *</label>
                <div class="form-group">
                    @php
                        $value = date('d/m/Y');
                        if (isset($edit) && $edit->estimated_close_date != "1970-01-01") {
                            @$value =
                                date('Y-m-d', strtotime(@$edit->estimated_close_date));
                        } else {
                            if (!empty(old('estimated_close_date'))) {
                                @$value = old('estimated_close_date');
                            } else {

                            }
                        }
                        //$value = @App\SysHelper::normalizeToDmy(@$value);
                    @endphp
                    <input class="form-control date-picker" id="estimated_close_date" type="text" autocomplete="off"
                        name="estimated_close_date" value="{{ @$value }}" required>
                </div>
            </div>
            <div class="col-3">
                <label class="form-label">Sales Person </label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                    </select>
                </div>
            </div>






        </div>
    </div>
</div>





<div class="d-flex mb-2 justify-content-center">
    <input type="hidden" name="quotation_generated" id="quotation_generated" value="">

</div>
<div class="deal-list-content-header">


    <div id="generate-quotation" style="height: auto;overflow: hidden; transition: all 0.5s ease;">

        <div class="tab-wrap mb-3">
            <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                        data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                        aria-selected="true">Quotation</button>
                </li>
            </ul>
            <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                    aria-labelledby="extra-fields-tab">

                    <div class="d-flex justify-content-end">

                    </div>


                    <div class="row gap-rows">
                        <div class="col-2">
                            <label class="form-label">Quote Validity:</label>
                            <div class="form-group">
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off"
                                    placeholder="Quote Validity" name="quote_validity" value="2 Weeks" required>
                            </div>
                        </div>
                        <div class="col-2" style="margin-top:-5px">
                            <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Payment Terms</span>
                                <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;"
                                    data-bs-toggle="modal" data-bs-target="#paymenttermsModal">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                </button>
                            </label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="payment_terms"
                                    id="payment_terms">

                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text" value=""
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt"
                                    style="display: none;">
                                <script>
                                    $('#payment_terms').on('change', function (e) {
                                        if ($('#payment_terms').val() == 22) {
                                            $('#payment_terms_txt').css("display", "block");
                                            $('#payment_terms_txt').prop('required', true);
                                        } else {
                                            $('#payment_terms_txt').css("display", "none");
                                            $('#payment_terms_txt').prop('required', false);
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-2">
                            <label class="form-label">Delivery Time:</label>
                            <div class="form-group">
                                <input class="form-control" id="delivery_time" type="text" autocomplete="off"
                                    placeholder="Delivery Time" name="delivery_time" value="2 Weeks" required>
                            </div>
                        </div>

                        <div class="col-2">
                            <label class="form-label">Currency:</label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="currency_id"
                                    id="currency_id">

                                    @foreach ($currencylist as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Terms and Condition:</label>
                            <div class="form-group">
                                <textarea class="form-control" rows="3" id="terms_and_condition" data-bs-toggle="modal"
                                    data-bs-target="#narrationModal" autocomplete="off"
                                    name="terms_and_condition"></textarea>
                            </div>
                            <script>
                                function updateTerms() {
                                    var $txt = $('#company option:selected').text();
                                    var $tc = "1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n" +
                                        "2. Please mention our Quotation No. in your Purchase Order\n" +
                                        "3. In case of non-availability of quote products " + $txt +
                                        " reserves the right to supply a functionally similar or better product.";
                                    $('#terms_and_condition').val($tc);
                                }

                                // Run once on page load
                                updateTerms();

                                // Run whenever company dropdown changes
                                $('#company').on('change', updateTerms);
                            </script>
                        </div>
                        <div class="col-1 mt-4">


                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                data-bs-target="#ModalExcelQuote">
                                <i class="ico icon-outline-import text-success" style="font-size: 16px"></i> Import
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="table-container" style="border: solid 1px #d9d9d9;">
            <table class="table table-hover form-item-table" id="myTable">
                <thead>
                    <tr>
                        <th class="resizable text-center" width="50px">@lang('No')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="250px">@lang('Part No') <a
                                class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#addproductModal"></a>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="250px">@lang('Description')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px">@lang('Cost')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px">@lang('Tax')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="40px">@lang('Qty')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px">@lang('Price')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px">@lang('Value')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="80px" scope="col">Dis <a
                                class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#discountModal"></a>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px">@lang('Taxable')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px">@lang('VAT')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="100px">@lang('Total')
                            <div class="resizer"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>



                    @php
                        $i = 1;
                    @endphp


                    @if (isset($cart) && count($cart) > 0)


                        @foreach ($cart as $cart_items)

                            <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{$i++}}" />
                                </td>
                                <td class="noborder">
                                    <select class="form-control noborder " name="part_number[]">
                                        <option value="{{ $cart_items->product_id }}">
                                            {{ $cart_items->partnumber }}
                                        </option>
                                    </select>
                                    {{-- on focus add this class and its funcanalities js-product-select --}}
                                </td>
                                <td><textarea class="form-control" name="description[]"
                                        rows="1">{{$cart_items->description}}</textarea></td>
                                <td>
                                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                        value="{{ number_format($cart_items->cost, 2) }}" onchange="calc_change_new(this)"
                                        onblur="formatCurrency(this)">
                                    <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="product_type_part_number_text[]"
                                        autocomplete="off" readonly="true" hidden>
                                </td>
                                <td><input type="number" class="form-control text-center" name="tax[]"
                                        value="{{$cart_items->vat}}" onchange="calc_change_new(this)"></td>
                                <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off"
                                        min="0" onchange="calc_change_new(this)" value="{{$cart_items->qty}}"></td>
                                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                        value="{{number_format($cart_items->price, 2)}}" autocomplete="off" min="0"
                                        onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                        readonly></td>
                                <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                        value="{{number_format($cart_items->discount, 2)}}" autocomplete="off" min="0"
                                        onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                        min="0" readonly></td>
                            </tr>


                        @endforeach

                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // trigger change on all qty fields once
                                document.querySelectorAll('input[name="qty[]"]').forEach(function (el) {
                                    el.dispatchEvent(new Event("change"));
                                });
                            });
                        </script>
                    @endif
                    <tr>
                        <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{$i}}" /></td>
                        <td class="noborder">
                            <select class="form-control noborder " name="part_number[]">
                            </select>
                            {{-- on focus add this class and its funcanalities js-product-select --}}
                        </td>
                        <td><textarea class="form-control" name="description[]" rows="1"></textarea></td>
                        <td>
                            <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                onchange="calc_change_new(this)" onblur="formatCurrency(this)">
                            <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                                readonly="true" hidden>
                            <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                                readonly="true" hidden>
                            <input class="form-control" type="text" name="product_type[]" autocomplete="off"
                                readonly="true" hidden>
                            <input class="form-control" type="text" name="product_type_part_number_text[]"
                                autocomplete="off" readonly="true" hidden>
                        </td>
                        <td><input type="number" class="form-control text-center" name="tax[]"
                                onchange="calc_change_new(this)"></td>
                        <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off"
                                min="0" onchange="calc_change_new(this)"></td>
                        <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                autocomplete="off" min="0" onchange="calc_change_new(this)"
                                onblur="formatCurrency(this)"></td>
                        <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                readonly></td>
                        <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                autocomplete="off" min="0" onchange="calc_change_new(this)"
                                onblur="formatCurrency(this)"></td>
                        <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                min="0" readonly></td>
                        <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                min="0" readonly></td>
                        <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                min="0" readonly></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" scope="col">Total</th>
                        <th class="text-end"><label id="lbl_total_cost">0</label></th>
                        <th class="text-center"></th>
                        <th class="text-center"><label id="lbl_total_qty">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_price">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_value">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_discount">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_taxableamount">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_vatamount">0</label></th>
                        <th class="text-end" scope="col"><label id="lbl_total_totalamount">0</label></th>
                    </tr>
                </tfoot>
            </table>
            <div id="contextMenu">
                <button type="button" id="addRow">Add Row</button>
                <button type="button" id="deleteRow">Delete Row</button>
            </div>
        </div>

       
    </div>

</div>

<table class="table form-item-table" id="">
                            <tr>
                                <td class="text-end"><b>Additional Discount : </b></td>
                                <td class="text-end" style="width: 50px;">
                                    <input type="number" class="form-control text-end" id="deal_discount_vat" name="deal_discount_vat"  step="any" placeholder="VAT %" />
                                </td>
                                <td class="text-end" style="width: 103px;">
                                    <input type="number" class="form-control text-end" id="deal_discount" name="deal_discount" value="0" step="any" placeholder="Aditional Discount" />
                                </td>
                            </tr>
                            </table>
 <table class="table table-hover form-item-table" id="">
                                    <thead>
                                        <tr>
                                            <th class="resizable text-center" width="300px" scope="col" >Name<div class="resizer"></div></th>
                                            <th class="resizable text-center" scope="col" >Credit Account<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="200px" >Amount<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="250px" >Remarks<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                
                            <select class="form-control js-example-basic-single noborder" name="cfc_name[]" id="cfc_name_1">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">@if (@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_account_code'])
                                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                                    @else
                                                        {{ @$value->account_name }}
                                                    @endif</option>
                                @endforeach
                            </select>
                                            </td>
                                            <td>
                                                <select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}"> @if (@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_supplier_code'])
                                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                                    @else
                                                        {{ @$value->account_name }}
                                                    @endif</option>
                                @endforeach
                            </select>
                                            </td>
                                            <td><input class="form-control text-end" type="number" id="cfc_amount_1" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(1)"></td>
                                            <td><input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                                autocomplete="off"></td>
                                        </tr>
                                        <tr>
                                            <td><select class="form-control js-example-basic-single noborder" name="cfc_name[]" id="cfc_name_2">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}"> @if (@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_account_code'])
                                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                                    @else
                                                        {{ @$value->account_name }}
                                                    @endif</option>
                                @endforeach
                            </select></td>
                                            <td><select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}"> @if (@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_supplier_code'])
                                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                                    @else
                                                        {{ @$value->account_name }}
                                                    @endif</option>
                                @endforeach
                            </select></td>
                                            <td><input class="form-control text-end" type="number" id="cfc_amount_2" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(2)"></td>
                                            <td><input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                                autocomplete="off"></td>
                                        </tr>
                                    </tbody>
                                </table>


{{ Form::close() }}



{{-- Models --}}
<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

@include('backEnd.inventory.itemAddModal')

<div class="modal side-panel fade" id="discountModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 155px !important; width:170px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Add Discount</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Discount Amount</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="discountInput" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="discount_add_btn">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Split Discount
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="descriptionModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="height: 300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Description</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Description:</label>
                                <div class="form-group">
                                    <textarea type="text" class="form-control" id="add_description"
                                        style="height: 150px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="addDescription()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).on("keydown", 'input[name="cost[]"], input[name="tax[]"], input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"]', function (e) {
        if (e.key === "Enter") {
            e.preventDefault(); // prevent form submit

            let row = $(this).closest("tr"); // current row
            let name = $(this).attr("name");

            if (name === "cost[]") {
                row.find('input[name="qty[]"]').focus();
            }
            else if (name === "tax[]") {
                row.find('input[name="qty[]"]').focus();
            }
            else if (name === "qty[]") {
                row.find('input[name="unitprice[]"]').focus();
            }
            else if (name === "unitprice[]") {
                row.find('input[name="discount[]"]').focus();
            }
            else if (name === "discount[]") {
                // Jump to next row's part_number[] and open Select2 dropdown
                let nextRow = row.next("tr");
                if (nextRow.length) {
                    let partNumberSelect = nextRow.find('select[name="part_number[]"]');
                    if (partNumberSelect.length) {
                        // Add the js-product-select class so the focus handler can initialize Select2
                        if (!partNumberSelect.hasClass('js-product-select')) {
                            partNumberSelect.addClass('js-product-select');
                        }

                        // Trigger focus - the existing focus handler for .js-product-select 
                        // will initialize Select2 and open the dropdown automatically
                        partNumberSelect.trigger('focus');
                    }
                }
            }

        }
    });

    // Normalize discount input while typing: remove leading zeros except when decimal like 0.x
    $(document).on('input', 'input[name="discount[]"]', function () {
        var $el = $(this);
        var val = ($el.val() || '').toString();
        if (!val) return;

        // Remove commas (formatting) for checking
        var raw = val.replace(/,/g, '');

        // if raw starts with 0 followed by non-dot, strip leading zeros
        if (raw.length > 1 && raw.charAt(0) === '0' && raw.charAt(1) !== '.') {
            var cleaned = raw.replace(/^0+/, '');
            if (cleaned === '') cleaned = '0';
            // set the cleaned value (no commas). formatting/blur will handle display later.
            $el.val(cleaned);
        }
    });
</script>

<script>
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function () {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'textarea[name="description[]"]', function () {
        currentDescriptionInput = $(this);
        $('#add_description').val(currentDescriptionInput.val());
        descriptionModal.show();
        setTimeout(() => $('#add_description').focus(), 500);

    });

    function addDescription() {
        if (currentDescriptionInput) {
            const val = $('#add_description').val();
            currentDescriptionInput.val(val);
            descriptionModal.hide();
            currentDescriptionInput = null;
        }
    }
</script>

{{-- Models --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const referenceInput2 = document.getElementById('note');
        const narrationTextarea2 = document.getElementById('narrationTextarea2');
        const insertButton2 = document.getElementById('insertNarration2');
        const narrationModal2 = document.getElementById('NoteModal');

        // Pre-fill textarea when modal opens
        narrationModal2.addEventListener('shown.bs.modal', () => {
            narrationTextarea2.value = referenceInput2.value;
            setTimeout(() => $('#narrationTextarea2').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton2.addEventListener('click', () => {
            referenceInput2.value = narrationTextarea2.value;
            bootstrap.Modal.getInstance(narrationModal2).hide();
        });
    });
</script>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const referenceInput2Address = document.getElementById('address');
        const narrationTextarea2Address = document.getElementById('narrationTextarea2Address');
        const insertButton2Address = document.getElementById('insertNarration2Address');
        const narrationModal2Address = document.getElementById('AddressModal');

        // Pre-fill textarea when modal opens
        narrationModal2Address.addEventListener('shown.bs.modal', () => {
            narrationTextarea2Address.value = referenceInput2Address.value;
            setTimeout(() => $('#narrationTextarea2Address').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton2Address.addEventListener('click', () => {
            referenceInput2Address.value = narrationTextarea2Address.value;
            bootstrap.Modal.getInstance(narrationModal2Address).hide();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const referenceInput2Email = document.getElementById('cust_email');
        const narrationTextarea2Email = document.getElementById('narrationTextarea2Email');
        const insertButton2Email = document.getElementById('insertNarration2Email');
        const narrationModal2Email = document.getElementById('EmailModal');

        // Pre-fill textarea when modal opens
        narrationModal2Email.addEventListener('shown.bs.modal', () => {
            narrationTextarea2Email.value = referenceInput2Email.value;
            setTimeout(() => $('#narrationTextarea2Email').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton2Email.addEventListener('click', () => {
            referenceInput2Email.value = narrationTextarea2Email.value;
            bootstrap.Modal.getInstance(narrationModal2Email).hide();
        });
    });
</script>

<div class="modal side-panel fade" id="NoteModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Notes</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control capitalize-title"
                            id="narrationTextarea2" rows="6" placeholder="Write narration here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal side-panel fade" id="AddressModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea2Address"
                            rows="6" placeholder="Write address here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2Address" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>





<div class="modal side-panel fade" id="EmailModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Email</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <input class="form-control" id="narrationTextarea2Email" rows="6"
                            placeholder="Write email here...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration2Email" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>



<script>
    function splitAmount(modalInputId, targetFieldName) {
        const amount = parseFloat(document.getElementById(modalInputId).value);
        if (isNaN(amount) || amount <= 0) {
            alert("Please enter a valid amount.");
            return;
        }

        const valueFields = document.querySelectorAll('input[name="value[]"]');
        const targetFields = document.querySelectorAll(`input[name="${targetFieldName}[]"]`);

        let totalValue = 0;
        let validRows = [];

        valueFields.forEach((input, index) => {
            const val = parseFloat(input.value);
            if (!isNaN(val) && val > 0) {
                totalValue += val;
                validRows.push({ index, input });
            }
        });

        if (totalValue === 0) {
            alert("All rows have empty or zero 'Value'. Nothing to split.");
            return;
        }

        validRows.forEach(({ index, input }) => {
            const rowVal = parseFloat(input.value);
            const share = (rowVal / totalValue) * amount;

            const targetInput = targetFields[index];
            targetInput.value = share.toFixed(2);

            const row = targetInput.closest('tr');
            calc_change_new(row);
        });

        if (typeof update_totals === 'function') {
            update_totals();
        }
    }

    document.getElementById("discount_add_btn").addEventListener("click", function () {
        splitAmount('discountInput', 'discount');
        $('#discountModal').modal('hide');
    });
</script>



<script>
    function calc_change_new(el) {
        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '0';
        // unitprice may contain expressions like +100, -50, +10%, -10% or absolute numbers like 1000
        var unitpriceRaw = ($row.find('input[name="unitprice[]"]').val() || '').toString().trim();

        // helper parser
        function parseUnitPrice(raw, cost) {
            if (!raw || raw === '') return null;
            raw = raw.replace(/\s+/g, '').replace(/,/g, '');

            // percentage: [+-]?number%
            var m = raw.match(/^([+-]?)(\d+(?:\.\d+)?)%$/);
            if (m) {
                var sign = m[1];
                var pct = parseFloat(m[2]);
                if (!Number.isFinite(pct)) return null;
                if (!Number.isFinite(cost)) cost = 0;
                return sign === '-' ? (cost * (1 - pct / 100)) : (cost * (1 + pct / 100));
            }

            // relative number + or -
            var m2 = raw.match(/^([+-])(\d+(?:\.\d+)?)$/);
            if (m2) {
                var sign2 = m2[1];
                var val = parseFloat(m2[2]);
                if (!Number.isFinite(val)) return null;
                if (!Number.isFinite(cost)) cost = 0;
                return sign2 === '-' ? (cost - val) : (cost + val);
            }

            // absolute number
            var v = parseFloat(raw);
            if (Number.isFinite(v)) return v;

            return null;
        }

        var unitprice = null;
        var discount = $row.find('input[name="discount[]"]').val().replace(/,/g, '') || '0';
        var fright = 0;
        var customcharges = 0;

        var decimal_point = @json(session('logged_session_data.decimal_point'));

        // Decide unitprice numeric value. If the field contained a relative expression, compute final
        var costRaw = $row.find('input[name="cost[]"]').val().replace(/,/g, '') || '0';
        var cost = parseFloat(costRaw);
        if (!Number.isFinite(cost)) cost = 0;

        if (typeof unitpriceRaw === 'string' && (unitpriceRaw.indexOf('%') !== -1 || unitpriceRaw[0] === '+' || unitpriceRaw[0] === '-')) {
            var computed = parseUnitPrice(unitpriceRaw, cost);
            if (computed !== null) {
                unitprice = computed;
                // write back formatted value to input
                var decimal_point = parseInt(@json(session('logged_session_data.decimal_point') ?? 2));
                if (!Number.isFinite(decimal_point)) decimal_point = 2;
                try { $row.find('input[name="unitprice[]"]').val(typeof formatAmount === 'function' ? formatAmount(Number(unitprice).toFixed(decimal_point)) : Number(unitprice).toFixed(decimal_point)); } catch (err) { $row.find('input[name="unitprice[]"]').val(Number(unitprice).toFixed(decimal_point)); }
            } else {
                // invalid expression -> keep original parsing behavior
                unitprice = parseFloat(unitpriceRaw) || 0;
            }
        } else {
            unitprice = parseFloat(unitpriceRaw.replace(/,/g, '')) || 0;
        }

        // Calculate value
        var fin_value = parseFloat(unitprice) * parseFloat(qty);
        $row.find('input[name="value[]"]').val(formatAmount(fin_value));

        // Calculate taxable amount
        var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
        $row.find('input[name="taxableamount[]"]').val(formatAmount(fin_taxableamount));

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
        $row.find('input[name="vatamount[]"]').val(formatAmount(fin_vatamount));

        // Calculate total amount
        var total_amount = fin_taxableamount + fin_vatamount;
        $row.find('input[name="totalamount[]"]').val(formatAmount(total_amount));

        $("#loading_bg").css("display", "none");
        update_totals();
    }
    function update_totals() {
        let total_qty = 0,
            total_price = 0,
            total_value = 0,
            total_discount = 0,
            //total_fright = 0,
            //total_customcharges = 0,
            total_taxableamount = 0,
            total_vatamount = 0,
            total_totalamount = 0;
        total_cost = 0;

        const decimal_point = @json(session('logged_session_data.decimal_point'));

        $('#myTable tbody tr').each(function () {
            const $row = $(this);

            total_qty += parseFloat($row.find('input[name="qty[]"]').val()) || 0;
            total_price += parseFloat($row.find('input[name="unitprice[]"]').val().replace(/,/g, '')) || 0;
            total_value += parseFloat($row.find('input[name="value[]"]').val().replace(/,/g, '')) || 0;
            total_discount += parseFloat($row.find('input[name="discount[]"]').val().replace(/,/g, '')) || 0;
            //total_fright += parseFloat($row.find('input[name="fright[]"]').val()) || 0;
            //total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val()) || 0;
            total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val().replace(/,/g, '')) || 0;
            total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val().replace(/,/g, '')) || 0;
            total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val().replace(/,/g, '')) || 0;

            total_cost += (
                parseFloat($row.find('input[name="cost[]"]').val().replace(/,/g, '')) || 0
            ) * (
                    parseFloat($row.find('input[name="qty[]"]').val()) || 0
                );

        });

        $('#lbl_total_qty').text(total_qty);
        $('#lbl_total_price').text(formatAmount(total_price));
        $('#lbl_total_value').text(formatAmount(total_value));
        $('#lbl_total_discount').text(formatAmount(total_discount));
        //$('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
        //$('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
        $('#lbl_total_taxableamount').text(formatAmount(total_taxableamount));
        $('#lbl_total_vatamount').text(formatAmount(total_vatamount));
        $('#lbl_total_totalamount').text(formatAmount(total_totalamount));
        $('#lbl_total_cost').text(formatAmount(total_cost));
    }
</script>
<script>

    $(document).on('focus', 'select[name="part_number[]"]', function () {
        const $select = $(this);

        // Add the class if not present
        if (!$select.hasClass('js-product-select')) {
            $select.addClass('js-product-select');
            //$select.remove('select2-hidden-accessible');

            // Initialize Select2
            initAccountSelect2(this); // your existing function
        }
    });




    $(document).ready(function () {



        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search_text: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.account_code + ' - ' + item.account_name
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'Select Account',
                minimumInputLength: 2
            });
        }

        // Initial init
        initAccountSelect2('.js-account-select');

        // Re-initialize on focus (if needed for dynamically added fields)
        $(document).on('focus', '.js-account-select', function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
            }
        });

        // Open dropdown and focus search box on click
        $(document).on('click', '.js-account-select', function () {
            $(this).select2('open');
        });

        // Focus the search input inside the opened Select2 dropdown
        $(document).on('select2:open', function () {
            setTimeout(function () {
                const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 0);
        });
    });
</script>

<script>
    $(document).ready(function () {
        function initAccountSelect2(selector) {
            $(selector).select2({
                ajax: {
                    url: '{{ route("autocomplete.get_product_list_ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search_text: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.part_number,
                                    description: item.description,
                                    hscode: item.hscode,
                                    product_type: item.product_type
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: '',
                minimumInputLength: 2,
                dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
            });

            $(selector).on('select2:select', function (e) {
                var selectedData = e.params.data;
                var $row = $(this).closest('tr'); // find the closest row




                // Set values using "name" attribute selectors inside the same row
                //$row.find('input[name="description[]"]').val(selectedData.description || '');
                $row.find('textarea[name="description[]"]').val(selectedData.description || '');
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
                $row.find('input[name="discount[]"]').val(0);
                $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                $row.find('input[name="cost[]"]').focus();
            });


            // prefill Select2 search with currently selected value when dropdown opens
            $(selector).on('select2:open', function () {
                try {
                    var sel = $(this).select2('data');
                    if (sel && sel.length && sel[0].text) {
                        setTimeout(function () {
                            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                            if (searchInput) {
                                searchInput.value = sel[0].text.trim();
                                // trigger input event so select2 filters on prefilling
                                var event = new Event('input', { bubbles: true });
                                searchInput.dispatchEvent(event);
                                try {
                                    var len = searchInput.value.length;
                                    searchInput.setSelectionRange(len, len);
                                } catch (err) { /* ignore */ }
                            }
                        }, 0);
                    }
                } catch (err) {
                    console.error('Error prefilling product search field', err);
                }
            });


        }

        initAccountSelect2('.js-product-select');

        // Re-initialize on focus if needed
        $(document).on('focus', '.js-product-select', function () {
            if (!$(this).hasClass("select2-hidden-accessible")) {
                initAccountSelect2(this);
                $(this).select2('open');
            }
        });

        // On click, open dropdown and focus on search field
        $(document).on('click', '.js-product-select', function () {
            $(this).select2('open');
        });

        // Optional: Auto focus on search input when dropdown opens
        $(document).on('select2:open', function () {
            setTimeout(function () {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 0);
        });
    });
</script>

<script>
    /*table row fill based on layout height*/
    window.onload = function () {
        const table = document.getElementById('myTable');
        const tbody = table.querySelector('tbody');

        // If there are no rows, do nothing
        if (tbody.rows.length === 0) return;

        const rowHeight = tbody.rows[0].offsetHeight;
        const pageHeight = window.innerHeight - 65;
        const tableTop = table.getBoundingClientRect().top;
        const availableHeight = pageHeight - tableTop;

        let existingRows = tbody.rows.length;
        let totalRows = Math.floor(availableHeight / rowHeight);

        const lastRow = tbody.rows[tbody.rows.length - 1];

        for (let i = existingRows + 1; i <= totalRows; i++) {
            const newRow = lastRow.cloneNode(true); // clone entire row

            const firstCellInput = newRow.cells[0].querySelector('input');
            if (firstCellInput) {
                firstCellInput.value = i;
            }
            const inputs = newRow.querySelectorAll('input');
            inputs.forEach((input, index) => {
                if (index !== 0) input.value = "";
            });

            tbody.appendChild(newRow);
        }
    };
    /*table row fill based on layout height*/
</script>


<script>
    $(document).ready(function () {
        if ($("#source").val() == "Other") { $("#source_o").css("display", "block"); $("#source_o").prop('required', true); $("#sourcediv").css("display", "block"); }
        else { $("#source_o").css("display", "none"); $("#source_o").prop('required', false); $("#sourcediv").css("display", "none"); }
    });

    $(document).on("change", "#source", function () {
        if ($("#source").val() == "Other") { $("#source_o").css("display", "block"); $("#source_o").prop('required', true); $("#sourcediv").css("display", "block"); }
        else { $("#source_o").css("display", "none"); $("#source_o").prop('required', false); $("#sourcediv").css("display", "none"); }
    });

    function change_cust_id() {
        var id = $("#cust_id").val();
        var user = $("#user_id").val();
        get_cust_name(id);
        get_sales_person(id, user);
        get_vat(id);
    }

    function get_cust_name(id) {
        console.log("Fetching customer details for ID:", id);
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('crm-leads-customername') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                console.log(dataResult);
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i].first_name + ' ' + dataResult['data'][i].last_name;
                        var address = dataResult['data'][i].flat_office_no + ', ' + dataResult['data'][i]
                            .area + ', ' + dataResult['data'][i].city + ', ' + dataResult['data'][i].statename + ', ' + dataResult['data'][i].name;
                        $("#cust_name").val(name.replace('null ', '').replace('null', ''));
                        $("#designation").val(dataResult['data'][i].designation);
                        $("#cust_no").val(dataResult['data'][i].mobile);
                        $("#cust_email").val(dataResult['data'][i].email);
                        $("#address").val(address);
                        $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');

                        //1.Reseller
                        if (dataResult['data'][i].account_type == 1) {
                            $("#isproject").val(1);
                            $('#is_professional_service').prop("checked", false);
                        }//2.Enduser
                        if (dataResult['data'][i].account_type == 2) {
                            $("#isproject").val(2);
                            $('#is_professional_service').prop("checked", false);
                        }//3.Ecommerce
                        if (dataResult['data'][i].account_type == 3) {
                            $("#isproject").val(3);
                            $('#is_professional_service').prop("checked", false);
                        }
                    }
                }
                else {
                    $("#cust_name").val();
                    $("#designation").val();
                    $("#cust_no").val();
                    $("#cust_email").val();
                    $("#address").val();
                    $("#isproject").val();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function get_vat(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-vat-by-id') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                    $("#loading_bg").css("display", "none");
                } else {
                    $("#net_vat").val(dataResult['data'][0].vat_percentage);
                    $("#loading_bg").css("display", "none");
                }
            }
        });
    }


    function get_sales_person(id, user) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-salesperson-list') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function (dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    $('#owner').find('option').remove();
                    for (var i = 0; i < len; i++) {
                        var id = dataResult['data'][i].id;
                        var name = dataResult['data'][i].full_name;
                        var sele = '';
                        if (user == id) { sele = 'selected'; }
                        var option = "<option value='" + id + "' " + sele + ">" + name + "</option>";
                        $("#owner").append(option);
                    }
                }
                else {
                    $('#owner').find('option').remove();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

    $(document).on("click", "#btn_add_company", function () {

        //$("#btn_add_company").css("display", "none");

        var company_name_add = $("#company_name_add").val();
        var cust_name_add = $("#cust_name_add").val();
        var designation_add = $("#designation_add").val();
        var cust_no_add = $("#cust_no_add").val();
        var cust_email_add = $("#cust_email_add").val();

        var cust_area = $("#cust_area").val();
        var place_id = $("#place_id").val();
        var cust_building_name = $("#cust_building_name").val();
        var cust_flat_office_no = $("#cust_flat_office_no").val();
        var customer_website = $("#customer_website").val();
        var maps_location = $("#maps_location").val();





        var country_add = $("#country_ship").val();

        var cust_city = $("#cust_city").val();
        var state_ship = $("#state_ship").val();
        var cust_pobox = $("#cust_pobox").val();
        var sales_person = $("#cust_sales_person").val();

        if (sales_person == null || sales_person == "") {
            alert("Please select Sales Person");
            return;
        }

        var payment_terms = $("#payment_terms2").val();
        var account_type = $("#account_type").val();
        var company_id = $("#company").val();

        var action = "{{ URL::to('add-customer-detail-popup') }}";
        $("#loading_bg").show();

        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                company_name_add: company_name_add,
                cust_name_add: cust_name_add,
                designation_add: designation_add,
                cust_no_add: cust_no_add,
                cust_email_add: cust_email_add,
                vat_country: country_add,
                city: cust_city,
                vat_state: state_ship,
                zip_code: cust_pobox,
                sales_person: sales_person,
                payment_terms: payment_terms,
                account_type: account_type,
                company_id: company_id,
                customer_website: customer_website,
                maps_location: maps_location,
                place_id: place_id,
                area: cust_area,
                building_name: cust_building_name,
                flat_no: cust_flat_office_no,
            },
            cache: false,
            success: function (dataResult) {
                //alert(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                    $("#btn_add_company").css("display", "block");
                }
                else if (dataResult['data'] == "ERROR2") {
                    alert("Company Name already exists!! Please Contact Support");
                    $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                    $("#btn_add_company").css("display", "block");
                }
                else {
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {

                        $('#cust_id').find('option').not(':first').remove();
                        var newCompanyId = dataResult['new_company_id'];

                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var name = dataResult['data'][i].name;
                            var name2 = dataResult['data'][i].code;
                            var option = "<option value='" + id + "'>" + name + "</option>";
                            $("#cust_id").append(option);
                        }
                        if (newCompanyId) {
                            $("#cust_id").val(newCompanyId).trigger('change');
                        }

                        $("#loading_bg").hide();
                        $("#addcompany").modal('hide');

                        toastr.success("Customer added successfully", "Success");
                        // $("#btn_add_company").css("display", "block");
                        //location.reload();
                        //$("#company_name").change();
                    }
                }
            },
            error: function (xhr, status, error) {
                // Optional: show error message
                alert("Failed To Add Company: " + error);
            },
            complete: function () {
                // This runs always, after success or error
                $("#loading_bg").hide();
            }
        });
    });

  
    $(document).ready(function () {
        // Trigger change event only if a country is selected by default
        if ($('#country_ship').val() !== '') {
            $('#country_ship').trigger('change');
        }
    });

    $(document).ready(function () {
        $('.open-comments-modal').click(function () {
            $("#loading_bg").css("display", "block");


            var leadId = $(this).data('deal-id');
            var $body = $('#commentsModalBody');
            $body.html('<tr><td colspan="3" class="text-center text-muted">Loading...</td></tr>');

            $.ajax({
                url: '/crm-deals/comments/' + leadId,
                method: 'GET',
                dataType: 'json',
                success: function (res) {
                    $body.empty();
                    if (res.data && res.data.length > 0) {
                        $.each(res.data, function (i, comment) {
                            var row = `
                                        <tr>
                                            <td>${comment.comments}</td>
                                            <td>${comment.createdby.first_name || '-'} ${comment.createdby.last_name || '-'}</td>
                                            <td>
                                           ${comment.commentsdoc ? ` <a class="text-info p-0"
                                                    href="{{asset('public/uploads/crm_deal_doc/')}}/${comment.commentsdoc}"
                                                    target="_blank"><i class="fa fa-paperclip"
                                                        aria-hidden="true"></i>&nbsp;&nbsp;View File</a>` : ''}

                                            </td>
                                            <td>${formatDateTime(comment.created_at)}</td>
                                        </tr>`;
                            $body.append(row);
                        });
                    } else {
                        $body.html(
                            '<tr><td colspan="3" class="text-center text-muted">No comments found</td></tr>'
                        );
                    }
                    $("#loading_bg").css("display", "none");

                    $('#commentsModal').modal('show');
                },
                error: function () {
                    $body.html(
                        '<tr><td colspan="3" class="text-danger text-center">Error loading comments</td></tr>'
                    );
                }
            });



        });

    });

    function formatDateTime(datetime) {
        var date = new Date(datetime);
        return date.toLocaleString('en-IN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }


    $(document).on('click', '.open-delete-modal', function () {
        var leadId = $(this).data('id');
        var actionUrl = "{{ url('crm-deals') }}/" + leadId + "/delete";
        $('#deleteForm').attr('action', actionUrl);
    });

    $(document).on('click', '.open-restore-modal', function () {
        var leadId = $(this).data('id');
        var actionUrl = "{{ url('crm-deals') }}/" + leadId + "/restore";
        $('#restoreForm').attr('action', actionUrl);
    });



</script>






<div class="modal side-panel fade" id="addcompany" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="top:25%;max-width:1000px!important;left: 37%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Add Customer</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row gap-rows row-cols-5">

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Customer Name</label>
                                    <input class="form-control" placeholder="" type="text" aria-describedby=""
                                        autocomplete="off" id="company_name_add" required>
                                    <style>
                                        #company_name_add_list ul {
                                            width: 380px;
                                            left: 16rem
                                        }
                                    </style>
                                    <div id="company_name_add_list">
                                    </div>
                                    <script>
                                        $(document).ready(function () {

                                            $('#company_name_add').keyup(function () {
                                                var query = $(this).val();
                                                if (query != '') {
                                                    var _token = $('input[name="_token"]').val();
                                                    $.ajax({
                                                        url: "{{ route('autocomplete.customer_name') }}",
                                                        method: "POST",
                                                        data: { query: query, _token: _token },
                                                        success: function (data) {
                                                            $('#company_name_add_list').fadeIn();
                                                            $('#company_name_add_list').html(data);
                                                        }
                                                    });
                                                }
                                            });

                                            $(document).on('click', 'li', function () {
                                                $('#customer_name').val('');
                                                $('#customer_name_display').val('');
                                                // toastr.info('Customer Already Exists.', 'Info');



                                                $('#company_name_add_list').fadeOut();
                                            });

                                            $(document).click(function (e) {
                                                if (!$(e.target).closest('#company_name_add, #company_name_add_list').length) {
                                                    $('#company_name_add_list').fadeOut();
                                                }
                                            });

                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Contact Person Name</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_name_add"
                                        required>
                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Mobile</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_no_add" required
                                        value="+971">
                                </div>
                            </div>
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Email</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_email_add"
                                        required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Country</label>
                                    <select class="form-control js-example-basic-single" name="country_ship"
                                        id="country_ship">
                                        <option value="">-Select-</option>
                                        @foreach ($country as $value)
                                            <option value="{{ @$value->id }}" {{ trim(strtolower($value->name)) == 'united arab emirates' ? 'selected' : '' }}>{{ @$value->name }}</option>
                                        @endforeach
                                    </select>

                                    <div style="display:none">

                                        <select class="form-select js-example-basic-single"
                                            style="width:30px;display:none" name="country_telephone"
                                            id="country_telephone" required>
                                            <option value="" disabled selected>Select Country</option>
                                            @foreach ($country as $key => $value)
                                                <option value="{{ @$value->iso2 }}|{{ @$value->id }}">{{ @$value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>


                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">State</label>
                                    <div id="sectionStateDiv_ship">
                                        <select class="form-control js-example-basic-single" name="state_ship"
                                            id="state_ship">
                                            <option data-display="" value=""></option>
                                            <?php    try { ?>
                                            @if (isset($editData) && $editData->vat_state != '')
                                                <option data-display="{{ $editData->vatstate->name }}"
                                                    value="{{ $editData->vat_state }}" selected>
                                                    {{ $editData->vatstate->name }}
                                                </option>
                                            @endif
                                            <?php    } catch (\Exception $e) {
    } ?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">City</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_city" required>
                                </div>
                            </div>


                            <div class="col">
                                <label for="" class="form-label">Area</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_area" required>

                            </div>

                            <div class="col">
                                <label for="" class="form-label">Building Name</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_building_name"
                                    required>

                            </div>

                            <div class="col">
                                <label for="" class="form-label">Flat/Office No</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_flat_office_no"
                                    required>

                            </div>
                            {{-- <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Address 1</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_address_add"
                                        required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Address 2</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_address_add2"
                                        required>
                                </div>
                            </div> --}}

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">PO Box</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_pobox" required>
                                </div>
                            </div>
                            <input type="hidden" name="place_id" id="place_id" value="">
                            <input type="hidden" name="customer_website" id="customer_website" value="">
                            <input type="hidden" name="maps_location" id="maps_location" value="">


                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Payment Terms</label>
                                    <select class="form-control js-example-basic-single" id="payment_terms2" required>
                                        @foreach ($paymentterms as $key => $value)
                                            <option value="{{ @$value->id }}" @if ($value->id == 3) selected @endif>
                                                {{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Customer Type</label>
                                    <select class="form-control js-example-basic-single" id="account_type" required>
                                        <option value="">-Select-</option>
                                        <option value="1" selected>Reseller</option>
                                        <option value="2">Enduser</option>
                                        <option value="3">Ecommerce</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Designation</label>
                                    <select class="form-control js-example-basic-single" name="designation_add"
                                        id="designation_add" required>
                                        <option value="">--Designation--</option>
                                        @if (count($designation) > 0)
                                            @foreach ($designation as $val)
                                                <option value="{{ $val->title }}" {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}>{{ $val->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" id="cust_sales_person"
                                        required>
                                        <option value="">-Select-</option>

                                        @foreach ($sales_person as $value)
                                            <option value="{{ $value->user_id }}">{{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light add-btn ms-2" id="btn_add_company">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save & Close
                </button>
            </div>
        </div>
    </div>
</div>


<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhC6fAWLvqoE4znv7d8ovf8y3pMR0OG7s&libraries=places&language=en">
    </script>



<script>
    $(document).ready(function () {
        const input = document.getElementById("company_name_add");

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ["establishment"],
            fields: [
                "place_id",
                "name",
                "formatted_address",
                "address_components",
                "geometry",
                "plus_code",
                "international_phone_number",
                "formatted_phone_number",
                "website",
                "url"
            ]

        });

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();

            if (!place.place_id) {
                console.error("No place details found");
                return;
            }

            $('#company_name_add_list').fadeOut();


            console.log("📍 COMPLETE PLACE DATA:", place);

            // Extract structured address
            let address = {
                country: "",
                country_code: "",
                state: "",
                city: "",
                area: "",
                building_name: "",
                street_number: "",
                route: "",
                po_box: "",
                postal_code: ""
            };

            if (place.address_components) {
                place.address_components.forEach(component => {
                    const types = component.types;

                    if (types.includes("country")) {
                        address.country = component.long_name;
                        address.country_code = component.short_name;
                    }
                    if (types.includes("administrative_area_level_1")) {
                        address.state = component.long_name;
                    }
                    if (types.includes("locality")) {
                        address.city = component.long_name;
                    }
                    if (types.includes("sublocality") || types.includes("sublocality_level_1")) {
                        address.area = component.long_name;
                    }
                    if (types.includes("premise") || types.includes("neighborhood")) {
                        address.building_name = component.long_name;
                    }
                    if (types.includes("street_number")) {
                        address.street_number = component.long_name;
                    }
                    if (types.includes("route")) {
                        address.route = component.long_name;
                    }
                    if (types.includes("post_box")) {
                        address.po_box = component.long_name;
                    }
                    if (types.includes("postal_code")) {
                        address.postal_code = component.long_name;
                        console.log("Postal Code:", address.postal_code);
                    }
                });
            }

            // Extract country mobile code from phone number
            let mobileCode = "";
            if (place.international_phone_number) {
                const match = place.international_phone_number.match(/^\+(\d{1,4})/);
                if (match) {
                    mobileCode = "+" + match[1];
                }
            }

            // Get coordinates
            const lat = place.geometry?.location?.lat() || "";
            const lng = place.geometry?.location?.lng() || "";

            // Fill all form fields
            setFieldvalue("company_name_add", place.name || "");
            setFieldvalue("cust_no_add", place.international_phone_number || "");
            // setFieldvalue("mobile_code", mobileCode);
            setFieldvalue("customer_website", place.website || "");


            setFieldvalue("maps_location", place.url || "");

            // setFieldvalue("country", address.country);
            var targetName = address.country || ""; // or any value you want to match


            function normalize(str) {
                return str.toLowerCase().replace(/\s+/g, '');
            }

            // Select the option based on its text (the visible name)
            $('#country_ship option').each(function () {
                if (normalize($(this).text()) === normalize(targetName)) {
                    $(this).prop('selected', true);
                    $('#country_ship').trigger('change');
                }
            });



            var targetName = address.state || ""; // or any value you want to match


            // Delay execution to ensure options are loaded
            setTimeout(function () {
                var matched = false;

                $('#state_ship option').each(function () {
                    if (normalize($(this).text()) === normalize(targetName)) {
                        $(this).prop('selected', true);
                        matched = true;
                        return false; // break the loop
                    }
                });

                if (matched) {
                    $('#state_ship').trigger('change');
                }

                console.log("State selection attempted for:", targetName);
            }, 600); // adjust 300ms as needed


            setFieldvalue("cust_city", address.city);
            setFieldvalue("cust_area", address.area);
            setFieldvalue("cust_building_name", address.building_name);
            setFieldvalue("cust_pobox", address.postal_code);
            setFieldvalue("place_id", place.place_id);

            setFieldvalue("longitude", lng);
            setFieldvalue("latitude", lat);


            // Show success message
            $("#successAlert").addClass("show");
            setTimeout(() => {
                $("#successAlert").removeClass("show");
            }, 3000);

        });

        // Helper function to set field value and add visual feedback
        function setFieldvalue(fieldId, value) {
            const field = $("#" + fieldId);
            if (field.length) {
                field.val(value);
                if (value) {
                    field.addClass("filled");
                } else {
                    field.removeClass("filled");
                }
            }
        }
    });
</script>


<!-- External JS for country codes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<script>
    $(document).ready(function () {

        $('#country_ship').on('change', function () {

            console.log("!222121")

            let shipId = $(this).val();    // example: "5"

            if (!shipId) {
                $('#country_telephone').val("").trigger('change');
                return;
            }

            // Loop through telephone options
            $('#country_telephone option').each(function () {
                let value = $(this).val(); // example: "AE|5"

                if (!value) return;

                let parts = value.split('|');
                let telId = parts[1]; // country ID

                if (telId == shipId) {
                    $('#country_telephone').val(value).trigger('change');
                }
            });

        });

    });

</script>

<script>
    $(document).ready(function () {
        // Map ISO2 → dial code
        var countryCodes = {};
        $.each(window.intlTelInputGlobals.getCountryData(), function (index, country) {
            countryCodes[country.iso2.toLowerCase()] = country.dialCode;
        });



        // When country changes, set country code in input
        $('#country_telephone').on('change', function () {
            // Extract ISO2 from value (before the | character)
            var fullValue = $(this).val(); // e.g. "US|1"
            var iso2 = fullValue ? fullValue.split('|')[0].toLowerCase() : '';
            console.log(iso2)
            console.log("!@1212KPPPP")
            var code = countryCodes[iso2] || '';
            var currentNumber = $('#cust_no_add').val().replace(/^\+\d+\s?/, ''); // remove previous code

            console.log("code = ", code)

            $('#cust_no_add').val(code ? '+' + code + ' ' + currentNumber : currentNumber);


        });
    });
</script>

<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Terms and Condition:</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea" rows="6"
                            placeholder="Write narration here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarration" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const referenceInput = document.getElementById('terms_and_condition');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('shown.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
            setTimeout(() => $('#narrationTextarea').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });


</script>





<script>
    $(document).ready(function () {
        $('#company').on('change', function () {
            let companyId = $(this).val();

            if (!companyId) {
                $('#new_code').text('');
                return;
            }

            $.ajax({
                url: "{{ url('/ajax/get-new-lead-code') }}",
                type: "GET",
                data: {
                    table: 'sys_crm_deals',
                    prefix: 'DL',
                    column: 'code',
                    company_id: companyId
                },
                success: function (response) {
                    if (response.new_code) {
                        $('#new_code').text(response.new_code);
                    } else {
                        $('#new_code').text('');
                        console.error('No code returned from server');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                    $('#new_code').text('');
                }
            });
        });



        // When Company select2 opens, prefill the search box with the currently selected option
        // so the user can edit/change the selection easily.
        $('#cust_id').on('select2:open', function () {
            var selectedText = $(this).find('option:selected').text().trim();
            var $search = $('.select2-container--open .select2-search__field');
            if ($search.length) {
                // Don't prefill if placeholder or empty
                if (selectedText && selectedText !== 'Select') {
                    $search.val(selectedText);
                    // trigger input so Select2 reacts to the injected value
                    $search.trigger('input');

                    // move cursor to end for easier editing (works in modern browsers)
                    var el = $search.get(0);
                    try {
                        if (el && el.setSelectionRange) {
                            var len = selectedText.length * 2; // safe trick to put cursor at the end
                            el.setSelectionRange(len, len);
                        }
                    } catch (e) {
                        // ignore if setSelectionRange not supported
                    }
                } else {
                    $search.val('');
                    $search.trigger('input');
                }
            }
        });

    });
</script>




<!-- Modal Support-->
<div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">

        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-upload-excel-cart', 'method' => 'POST', 'id' => 'crm-quote-upload-excel-cart']) }}


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Quotation Excel Import</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <script>
                function add_excel_data() {
                    $('#excel_company_id').val($('#company_id').val());
                    $('#excel_currency_id').val($('#currency_id').val());
                    $('#excel_customer_type').val($('#customer_type').val());
                    $('#excel_quote_validity').val($('#quote_validity').val());
                    $('#excel_payment_terms').val($('#payment_terms').val());
                    $('#excel_delivery_date').val($('#delivery_date').val());
                    $('#excel_payment_terms_txt').val($('#payment_terms_txt').val());
                    $('#excel_delivery_time').val($('#delivery_time').val());
                }
            </script>

            <div class="modal-body">
                <div class="row">
                    <div class="col-auto">
                        <label for="" class="form-label">Select File (.csv)</label>
                    </div>
                    <div class="col-auto">
                        <input class="form-control" type="file" id="excel-file" accept=".xlsx, .xls, .csv" />
                    </div>
                    <div class="col-auto">
                        <button type="button" onclick="readExcel()" class="btn btn-light">Preview</button>
                        {{-- <input type="file" name="import_file" class="btn-danger" required /> --}}

                    </div>
                    <div class="col-auto">
                        (<a href="{{ url('public/uploads/product_upload/quotation_sample_format.csv') }}"
                            target="_blank">Sample File</a>)
                    </div>
                    <div class="col-md-12 mt-2">
                        <table id="excel-table" class="table table-bordered table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width:220px;">Part No</th>
                                    <th>Description</th>
                                    <th style="width:100px;" class="text-end">Cost</th>
                                    <th style="width:70px;">Qty</th>
                                    <th style="width:100px;" class="text-end">Unit Price</th>
                                    <th style="width:100px;" class="text-end">Discount</th>
                                    <th style="width:100px;" class="text-end">VAT</th>
                                    <th style="width:50px;" class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php
    $part_number = $items->pluck('part_number');
                                ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
                <script>
                    function readExcel() {
                        add_excel_data();
                        var file = document.getElementById('excel-file').files[0];
                        if (!file) {
                            alert("Please select an Excel file.");
                            return;
                        }

                        var reader = new FileReader();
                        reader.onload = function (event) {
                            var data = event.target.result;
                            var workbook = XLSX.read(data, {
                                type: 'binary'
                            });

                            // Assuming the data is in the first sheet
                            var sheet = workbook.Sheets[workbook.SheetNames[0]];
                            var rows = XLSX.utils.sheet_to_json(sheet, {
                                header: 1
                            });

                            var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
                            tableBody.innerHTML = ""; // Clear any previous data

                            // Loop through each row and add data to the table
                            for (var i = 1; i < rows.length; i++) { // Skip header row
                                var row = rows[i];
                                if (row.length < 6) continue; // Skip invalid rows



                                var part_number = <?php    echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                var lowercase_part_number = part_number.map(function (value) {
                                    return value.toLowerCase();
                                });

                                var json_output = JSON.stringify(lowercase_part_number);

                                var newRow = tableBody.insertRow(tableBody.rows.length);

                                var rowVal = String(row[0] ?? '');
                                var trimmedValue = rowVal.trim();

                                if (json_output.includes(trimmedValue.toLowerCase())) { // Use .includes() for array checking

                                } else {
                                    newRow.style.backgroundColor = "#ffbebe";
                                }

                                // Part No
                                var partNoCell = newRow.insertCell(0);
                                var partNoInput = document.createElement('input');
                                partNoInput.type = 'text'; // Change to text input
                                partNoInput.name = 'excel_part_no[]';
                                partNoInput.value = rowVal.trim();
                                partNoInput.classList.add('form-control');
                                partNoCell.appendChild(partNoInput);

                                // Description
                                var descriptionCell = newRow.insertCell(1);
                                var descriptionInput = document.createElement('input');
                                descriptionInput.type = 'text'; // Change to text input
                                descriptionInput.name = 'excel_description[]';
                                descriptionInput.value = (row[1] || '').toString().trim();
                                descriptionInput.classList.add('form-control');
                                descriptionCell.appendChild(descriptionInput);

                                // Cost (Right-aligned)
                                var costCell = newRow.insertCell(2);
                                var costInput = document.createElement('input');
                                costInput.type = 'text'; // Change to text input
                                costInput.name = 'excel_cost[]';
                                costInput.value = row[2];
                                costInput.classList.add('text-end');
                                costInput.classList.add('form-control');
                                costCell.appendChild(costInput);

                                // Qty
                                var qtyCell = newRow.insertCell(3);
                                var qtyInput = document.createElement('input');
                                qtyInput.type = 'text'; // Change to text input
                                qtyInput.name = 'excel_qty[]';
                                qtyInput.value = row[3];
                                qtyInput.classList.add('form-control');
                                qtyCell.appendChild(qtyInput);

                                // Unit Price (Right-aligned)
                                var unitPriceCell = newRow.insertCell(4);
                                var unitPriceInput = document.createElement('input');
                                unitPriceInput.type = 'text'; // Change to text input
                                unitPriceInput.name = 'excel_unit_price[]';
                                unitPriceInput.value = row[4];
                                unitPriceInput.classList.add('text-end');
                                unitPriceInput.classList.add('form-control');
                                unitPriceCell.appendChild(unitPriceInput);

                                // Discount (Right-aligned)
                                var discountCell = newRow.insertCell(5);
                                var discountInput = document.createElement('input');
                                discountInput.type = 'text'; // Change to text input
                                discountInput.name = 'excel_discount[]';
                                discountInput.value = row[5];
                                discountInput.classList.add('text-end');
                                discountInput.classList.add('form-control');
                                discountCell.appendChild(discountInput);

                                // VAT (Right-aligned)
                                var vatCell = newRow.insertCell(6);
                                var vatInput = document.createElement('input');
                                vatInput.type = 'text'; // Change to text input
                                vatInput.name = 'vat_excel[]';
                                vatInput.value = row[6];
                                vatInput.classList.add('text-end');
                                vatInput.classList.add('form-control');
                                vatCell.appendChild(vatInput);

                                var deleteCell = newRow.insertCell(7); // Last cell for delete button
                                var deleteButton = document.createElement('button');
                                deleteButton.type = 'button'; // Make sure the button doesn't submit a form

                                deleteButton.classList.add('btn-sm', 'btn-light');
                                deleteButton.innerHTML = '<i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>';
                                deleteButton.onclick = function () {
                                    // Delete the row when the button is clicked
                                    var rowToDelete = this.parentNode.parentNode;
                                    rowToDelete.remove();
                                };
                                deleteCell.appendChild(deleteButton);

                            }
                        };
                        reader.readAsBinaryString(file);
                    }
                </script>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>

        </div>
        {{ Form::close() }}
    </div>
</div>
<!-- Modal Support-->

{{-- Form Validation Script --}}
<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
    $(document).ready(function () {
        // Initialize form validation for quotation-store-form
        FormValidator.init('quotation-store-form', {
            showAllErrors: true,
            scrollToFirst: true,
            highlightFields: true,
            toastrPosition: 'toast-top-right',
            toastrTimeout: 6000
        });
    });
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
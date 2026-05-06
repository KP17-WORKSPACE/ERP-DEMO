<?php try { ?>


@if (isset($edit))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'proforma-invoice-update/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-quote-form', 'novalidate' => true]) }}
@else
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-quote-form', 'novalidate' => true]) }}
@endif
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
<input type="hidden" name="quote_id" value="{{ $quote_id }}">
<input type="hidden" name="net_vat" id="net_vat" value="{{ $edit->customername->vat_percentage }}">

  <style>
    .no-dim {
        pointer-events: none !important;
        opacity: 1 !important;

    }
                                        </style>
<style>
.form-item-table .select2-container--default .select2-selection--single{ border: none !important;}
.form-item-table.select2-container--default .select2-selection--single .select2-selection__arrow b { display: none !important; }
</style>

<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        Edit - {{ $proforma_invoice->doc_number }} ({{ $edit->code }})
        
    
        
        
        @if($edit->stage == 4) 

         <?php
                    $data = App\SysHelper::deal_track_status($edit->id);
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
                                            

                                            @if(App\SysHelper::set_track($edit->id) == 1)
                                                <a class="badge bg-{{ $color }}  py-1 px-2 @if($data == "Fulfill") @else deal-track-sales-person @endif" @if($data == "Fulfill") href="{{ url('crm-deals/show/'.$edit->id.'?deal_action=edit') }}" @endif  data-id="{{ $edit->id }}"  title="Click to Fullfill">
                                                 {{ $data }} </a>
                                            @endif
                                            @endif
    </h4>
    <div class="purchase-order-content-header-right">
  
        <input type="hidden" name="document_number" id="document_number" value="{{ @$quotationitems[0]->document_number }}">

         <a class="btn btn-light text-dark" href="{{url('proforma-invoice?proforma_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>

        <button type="submit" class="btn btn-light">
            <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
        </button>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu">

                 <li><button type="button" class="dropdown-item d-flex align-items-center text-dark"  data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i
                            class="ico icon-bold-download-minimalistic text-success  title-15 me-2"></i> Download</button>
                </li>


            </ul>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade side-panel" id="staticBackdrop" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog draggable modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Download</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center align-items-center gap-3">
           <a href="{{ url('crm-quote/' . $edit->id . '/download/' . $edit->quote_id) }}" class="btn btn-light text-dark"> <i
                            class="ico icon-bold-download-minimalistic text-success" style="font-size:13px"></i> Business Proposal</a>
        <a href="{{ url('crm-quote-pdf/' . $edit->id) }}" class="btn btn-light text-dark"><i
                            class="ico icon-bold-download-minimalistic text-success" style="font-size:13px"></i> Quotation</a>
      </div>
     
    </div>
  </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row gap-rows">

              <div class="col-4">
                <label class="form-label">Customer</label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required
                        onchange="change_cust_id()">
                        <option value=""></option>
                        @foreach ($vendors as $value)
                              <option value="{{ @$value->id }}" @if (@$edit->cust_id == $value->id) selected @endif>{{ trim(@$value->name) }}@if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] == 1) ({{ trim(@$value->code) }})@endif</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-3">
                <label class="form-label">Deal Name</label>
                <div class="form-group">
                    <input class="form-control capitalize-title" type="text" name="deal_name" autocomplete="off" id="deal_name"
                        value="{{ isset($edit) ? (!empty(@$edit->deal_name) ? @$edit->deal_name : old('deal_name')) : old('deal_name') }}"
                        required>
                </div>
            </div>


            <input type="hidden" name="deal_id" id="deal_id" value="{{ @$edit->id }}">
           
          

            <div class="col-2">
                <label class="form-label">Est. Closing Date *</label>
                <div class="form-group">


                    @php
                        @$value = @$edit->estimated_close_date;

                    @endphp
                    <input class="form-control date-picker" id="estimated_close_date" type="text" autocomplete="off"
                        name="estimated_close_date" value="{{ @App\SysHelper::normalizeToDmy(@$value) }}" required>
                </div>
            </div>
           


             <div class="col-3">
                <label class="form-label">Sales Person </label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="owner" id="owner" required>

                        @foreach ($staff as $value)
                                                       
                    <option value="{{ $value->user_id }}"
                        @if (@$edit->owner == $value->user_id) selected @endif
                    >
                        {{ $value->full_name }}
                    </option>
                    @endforeach

                    </select>
                </div>
            </div>

            


        <div class="col-2">
                <label class="form-label">LPO Number<span>*</span></label>
                <div class="form-group">
                    <input class="form-control" type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                        value="{{ $proforma_invoice->reference_no }}" required>
                </div>
            </div>
            
            <div class="col-2">
                <label class="form-label">LPO Date</label>
                <div class="form-group">
                    @php
                        if (isset($proforma_invoice) && !empty($proforma_invoice->reference_date)) {
                            $value_date = Carbon\Carbon::parse($proforma_invoice->reference_date)->format('d/m/Y');
                        } elseif (!empty(old('lpo_date'))) {
                            $value_date = Carbon\Carbon::parse(old('lpo_date'))->format('d/m/Y');
                        } else {
                            $value_date = Carbon\Carbon::now()->format('d/m/Y');
                        }
                    @endphp
                    <input class="form-control date-picker" id="lpo_date" type="text" autocomplete="off" name="doc_date"
                        value="{{ @$value_date }}" style="margin-top: 0px">
                </div>
            </div>


            
              <div class="col-2">
                                                <label class="form-label">Commercial Invoice</label>
                                                <div class="form-group">
                               

                                     <div class="form-group">
                                  
                                         <select class="form-control js-example-basic-single" required name="proforma_invoice" id="proforma_invoice">
                                       
                           
                                            <option value="CI" @if($proforma_invoice->proforma_invoice == 'CI') selected @endif>Commercial Invoice</option>
                                            <option value="PI" @if($proforma_invoice->proforma_invoice == 'PI') selected @endif>Proforma Invoice</option>
                                  
                                        
                                    </select>
                                </div>

                                                </div>
                                            </div>

            <div class="col">
                <label class="form-label">Narration</label>
                <input class="form-control"  type="text"
                    name="narration" autocomplete="off" id="narration"
                    value="{{ isset($proforma_invoice) ? $proforma_invoice->narration : old('narration') }}">
            </div>




         


        </div>
    </div>
</div>


 <style>
                            .col-5-custom {
                                flex: 0 0 auto;
                                width: 20%;
                            }
                        </style>




<div class="deal-list-content-header">
    <table width="100%">
        <tbody>

            <tr>
                <td class="text-end float-end">
                   
                </td>
            </tr>
        </tbody>
    </table>
    <script>
        function quote_generate() {
            var x = document.getElementById("generate-quotation");
            if (x.style.height === "0px") {
                x.style.height = "auto";
                document.getElementById("quotation_generated").value = "1";
            } else {
                x.style.height = "0px";
                document.getElementById("quotation_generated").value = "0";
            }
        }
    </script>
    
    {{-- class="collapse multi-collapse" id="generate-quotation" --}}
    <div id="generate-quotation"
        style="height: {{ (count($quotationitems) > 0 || count($cart) > 0) ||  request()->query('new') == 'yes' ? 'auto' : '0px' }}; overflow: hidden; transition: all 0.5s ease;">

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
                     
                    <div class="row gap-rows">
                        <div class="col-2">
                            <label class="form-label">Quote Validity:</label>
                            <div class="form-group">
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off"
                                    placeholder="Quote Validity" name="quote_validity" value="2 Weeks" required>
                            </div>
                        </div>
                        <div class="col-2">
                            <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Payment Terms</span>
                                <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;" data-bs-toggle="modal" data-bs-target="#paymenttermsModal">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                </button>
                            </label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="payment_terms"
                                    id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}" @if (count($quotationitems) > 0) @if (@$quotationitems[0]->payment_terms == $value->id) selected @endif @elseif(@$edit->customername->payment_terms == $value->id) selected @endif>
                                            {{ @$value->title }}
                                        </option>
                                    @endforeach

                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text" value=""
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt"
                                    style="display: none;">
                                <script>
                                    $(document).ready(function () {
                                        $('#payment_terms').on('change', function () {
                                            if ($(this).val() == 22) {
                                                $('#payment_terms_txt').show().prop('required', true);
                                            } else {
                                                $('#payment_terms_txt').hide().prop('required', false);
                                            }
                                        });

                                        // Trigger once on load (in case the field already has value 22)
                                        $('#payment_terms').trigger('change');
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
                            <label class="form-label">Currency:<a style="float: right;"
                                    data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i
                                        class="ico icon-outline-pen-2"></i></a></label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="currency_id" id="currency_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}" @if (@$edit->deal_currency == $value->id) selected
                                        @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-3">
                            <label class="form-label">Terms and Condition:</label>
                            <div class="form-group">
                                <textarea class="form-control" rows="3" data-bs-toggle="modal"
                                    data-bs-target="#narrationModal" id="terms_and_condition" autocomplete="off"
                                    name="terms_and_condition">{{ @$edit->terms_and_condition ?? '1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No. in your Purchase Order
3. In case of non-availability of quote products SYSCOM reserves the right to supply a functionally similar or better product.' }}</textarea>
                            </div>
                            {{--
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
                            </script> --}}
                        </div>

                        <div class="col-1 mt-4">
  <button type="button" class="btn btn-sm btn-light" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ModalExcelQuote">
                                        <i class="ico icon-outline-import text-success" style="font-size: 16px"></i> Import
                                    </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>




        <div class="table-container mb-3" style="border: solid 1px #d9d9d9;">
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

                    @if (count($quotationitems) > 0)
                        @foreach ($quotationitems as $item)
                        
                            <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]"
                                        value="{{ $i++ }}" /></td>
                                <td class="noborder">
                                    <select class="form-control noborder " name="part_number[]">
                                        <option value="{{ $item->product_id }}">
                                            {{ $item->productname->part_number }}
                                        </option>
                                    </select>
                                </td>
                                <td><textarea class="form-control" name="description[]" rows="1">{{ $item->description }}</textarea></td>
                                <td>
                                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                        value="{{ number_format((float) $item->cost, 2, '.', '') }}"
 onchange="calc_change_new(this)" onblur="formatCurrency(this)">
                                    <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="product_type[]" value="{{ $item->product_type }}" autocomplete="off"
                                        readonly="true" hidden>
                                    <input class="form-control" type="text" name="product_type_part_number_text[]"
                                        autocomplete="off" readonly="true" hidden>
                                </td>
                                <td><input type="number" class="form-control text-center" name="tax[]"
                                        onchange="calc_change_new(this)" value="{{ $item->vat }}"></td>
                                <td><input class="form-control text-center" type="number" id="qty_{{ $item->id }}" name="qty[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" value="{{ $item->qty }}">
                                </td>
                                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)" value="{{ @App\SysHelper::com_curr_format($item->price,2,'.',',') }}">
                                </td>
                                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                        readonly></td>
                                <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                        value="{{ @App\SysHelper::com_curr_format($item->discount,2,'.',',') }}"></td>
                                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                        min="0" readonly></td>
                            </tr>
                        @endforeach
                    @endif

                    @if (isset($cart) && count($cart) > 0)

                    @foreach ($cart as $cart_items)
                   

                     <tr>
                                <td><input type="text" class="form-control text-center" name="sort_id[]"
                                        value="{{ $i++ }}" /></td>
                                <td class="noborder">
                                    <select class="form-control noborder " name="part_number[]">
                                        <option value="{{ $cart_items->product_id }}">
                                            {{ $cart_items->partnumber }}
                                        </option>
                                    </select>
                                </td>
                                <td><textarea class="form-control" name="description[]" rows="1">{{ $cart_items->description }}</textarea></td>
                                <td>
                                    <input class="form-control text-end" type="text" name="cost[]" autocomplete="off"
                                        value="{{ $cart_items->cost }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
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
                                        onchange="calc_change_new(this)" value="{{ $cart_items->vat }}"></td>
                                <td><input class="form-control text-center" type="number" id="qty_{{ $cart_items->id }}" name="qty[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" value="{{ $cart_items->qty }}">
                                </td>
                                <td><input class="form-control text-end" type="text" name="unitprice[]" step="any"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)" value="{{ @App\SysHelper::com_curr_format($cart_items->price,2,'.',',') }}">
                                </td>
                                <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0"
                                        readonly></td>
                                <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                        autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"
                                        value="{{ @App\SysHelper::com_curr_format($cart_items->discount,2,'.',',') }}"></td>
                                <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off"
                                        min="0" readonly></td>
                                <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off"
                                        min="0" readonly></td>
                            </tr>
                    
                    @endforeach

                
                     @endif


                    <tr>
                        <td><input type="text" class="form-control text-center" name="sort_id[]"
                                value="{{ $i }}" /></td>
                        <td class="noborder">
                            <select class="form-control noborder " name="part_number[]">
                            </select>
                            {{-- on focus add this class and its funcanalities js-product-select --}}
                        </td>
                        <td><textarea class="form-control" name="description[]" rows="1"></textarea></td>
                        <td>
                            <input class="form-control text-end" type="text" name="cost[]" autocomplete="off" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
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
                                autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                        <td><input class="form-control text-end" type="text" name="value[]"  min="0"  autocomplete="off"
                                readonly></td>
                        <td><input class="form-control text-end" type="text" step="Any" name="discount[]"
                                autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
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
                        <th class="text-end"><label id="lbl_total_cost" >0</label></th>
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

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                if (typeof calc_change_new === 'function') {
                    // prevent potential event reentrancy / infinite loops
                    var qtyInputs = document.querySelectorAll('input[name="qty[]"]');
                    qtyInputs.forEach(function (el) {
                        // directly call calculation function once per row, no event dispatch
                        calc_change_new(el);
                    });
                }
            });
        </script>


    </div>

</div>

<table class="table form-item-table mb-3">
    <tr class="align-middle text-center"> <!-- centers vertically + horizontally -->
        <td class="text-end"><b>Additional Discount :</b></td>
        <td class="text-end" style="width: 50px;">
                                    <input type="number" class="form-control text-end" id="deal_discount_vat" name="deal_discount_vat" value="{{ $edit->deal_discount_vat ?? '' }}"  step="any" placeholder="VAT %" />
                                </td>
        <td style="width: 103px;">
            <input type="text" class="form-control text-center"
                id="deal_discount" name="deal_discount" step="any"
                placeholder="0.00"
                value="@if(!empty($edit->deal_discount) && $edit->deal_discount > 0 && (count($quotationitems) > 0)){{ @App\SysHelper::com_curr_format($edit->deal_discount,2,'.','') }}@endif"
            />
        </td>
    </tr>
</table>
<table class="table table-hover form-item-table" id="">
            <thead>
                <tr>
                    <th class="resizable text-center" width="300px" scope="col">Name<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" scope="col">Credit Account<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="200px">Amount<div class="resizer"></div>
                    </th>
                    <th class="resizable text-center" width="250px">Remarks<div class="resizer"></div>
                    </th>
                </tr>
            </thead>
            <tbody>
      
                <tr>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_name[]" id="cfc_name_1">
                            <option value=""></option>
                            @foreach ($customs_freight_account as $key => $value)

                            @php
    $settings = App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'));

    $code = @$value->account_code;
    $showCode = true;

    // ensure $code is a string before checking
    $codeStr = (string) ($code ?? '');

    if (!$settings['is_account_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'ACC')) {
        $showCode = false;
    } elseif (!$settings['is_subaccount_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'SACC')) {
        $showCode = false;
    } elseif (!$settings['is_customer_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'CUS')) {
        $showCode = false;
    } elseif (!$settings['is_supplier_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'SUP')) {
        $showCode = false;
    }
@endphp

                                <option value="{{ @$value->id }}" {{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->selling_exp_account) ? (@$edit_cfc[0]->selling_exp_account == $value->id ? 'selected' : '') : '') : '' }}>
                                    
                                    @if ($showCode)
                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                    @else
                                        {{ @$value->account_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select></td>
                    <td> <select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_1"
                            readonly="true">
                            <option value="none"></option>
                            @foreach ($supplier as $key => $value)
                                <option value="{{ @$value->id }}" {{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->credit_account) ? (@$edit_cfc[0]->credit_account == @$value->id ? 'selected' : '') : '') : '' }}>
                                    
                                                 

                                                    @if (@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_supplier_code'])
                                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                                    @else
                                                        {{ @$value->account_name }}
                                                    @endif


                                </option>
                            @endforeach
                        </select></td>
                    <td><input class="form-control text-end" type="number" id="cfc_amount_1" name="cfc_amount[]"
                            autocomplete="off" min="0" onchange="cfc_amount_change(1)"
                            value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->amount) ? @$edit_cfc[0]->amount : old('')) : old('') }}"
                            step="any"></td>
                    <td><input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                            autocomplete="off"
                            value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->remarks) ? @$edit_cfc[0]->remarks : old('')) : old('') }}">
                    </td>
                </tr>
                <tr>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_name[]" id="cfc_name_2">
                            <option value=""></option>
                            @foreach ($customs_freight_account as $key => $value)
                                <option value="{{ @$value->id }}" {{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->selling_exp_account) ? (@$edit_cfc[1]->selling_exp_account == $value->id ? 'selected' : '') : '') : '' }}>
                                   @if ($showCode)
                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                    @else
                                        {{ @$value->account_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select></td>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_2"
                            readonly="true">
                            <option value="none"></option>
                            @foreach ($supplier as $key => $value)
                                <option value="{{ @$value->id }}" {{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->credit_account) ? (@$edit_cfc[1]->credit_account == @$value->id ? 'selected' : '') : '') : '' }}>
                                    @if (@App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'))['is_supplier_code'])
                                                        {{ @$value->account_name }} ({{ @$value->account_code }})
                                                    @else
                                                        {{ @$value->account_name }}
                                                    @endif
                                </option>
                            @endforeach
                        </select></td>
                    <td><input class="form-control text-end" type="number" id="cfc_amount_2" name="cfc_amount[]"
                            autocomplete="off" min="0" onchange="cfc_amount_change(2)"
                            value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->amount) ? @$edit_cfc[1]->amount : old('')) : old('') }}"
                            step="any"></td>
                    <td><input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                            autocomplete="off"
                            value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->remarks) ? @$edit_cfc[1]->remarks : old('')) : old('') }}">
                    </td>
                </tr>
            </tbody>
        </table>


<!-- Hidden inputs to store serial numbers per row (JSON format) -->
@foreach ($quotationitems as $index => $item)
    @if($item->product_type == 2)
        @php
            $existingSerials = !empty($item->serial_numbers) ? $item->serial_numbers : '';
            $partNumber = $item->productname->part_number;
            $rowIndex = $index;
        @endphp
        <input type="hidden" 
               id="serial_data_row_{{ $rowIndex }}" 
               name="serial_numbers_by_row[{{ $rowIndex }}]" 
               class="serial-data-storage" 
               data-row-index="{{ $rowIndex }}"
               data-product-id="{{ $item->product_id }}"
               data-part-number="{{ $partNumber }}"
               data-qty="{{ $item->qty }}"
               value="{{ $existingSerials }}">

           <input type="hidden"
       name="part_number_by_row[{{ $rowIndex }}]"
       value="{{ $partNumber }}">

      
        
    @endif
@endforeach

@if (isset($cart) && count($cart) > 0)
    @foreach ($cart as $cartIndex => $cart_items)
        @php
            $cartProduct = @App\SmItem::find($cart_items->product_id);
            $cartProductType = $cartProduct ? $cartProduct->product_type : null;
            $cartPartNumber = $cart_items->partnumber;
            $cartRowIndex = 'cart_' . $cartIndex;
        @endphp
        @if($cartProductType == 2)
            <input type="hidden" 
                   id="serial_data_row_{{ $cartRowIndex }}" 
                   name="serial_numbers_by_row[{{ $cartRowIndex }}]" 
                   class="serial-data-storage" 
                   data-row-index="{{ $cartRowIndex }}"
                   data-product-id="{{ $cart_items->product_id }}"
                   data-part-number="{{ $cartPartNumber }}"
                   data-qty="{{ $cart_items->qty }}"
                   value="">
            <input type="text" name="part_number_for_serial_{{ $cartRowIndex }}" 
                   id="part_number_for_serial_{{ $cartRowIndex }}"
                   value="{{ $cartPartNumber }}" hidden>
            <input type="text" name="product_id_for_serial_{{ $cartRowIndex }}" 
                   id="product_id_for_serial_{{ $cartRowIndex }}" 
                   value="{{ $cart_items->product_id }}" hidden>
        @endif
    @endforeach
@endif


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
    document.addEventListener('DOMContentLoaded', function() {
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
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea2" rows="6"
                            placeholder="Write narration here..."></textarea>
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




<script>
$(document).on("keydown", 'input[name="cost[]"], input[name="tax[]"], input[name="qty[]"], input[name="unitprice[]"], input[name="discount[]"]', function(e) {
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
                validRows.push({
                    index,
                    input
                });
            }
        });

        if (totalValue === 0) {
            alert("All rows have empty or zero 'Value'. Nothing to split.");
            return;
        }

        validRows.forEach(({
            index,
            input
        }) => {
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
        if (calc_change_new.__running) {
            return;
        }
        calc_change_new.__running = true;

        $("#loading_bg").css("display", "block");

        // Get the current row
        var $row = $(el).closest('tr');

        // Read values from the current row
        var net_vat = $row.find('input[name="tax[]"]').val() || '0';

        var qty = $row.find('input[name="qty[]"]').val() || '0';
        // unitprice may contain expressions like +100, -50, +10%, -10% or absolute numbers like 1000
        var unitpriceRaw = ($row.find('input[name="unitprice[]"]').val() || '').toString().trim();

        function parseUnitPrice(raw, cost) {
            if (!raw || raw === '') return null;
            raw = raw.replace(/\s+/g, '').replace(/,/g, '');

            var m = raw.match(/^([+-]?)(\d+(?:\.\d+)?)%$/);
            if (m) {
                var sign = m[1];
                var pct = parseFloat(m[2]);
                if (!Number.isFinite(pct)) return null;
                if (!Number.isFinite(cost)) cost = 0;
                return sign === '-' ? (cost * (1 - pct / 100)) : (cost * (1 + pct / 100));
            }

            var m2 = raw.match(/^([+-])(\d+(?:\.\d+)?)$/);
            if (m2) {
                var sign2 = m2[1];
                var val = parseFloat(m2[2]);
                if (!Number.isFinite(val)) return null;
                if (!Number.isFinite(cost)) cost = 0;
                return sign2 === '-' ? (cost - val) : (cost + val);
            }

            var v = parseFloat(raw);
            if (Number.isFinite(v)) return v;
            return null;
        }

        var unitprice = null;
        var discount = $row.find('input[name="discount[]"]').val().replace(/,/g, '') || '0';
        var fright = 0;
        var customcharges = 0;

        var decimal_point = @json(session('logged_session_data.decimal_point'));

        // Compute unitprice numeric value — support relative/percentage expressions
        var costRaw = $row.find('input[name="cost[]"]').val().replace(/,/g, '') || '0';
        var cost = parseFloat(costRaw);
        if (!Number.isFinite(cost)) cost = 0;

        if (typeof unitpriceRaw === 'string' && (unitpriceRaw.indexOf('%') !== -1 || unitpriceRaw[0] === '+' || unitpriceRaw[0] === '-')) {
            var computed = parseUnitPrice(unitpriceRaw, cost);
            if (computed !== null) {
                unitprice = computed;
                var decimal_point = parseInt(@json(session('logged_session_data.decimal_point') ?? 2));
                if (!Number.isFinite(decimal_point)) decimal_point = 2;
                try { $row.find('input[name="unitprice[]"]').val(typeof formatAmount === 'function' ? formatAmount(Number(unitprice).toFixed(decimal_point)) : Number(unitprice).toFixed(decimal_point)); } catch (err) { $row.find('input[name="unitprice[]"]').val(Number(unitprice).toFixed(decimal_point)); }
            } else {
                unitprice = parseFloat(unitpriceRaw.replace(/,/g, '')) || 0;
            }
        } else {
            unitprice = parseFloat(unitpriceRaw.replace(/,/g, '')) || 0;
        }

        // Calculate value
        var fin_value = parseFloat(unitprice) * parseFloat(qty);
         if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="value[]"]').val(formatAmount(fin_value));
        } else {
            $row.find('input[name="value[]"]').val('');
        }

        // Calculate taxable amount
        var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
           if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="taxableamount[]"]').val(formatAmount(fin_taxableamount));
        } else {
            $row.find('input[name="taxableamount[]"]').val('');
        }

        // Calculate VAT
        var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
           if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="vatamount[]"]').val(formatAmount(fin_vatamount));
        } else {
            $row.find('input[name="vatamount[]"]').val('');
        }

        // Calculate total amount
        var total_amount = fin_taxableamount + fin_vatamount;
            if (parseFloat(qty) > 0 && parseFloat(unitprice) > 0) {
            $row.find('input[name="totalamount[]"]').val(formatAmount(total_amount));
        } else {
            $row.find('input[name="totalamount[]"]').val('');
        }

        $("#loading_bg").css("display", "none");
        update_totals();
        calc_change_new.__running = false;
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
                    url: '{{ route('autocomplete.get_cust_account_list_ajax') }}',
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
                const searchInput = document.querySelector(
                    '.select2-container--open .select2-search__field');
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
                    url: '{{ route('autocomplete.get_product_list_ajax') }}',
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
                console.log(selectedData)

                // Set values using "name" attribute selectors inside the same row
                //$row.find('input[name="description[]"]').val(selectedData.description || '');
                $row.find('textarea[name="description[]"]').val(selectedData.description || '');
                $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
                $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
                $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
                $row.find('input[name="product_type_part_number_text[]"]').val(selectedData
                    .description || '');
                $row.find('input[name="discount[]"]').val(0);
                $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                $row.find('input[name="cost[]"]').focus();
            });


                // prefill Select2 search with currently selected value when dropdown opens
            $(selector).on('select2:open', function() {
                try {
                    var sel = $(this).select2('data');
                    if (sel && sel.length && sel[0].text) {
                        setTimeout(function() {
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
                document.querySelector('.select2-container--open .select2-search__field')
                    ?.focus();
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
        if ($("#source").val() == "Other") {
            $("#source_o").css("display", "block");
            $("#source_o").prop('required', true);
            $("#sourcediv").css("display", "block");
        } else {
            $("#source_o").css("display", "none");
            $("#source_o").prop('required', false);
            $("#sourcediv").css("display", "none");
        }
    });

    $(document).on("change", "#source", function () {
        if ($("#source").val() == "Other") {
            $("#source_o").css("display", "block");
            $("#source_o").prop('required', true);
            $("#sourcediv").css("display", "block");
        } else {
            $("#source_o").css("display", "none");
            $("#source_o").prop('required', false);
            $("#sourcediv").css("display", "none");
        }
    });

    function change_cust_id() {
        var id = $("#cust_id").val();
        var user = $("#user_id").val();
        get_cust_name(id);
        get_sales_person(id, user);
        get_vat(id);
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
                success: function(dataResult) {
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

    function get_cust_name(id) {
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
                console.log(dataResult)
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                            .first_name + ' ' + dataResult['data'][i].last_name;
                        var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                            .address2 + ', ' + dataResult['data'][i].city + ', ' + dataResult['data'][i]
                                .statename + ', ' + dataResult['data'][i].name;
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
                        } //2.Enduser
                        if (dataResult['data'][i].account_type == 2) {
                            $("#isproject").val(2);
                            $('#is_professional_service').prop("checked", false);
                        } //3.Ecommerce
                        if (dataResult['data'][i].account_type == 3) {
                            $("#isproject").val(3);
                            $('#is_professional_service').prop("checked", false);
                        }
                    }
                } else {
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
                        if (user == id) {
                            sele = 'selected';
                        }
                        var option = "<option value='" + id + "' " + sele + ">" + name + "</option>";
                        $("#owner").append(option);
                    }
                } else {
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
        var cust_address_add = $("#cust_address_add").val();
        var cust_address_add2 = $("#cust_address_add2").val();
        var country_add = $("#country_ship").val();

        var cust_city = $("#cust_city").val();
        var state_ship = $("#state_ship").val();
        var cust_pobox = $("#cust_pobox").val();
        var sales_person = $("#cust_sales_person").val();
        var payment_terms = $("#payment_terms").val();
        var account_type = $("#account_type").val();
        var company_id = $("#company").val();

        var action = "{{ URL::to('add-customer-detail-popup') }}";
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
                cust_address_add: cust_address_add,
                cust_address_add2: cust_address_add2,
                vat_country: country_add,
                city: cust_city,
                vat_state: state_ship,
                zip_code: cust_pobox,
                sales_person: sales_person,
                payment_terms: payment_terms,
                account_type: account_type,
                company_id: company_id,
            },
            cache: false,
            success: function (dataResult) {
                //alert(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found in something!!");
                    $("#btn_add_company").css("display", "block");
                } else if (dataResult['data'] == "ERROR2") {
                    alert("Company Name already exists!! Please Contact Support");
                    $('#company_name_add').css("border", "1px solid red");
                    $('#company_name_add').focus();
                    $("#btn_add_company").css("display", "block");
                } else {
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
                        alert('Company Name Added Successfully!!');
                        $('#btn_close2').click();
                        $("#btn_add_company").css("display", "block");
                        //location.reload();
                        //$("#company_name").change();
                    }
                }
            }
        });
    });

    $(document).ready(function () {
        // Trigger change event only if a country is selected by default
        if ($('#country_ship').val() !== '') {
            $('#country_ship').trigger('change');
        }




           // When Company select2 opens, prefill the search box with the currently selected option
        // so the user can edit/change the selection easily.
        $('#cust_id').on('select2:open', function() {
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
{{--
<div class="modal side-panel fade" id="ModalNote" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Note</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <span class="font-weight-bold">Internal Note</span>
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' =>
                                'crm-deals-comments-add', 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                                <textarea name="comments" class="form-control" id="comments" cols="10"
                                    rows="3"></textarea>
                                <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                                <input type="hidden" id="commentsid" name="commentsid" value="{{ $edit->id }}" />
                                <div class="mt-2 justify-content-end d-flex">
                                    <button type="submit" class="btn btn-light add-btn ms-2">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Internal
                                        Note
                                    </button>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-2">
                                @if ($edit->note != '')<b>Deal Notes :- </b>
                                <div class="notes border-bottom mt-2"> {!! nl2br($edit->note) !!} </div>
                                @endif
                                @if (count($comments) > 0)
                                <div class="notes border-bottom mt-3">
                                    @foreach ($comments as $cmts)
                                    <div>
                                        @if ($cmts->created_by == Auth::user()->id)
                                        <a href="{{url('crm-deals-comments-delete/'.$cmts->id.'')}}"
                                            onclick="return confirm('Are you sure?')"><i
                                                class="fa fa-window-close text-sm text-danger float-right"
                                                aria-hidden="true"></i></a>
                                        @endif
                                        <p class="mb-0">{!! nl2br($cmts->comments) !!}
                                            @if ($cmts->commentsdoc != '')
                                            <a class="text-info p-0"
                                                href="{{asset('public/uploads/crm_deal_doc/')}}/{{ $cmts->commentsdoc }}"
                                                target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip"
                                                    aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                                            @endif
                                            <span class="text-muted text-end">{{ $cmts->createdby->first_name }}
                                                {{ $cmts->createdby->last_name }}, On {{date('d/m/Y h:i A',
                                                strtotime($cmts->created_at))}}</span>
                                        </p>

                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>



                    </div>
                </div>
            </div>

        </div>
    </div>
</div> --}}






<!-- Modal Change Currancy-->
<div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="ModalChangeCurrancy" aria-hidden="true">
    @php
      @$currency = $currencylist->firstWhere('id', $currency_id);
@$currencyCode = $currency ? $currency->code : null;

    @endphp
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Change Currancy ({{ @$currencyCode }})</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">
                <div class="row">
                            <input type="hidden" name="from_currency_id" value="{{ $currency_id }}" />
{{-- 
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Currancy From</label>
                             <select  class="form-control js-example-basic-single" name="from_currency_id" required>
                                @foreach ($currencylist as $value)
                                    @if (@$currency_id == $value->id)
                                        <option value="{{ @$value->id }}" @if (@$currency_id == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endif
                                @endforeach
                            </select> --
                        </div>
                    </div> --}}

                    <div class="col-md-12">
                        <div class="mb-3 mt-2">
                            <label for="" class="form-label">Convert To</label>
                            <select class="form-control js-example-basic-single" name="to_currency_id" id="to_currency_id" required
                                onchange="set_rate()">
                                <option value="">Select</option>
                                @foreach ($currencylist2 as $value)
                                    <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                @endforeach
                            </select>
                            @foreach ($currencylist2 as $value)
                                <input type="hidden" id="rate_{{ @$value->id }}" name="rate_{{ @$value->id }}"
                                    value="{{ @$value->rate }}" />
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Default Currency Conversion Rate</label>
                            <input type="text" class="form-control" id="to_currency_rate" name="to_currency_rate"
                                required />
                        </div>
                    </div>
                    <script>
                        function set_rate() {
                            var id = $('#to_currency_id').val();
                            var rate = $('#rate_' + id).val();

                            $('#to_currency_rate').val(rate);
                        }
                    </script>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="cur_quote_id" value="{{ $quote_id }}" />
                <input type="hidden" name="cur_deal_id" value="{{ $edit->id }}" />
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Change
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<!-- Modal Change Currancy-->



<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Terms and Condition:</h4>
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



   <!-- Modal Support-->
    <div class="modal fade" id="ModalSupport" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer</label>
                                <input type="text" class="form-control" value="{{ $edit->customername->name }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal Id</label>
                                <input type="text" class="form-control" value="{{ $edit->deal_code->code }}" readonly>
                                <input type="hidden" name="deal_id" id="deal_id" value="{{ $edit->id }}">
                            </div>
                        </div>

                          <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="text" class="form-control date-picker" name="support_date" id="support_date" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">From</label>
                                <input type="time" class="form-control" name="time_from" id="time_from" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">To</label>
                                <input type="time" class="form-control" name="time_to" id="time_to" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" id="site_name" value="{{ $edit->address }}" required>
                            </div>
                        </div>
                      
                        <div class="col-md-12">
                            <div class="mb-3">
                                
                             
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Scope of Work</label>
                                <button type="button" class="btn btn-sm btn-light border" onclick="add_scope_of_work()">
                                    <i class="ico icon-outline-add-square me-1"></i> Add
                                </button>
                            </div>

                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                <tr id="row_1">
                                    <td class="text-muted text-center" width="5%">1.</td>
                                    <td>
                                        <input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" required>
                                    </td>
                                    <td width="5%"></td>
                                </tr>

                                @for ($i = 2; $i <= 20; $i++)
                                    <tr id="row_{{ $i }}" style="display: none;">
                                        <td class="text-muted text-center" width="5%">{{ $i }}.</td>
                                        <td>
                                            <input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_{{ $i }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-light" onclick="delete_work({{ $i }})">
                                                <i class="ico icon-outline-trash-bin-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                            <input type="hidden" id="scope_of_work_row_id" value="1" />



                            <script>
                            function add_scope_of_work() {
                                // Find first hidden row
                                let nextHidden = $('tr[id^="row_"]:hidden').first();

                                if (nextHidden.length > 0) {
                                    // Check the current last visible input is not empty
                                    let lastVisible = $('tr[id^="row_"]:visible').last();
                                    let input = lastVisible.find('input');
                                    if (input.val().trim() === '') {
                                        input.focus();
                                        return;
                                    }

                                    // Show next hidden row
                                    nextHidden.fadeIn();
                                    let id = nextHidden.attr('id').split('_')[1];
                                    $('#scope_of_work_' + id).prop("required", true);

                                    // Update hidden counter
                                    $('#scope_of_work_row_id').val(id);
                                }
                            }

                            function delete_work(id) {
                                // Clear value, hide row
                                $('#scope_of_work_' + id).val('').prop("required", false);
                                $('#row_' + id).fadeOut();

                                // Update counter to last visible row index
                                let lastVisible = $('tr[id^="row_"]:visible').last().attr('id');
                                let lastId = lastVisible ? parseInt(lastVisible.split('_')[1]) : 1;
                                $('#scope_of_work_row_id').val(lastId);
                            }
                            </script>


                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="customer_id" id="customer_id" required value="{{ $edit->cust_id }}" />
                    <input type="hidden" name="sales_person_id" id="sales_person_id" required value="{{ $edit->owner }}" />
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
    <div class="modal side-panel fade" id="ModalSupportCmt" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Add Service Comments</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-comments', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                @if (count($support)!=0)
                    <input type="hidden" name="support_id" value="{{ $support[0]->id }}" />
                @endif
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea class="form-control" name="remarks" id="remarks3" rows="10" required></textarea>
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
    <div class="modal side-panel fade" id="ModalCollaboration" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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
                            @if (count($collaboration)>0)
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

        @if ($quotationitems->where('product_type', 2)->count() < 1)

       <!-- Modal End User -->
    <div class="modal side-panel fade" id="ModalEndUserDetails" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h4 class="modal-title" id="exampleModalLabel">End User Details</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    @if ($enduser=="")
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-add-end-user', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="end_user_deal_id" value="{{ $edit->id }}" />
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name *</label>
                                <input type="text" class="form-control capitalize-title" name="end_user_company_name" id="end_user_company_name" required />
                            </div>
                        </div>
                          @if ($quotationitems->where('product_type', 2)->count() > 0)
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Device Serial</label>
                                <div class="input-group">
                                    <input type="text" class="form-control capitalize-title" name="device_serial" id="device_serial" readonly style="cursor:pointer;" />
                                    <button type="button" class="btn btn-light border"  >
                                        <i class="ico icon-outline-list-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Address *</label>
                                <input type="text" class="form-control" name="address_line_a" id="address_line_a" required />
                            </div>
                        </div> --}}
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
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person *</label>
                                <input type="text" class="form-control capitalize-title" name="end_user_contact_person" id="end_user_contact_person" required />
                            </div>
                        </div>
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="job_title" id="job_title" />
                            </div>
                        </div>  --}}
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" />
                            </div>
                        </div>
                        {{-- <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Project Name</label>
                                <input type="text" class="form-control" name="project_name" id="project_name" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Brief description about this project</label>
                                <input class="form-control" name="project_description" id="project_description">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">When it is expected to Close</label>
                                <input type="text" class="form-control date-picker" name="expected_close_date" id="expected_close_date" />
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer">
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Save
						</button>
                </div>
                {{ Form::close() }}        
                @else
          <div class="modal-body">
    <div class="row">

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Company Name</p> <br>
           <span class="truncate-text-custom">{{ $enduser->end_user_company_name }}</span> 
        </div>
                          @if ($quotationitems->where('product_type', 2)->count() > 0)


        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Device Serial</p><br>
             <span class="truncate-text-custom">{{ $enduser->device_serial }}</span> 
        </div>
        @endif

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Contact Person</p><br>
           <span class="truncate-text-custom">{{ $enduser->end_user_contact_person }}</span> 
        </div>

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Mobile No</p><br>
            <span class="truncate-text-custom">{{ $enduser->mobile_no }}</span> 
        </div>

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Email</p><br>
            <span class="truncate-text-custom">{{ $enduser->email }}</span>
        </div>

        {{-- <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Project Name</p><br>
            <span class="truncate-text-custom">{{ $enduser->project_name }}</span>
        </div>

        <div class="col-3 mb-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Brief description about this project</p><br>
            <span class="truncate-text-custom">{{ $enduser->project_description }}</span>
        </div>

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">When it is expected to Close</p><br>
            <span class="truncate-text-custom">{{ date('d-M-Y', strtotime($enduser->expected_close_date)) }}</span>
        </div> --}}

    </div>
            </div>


                @endif

            </div>
        </div>
    </div>
    <!-- Modal End User -->
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
    document.addEventListener('DOMContentLoaded', function() {
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
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea2Address" rows="6"
                            placeholder="Write address here..."></textarea>
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
                        <input class="form-control" id="narrationTextarea2Email" 
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







{{-- Modal PO --}}
<div class="modal side-panel  fade" id="professionalservice_popup" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title ps-0">Professional Service</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 p-0">
                                    <div class="">
                                        <table  class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                            <thead>
                                                <tr >
                                                    <th style="width:15px"><input type="checkbox" id="po_check_all_pro" onclick="po_check_fun_pro()" checked/>
                                                    <th style="width:15px">No</th>
                                                    <script>
                                                        function po_check_fun_pro(){
                                                            if($("#po_check_all_pro").prop('checked') == true){
                                                                $('.po_check_pro').prop('checked', true);
                                                            } else{
                                                                $('.po_check_pro').prop('checked', false);
                                                            }
                                                        }
                                                    </script>
                                                    </th>
                                                    <th style="width:90px">@lang('Part No')</th>
                                                    <th style="width:100px">@lang('Description')</th>
                                                    <th style="width:30px" class="text-center">@lang('Qty')</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                               
                    @if (count($quotationitems) > 0)
                        @forelse ($quotationitems as $item2)   
                             <tr>
                                    <td>
                                        <input type="checkbox" class="po_check_pro" name="po_check_pro[]" value="{{ $item2->id }}" checked />
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td style="width:90px; word-wrap: break-word;">
                                        {{ $item2->productname ? $item2->productname->part_number : '' }}
                                    </td>
                                    <td style="width:100px; word-wrap: break-word;">
                                        {{ $item2->productname ? $item2->productname->description : '' }}
                                    </td>
                                    <td style="width:30px" class="text-center">
                                        {{ $item2->qty }}
                                    </td>
                                </tr>
                        @empty

                            <tr>
                                <td colspan="4" class="text-center">No items found.</td>
                            </tr>

                        @endforelse
                    @endif

                                             
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <div class="modal-footer d-flex justify-content-center p-0">
    <button type="button" class="btn btn-light add-btn ms-2" id="proffesional_service_submit_btn"> 
        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
    </button>
</div>

                            
                       
                        
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- Modal PO --}}

 

      <!-- Modal Support-->
    <div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="">
                     
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-upload-excel-quote-edit', 'method' => 'POST', 'id' => 'crm-quote-upload-excel-quote-edit']) }}
             
              
                <input type="hidden" id="excel_deal_id" name="excel_deal_id" value="{{ $edit->id }}" />
                <input type="hidden" id="excel_cust_id" name="excel_cust_id" value="{{ $edit->cust_id }}" />
                <input type="hidden" id="excel_vat" name="excel_vat" value="{{ @$edit->customername->vat_percentage ?? 0 }}" />
               
           
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Quotation Excel Import</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
           
                <script>
                    function add_excel_data()
                    {
                        $('#excel_company_id').val($('#company_id').val());
                        $('#excel_currency_id').val($('#currency_id').val());
                        $('#excel_customer_type').val($('#customer_type').val());
                        $('#excel_quote_validity').val($('#quote_validity').val());
                        $('#excel_payment_terms').val($('#payment_terms').val());
                        $('#excel_delivery_date').val($('#delivery_date1').val());
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
                                <button type="button" onclick="readExcel()" class="btn btn-light text-success">Preview</button>
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
                                        reader.onload = function(event) {
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



                                                var part_number = <?php echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                                var lowercase_part_number = part_number.map(function(value) {
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
                                                deleteButton.onclick = function() {
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
        $(document).on("change", "#delivery_company", function () {
            var name = $("#delivery_company").val();
           
            get_cust_name2(name);
        });

        function get_cust_name2(name) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-deals-customername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                },
                cache: false,
                success: function(dataResult) {
                    console.log(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                var name = dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                $("#delivery_name").val(name.replace('null ','').replace('null',''));
                                $("#delivery_number").val(dataResult['data'][i].mobile);
                                $("#delivery_email").val(dataResult['data'][i].email);
                                // $("#delivery_address1").val(dataResult['data'][i].address);
                                // $("#delivery_address2").val(dataResult['data'][i].address2);

                                $('#delivery_area1').val(dataResult['data'][i].area);
                                $('#delivery_building').val(dataResult['data'][i].building_name);
                                $('#delivery_flat_office_no').val(dataResult['data'][i].flat_office_no);

                                
                                
                                $("#delivery_city").val(dataResult['data'][i].city);
                                $("#delivery_zip_code").val(dataResult['data'][i].zip_code);
                                $("#country_n_e").val(dataResult['data'][i].country_id);
                                $("#state_n_e").val(dataResult['data'][i].state_id);

                                // Tell Select2 to refresh its display without firing 'change'
                                $("#country_n_e").trigger('change.select2');
                                $("#state_n_e").trigger('change.select2');
                            
                                
                            }
                        }
                        else{
                            $("#delivery_name").val('');
                            $("#delivery_number").val('');
                            $("#delivery_email").val('');
                            $("#cust_email").val('');
                            $("#delivery_address1").val('');
                            $("#delivery_address2").val('');
                            $("#delivery_city").val('');
                            $("#delivery_zip_code").val('');
                            $("#state_n_e").val('');
                            $("#country_n_e").val('');
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        });
        </script>



<script>
$(function() {
    // Cache selectors for performance
    const $id1 = $('#terms_and_condition');
    const $id2 = $('#payment_terms');

    // Trimmed values to ignore spaces
    const val1 = $.trim($id1.val());
    const val2 = $.trim($id2.val());

    // Check both
    if (val1 && val2) {
        console.log(' Both inputs have values:', val1, val2);
       
    } else if (val1 || val2) {
        console.log(' One of the inputs has a value.');
       
    } else {
        console.log(' Both inputs are empty.');
        change_cust_id()
         var $txt = $('#company option:selected').text();
        var $tc = "1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n" +
                  "2. Please mention our Quotation No. in your Purchase Order\n" +
                  "3. In case of non-availability of quote products " + $txt + 
                  " reserves the right to supply a functionally similar or better product.";
        $id1.val($tc);
       
    }

});
</script>


             <script>
document.addEventListener("DOMContentLoaded", function () {

    // --- Restore last active tab ---
    let lastTab = localStorage.getItem("active-dealedit-tab");
    if (lastTab) {
        let tabTrigger = document.querySelector('[data-bs-target="' + lastTab + '"]');
        if (tabTrigger) {
            let tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    // --- Save tab when user changes it ---
    let tabButtons = document.querySelectorAll('#purchaseDetailsTabs button[data-bs-toggle="tab"]');

    tabButtons.forEach(btn => {
        btn.addEventListener("shown.bs.tab", function (e) {
            localStorage.setItem("active-dealedit-tab", e.target.getAttribute("data-bs-target"));
        });
    });

});
</script>



          <script>
$(document).ready(function () {
    function toggleFollowupField() {
        const stageVal = $('#stage').val();
        if (stageVal === '1' || stageVal === '2') {
            $('#followup_date_div').show();
            $('#followup_date').prop('required', true);
        } else {
            $('#followup_date_div').hide();
            $('#followup_date').prop('required', false);
        }
    }

    // Run on load (important for edit forms)
    toggleFollowupField();

    // Run on change
    $('#stage').on('change', toggleFollowupField);
});
flatpickr(".date-time-picker", {
  enableTime: true,
  dateFormat: "d/m/Y h:i K", // dd/mm/yyyy hh:mm AM/PM
  allowInput: true,          // allows typing
  time_24hr: false,          // 12-hour format with AM/PM
  minuteIncrement: 1         // finer control
});
</script>

<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize form validation for crm-quote-form
    FormValidator.init('crm-quote-form', {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        toastrPosition: 'toast-top-right',
        toastrTimeout: 6000
    });
});
</script>

     

    

<script>
        

     function openReservedStockListModal(el) {
    // Show loading indicator
    $('#loading_bg').show();

    // data-stock is ALREADY an object
    var value = $(el).data('stock');
    //if value dont exist return
    if (!value) {
        console.error('No stock data found on element:', el);
        $('#loading_bg').hide();
        return;
    }
    var balance_qty = $(el).data('balance');

    console.log('Opening reserved stock list for:', value);

    $('#reservedStockListModalLabel').text('Reserved Stock - ' + value.part_number);
    $('#reserved_stock_partno').val(value.stockid);
    $('#reserved_stock_balance_qty').val(balance_qty);
    $('#reserved_stock_part_number').val(value.part_number);

    // Load reserved stock data via AJAX
    loadReservedStockData(value.stockid, value.part_number, balance_qty);

    $('#reservedStockListModal').modal('show');
}


        function loadReservedStockData(stockId, partNumber, balance_qty) {
            $('#reservedStockTableBody').html('<tr><td colspan="9" class="text-center">Loading...</td></tr>');

            $('#reservedStockListTitle').text('Reserved Stock - ' + partNumber);

            $.ajax({
                url: "{{ URL::to('get-reserved-stock-list') }}",
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    stock_id: stockId,
                    part_number: partNumber
                },
                success: function(response) {
                    console.log("response", response);
                    if (response.success && response.data.length > 0) {
                        let tableBody = '';
                        response.data.forEach(function(item) {
                            tableBody += `
                                <tr>
                                    <td class="text-center" style="padding: 1px 3px;">${item.doc_number}</td>
                                    <td class="text-center" style="padding: 1px 3px;">${item.deal_id || '-'}</td>
                                    <td style="padding: 1px 3px;">${item.customer_name}</td>
                                    <td style="padding: 1px 3px;">${item.sales_person || 'N/A'}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserved_qty}</td>
                                    <td style="padding: 1px 3px;" class="text-center">${item.reserve_date}</td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.created_by} ${item.created_at} </td>
                                    <td style="padding: 1px 3px;" class="text-start">${item.updated_by} ${item.updated_at}</td>
                                   
                                </tr>
                            `;
                        });
                        $('#reservedStockTableBody').html(tableBody);
                    } else {
                        $('#reservedStockTableBody').html(
                            '<tr><td colspan="9" class="text-center text-muted">No reserved stock found</td></tr>'
                        );
                    }
                    $('#loading_bg').hide(); // Hide loader after data is loaded
                },
                error: function() {
                    $('#reservedStockTableBody').html(
                        '<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
                    $('#loading_bg').hide(); // Hide loader even on error
                }
            });
        }

       </script>

<!-- Modal: Narration Input (opens when user clicks #narration) -->
<div class="modal side-panel fade" id="NarrationInputModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="narrationInputModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="narrationInputModalLabel">Enter Narration</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control" id="narrationModalTextarea" rows="6" placeholder="Write narration here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveNarrationBtn" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const narrationInput = document.getElementById('narration');
        const narrationModalEl = document.getElementById('NarrationInputModal');
        const narrationModalTextarea = document.getElementById('narrationModalTextarea');
        const saveNarrationBtn = document.getElementById('saveNarrationBtn');

        if (narrationInput && narrationModalEl) {
            // Open modal when input is clicked
            narrationInput.addEventListener('click', function () {
                narrationModalTextarea.value = narrationInput.value || '';
                const modal = new bootstrap.Modal(narrationModalEl);
                modal.show();
            });

            // Open modal on Enter key
            narrationInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    narrationModalTextarea.value = narrationInput.value || '';
                    const modal = new bootstrap.Modal(narrationModalEl);
                    modal.show();
                }
            });

            // Autofocus textarea when modal opens
            narrationModalEl.addEventListener('shown.bs.modal', function () {
                setTimeout(function () { narrationModalTextarea.focus(); }, 100);
            });

            // Save back to input and close
            saveNarrationBtn.addEventListener('click', function () {
                narrationInput.value = narrationModalTextarea.value;
                bootstrap.Modal.getInstance(narrationModalEl).hide();
            });

            // Ctrl/Cmd+Enter to save
            narrationModalTextarea.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    saveNarrationBtn.click();
                }
            });
        }
    });
</script>



<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">Edit Quote (Deal ID - {{ $edit->deal_code->code }})</h2>
                <span class="page-label">Home - Deal - Edit Quote</span>
            </div>
            <div>
                <a href="{{ url('crm-deals/'.$edit->id.'/view') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> View Deal {{ $edit->deal_code->code }}</a>
            </div>
        </div>



        <div class="card p-4 d-flex mb-3">
        <div class="row">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-create', 'method' => 'POST', 'id' => 'crm-quote-create']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="cust_id" id="cust_id" value="{{ $edit->cust_id }}" />
                        <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($companylist as $value)
                                        <option value="{{ @$value->id }}" @if($value->id == $edit->company_id) selected @endif>{{ @$value->company_name }} - {{ @$value->city }}</option>
                                    @endforeach
                                </select>
                                <script>
                                    var $txt = $('#main_company_id option:selected').text();
                                    var $tc="1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n2. Please mention our Quotation No.in your Purchase Order\n3. In case of non-availability of quote products "+$txt+" reserved the rights to supply a functionally similar or better product.";
                                    $('#terms_and_condition').val($tc);
                                </script>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Customer Type</label>
                                <select class="form-control" name="customer_type" id="customer_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1" @if ($customer_type == 1) selected @endif>Reseller</option>
                                    <option value="2" @if ($customer_type == 2) selected @endif>Enduser</option>
                                    <option value="3" @if ($customer_type == 3) selected @endif>Ecommerce</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Quote Validity</label>
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off" placeholder="Quote Validity" name="quote_validity" value="{{ $quote_validity }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Payment Terms</label>
                                <select class="form-control" name="payment_terms" id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}" @if ($payment_terms == $value->id) selected @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text" value="{{ $payment_terms_name }}"
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                                <script>
                                    $('#payment_terms').on('change', function(e) {
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
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Delivery Time</label>
                                <input class="form-control" id="delivery_time" type="text" autocomplete="off" placeholder="Delivery Time" name="delivery_time" value="{{ $delivery_time }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Currency</label>
                                <select class="form-control" name="currency_id" id="currency_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($currencylist as $value)
                                        <option value="{{ @$value->id }}" @if ($currency_id == $value->id) selected @endif>{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Closing Date</label>
                                <input class="form-control" id="delivery_date" type="date" name="delivery_date" value="{{ $delivery_date }}" required>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label for="" class="form-label">Terms and Condition</label>
                                <textarea class="form-control" rows="3" id="terms_and_condition" autocomplete="off" name="terms_and_condition">1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No.in your Purchase Order
3. In case of non-availability of quote products {{ $edit->companyname->company_name }} reserved the rights to supply a functionally similar or better product.</textarea>
                            </div>
                        </div>
                    </div>
                </div>

        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="equipment comon-status row d-block">
                    <hr />
                    <a class="btn-sm btn-danger float-right" data-toggle="modal" data-target="#ModalExcelQuote" data-backdrop="static" data-keyboard="false">Quotation Excel Import</a>
                    <h6 class="primary-color">@lang('Item Details'):</h6> 
                    
                    <table class="table table-bordered table-striped" id="table_id" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:300px;">@lang('Part No')</th>
                                <th>@lang('Description')</th>
                                <th style="width:80px;">@lang('Cost')</th>
                                <th style="width:60px;">@lang('Vat')</th>
                                <th style="width:50px;">@lang('Qty')</th>
                                <th style="width:80px;">@lang('Unit Price')</th>
                                <th style="width:80px;">@lang('Value')</th>
                                <th style="width:80px;">@lang('Discount')</th>
                                <th style="width:80px;">@lang('Taxable Amount')</th>
                                <th style="width:80px;">@lang('VAT Amount')</th>
                                <th style="width:80px;">@lang('Total')</th>
                                <th style="width:20px;"></th>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked hidden>

                                    <input class="form-control" type="text" id="part_number" name="part_number" autocomplete="off">
                                            <div id="part_number_list">
                                            </div>                            
                                            <script>
                                                $(document).ready(function(){
                                                
                                                 $('#part_number').keyup(function(){ 
                                                        var query = $(this).val();
                                                        if(query != '')
                                                        {
                                                         var _token = $('input[name="_token"]').val();
                                                         $.ajax({
                                                          url:"{{ route('autocomplete.fetch_product_partnumber_deal') }}",
                                                          method:"POST",
                                                          data:{query:query, _token:_token},
                                                          success:function(data){
                                                           $('#part_number_list').fadeIn();  
                                                                    $('#part_number_list').html(data);
                                                          }
                                                         });
                                                        }
                                                    });
                                                
                                                    $(document).on('click', 'li', function(){  
                                                        $('#part_number').val($(this).text());
                                                        var part_number = $(this).text();
                                                        var id = part_number.replace(/\s+/, '');
                                                        id = id.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '').replace('&', '').replace('/', '');
                                                        $('#part_number_new').val($('#id_'+id).val());
                                                        $('#description_new').val($('#descroption_'+id).val());
                                                        $('#part_number_list').fadeOut();  
                                                    });  
                                                
                                                });
                                                </script>

                                        <input type="hidden" name="part_number[]" id="part_number_new" />
                                    
                                    {{--  <select class="form-control js-example-basic-single" name="part_number[]" id="part_number_new" onchange="ddl_part_change_new()">
                                        <option value="none"></option>
                                        @foreach ($items as $key => $value)
                                            <option value="{{ @$value->id }}">{{ @$value->part_number }} <p style="display: none !important;"></p></option>
                                        @endforeach
                                    </select>  --}}
                                </td>
                                <td>
                                    {{--  <select class="form-control" name="part_number_txt[]" id="part_number_txt_new" readonly="true" hidden>
                                        <option value="none"></option>
                                        @foreach ($items as $key => $value)
                                            <option value="{{ @$value->id }}">{{ @$value->description }}</option>
                                        @endforeach
                                    </select>  --}}
                                    <input class="form-control" type="text" id="description_new" name="description[]" autocomplete="off">
                                </td>
                                <td>
                                    <input class="form-control add_cost" type="number" id="cost" name="cost[]" step="any" autocomplete="off" min="0">
                                </td>
                                <td>
                                    <?php /*<input type="hidden" id="net_vat" name="net_vat" value="{{ $edit->customername->vat_percentage }}"/> */ ?>
                                    <select class="form-control" name="net_vat[]" id="net_vat" onchange="calc_change_new()">
                                        @php
                                            $customerVat = $edit->customername->vat_percentage;

                                            // Get unique VATs excluding the customer's
                                            $otherVats = collect($basecompany_vat)
                                                ->pluck('vat_percentage')
                                                ->filter(function ($vat) use ($customerVat) {
                                                    return $vat !== null && $vat !== '' && $vat != $customerVat;
                                                })
                                                ->unique()
                                                ->sort();
                                        @endphp
                                  
                                        @if (!empty($customerVat))
                                            <option value="{{ $customerVat }}" selected>{{ $customerVat }}%</option>
                                        @endif

                                        @foreach ($otherVats as $vat)
                                            <option value="{{ $vat }}">{{ $vat }}%</option>
                                        @endforeach

                                            <option value="0">None</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="qty" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control add_unitprice" type="text" id="unitprice" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new()">
                                    <script>
                                        var isProcessingEnter = false;

                                         function applyPercentageLogic($unitInput) {
                                                try {
                                                    const $row = $unitInput.closest('tr');
                                                    const $costInput = $row.find('.add_cost');
                                                    const unitVal = $unitInput.val().trim();

                                                    if (unitVal.startsWith('+')) {
                                                        const percent = parseFloat(unitVal.slice(1));
                                                        const costVal = parseFloat($costInput.val());

                                                        if (!isNaN(percent) && !isNaN(costVal)) {
                                                            const newUnitPrice = costVal + (costVal * percent / 100);
                                                            $unitInput.val(newUnitPrice.toFixed(2));
                                                            return true;
                                                        }
                                                    }
                                                } catch (err) {
                                                    console.error('Error in applyPercentageLogic:', err);
                                                }
                                                return false;
                                            }

                                        // $("#unitprice").on('keyup', function (e) {
                                        //     if (e.key === 'Enter' || e.keyCode === 13) {
                                               
                                        //         const $unitInput = $(this);
                                        //         applyPercentageLogic($unitInput)
                                                
                                        //         calc_change_new();
                                        //         if($('#btn_add_row').css('display') == 'none'){
                                        //             $('#update_add_row').click();
                                        //         }
                                        //         if($('#update_add_row').css('display') == 'none'){
                                        //             $('#btn_add_row').click();
                                        //         }
                                        //     }
                                        // });

                                        $(document).on('blur', '.add_unitprice', function () {
                                          // skip if Enter just triggered
                                            const $unitInput = $(this);
                                            if (applyPercentageLogic($unitInput)) {
                                                calc_change_new();
                                            }
                                        });

                                        
                                    </script>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="value" name="value[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="discount" name="discount[]" autocomplete="off" min="0" value="0" onchange="calc_change_new()">
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="taxableamount" name="taxableamount[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="vatamount" name="vatamount[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input class="form-control" type="number" id="totalamount" name="totalamount[]" autocomplete="off" min="0" readonly>
                                </td>
                                <td>
                                    <input type="hidden" id="cart_item_id" />
                                    <input type="hidden" id="deal_ref_id" />
                                    <a id="btn_add_row" onclick="return add_rows()" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                    <a id="update_add_row" style="display: none;" onclick="return row_update()" class="btn btn-warning">Update</a>
                                </td>
                            </tr>
                            
                            <script>
                                // Bind once on DOM ready
                                document.addEventListener('DOMContentLoaded', function () {
                                    // Focus qty on Enter in tax
                                    document.querySelectorAll('input[name="cost[]"]').forEach(function (el) {
                                        el.addEventListener('keydown', function (e) {
                                            if (e.key === 'Enter') {
                                                e.preventDefault();
                                                this.closest('tr').querySelector('input[name="qty[]"]').focus();
                                            }
                                        });
                                    });
                            
                                    // Focus unitprice on Enter in qty
                                    document.querySelectorAll('input[name="qty[]"]').forEach(function (el) {
                                        el.addEventListener('keydown', function (e) {
                                            if (e.key === 'Enter') {
                                                e.preventDefault();
                                                this.closest('tr').querySelector('input[name="unitprice[]"]').focus();
                                            }
                                        });
                                    });
                            
                                    // Call add_rows on Enter in unitprice
                                    document.querySelectorAll('input[name="unitprice[]"]').forEach(function (el) {
                                        el.addEventListener('keydown', function (e) {
                                            if (e.key === 'Enter') {
                                                e.preventDefault();
                                                $('#btn_add_row').prop('disabled', true);
                                                const $unitInput = $(this);
                                                applyPercentageLogic($unitInput)
                                                calc_change_new();
                                                return add_rows();
                                            }
                                        });
                                    });
                                });                                
                            </script>
                            
                            <script>
                            function ddl_part_change_new() {
                                var selOpt = $('#part_number_new :selected').val();
                                $('#part_number_txt_new option[value=' + selOpt + ']').attr('selected', 'selected');
                                var selOpt2 = $('#part_number_txt_new :selected').text();
                                $('#description_new').val(selOpt2.trim());
                                $('#description_new').focus();
                            }
                            function calc_change_new(id) {
                                var net_vat = $('#net_vat').val();
        
                                var qty = $('#qty').val();
                                var unitprice = $('#unitprice').val();
                                var value = $('#value').val();
                                var discount = $('#discount').val();
        
                                qty = (qty === '') ? '0' : qty;
                                unitprice = (unitprice === '') ? '0' : unitprice;
                                var fin_value = (unitprice * qty);
                                $('#value').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
        
        
                                value = (value === '') ? '0' : value;
                                discount = (discount === '') ? '0' : discount;
                                var fin_taxableamount = ((unitprice * qty) - Number(discount));
                                $('#taxableamount').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                                var fin_vatamount = ((unitprice * qty) - Number(discount)) * ((Number(net_vat)) / 100);
                                var vatamount = $('#vatamount').val(fin_vatamount.toFixed(@json(session('logged_session_data.decimal_point'))));

                                $('#totalamount').val((Number(fin_taxableamount) + Number(fin_vatamount)).toFixed(@json(session('logged_session_data.decimal_point'))));

                                var pid = $('#part_number_new').val();
                                if(pid==35710 || pid==36223 || pid==26328 || pid==26324){
                                    $('#cost').val(unitprice*75/100);
                                }
        
                            }
                            function add_rows() {
                                if($("#part_number_new").val()=="none"){$("#part_number_new").focus(); return false;}
                                if($("#cost").val()==""){$("#cost").focus(); return false;}
                                if($("#cost").val()=="0"){$("#cost").focus(); return false;}
                                if($("#qty").val()==""){$("#qty").focus(); return false;}
                                if($("#unitprice").val()==""){$("#unitprice").focus(); return false;}
                                if($("#taxableamount").val()==""){$("#taxableamount").focus(); return false;}
                                if($("#vatamount").val()==""){$("#vatamount").focus(); return false;}
                                $("#loading_bg").css("display", "block");

                                var action = "{{ URL::to('crm-quote-add-items-cart') }}";
                                $.ajax({
                                    url: action,
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        deal_id : $("#deal_id").val(),
                                        cust_id : $("#cust_id").val(),
                                        company_id : $("#company_id").val(),
                                        currency_id : $("#currency_id").val(),
                                        customer_type : $("#customer_type").val(),
                                        quote_validity : $("#quote_validity").val(),
                                        payment_terms : $("#payment_terms").val(),
                                        delivery_date : $("#delivery_date").val(),
                                        payment_terms_txt : $("#payment_terms_txt").val(),
                                        delivery_time : $("#delivery_time").val(),
                                        product_id : $("#part_number_new").val(),
                                        qty : $("#qty").val(),
                                        price : $("#unitprice").val(),
                                        description : $("#description_new").val(),
                                        discount : $("#discount").val(),
                                        vat : $('#net_vat').val(),
                                        cost : $('#cost').val(),
                                    },
                                    cache: false,
                                    success: function(dataResult) {
                                        var dataResult = JSON.parse(dataResult);
                                        var len = 0;
                                       
                                        if (dataResult['data'] == "ERROR") {
                                            alert("Error found in something!!");
                                        } else {
                                            $("#loading_bg").css("display", "none");
                                            location.reload(true);
                                        }
                                    }
                                });
                                
                                $("#loading_bg").css("display", "none");
                            }  
                            
                            
                            function row_edit(id) {
                                $('#btn_add_row').css("display",'none');
                                $('#update_add_row').css("display",'block');

                                var partno = $('#partno_'+id).val();
                                var pid = $('#pid_'+id).val();
                                //alert(partno);
                                //alert(pid);
                                
                                $("#part_number_new").val(partno);
                                $("#part_number").val(pid);
                                $('#description_new').val($('#description_'+id).val());
                                $('#net_vat').val($('#tax_'+id).val());
                                $('#qty').val($('#qty_'+id).val());
                                $('#unitprice').val($('#unitprice_'+id).val());
                                $('#value').val($('#value_'+id).val());
                                $('#discount').val($('#discount_'+id).val());
                                $('#taxableamount').val($('#taxableamount_'+id).val());
                                $('#vatamount').val($('#vatamount_'+id).val());
                                $('#taxableamount').val($('#taxableamount_'+id).val());
                                $('#totalamount').val($('#totalamount_'+id).val());
                                $('#cost').val($('#cost_'+id).val());

                                $('#cart_item_id').val($('#cart_item_id_'+id).val());
                                $('#deal_ref_id').val($('#deal_ref_id_'+id).val());
                            }
                            
                            function row_update() {
                                $("#loading_bg").css("display", "block");
                                var action = "{{ URL::to('crm-quote-update-items-cart') }}";
                                $.ajax({
                                    url: action,
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        itm_id : $('#cart_item_id').val(),
                                        deal_id : $("#deal_id").val(),
                                        cust_id : $("#cust_id").val(),
                                        company_id : $("#company_id").val(),
                                        currency_id : $("#currency_id").val(),
                                        customer_type : $("#customer_type").val(),
                                        quote_validity : $("#quote_validity").val(),
                                        payment_terms : $("#payment_terms").val(),
                                        delivery_date : $("#delivery_date").val(),
                                        payment_terms_txt : $("#payment_terms_txt").val(),
                                        delivery_time : $("#delivery_time").val(),
                                        product_id : $("#part_number_new").val(),
                                        qty : $("#qty").val(),
                                        price : $("#unitprice").val(),
                                        description : $("#description_new").val(),
                                        discount : $("#discount").val(),
                                        vat : $('#net_vat').val(),
                                        cost : $('#cost').val(),
                                    },
                                    cache: false,
                                    success: function(dataResult) {
                                        var dataResult = JSON.parse(dataResult);
                                        var len = 0;
                                        if (dataResult['data'] == "ERROR") {
                                            alert("Error found in something!!");
                                        } else {
                                            $("#loading_bg").css("display", "none");
                                            location.reload(true);
                                        }
                                    }
                                });
                                $("#loading_bg").css("display", "none");
                                $("#edit_cart_close").click();
                            }

                        </script>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped" id="po-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:200px;">@lang('Part No')</th>
                                <th style="width:300px;">@lang('Description')</th>
                                <th class="text-right" style="width:70px;">@lang('Cost')</th>
                                <th class="text-center" style="width:70px;">@lang('Vat')</th>
                                <th class="text-center"style="width:70px;">@lang('Qty')</th>
                                <th class="text-right"style="width:80px;">@lang('Unit Price')</th>
                                <th class="text-right"style="width:70px;">@lang('Value')</th>
                                <th class="text-right"style="width:70px;">@lang('Discount')</th>
                                <th class="text-right"style="width:120px;">@lang('Taxable Amount')</th>
                                <th class="text-right"style="width:100px;">@lang('VAT Amount')</th>
                                <th class="text-right"style="width:100px;">@lang('Total Amount')</th>
                                <th class="text-right"style="width:65px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <script>
                                function show_tool_tip(id){
                                    $('#desc_'+id).css('white-space','');
                                }
                                function hide_tool_tip(id){
                                    $('#desc_'+id).css('white-space','nowrap');
                                }
                            </script>
                            @php 
                            $t_value=0;
                                $t_discount=0;
                                $t_taxableamount=0;
                                $t_vatamount=0; @endphp

                            @if (count($quotationitems)>0)
                            @foreach ($quotationitems as $dt)
                            @php
                                $value = $dt->price * $dt->qty;
                                $taxableamount = $value - $dt->discount;
                                $vatamount = $taxableamount * $dt->vat / 100;

                                $t_value += $value;
                                $t_discount += $dt->discount;
                                $t_taxableamount += $taxableamount;
                                $t_vatamount += $vatamount;
                            @endphp
                            <tr>
                                <td>{{ $dt->part_number }} <input type="hidden" id="partno_{{ $dt->id }}" value="{{ $dt->product_id }}" />
                                    <input type="hidden" id="pid_{{ $dt->id }}" value="{{ $dt->part_number }}" /></td>
                                <td>
                                    <div id="desc_{{ $dt->id }}" onmouseover="show_tool_tip({{ $dt->id }})" onmouseout="hide_tool_tip({{ $dt->id }})" style="width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{!! $dt->description !!}
                                    </div> 
                                    <input type="hidden" id="description_{{ $dt->id }}" value="{{ $dt->description }}" /></td>
                                <td align="right">{{ $dt->cost }} <input type="hidden" id="cost_{{ $dt->id }}" value="{{ $dt->cost }}" /></td>
                                <td class="text-center">{{ $dt->vat }}%<input type="hidden" id="tax_{{ $dt->id }}" value="{{ intval($dt->vat) }}" /></td>
                                <td align="center">{{ $dt->qty }} <input type="hidden" id="qty_{{ $dt->id }}" value="{{ $dt->qty }}" /></td>
                                <td align="right">{{ @App\SysHelper::com_curr_format($dt->price,2,'.',',') }} <input type="hidden" id="unitprice_{{ $dt->id }}" value="{{ $dt->price }}" /></td>
                                <td align="right">{{ @App\SysHelper::com_curr_format($value,2,'.',',') }} <input type="hidden" id="value_{{ $dt->id }}" value="{{ $value }}" /></td>
                                <td align="right">{{ @App\SysHelper::com_curr_format($dt->discount,2,'.',',') }} <input type="hidden" id="discount_{{ $dt->id }}" value="{{ $dt->discount }}" /></td>
                                <td align="right">{{ @App\SysHelper::com_curr_format($taxableamount,2,'.',',') }} <input type="hidden" id="taxableamount_{{ $dt->id }}" value="{{ $taxableamount }}" /></td>
                                <td align="right">{{ @App\SysHelper::com_curr_format($vatamount,2,'.',',') }} <input type="hidden" id="vatamount_{{ $dt->id }}" value="{{ $vatamount }}" /></td>
                                <td align="right">{{ @App\SysHelper::com_curr_format($taxableamount+$vatamount,2,'.',',') }} <input type="hidden" id="totalamount_{{ $dt->id }}" value="{{ $taxableamount+$vatamount }}" /></td>
                                <td>
                                    <input type="hidden" id="cart_item_id_{{ $dt->id }}" value="{{ $dt->id }}" />
                                    <input type="hidden" id="deal_ref_id_{{ $dt->id }}" value="{{ $dt->refid }}" />
                                    <a onclick="row_edit({{ $dt->id }})" class="btn-sm btn-primary"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                    <a href="{{ url('crm-quote-cart/'.$dt->id.'/delete') }}" class="btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>
                                </tr>
                            @endforeach                            
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td></td>
                                <td></td>
                                <td class="font-weight-bold"></td>
                                <td></td>
                                <td class="text-center font-weight-bold"><label id="qty_total">{{ $quotationitems->sum('qty') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="unitprice_total"></label></td>
                                <td class="text-right font-weight-bold"><label id="value_total">{{ @App\SysHelper::com_curr_format($t_value,2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="discount_total">{{ @App\SysHelper::com_curr_format($t_discount,2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($t_taxableamount,2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($t_vatamount,2,'.',',') }}</label></td>
                                <td class="text-right font-weight-bold"><label id="amount_total">{{ @App\SysHelper::com_curr_format($t_taxableamount + $t_vatamount,2,'.',',') }}</label></td>
                                <td></td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="11" class="text-right font-weight-bold">Aditional Discount
                                <td colspan="1" class="text-right">
                                    <input type="number" class="form-control text-right" id="deal_discount" name="deal_discount" value="0" step="any" placeholder="Aditional Discount" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>

<script>
function ddl_part_change(id)
{
var selOpt = $('#part_number_'+id+' :selected').val();
$('#part_number_txt_'+id+' option[value='+selOpt+']').attr('selected','selected');        
var selOpt2 = $('#part_number_txt_'+id+' :selected').text();
$('#description_'+id+'').val(selOpt2);
$('#description_'+id+'').focus();
}

function calc_change(id) {
var net_vat = $('#net_vat').val();
//var net_vat = $('#vat_percentage').val();

var qty = $('#qty_' + id + '').val();
var unitprice = $('#unitprice_' + id + '').val();
var value = $('#value_' + id + '').val();
var discount = $('#discount_' + id + '').val();
var taxamount = $('#taxamount_' + id + '').val();
var vatamount = $('#vatamount_' + id + '').val();
var totalamount = $('#totalamount_' + id + '').val();


qty = (qty === '') ? '0' : qty;
unitprice = (unitprice === '') ? '0' : unitprice;
var fin_value = (unitprice * qty);
$('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));


value = (value === '') ? '0' : value;
discount = (discount === '') ? '0' : discount;
var fin_taxableamount = ((unitprice * qty) - Number(discount));
$('#taxamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
$('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));

var fin_totalamount = (fin_taxableamount + fin_vatableamount);
$('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));

calc_total();
}

$(document).on("change", ".unitprice", function () {
var tot = 0;
$(".unitprice").each(function() {
var vale = $(this).val();
if(!isNaN(parseFloat(vale))){
    tot = parseInt(tot) + parseInt(vale);
}
});
alert(tot);
});


function calc_total()
{
var countrow = document.getElementById('si-row-count').value;

//var countrow = $('#si-table >tbody >tr').length;
var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
for(var i=1; i<=countrow; i++)
{
t1 += Number($('#qty_'+i).val());
t2 += Number($('#unitprice_'+i).val());
t3 += Number($('#value_'+i).val());
t4 += Number($('#discount_'+i).val());
t5 += Number($('#customcharges_'+i).val());
t6 += Number($('#taxableamount_'+i).val());
t7 += Number($('#vatamount_'+i).val());
}
$('#qty_total').text(t1);
$('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#customcharges_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#taxableamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#vatamount_total').text(t7.toFixed(@json(session('logged_session_data.decimal_point'))));
$('#net_total').text((t6+t7).toFixed(@json(session('logged_session_data.decimal_point'))));
}

function fn_payment_terms()
{
var val_payment_terms = $('#payment_terms').val();
if(val_payment_terms==22)
{
$('#div_payment_terms').css('display','block');
}
else
{
$('#div_payment_terms').css('display','none');
}
}
function fn_shipping_name()
{
var shipping_id = $('#shipping_name').val();
var shipping_data = $('#ship_'+shipping_id).val();        
var ret = shipping_data.split("#");
$('#shipping_address_1').val(ret[0]);
$('#shipping_address_1').focus();
$('#shipping_address_2').val(ret[1]);
$('#shipping_address_2').focus();
$('#shipping_contact_no').val(ret[2]);
$('#shipping_contact_no').focus();
}

jQuery(document).ready(function(){
    jQuery('input').keypress(function(event){
        var enterOkClass =  jQuery(this).attr('class');
        if (event.which == 13 && enterOkClass != 'enterSubmit') {
            event.preventDefault();
            return false;   
        }
    });
});
</script>



                    </div>
            </div>
        </div>



        <div class="equipment comon-status row mt-4 d-block">
            <table class="table table-bordered table-striped" id="pi-table2" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width:100px;">@lang('Name')</th>
                        <th style="width:350px;">@lang('Credit Account')</th>
                        <th style="width:70px;">@lang('Amount')</th>
                        <th style="width:80px;">@lang('Remarks')
                            <input type="hidden" value="1" id="fright_row" />
                            <a style="cursor: pointer;" class="btn-md float-right" onclick="add_fright()"><i class="fa fa-plus-square" aria-hidden="true"></i></a></th>
                    </tr>
                    <script>
                        function add_fright()
                        {
                            var id = $('#fright_row').val();
                            id=Number(id)+1;
                            $('#fright_row').val(id);
                            $('#fright_row_'+id).css("display", "");
                        }
                    </script>
                </thead>
                <tbody>
                    <tr id="fright_row_1">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_1">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_1" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(1)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_2">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_2">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_2" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(2)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_3">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_3">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_3"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_3" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(3)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_3" name="cfc_remarks[]"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_4">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_4">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_4"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_4" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(4)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_4" name="cfc_remarks[]"
                                autocomplete="off">
                        </td>
                    </tr>
                    <tr style="display: none;" id="fright_row_5">
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_name[]" id="cfc_name_5">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control js-example-basic-single" name="cfc_credit_account[]" id="cfc_credit_account_5"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="number" id="cfc_amount_5" name="cfc_amount[]" step="any"
                                autocomplete="off" min="0" onchange="cfc_amount_change(5)">
                        </td>
                        <td>
                            <input class="form-control" type="text" id="cfc_remarks_5" name="cfc_remarks[]"
                                autocomplete="off">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        
        <div class="modal-footer">
            {{--  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>  --}}
            <button class="btn btn-primary" name="submit" value="GQ">Generate Quote</button>
        </div>
        {{ Form::close() }}



    </div>


    <!-- Modal Excel Quote-->
    <div class="modal fade" id="ModalExcelQuote" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Quotation Excel Import</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-upload-excel-cart', 'method' => 'POST', 'id' => 'crm-quote-upload-excel-cart']) }}
                <input type="hidden" id="excel_deal_id" name="excel_deal_id" value="{{ $edit->id }}" />
                <input type="hidden" id="excel_cust_id" name="excel_cust_id" value="{{ $edit->cust_id }}" />
                <input type="hidden" id="excel_vat" name="excel_vat" value="{{ @$edit->customername->vat_percentage ?? 0 }}" />
                <input type="hidden" id="excel_company_id" name="excel_company_id" value="" />
                <input type="hidden" id="excel_currency_id" name="excel_currency_id" value="" />
                <input type="hidden" id="excel_customer_type" name="excel_customer_type" value="" />
                <input type="hidden" id="excel_quote_validity" name="excel_quote_validity" value="" />
                <input type="hidden" id="excel_payment_terms" name="excel_payment_terms" value="" />
                <input type="hidden" id="excel_delivery_date" name="excel_delivery_date" value="" />
                <input type="hidden" id="excel_payment_terms_txt" name="excel_payment_terms_txt" value="" />
                <input type="hidden" id="excel_delivery_time" name="excel_delivery_time" value="" />

                <script>
                    function add_excel_data()
                    {
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
                        <div class="col-md-2">
                                <label for="" class="form-label">Select File (.csv)</label>
                        </div>
                        <div class="col-md-4">
                                <input class="form-control" type="file" id="excel-file" accept=".xlsx, .xls, .csv" />
                        </div>
                        <div class="col-md-4">
                                <button type="button" onclick="readExcel()" class="btn btn-success">Preview</button>
                                {{-- <input type="file" name="import_file" class="btn-danger" required /> --}}
                                (<a href="{{ url('public/uploads/product_upload/quotation_sample_format.csv') }}" target="_blank">Sample File</a>)
                        </div>

                        <div class="col-md-12 mt-2">
                            <table id="excel-table" class="table table-bordered table-striped" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width:220px;">Part No</th>
                                        <th>Description</th>
                                        <th style="width:100px;" class="text-right">Cost</th>
                                        <th style="width:70px;">Qty</th>
                                        <th style="width:100px;" class="text-right">Unit Price</th>
                                        <th style="width:100px;" class="text-right">Discount</th>
                                        <th style="width:100px;" class="text-right">VAT</th>
                                        <th style="width:50px;" class="text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be inserted here -->
                                </tbody>
                            </table>

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
                                        var workbook = XLSX.read(data, { type: 'binary' });
                            
                                        // Assuming the data is in the first sheet
                                        var sheet = workbook.Sheets[workbook.SheetNames[0]];
                                        var rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                            
                                        var tableBody = document.getElementById('excel-table').getElementsByTagName('tbody')[0];
                                        tableBody.innerHTML = "";  // Clear any previous data
                            
                                        // Loop through each row and add data to the table
                                        for (var i = 1; i < rows.length; i++) {  // Skip header row
                                            var row = rows[i];
                                            if (row.length < 6) continue;  // Skip invalid rows


                                            
                                            var part_number = <?php echo json_encode($part_number); ?>; // Convert PHP array to JS array

                                            var lowercase_part_number = part_number.map(function(value) {
                                                return value.toLowerCase();
                                            });

                                            var json_output = JSON.stringify(lowercase_part_number);

                                            var newRow = tableBody.insertRow(tableBody.rows.length);
                                            
                                            var rowVal = String(row[0] ?? '');
                                            var trimmedValue = rowVal.trim();
                                            
                                            if (json_output.includes(trimmedValue.toLowerCase())) {  // Use .includes() for array checking
    
                                            } else {
                                                newRow.style.backgroundColor = "#ffbebe";
                                            }
                            
                                            // Part No
                                            var partNoCell = newRow.insertCell(0);
                                            var partNoInput = document.createElement('input');
                                            partNoInput.type = 'text';  // Change to text input
                                            partNoInput.name = 'excel_part_no[]';
                                            partNoInput.value = rowVal.trim();
                                            partNoInput.classList.add('form-control');
                                            partNoCell.appendChild(partNoInput);
                            
                                            // Description
                                            var descriptionCell = newRow.insertCell(1);
                                            var descriptionInput = document.createElement('input');
                                            descriptionInput.type = 'text';  // Change to text input
                                            descriptionInput.name = 'excel_description[]';
                                            descriptionInput.value = row[1].trim();
                                            descriptionInput.classList.add('form-control');
                                            descriptionCell.appendChild(descriptionInput);
                            
                                            // Cost (Right-aligned)
                                            var costCell = newRow.insertCell(2);
                                            var costInput = document.createElement('input');
                                            costInput.type = 'text';  // Change to text input
                                            costInput.name = 'excel_cost[]';
                                            costInput.value = row[2];
                                            costInput.classList.add('text-right');
                                            costInput.classList.add('form-control');
                                            costCell.appendChild(costInput);
                            
                                            // Qty
                                            var qtyCell = newRow.insertCell(3);
                                            var qtyInput = document.createElement('input');
                                            qtyInput.type = 'text';  // Change to text input
                                            qtyInput.name = 'excel_qty[]';
                                            qtyInput.value = row[3];
                                            qtyInput.classList.add('form-control');
                                            qtyCell.appendChild(qtyInput);
                            
                                            // Unit Price (Right-aligned)
                                            var unitPriceCell = newRow.insertCell(4);
                                            var unitPriceInput = document.createElement('input');
                                            unitPriceInput.type = 'text';  // Change to text input
                                            unitPriceInput.name = 'excel_unit_price[]';
                                            unitPriceInput.value = row[4];
                                            unitPriceInput.classList.add('text-right');
                                            unitPriceInput.classList.add('form-control');
                                            unitPriceCell.appendChild(unitPriceInput);
                            
                                            // Discount (Right-aligned)
                                            var discountCell = newRow.insertCell(5);
                                            var discountInput = document.createElement('input');
                                            discountInput.type = 'text';  // Change to text input
                                            discountInput.name = 'excel_discount[]';
                                            discountInput.value = row[5];
                                            discountInput.classList.add('text-right');
                                            discountInput.classList.add('form-control');
                                            discountCell.appendChild(discountInput);
                                            
                                            // VAT (Right-aligned)
                                            var vatCell = newRow.insertCell(6);
                                            var vatInput = document.createElement('input');
                                            vatInput.type = 'text';  // Change to text input
                                            vatInput.name = 'vat_excel[]';
                                            vatInput.value = row[6];
                                            vatInput.classList.add('text-right');
                                            vatInput.classList.add('form-control');
                                            vatCell.appendChild(vatInput);
                                            
                                            var deleteCell = newRow.insertCell(7);  // Last cell for delete button
                                            var deleteButton = document.createElement('button');
                                            deleteButton.type = 'button';  // Make sure the button doesn't submit a form
                                            deleteButton.textContent = 'Delete';
                                            deleteButton.classList.add('btn-sm');
                                            deleteButton.classList.add('btn-danger');
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
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary excel_model_close" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                    {{-- onclick="return add_excel_data()" --}}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Excel Quote-->




<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



<style>
    .leadbox{border: solid 1px #ededef; border-radius: 5px; background: #ffffff; padding: 5px 5px 10px 15px; margin-right: 15px;}
    .leadbox2{border: solid 1px #ededef; border-radius: 5px; background: #ffffff; padding: 7px 5px 0px 15px; font-size: 17px;}
    .pro-box{ border: solid 1px #ededef; margin: 5px 15px 5px 5px; padding: 10px;}
    .pro-box:hover{ background: #eff3e7;}
</style>



    <script>
        function add_toquote(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_qty_"+id).val();
            var price = $("#txt_price_"+id).val();
            var description = $("#txt_description_"+id).val();
            var deal_id = $("#deal_id").val();
            var company_id = $("#company_id").val();
            var currency_id = $("#currency_id").val();
            var customer_type = $("#customer_type").val();
            var payment_terms = $("#payment_terms").val();
            var delivery_date = $("#delivery_date").val();
            var payment_terms_txt = $("#payment_terms_txt").val();
            var delivery_time = $("#delivery_time").val();
            var quote_id = $("#quote_id").val();

            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_btn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-additemsedit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    deal_id: deal_id,
                    company_id: company_id,
                    currency_id: currency_id,
                    customer_type: customer_type,
                    payment_terms: payment_terms,
                    delivery_date: delivery_date,
                    payment_terms_txt: payment_terms_txt,
                    delivery_time: delivery_time,
                    quote_id:quote_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function upd_toquote(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_uqty_"+id).val();
            var price = $("#txt_uprice_"+id).val();
            var description = $("#txt_udescription_"+id).val();
            var discount = $("#txt_udiscount_"+id).val();
            var pro_id = $("#txt_upro_id_"+id).val();
            var deal_id = $("#deal_id").val();
            
            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_uqty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_ubtn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-upditemsedit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    discount: discount,
                    pro_id: pro_id,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function del_toquote(id) {
            
            var result = confirm('Are you sure you want to delete this item?');
            if (!result) {
                return false;
            }

            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_del_"+id).val();
            $(btn).attr('disabled', true);
            var deal_id = $("#deal_id").val();
            var quote_id = $("#quote_id").val();
    
            var action = "{{ URL::to('crm-quote-deleteitemsedit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    deal_id: deal_id,
                    quote_id: quote_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function sort_up(id,sort_id,deal_id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-quote-sort-up') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    sort_id: sort_id,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        function sort_down(id,sort_id,deal_id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-quote-sort-down') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    sort_id: sort_id,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

    </script>
@endsection
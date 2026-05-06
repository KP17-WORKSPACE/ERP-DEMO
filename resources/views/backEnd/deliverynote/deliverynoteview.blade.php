@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    
    <?php try { ?>
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between">
                <div class="mb-3">
                    <h2 class="page-heading m-0">Delivery Note View</h2>
                    <span class="page-label">Home - Delivery Note</span>
                </div>
                <div>
                    <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-list"></i> Attachment</a>
                    <a href="{{ url('delivery-note-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                    <a href="{{ url('delivery-note/'.$edit->id.'/edit') }}" type="button" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
                    <!-- Input with Search -->
                    <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                        <input type="text" id="quick_search_doc_number" placeholder="DN Number" class="form-control pr-4" /> 
                        <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                        <i class="fas fa-search"></i>
                        </span>
                    </div>
                    <script>
                        const baseUrl = "{{ url('get-edit-url-delivery-note') }}";                
                        document.getElementById('quick_search_doc_number').addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                var val = this.value.trim();
                                if (val !== '') {                                
                                    window.location.href = baseUrl + '/' + val;
                                }
                            }
                        });
                    </script>
                    <!-- Input with Search -->
                    <a href="{{ url('delivery-note') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                </div>
            </div>
            <div class="card p-4 mb-2">
            
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
            <input type="hidden" name="si_no" id="si_no" value="{{ isset($edit) ? $edit->invoice_no : '' }}">
            <input type="hidden" id="net_vat" name="net_vat">
            <div class="row">
                                <div class="col-lg-4 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Customer') <span>*</span></label>
                                        <select class="form-control js-example-basic-single" name="customer_id" id="customer_id" required disabled>
                                            <option data-display="@lang('Customer')" value="">@lang('Customer')</option>
                                            @foreach ($customer as $value)
                                                <option value="{{ @$value->id }}"
                                                    {{ isset($edit) ? (!empty(@$edit->customer_id) ? (@$edit->customer_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->account_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-8 mb-2">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <label class="txtlbl">DLN Number<span>*</span></label>
                                                <input
                                                    class="form-control"
                                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                                    value="{{ $edit->doc_number }}"
                                                    readonly>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('doc_number'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('doc_number') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">DLN Date</label>
                                                        <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                                            name="doc_date" value="{{ @$edit->doc_date }}" required>
                                                    </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Created By') <span>*</span></label>
                                            <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by" value="{{ isset($edit) ? (!empty(@$edit->created_by) ? @$edit->createdby->full_name : old('created_by')) : Auth::user()->full_name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">Currency<span>*</span></label>
                                                <select class="form-control" name="currency" id="currency">
                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($edit) ? (!empty(@$edit->currency) ? (@$edit->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                                            {{ @$value->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Pending list</label>
                                        <div id="plist" style="width: 100%; height: 250px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                                        <a data-modal-size="modal-md" data-target="#dn_pending_popup_win" id="addDNPending" data-toggle="modal"></a>
                                        <input type="hidden" id="si_id" name="si_id" >
                                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                                    </div>
                                </div>
                                <div class="col-lg-8 mb-2">
                                    <div class="row">
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('LPO No') <span>*</span> </label>
                                                <input class="form-control" type="text" id="lpo_no" name="lpo_no"
                                                value="{{ isset($edit) ? (!empty(@$edit->lpo_no) ? @$edit->lpo_no : old('lpo_no')) : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('LPO Date')</label>
                                                @php $value = date('Y-m-d');
                                                if(isset($edit) && !empty($edit->lpo_date) ){ @$value = date('Y-m-d', strtotime(@$edit->lpo_date)); }
                                                @endphp
                                                <input class="form-control" id="lpo_date" type="date" name="lpo_date" value="{{ @$value }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Payment Terms') <span>*</span></label>
                                                <select class="form-control" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                                    <option data-display="@lang('Payment Terms') *" value="" >@lang('Payment Terms') *</option>
                                                    @foreach($paymentterms as $value)
                                                         <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->paymentterms)? @$edit->paymentterms == @$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('SIV No') <span>*</span> </label>
                                                <input class="form-control" type="text" id="invoice_no" name="invoice_no"
                                                value="{{ isset($edit) ? (!empty(@$edit->invoice_no) ? @$edit->invoice_no : old('invoice_no')) : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('SIV Date')</label>
                                                @php $value = date('Y-m-d');
                                                if(isset($edit) && !empty($edit->invoice_date) ){ @$value = date('Y-m-d', strtotime(@$edit->invoice_date)); }
                                                else{ if(!empty(old('invoice_date'))){ @$value = old('invoice_date'); }else{ @$value = date('Y-m-d'); } }
                                                @endphp
                                                <input class="form-control" id="invoice_date" type="date" name="invoice_date" value="{{ @$value }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Salesman')<span>*</span></label>
                                                <select class="form-control" name="sales_man" id="sales_man" required>
                                                    <option value="">-Select-</option>
                                                    @foreach ($staff as $value)
                                                    <option value="{{ @$value->user_id }}"
                                                        @if(isset($edit)) @if($edit->salesman == $value->user_id) selected @endif @else @if($value->user_id == Auth::user()->id) selected  @endif @endif
                                                        >{{ @$value->full_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Warehouse') <span>*</span> </label>
                                                <input class="form-control" type="text" id="warehouse" name="warehouse"
                                                value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Driver') <span></span> </label>
                                                <input class="form-control" type="text" id="driver" name="driver"
                                                value="{{ isset($edit) ? (!empty(@$edit->driver) ? @$edit->driver : old('driver')) : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Vehicle No') <span>*</span> </label>
                                                <input class="form-control" type="text" id="vehicleno" name="vehicleno"
                                                value="{{ isset($edit) ? (!empty(@$edit->vehicleno) ? @$edit->vehicleno : old('vehicleno')) : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Supplier Name') <span>*</span> </label>
                                                <input class="form-control" type="text" id="supplier_name" name="supplier_name"
                                                value="{{ isset($edit) ? (!empty(@$edit->supplier_name) ? @$edit->supplier_name : old('supplier_name')) : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">  @lang('Deal Id') <span>*</span> </label>
                                                <input class="form-control" type="text" id="deal_id" name="deal_id"
                                                value="{{ @App\SysHelper::get_code_from_dealid($edit->deal_id) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mb-2">
                                            <div class="input-effect">
                                                <label class="dynamicslbl">@lang('Narration') <span>*</span></label>
                                                <input class="form-control" type="text" name="narration" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : old('narration') }}" id="narration">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-lg-4 mb-2" style="display: none;">
                                <div class="input-effect">Pending List
                                    <div class="input-effect" id="sectionDnSINumberDiv">
                                        <select class="niceSelect w-100 bb form-control" name="dn_si_numbers" id="dn_si_numbers">
                                            <option data-display="@lang('Select Sales Invoive Number') *" value="0">@lang('Select Sales Invoive Number') *</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-2">
                                <div class="input-effect">
                                    <a class="primary-btn fix-gr-bg text-white" data-modal-size="modal-md" data-target="#dn_list_popup_win" id="getCtrlDelNote" data-toggle="modal"><span class="ti-search"></span> </a>
                                </div>
                            </div>
                        </div>

                        <div class="equipment comon-status row mt-4 d-block">
                            <table class="table table-bordered table-striped" id="DelNoteList_table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:100px;">@lang('Part No')</th>
                                        <th style="width:150px;">@lang('Description')</th>
                                        <th style="width:70px;">@lang('Vat')</th>
                                        <th style="width:70px;">@lang('Qty')</th>
                                        <th style="width:80px;">@lang('Unit Price')</th>
                                        <th style="width:70px;">@lang('Value')</th>
                                        <th style="width:70px;">@lang('Discount')</th>
                                        <th style="width:120px;">@lang('Taxable Amount')</th>
                                        <th style="width:100px;">@lang('VAT Amount')</th>
                                        <th style="width:100px;">@lang('Total Amount')</th>
                                        <th style="width:70px;">@lang('Srl No')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $qty_total=0;
                                    $value_total=0;
                                    $discount_total=0;
                                    $taxableamount_total=0;
                                    $vatamount_total=0;
                                    $total_amount=0;
                                    @endphp
                                    @if (count($select_cart)>0)
                                    @php $i=0; @endphp
                                        @foreach ($select_cart as $cart)
                                        @php                                        
                                        $value = @App\SysHelper::com_curr_format($cart->qty * $cart->unitprice, 2, '.', '');
                                        $taxamount=@App\SysHelper::com_curr_format($value - $cart->discount, 2, '.', '');
                                        $vatamount = $cart->vatamount;
                                        $totalamount = (($cart->qty * $cart->unitprice) - $cart->discount)+(($cart->qty * $cart->unitprice) - $cart->discount)*$cart->vatamount/100;
                                        
                                        $qty_total += $cart->qty;
                                        $value_total += $value;
                                        $discount_total += $cart->discount;
                                        $taxableamount_total += $taxamount;
                                        $vatamount_total += $vatamount;
                                        $total_amount += $taxamount+$vatamount;
                                        
                                        @endphp
                                        <tr>
                                            <td><input class="form-control" type="text" id="part_number_{{ $i }}" name="part_number[]" value="{{ $cart->partno }}"+pin.partnumber+"" readonly>
                                            <input type="hidden" id="part_id_{{ $i }}" name="part_id[]" value="{{ $cart->part_number }}"</td>
                                            <td class="jshide"><input class="form-control" type="text" id="description_{{ $i }}" name="description[]" autocomplete="off" min="0" value="{{ $cart->description }}" ></td>
                                            <td><input class="form-control" type="number" id="tax_{{ $i }}" name="tax[]" autocomplete="off" min="0" value="{{ $cart->tax }}" readonly></td>
                                            <td><input class="form-control qty" type="number" id="qty_{{ $i }}" name="qty[]" autocomplete="off" min="0" value="{{ $cart->qty }}" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="unitprice_{{ $i }}" value="{{ @App\SysHelper::com_curr_format( $cart->unitprice, 2, '.', '')}}" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="value_{{ $i }}" value="{{ $value }}" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="discount_{{ $i }}" value=" {{ @App\SysHelper::com_curr_format( $cart->discount , 2, '.', '') }}" name="discount[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="taxableamount_{{ $i }}" value="{{ $taxamount }}" name="taxableamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="vatamount_{{ $i }}" value="{{ $vatamount }}" name="vatamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="totalamount_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($totalamount , 2, '.', '') }}" name="totalamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control srl" type="test" id="srl_{{ $i }}" name="srl[]" onclick="srlno_add({{ $i }})" value="{{ app\Http\Controllers\SysDeliveryNoteController::get_srl_no($cart->dn_id,$cart->part_number,$cart->id) }}" ></td>
                                            </tr>
                                        @php $i++; @endphp
                                        @endforeach                                        
                                    @endif
                                </tbody>                                
                                <thead>
                                    <input type="hidden" id="dn_row_count">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th><label id="qty_total">{{ $qty_total }}</label></th>
                                        <th class="text-right"></th>
                                        <th class="text-right"><label id="value_total">{{ @App\SysHelper::com_curr_format($value_total,2,'.',',') }}</label></th>
                                        <th class="text-right"><label id="discount_total">{{ @App\SysHelper::com_curr_format($discount_total,2,'.',',') }}</label></th>
                                        <th class="text-right"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($taxableamount_total,2,'.',',') }}</label></th>
                                        <th class="text-right"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($vatamount_total,2,'.',',') }}</label></th>
                                        <th class="text-right"><label id="total_amount">{{ @App\SysHelper::com_curr_format($total_amount,2,'.',',') }}</label></th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>

                            <div style="display: none;">
                                @if(!isset($view))
                                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowDN"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                @endif
                            </div>

                        </div>
                        <div class="row mt-4">
                    <div class="col-lg-12 text-center">
                        <a class="btn btn-info" href="{{url('get-url-sales-invoice-pdf-download/'.$edit->invoice_no)}}"><span class="ti-check"></span>Print Sales Invoice</a>
                        <a class="btn btn-warning" href="{{url('delivery-note/'.$edit->id.'/download')}}"><span class="ti-check"></span>Print Delivery Note</a>
                    </div>
                </div>
                <!-- Bank Info Details -->
                <!-- end row -->
    </div>
            </div>
        </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">

            
    </div>
    </div>
</section>


<div class="modal fade admin-query" id="attachment_popup_win" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0 p-3">
                <h4 class="modal-title">Attachments - {{ $edit->invoice_no }}</h4>
                <button class="close" data-dismiss="modal" type="button">
                    ×
                </button>
            </div>
            <div class="modal-body m-0 p-3">
                <input type="hidden" id="hd_pending_dn_id"/>
                <div class="container-fluid">
                    
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <table id="att-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 30%;">Date</th>
                                    <th style="width: 50%;">Attachment</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                    <br />

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-12 text-right">
                                    <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                        @lang('Close')
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function view_attachment(){
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('view-sales-invoice-attachment2') }}";
    $.ajax({
        url: action,
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            si_no : $('#si_no').val(),
        },
        cache: false,
        success: function(dataResult) {
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            var getSelectedRows="";
                if(dataResult['data'] != null){
                    len = dataResult['data'].length;
                }
                if(len > 0){
                    for(var i=0; i<len; i++){
                        getSelectedRows +="<tr>\
                            <td>"+ Number(i+1) +"</td>\
                            <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                            <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                            </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows); 
                }
                else{
                    $('#att-table tbody').empty();
                }
        }
    });
    $("#loading_bg").css("display", "none");
}
</script>

{{-- popup --}}
<form id="po">
    <div class="modal fade admin-query" id="dn_pending_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Sales Invoice Pending List</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
                    <div class="container-fluid">
                        {{-- <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Select All') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_new_reference" name="bi_new_reference" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Product Code') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_amount_to_adjust" name="bi_amount_to_adjust" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Contains') <span>*</span> </label>
                                    <input class="dynamicstxt primary-input form-control" type="text" id="bi_contains" name="bi_contains" value="" >
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_6 red_alert"></span>                                    
                                </div>
                            </div>
                        </div> --}}

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>@lang('#') </th>
                                                <th>@lang('Part No')</th>
                                                <th>@lang('Qty')</th>
                                                <th>@lang('Unit Price')</th>
                                                <th>@lang('Value')</th>
                                                <th>@lang('Discount')</th>
                                                <th>@lang('Taxable Amount')</th>
                                                <th>@lang('VAT Amount')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-right">
                                        <button class="btn btn-warning" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('Close')
                                        </button>
                                        
                                        <button class="btn btn-success" type="button" id="addDNPendingItems">
                                            Add Selected
                                        </button>
                                        {{-- <input class="primary-btn fix-gr-bg" type="" value="save" onclick="return validateAttachForm()"> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{{-- popup --}}

{{-- popup --}}
    <div class="modal fade admin-query" id="dn_srlno_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title"><div id="div_serialno_title"></div></h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Serial No') <span>*</span> </label>
                                    <textarea class="dynamicstxt primary-input form-control" id="srlno_textarea" name="srlno_textarea"></textarea>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                        </div>

                        <br />

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-right">
                                        <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                            @lang('Close')
                                        </button>
                                        <input type="hidden" id="srl_id" />
                                        <button class="btn btn-success" type="button" onclick="srlno_add_item()">
                                            Add Selected
                                        </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- popup --}}
<a data-modal-size="modal-md" data-target="#dn_srlno_popup_win" id="add_srlno_popup" data-toggle="modal"></a>

<div>
    
    <script>
        function srlno_add(id){
            var hdtxt = $("#description_"+id).val();
            var srl = $("#srl_"+id).val();
            $("#srl_id").val(id);
            $("#srlno_textarea").val(srl);
            $("#div_serialno_title").html(hdtxt);
            document.getElementById('add_srlno_popup').click();
        }
        function srlno_add_item(){
            var id = $("#srl_id").val();
            var srltxt = $("#srlno_textarea").val();
            $("#srl_"+id).val(srltxt);
            document.getElementById('add_srl_cls').click();
        }

        function popup_si_pending(id){
        $("#loading_bg").css("display", "block");
        $("#hd_pending_dn_id").val(id);
        $("#si_id").val(id);
        document.getElementById('addDNPending').click();
        $("#loading_bg").css("display", "none");
    }

    $(document).on("change", "#customer_id", function () {
        var cus_id = $("#customer_id").val();
        get_vat(id);
        get_dn_list(cus_id);
    });

    function get_vat(id) {
        $("#loading_bg").css("display", "block");        
        var action = "{{ URL::to('get-vat-by-ca') }}";
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
                        $("#net_vat").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");     }
                    }
            });
    }
    
    function get_dn_list(cus_id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('sales-invoice-pending') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                cus_id: cus_id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        $("#plist").empty();
                        for(var i=0; i<len; i++){
                            var id = dataResult['data'][i].id;
                            var doc_number = dataResult['data'][i].doc_number;
                            var option = "<option value='" + id + "'>" + doc_number +
                                "</option>";
                            var innerHtml =
                                "<input type='radio' onclick='popup_si_pending(" + id +
                                ")' id='pending_dn_" + i +
                                "' name='pending_dn' value='" + doc_number +
                                "'> <label for='pending_dn_" + i + "'> " + doc_number +
                                "</label><br />";
                            $("#plist").append(innerHtml);
                        }                        
                    }
                    else{
                        $("#plist").empty();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }


    function calc_change(id) {
        var net_vat = $('#net_vat').val();    
        var qty = $('#qty_' + id + '').val();
        var unitprice = $('#unitprice_' + id + '').val();
        var discount = $('#discount_' + id + '').val();
    
    
        qty = (qty === '') ? '0' : qty;
        unitprice = (unitprice === '') ? '0' : unitprice;
        discount = (discount === '') ? '0' : discount;

        var fin_value = (unitprice * qty);
        $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));    
        
        var fin_taxableamount = ((unitprice * qty) - Number(discount));
        $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
        var fin_vatableamount = ((unitprice * qty) - Number(discount)) * (Number(net_vat) / 100);
        $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
        var fin_totalamount = (fin_taxableamount + fin_vatableamount);
        $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));
    
        calc_total();
    }


    function calc_total()
    {
    var countrow = $('#dn_row_count').val();
    
    alert(countrow);
    //var countrow = $('#si-table >tbody >tr').length;
    var t1=0, t2=0, t3=0, t4=0, t5=0, t6=0, t7=0;
    for(var i=0; i <= countrow; i++)
    {
        t1 += Number($('#qty_'+i).val());
        t3 += Number($('#value_'+i).val());
        t4 += Number($('#discount_'+i).val());
        t5 += Number($('#taxableamount_'+i).val());
        t6 += Number($('#vatamount_'+i).val());
        t7 += Number($('#totalamount_'+i).val());
    }
        $('#qty_total').text(t1);
        $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
        $('#total_amount').text((t7).toFixed(@json(session('logged_session_data.decimal_point'))));
    }
        
        $(document).ready(function () {
            $("#btnSubmit2").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit2").prop('disabled', true);
            }
        });
    </script>
@endsection
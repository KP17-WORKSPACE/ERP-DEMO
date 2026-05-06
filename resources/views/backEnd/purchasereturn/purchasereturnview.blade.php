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
                <h2 class="page-heading m-0">Purchase Return View</h2>
                <span class="page-label">Home - Purchase Return</span>
            </div>
            <div>
                <a href="{{ url('purchase-return-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i>New</a>
                <a href="{{ url('purchase-return/'.$edit->id.'/edit') }}" type="button" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="PR Number" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-edit-url-purchase-return') }}";                
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
                <a href="{{url('purchase-return')}}" type="button" class="btn btn-info"><i class="fa fa-list"></i>List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            @if(isset($edit))
            <input type="hidden" value="{{ $edit->id }}" name="purchase-return-id">
            <input type="hidden" name="pi_id" value="{{ $edit->pi_id }}">
            @endif
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <div class="row">
                <div class="col-lg-4 mb-2">
                    <label class="txtlbl">@lang('Vendor') <span>*</span></label>
                    <select class="form-control js-example-basic-single" name="vendors" id="vendors" disabled>
                        <option value=""></option>
                        @foreach ($vendors as $value)
                        <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendors) ? (@$edit->vendors == @$value->id ? 'selected' : '') : '') : '' }}>
                            {{ @$value->account_name }}
                        </option>
                        @endforeach
                    </select>

                    <script>    
                        $(document).on("change", "#vendors", function () {
                            var id = $("#vendors").val();
                            //get_pi_list(id);

                        });
                    </script>
                </div>
                <div class="col-lg-8">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="input-effect">
                                <label class="txtlbl">Purchase Return Number<span>*</span></label>
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
                        <div class="col-lg-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">Purchase Return Date</label>
                                        @php
                                            $value = date('Y-m-d');
                                            if (isset($edit) && !empty($edit->date)) {
                                                @$value = date('Y-m-d', strtotime(@$edit->date));
                                            } else {
                                                if (!empty(old('pi_date'))) {
                                                    @$value = old('pi_date');
                                                } else {
                                                    @$value = date('Y-m-d');
                                                }
                                            }
                                        @endphp
                                        <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                            name="doc_date" value="{{ @$value }}" style="margin-top: 0px">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('doc_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('doc_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">Currency<span>*</span></label>
                                <select
                                    class="form-control"
                                    name="currency" id="currency">
                                    {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit) ? (!empty(@$edit->customer_id) ? (@$edit->currency == @$value->id ? 'selected' : '') : '') : '' }}>
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
                        <div id="plist" style="width: 100%; height: 320px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a data-modal-size="modal-md" data-target="#pi_pending_popup_win" id="addPIPending" data-toggle="modal"></a>
                        
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>

                </div>
                
                        
                <div class="col-lg-8 mb-2">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="input-effect">
                                <label class="txtlbl">PIV Number<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="pi_number" autocomplete="off" id="pi_number" value="{{ $edit->pi_number }}" readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('doc_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pi_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">PIV Date</label>
                                        @php
                                            $value = date('Y-m-d');
                                            if (isset($edit) && !empty($edit->date)) {
                                                @$value = date('Y-m-d', strtotime(@$edit->date));
                                            } else {
                                                if (!empty(old('pi_date'))) {
                                                    @$value = old('pi_date');
                                                } else {
                                                    @$value = date('Y-m-d');
                                                }
                                            }
                                        @endphp
                                        <input class="form-control" id="pi_date" type="date" autocomplete="off"
                                            name="pi_date" value="{{ @$value }}" style="margin-top: 0px" readonly>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('pi_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('pi_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-0">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Number') <span>*</span></label>
                                <input
                                    class="txtbx primary-input form-control {{ $errors->has('lpo_number') ? ' is-invalid' : '' }}"
                                    type="text" name="lpo_number" autocomplete="off" id="lpo_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->lpo_number) ? @$edit->lpo_number : old('lpo_number')) : '' }}">
                                <span class="focus-border"></span>
                                @if ($errors->has('lpo_number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('lpo_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('LPO Date') <span>*</span></label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($edit) && !empty($edit->date)) {
                                        @$value = date('Y-m-d', strtotime(@$edit->date));
                                    } else {
                                        if (!empty(old('lpo_date'))) {
                                            @$value = old('lpo_date');
                                        } else {
                                            @$value = date('Y-m-d');
                                        }
                                    }
                                @endphp
                                <input class="form-control" id="lpo_date" type="date" autocomplete="off" name="lpo_date" value="{{ @$value }}" style="margin-top:0px;">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Payment Terms')<span>*</span></label>
                                <select
                                    class="form-control"
                                    name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                    <option value=""></option>
                                    @foreach ($paymentterms as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit) ? (!empty(@$edit->payment_terms) ? (@$edit->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="div_payment_terms" style="display: none; padding-top: px;">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Other Payment Terms')<span>*</span></label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('payment_terms2') ? ' is-invalid' : '' }}"
                                        type="text" name="payment_terms2" autocomplete="off" id="payment_terms2"
                                        value="{{ isset($edit) ? (!empty(@$edit->payment_terms2) ? @$edit->payment_terms2 : old('payment_terms2')) : '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Number')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="bill_number" autocomplete="off" id="bill_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->bill_number) ? @$edit->bill_number : old('bill_number')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Bill Date')*</label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($edit) && !empty($edit->bill_date)) {
                                        @$value = date('Y-m-d', strtotime(@$edit->bill_date));
                                    }
                                @endphp
                                <input class="form-control" id="bill_date" type="date" autocomplete="off"
                                    name="bill_date" value="{{ @$value }}" required >
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('AWB No') <span>*</span></label>
                                <input class="txtbx primary-input form-control {{ $errors->has('awbno') ? ' is-invalid' : '' }}"
                                    type="text" name="awbno" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->awbno) ? @$edit->awbno : old('awbno')) : old('awbno') }}"
                                    id="awbno">
                            </div>        
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Warehouse') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="warehouse" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->warehouse) ? @$edit->warehouse : old('warehouse')) : old('warehouse') }}"
                                    id="warehouse">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Reference') <span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="reference" autocomplete="off"
                                    value="{{ isset($edit) ? (!empty(@$edit->reference) ? @$edit->reference : old('reference')) : old('reference') }}"
                                    id="reference">
                                <span class="focus-border"></span>
                                @if ($errors->has('reference'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('reference') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="createdby"
                                    value="{{ Auth::user()->full_name }}"
                                    readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('createdby'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('createdby') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Salesman Name')<span>*</span></label>
                                <select class="form-control js-example-basic-single" required name="sales_person" id="sales_person">
                                    <option value=""></option>
                                    @foreach ($salesman as $value)
                                        <option value="{{ @$value->user_id }}" @if($edit->sales_person==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input
                                    class="form-control"
                                    type="text" name="salesman_name" autocomplete="off" id="salesman_name"
                                    value="{{ isset($edit) ? (!empty(@$edit->salesman_name) ? @$edit->salesman_name : old('salesman_name')) : '' }}"> --}}
                            </div>
                        </div>
                        <div class="col-lg-8 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Narration')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off" id="narration"
                                    value="{{ isset($edit) ? (!empty(@$edit->narration) ? @$edit->narration : old('narration')) : '' }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Deal Id')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : '' }}">
                            </div>
                        </div>

                    </div>
                    
                </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="equipment comon-status row mt-4 d-block">
                    <table class="table table-bordered table-striped" id="pi-ret-table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>@lang('Part No')</th>
                                <th>@lang('TAX')</th>
                                <th>@lang('Qty')</th>
                                <th>@lang('Unit Price')</th>
                                <th>@lang('Value')</th>
                                <th>@lang('Discount')</th>
                                <th>@lang('Taxable Amount')</th>
                                <th>@lang('VAT Amount')</th>
                                <th>@lang('Total Amount')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            @if (count($editList))
                            @foreach ($editList as $list)
                            <tr id="pr_row_{{ $i }}">
                                <td><input type="text" class="form-control" name="part_number[]" id="part_number_{{ $i }}" value="{{ $list->partnumber->part_number }}" /></td>
                                <td><input type="number" class="form-control text-right" name="vat[]" id="vat_{{ $i }}" value="{{ $list->vat }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control" name="qty[]" id="qty_{{ $i }}" value="{{ $list->qty }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control text-right" name="unitprice[]" id="unitprice_{{ $i }}" value="{{ $list->unitprice }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control text-right" name="value[]" id="value_{{ $i }}" value="{{ $list->value }}" /></td>
                                <td><input type="number" class="form-control text-right" name="discount[]" id="discount_{{ $i }}" value="{{ $list->discount }}" onchange="calc_change({{ $i }})" /></td>
                                <td><input type="number" class="form-control text-right" name="taxableamount[]" id="taxableamount_{{ $i }}" value="{{ $list->taxableamount }}" /></td>
                                <td><input type="number" class="form-control text-right" name="vatamount[]" id="vatamount_{{ $i }}" value="{{ $list->vatamount }}" /></td>
                                <td><input type="number" class="form-control text-right" name="totalamount[]" id="totalamount_{{ $i }}" value="{{ $list->taxableamount + $list->vatamount }}" /></td>
                                <td>
                                    <input type="hidden" name="pr_id[]" value="{{ $list->pr_id }}" />
                                    <input type="hidden" name="item_id[]" value="{{ $list->id }}" />
                                    <input type="hidden" name="partno[]" value="{{ $list->partno }}" />
        
                                    <input type="hidden" name="isdelete[]" id="isdelete_{{ $i }}" value="0" /></td>
                            </tr>
                            <?php $i++;?>
                            @endforeach                                
                            @endif
                            <input type="hidden" value="{{ $i-- }}" id="pr_row_count" />
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="text-left"><label id="qty_total">{{ $editList->sum('qty') }}</label></td>
                                <td class="text-right"></td>
                                <td class="text-right"><label id="value_total">{{ @App\SysHelper::com_curr_format($editList->sum('value'),2,'.',',') }}</label></td>
                                <td class="text-right"><label id="discount_total">{{ @App\SysHelper::com_curr_format($editList->sum('discount'),2,'.',',') }}</label></td>
                                <td class="text-right"><label id="taxableamount_total">{{ @App\SysHelper::com_curr_format($editList->sum('taxableamount'),2,'.',',') }}</label></td>
                                <td class="text-right"><label id="vatamount_total">{{ @App\SysHelper::com_curr_format($editList->sum('vatamount'),2,'.',',') }}</label></td>
                                <td class="text-right"><label id="totalamount_total">{{ @App\SysHelper::com_curr_format($editList->sum('taxableamount') + $editList->sum('vatamount'),2,'.',',') }}</label></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    
            <script>
                function row_delete(id){
                    $('#pr_row_' + id + '').css('display', 'none');
                    $('#isdelete_' + id + '').val(1);
                }
                function calc_change(id) {
            
                    var vat = $('#vat_' + id + '').val();
                    var qty = $('#qty_' + id + '').val();
                    var unitprice = $('#unitprice_' + id + '').val();
                    var discount = $('#discount_' + id + '').val();
        
                    vat = (vat === '') ? '0' : vat;
                    qty = (qty === '') ? '0' : qty;
                    unitprice = (unitprice === '') ? '0' : unitprice;
                    discount = (discount === '') ? '0' : discount;
        
                    var fin_value = (unitprice * qty);
                    $('#value_' + id + '').val(fin_value.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    var fin_taxableamount = ((unitprice * qty) - Number(discount));
                    $('#taxableamount_' + id + '').val(fin_taxableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    var fin_vatableamount = ((unitprice * qty) - Number(discount)) * ((Number(vat)) / 100);
                    $('#vatamount_' + id + '').val(fin_vatableamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    var fin_totalamount = Number(fin_taxableamount) + Number(fin_vatableamount)
        
                    $('#totalamount_' + id + '').val(fin_totalamount.toFixed(@json(session('logged_session_data.decimal_point'))));
        
                    calc_total();
        
                }
                function calc_total() {
                    var rowCount = $('#pr_row_count').val();
                    var t1 = 0;
                    var t2 = 0;
                    var t3 = 0;
                    var t4 = 0;
                    var t5 = 0;
                    var t6 = 0;
                    for (var i = 1; i < rowCount; i++) {
                        try {
                            t1 += Number($('#qty_' + i).val());
                            t2 += Number($('#value_' + i).val());
                            t3 += Number($('#discount_' + i).val());
                            t4 += Number($('#taxableamount_' + i).val());
                            t5 += Number($('#vatamount_' + i).val());
                            t6 += Number($('#totalamount_' + i).val());
                        }
                        catch(err) {
                            
                        }
                    }
                    $('#qty_total').text(t1);                    
                    $('#value_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#discount_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#taxableamount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#vatamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
                    $('#totalamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
                }
            </script>

                </div>
                
            </div>
        </div>






            
    </div>
    </div>

        </div>
    </div>


    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="pi_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Purchase Invoice Item List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_pi_id" />
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
                                        <table id="table_id" class="display school-table" cellspacing="0"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('#') </th>
                                                    <th>@lang('Part No')</th>
                                                    <th>@lang('PI Qty')</th>
                                                    <th>@lang('TAX')</th>
                                                    <th>@lang('Qty')</th>
                                                    <th>@lang('Unit Price')</th>
                                                    <th>@lang('Value')</th>
                                                    <th>@lang('Discount')</th>
                                                    <th>@lang('Taxable Amount')</th>
                                                    <th>@lang('VAT Amount')</th>
                                                    <th>@lang('Total Amount')</th>
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
                                        <button class="btn btn-primary bg-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('Close')
                                        </button>

                                        <button class="btn btn-primary bg-success" type="button" id="addPIPendingItems">
                                            Add Selected
                                        </button>
                                        {{-- <input class="btn btn-primary fix-gr-bg" type="" value="save" onclick="return validateAttachForm()"> --}}
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
    <div class="modal fade admin-query" id="adj_popup_win">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Bill Wise Adjestments</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return-add-adjestment', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'purchase-return-add-adjestment']) }}
                    <input type="hidden" value="{{ $edit->doc_number }}" name="adj_pri_no">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('PIV No')</th>
                                                <th style="width:100px;">@lang('LPO NO')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                <th style="width:100px;">@lang('Balance')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($pri_adjestment)>0)
                                            @foreach ($pri_adjestment as $dt)
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ $dt->doc_date }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_pi_no" value="{{ $dt->piv_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_lpo_no" value="{{ $dt->lpo_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total" id="adj_total" value="{{ $dt->total_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_paid" id="adj_paid" value="{{ $dt->paid_amount }}" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance" id="adj_balance" value="{{ $dt->balance_amount }}" readonly /></td>
                                            </tr>                                                
                                            @endforeach
                                            @else
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ $edit->doc_date }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_pi_no" value="{{ $edit->pi_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_lpo_no" value="{{ $edit->lpo_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total" id="adj_total" value="{{ $invoice_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_paid" id="adj_paid" value="" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance" id="adj_balance" value="" readonly /></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><label id="footer_total" /></th>
                                                <th><label id="footer_paid" /></th>
                                                <th><label id="footer_balance" /></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            function get_set_amount(id)
                            {
                                var adj_total = Number($('#adj_total').val());
                                var adj_paid = Number($('#adj_paid').val());
                                $('#adj_balance').val(adj_total - adj_paid);
                            }
                        </script>


                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-right">
                                    <div class="">
                                        @if (count($pri_adjestment)>0)
                                        @else
                                        <button class="btn btn-success fix-gr-bg" type="submit">Add</button>
                                        @endif
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    {{-- popup --}}

    
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <script>
        function get_pi_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-pi-list') }}";
            $.ajax({
                url: action,
                type: "get",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
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
                                        "<input type='radio' onclick='popup_pi_pending(" + id + ")' id='pending_pi_" + i + "' name='pending_pi' value='" + doc_number + "'><label for='pending_pi_" + i + "'> " + doc_number +"</label><br />";

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

        function popup_pi_pending(id) {
            $("#loading_bg").css("display", "block");
            $("#hd_pending_pi_id").val(id);
            $("#pi_id").val(id);            
            document.getElementById('addPIPending').click();
            $("#loading_bg").css("display", "none");
        }


    </script>

    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
        
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
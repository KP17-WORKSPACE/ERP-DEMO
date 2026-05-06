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
                <h2 class="page-heading m-0">Proforma Invoice</h2>
                <span class="page-label">Home - Proforma Invoice</span>
            </div>
            <div>
                <a href="{{ url('proforma-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i>
                    New</a>
                <a href="{{ url('proforma-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i>
                    List</a>
            </div>
        </div>
        <div class="card p-4 mb-2">

            @if (isset($edit))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'proforma-invoice-update/' . $editData->id, 'method' => 'PUT', 'id' => 'quotations-form']) }}
            @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'proforma-invoice', 'method' => 'POST', 'id' => 'quotations-form']) }}
            @endif

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
            
            <input type="hidden" name="net_vat" id="net_vat">

            <div class="row">
                <div class="col-lg-4 mb-2">
                    <div class="input-effect">
                        <label class="dynamicslbl">Customer Name<span>*</span></label>
                        <select class="form-control js-example-basic-single" name="customer" id="customer" required>
                            <option value=""></option>
                            @foreach ($customer as $value)
                                <option value="{{ @$value->id }}"
                                    {{ isset($editData) ? (!empty($editData->customer) ? (@$editData->customer == @$value->id ? 'selected' : '') : '') : '' }}>
                                    {{ @$value->account_name }}
                                </option>
                            @endforeach
                        </select>
                        <a id="get_pending_list" class="bg-danger pl-2 pr-2"
                            style="float: right; cursor: pointer; display: none;">Get Pending</a>
                    </div>
                </div>                
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">@lang('Proforma') @lang('Number')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                    value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_proforma_invoice','PF','doc_number') }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Proforma Date</label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($edit) && !empty($edit->doc_date) ){ @$value =
                                date('Y-m-d', strtotime(@$edit->doc_date)); }
                                else{ if(!empty(old('doc_date'))){ @$value = old('doc_date');
                                }else{
                                @$value = date('Y-m-d'); } }
                                @endphp
                                <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                    name="doc_date" value="{{ @$value }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">@lang('Currency')</label>
                                <select class="form-control" name="currency" id="currency" required>
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($editData) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
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
                        <div id="plist"
                            style="width: 100%; height: 154px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                        </div>
                        <a data-modal-size="modal-md" data-target="#qt_pending_popup_win" id="addQtPending"
                            data-toggle="modal"></a>
                        <input type="hidden" id="qt_id" name="qt_id">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">LPO Number<span>*</span></label>
                                <input class="form-control" type="text" name="lpo_number" autocomplete="off" id="lpo_number" required
                                    value="{{ isset($editData) ? (!empty(@$editData->lpo_number) ? @$editData->lpo_number : old('lpo_number')) : old('lpo_number') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">LPO Date</label>
                                @php
                                    $value = date('Y-m-d');
                                    if (isset($editData) && !empty($editData->lpo_date)) {
                                        @$value = date('Y-m-d', strtotime(@$editData->lpo_date));
                                    } else {
                                        if (!empty(old('lpo_date'))) {
                                            @$value = old('lpo_date');
                                        } else {
                                            @$value = date('Y-m-d');
                                        }
                                    }
                                @endphp
                                <input class="form-control" id="lpo_date" type="date" name="lpo_date"
                                    value="{{ @$value }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Payment Terms<span>*</span></label>
                                <select class="form-control" name="payment_terms" id="payment_terms" required>
                                    <option value=""></option>
                                    @foreach ($paymentterms as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($editData) ? (!empty(@$editData->payment_terms) ? (@$editData->payment_terms == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Delivery Terms<span></span></label>
                                <input class="form-control" type="text" name="delivery_terms" autocomplete="off"
                                    id="delivery_terms"
                                    value="{{ isset($editData) ? (!empty(@$editData->delivery_terms) ? @$editData->delivery_terms : old('delivery_terms')) : old('delivery_terms') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Narration<span></span></label>
                                <input class="form-control" type="text" name="narration" autocomplete="off" id="narration"
                                    value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('number')) : '' }}">
                                @if ($errors->has('number'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Sales Man<span>*</span></label>
                                <select class="form-control" name="sales_man" id="sales_man" required>
                                    <option value=""></option>
                                    @foreach ($sales_man as $value)
                                        <option value="{{ @$value->user_id }}"
                                            {{ isset($editData) ? (!empty(@$editData->sales_man) ? (@$editData->sales_man == @$value->user_id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <div class="input-effect">
                                <label class="dynamicslbl">Deal ID<span>*</span></label>
                                <input class="form-control" type="number" name="deal_id" autocomplete="off" id="deal_id"
                                    value="{{ isset($editData) ? (!empty(@$editData->deal_id) ? @$editData->deal_id : old('deal_id')) : '' }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-2">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="true">Shipping Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="vat-tab" data-toggle="tab" href="#vat" role="tab" aria-controls="vat" aria-selected="false">VAT Details</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="enduser-tab" data-toggle="tab" href="#enduser" role="tab" aria-controls="enduser" aria-selected="false">End User Details</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Name') <span></span></label>
                                        <input type="text" class="form-control" value="" id="shipping_name" name="shipping_name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="dynamicslbl">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" value="" id="shipping_address" name="shipping_address">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="vat" role="tabpanel" aria-labelledby="vat-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer Type')</label>
                                            <select class="form-control" name="customer_type" id="customer_type" required>
                                                <option value="0" ></option>
                                                @foreach($customertype as $value)
                                                        <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->customer_type)? @$edit->customer_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Sale Type')</label>
                                            <select class="form-control" name="sale_type" id="sale_type">
                                                <option value="0" ></option>
                                                @foreach($saletype as $value)
                                                        <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->sale_type)? @$edit->sale_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer Country') <span></span></label>
                                            <select class="form-control" name="customer_country" id="country">
                                                <option data-display="" value="0"></option>
                                                @foreach ($countries as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        <?php try{?>                                                        
                                                        @if (isset($edit)) @if (@$edit->customer_country == $value->id) selected @endif
                                                        @endif
                                                        <?php } catch (\Throwable $th) {} ?>
                                                        >{{ @$value->name }} </option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer State') <span></span></label>
    
                                            <div id="sectionStateDiv">
                                                <select class="form-control" name="customer_state" id="state">
                                                    <option data-display="" value="0"></option>
                                                    <?php try{?>
                                                        @foreach ($states as $key => $value)
                                                    @if (isset($edit))
                                                        <option data-display="{{ $edit->vatstate->name }}"
                                                            value="{{ $edit->customer_state }}" selected>
                                                            {{ $edit->vatstate->name }}</option>
                                                        @else
                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                    @endif
                                                    @endforeach
                                                    <?php } catch (\Throwable $th) {} ?>
            
                                                </select>
                                            </div>
    
                                        </div>
                                    </div>
                                </div>
                        </div>
                        
                        <div class="tab-pane" id="enduser" role="tabpanel" aria-labelledby="enduser-tab">
                            <div class="row mt-2">
                                <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('End User Name') <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->end_user_name) ? @$edit->end_user_name : '') : old('end_user_name') }}" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_name) ? @$edit->contact_person_name : '') : old('contact_person_name') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_email) ? @$edit->contact_person_email : '') : old('contact_person_email') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person No') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_no) ? @$edit->contact_person_no : '') : old('contact_person_no') }}">
                                            
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>




                
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="equipment comon-status row mt-4 d-block mr-2 ml-2">
                        <table class="table table-bordered table-striped" id="pfo-table" width="100%" cellspacing="0">
                            <input type="hidden" id="row-count" />
                            <thead>
                                <tr>
                                    <th style="width:100px;">@lang('Part No')</th>
                                    {{-- <th style="width:200px;">@lang('Sl. No')</th> --}}
                                    <th style="width:150px;">@lang('Description')</th>
                                    <th style="width:70px;">@lang('Qty')</th>
                                    <th style="width:80px;">@lang('Unit Price')</th>
                                    <th style="width:70px;">@lang('Value')</th>
                                    <th style="width:70px;">@lang('Discount')</th>
                                    <th style="width:120px;">@lang('Taxable Amount')</th>
                                    <th style="width:100px;">@lang('VAT Amount')</th>
                                    <th style="width:100px;">@lang('Total Amount')</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                {{-- <tr>
                                    <td></td>
                                    <td class="sstablefoot"><label id="qty_total">0</label></td>
                                    <td class="sstablefoot"><label id="unitprice_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="value_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="discount_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="taxableamount_total">0.00</label></td>
                                    <td class="sstablefoot"><label id="vatamount_total">0.00</label></td>
                                </tr> --}}
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
                            <button type="submit" class="btn btn-primary">
                                <span class="ti-check"></span>
                                @if (isset($edit))
                                    @lang('lang.update')
                                @else
                                    @lang('lang.save')
                                @endif
                                @lang('Proforma Invoice')

                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>







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
                    <div class="add-visitor">




                    </div>


                    <div class="row">
                        <div class="col-lg-12">
                            <hr />
                        </div>
                    </div>

                </div>
                {{ Form::close() }}
            </div>
        </div>
        </div>
    </section>


    {{-- popup --}}
    <form id="po">
        <div class="modal fade admin-query" id="qt_pending_popup_win" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
                <div class="modal-content">
                    <div class="modal-header m-0 p-3">
                        <h4 class="modal-title">Quotation Pending List</h4>
                        <button class="close" data-dismiss="modal" type="button">
                            ×
                        </button>
                    </div>
                    <div class="modal-body m-0 p-3">
                        <input type="hidden" id="hd_pending_qt_id" />
                        <div class="container-fluid">
                            {{--  <div class="row">
                                <div class="col-lg-4 mb-20">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('Select All') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input form-control" type="text"
                                            id="bi_new_reference" name="bi_new_reference" value="">
                                        <span class="focus-border"></span>
                                        <span class="modal_input_validation_1 red_alert"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-20">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('Product Code') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input form-control" type="text"
                                            id="bi_amount_to_adjust" name="bi_amount_to_adjust" value="">
                                        <span class="focus-border"></span>
                                        <span class="modal_input_validation_2 red_alert"></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-20">
                                    <div class="input-effect">
                                        <label class="dynamicslbl"> @lang('Contains') <span>*</span> </label>
                                        <input class="dynamicstxt primary-input form-control" type="text"
                                            id="bi_contains" name="bi_contains" value="">
                                        <span class="focus-border"></span>
                                        <span class="modal_input_validation_6 red_alert"></span>
                                    </div>
                                </div>
                            </div>  --}}

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="equipment comon-status row mt-40 d-block">
                                        <table id="table_id" class="display school-table" cellspacing="0"
                                            width="100%">
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
                                        <button class="btn btn-warning" data-dismiss="modal" type="button"
                                            id="btn_close2">
                                            @lang('Close')
                                        </button>

                                        <button class="btn btn-success" type="button" id="addQtPendingItems">
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

    <script>
        function popup_qt_pending(id) {
            $("#hd_pending_qt_id").val(id);
            $("#qt_id").val(id);
            document.getElementById('addQtPending').click();
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

        function calc_total(){
            var numItems = $('.rno').length;

            //alert(numItems);

            var countrow = document.getElementById('row-count').value;
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
                t5 += Number($('#taxamount_' + i).val());
                t6 += Number($('#vatamount_' + i).val());
            }
            $('#qty_total').text(t1);
            $('#unitprice_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#value_total').text(t3.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#discount_total').text(t4.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#taxableamount_total').text(t5.toFixed(@json(session('logged_session_data.decimal_point'))));
            $('#vatamount_total').text(t6.toFixed(@json(session('logged_session_data.decimal_point'))));
        }
    </script>

    <script>
        //popup_qt_pending(8);

        $(window).ready(function() {
            $("#quotations-form").on("keypress", function(event) {
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

        
        $(document).on("change", "#customer", function () {
            $("#loading_bg").css("display", "block");
            var id = $("#customer").val();
            get_guote_list(id);
            get_vat(id);
            $("#loading_bg").css("display", "none");
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

        function get_guote_list(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('quotation-pending') }}";
            $.ajax({
                url: action,
                type: "POST",
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
                                if(dataResult['data']!="ERROR"){
                                    var id = dataResult['data'][i].id;
                                    var deal_name = dataResult['data'][i].deal_name;
                                    var option = "<option value='" + id + "'>" + id +'- ' + deal_name +
                                        "</option>";
                                    var innerHtml =
                                        "<input type='radio' onclick='popup_qt_pending(" + id +
                                        ")' id='pending_qt_" + i +
                                        "' name='pending_qt' value='" + deal_name +
                                        "'> <label for='pending_qt_" + i + "'> " + id +'- ' + deal_name +
                                        "</label><br />";

                                    $("#plist").append(innerHtml);
                                }
                                else{
                                    $("#plist").empty();
                                }                     
                            }
                        }
                        else{
                            $("#plist").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        

        $(document).ready(function() {

            $("#get_pending_list").click(function() {
                var val = $("#customer_with_vat").val();
                var url = $('#url').val();
                alert(val);
                $.ajax({
                    type: "POST",
                    data: {
                        id: val
                    },
                    url: url + '/' + 'quotation-pending-exe',
                    cache: false,
                    success: function(response) {
                        var response = JSON.parse(response);
                        var len = 0;
                        if (response['data'] == "ERROR") {
                            alert("Error found in something!!");
                        } else {
                            if (response['data'] != null) {
                                len = response['data'].length;
                            }
                            if (len > 0) {
                                for (var i = 0; i < len; i++) {
                                    var id = response['data'][i].id;
                                    var doc_number = response['data'][i].doc_number;
                                    var option = "<option value='" + id + "'>" + doc_number +
                                        "</option>";
                                    var innerHtml =
                                        "<input type='radio' onclick='popup_qt_pending(" + id +
                                        ")' id='pending_qt_" + i +
                                        "' name='pending_qt' value='" + doc_number +
                                        "'> <label for='pending_qt_" + i + "'> " + doc_number +
                                        "</label><br />";

                                    $("#plist").append(innerHtml);

                                }
                                //$('#btn_close2').click();
                            } else {
                                $("#plist").empty();
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {}
                });
            });

        });
    </script>
@endsection


<script>
    $(document).on("click", "#addGRNPending", function(event) {
    var url = $('#url').val();
    var grn_id = $('#hd_pending_grn_id').val();
    var po_id = $('#hd_pending_po_id').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { grn_id: grn_id, po_id: po_id },
        dataType: 'json',
        url: url + '/' + 'goods-receipt-note-for-pi-item-list',
        success: function(data) {
            console.log(data);
            var a = '';            
            var tr="";
            var pro_qty = "0";
            
            var qty_total = 0;
            var unitprice_total = 0;
            var value_total = 0;
            var discount_total = 0;
            var taxableamount_total = 0;
            var vatamount_total = 0;
            var totalamount_total = 0;

            $.each(data, function(i, item) {
                
                if (item.length) {
                    $.each(item, function(i, pin) {

                        if (pin.pro_qty != null){
                            pro_qty=pin.pro_qty;
                        }
                        
                        tr +=  "<tr>\
                        <td><input class='form-control text-center' type='number' id='sort_id_" + i + "' name='sort_id[]' value='"+(i+1)+"' ></td>\
                        <td><select class='form-control' id='part_number_" + i + "' name='part_number[]'><option value='"+pin.part_id+"'>"+ pin.part_number +"</option></select></td>\
                        <td><input type='hidden' id='part_id_" + i + "' name='part_number_txt[]' value='"+pin.part_number+"'/>\
                        <input type='hidden' id='part_id_" + i + "' name='hscode_txt[]' value='"+pin.part_id+"'/>\
                        <input type='hidden' id='part_id_" + i + "' name='product_type[]' value='"+pin.part_id+"'/>\
                        <input type='hidden' id='part_id_" + i + "' name='product_type_part_number_text[]' value='"+pin.part_id+"'/>\
                        <input type='hidden' id='grn_qty_" + i + "' name='grn_qty[]' value='"+pin.grn_qty+"'/>\
                        <input class='form-control' type='text' name='description[]' autocomplete='off' value='"+pin.description+"' >\
                        <td><input class='form-control text-center' type='number' id='tax_" + i + "' name='tax[]' value='"+pin.tax+"' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
                        <td><input class='form-control text-center' type='number' id='qty_" + i + "' name='qty[]' value='"+pin.grn_qty+"' autocomplete='off' min='0'onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='unitprice_" + i + "' name='unitprice[]' value='"+pin.unitprice+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='value_" + i + "' name='value[]' value='"+pin.value+"' autocomplete='off' min='0'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='discount_" + i + "' name='discount[]' value='"+pin.discount+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='fright_" + i + "' name='fright[]' value='"+pin.fright+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='customcharges_" + i + "' name='customcharges[]' value='"+pin.customcharges+"' autocomplete='off' min='0' onchange='calc_change_new(this)'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='taxamount_" + i + "' name='taxableamount[]' value='"+pin.taxableamount+"'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='vatamount_" + i + "' name='vatamount[]' value='"+pin.vatamount+"'></td>\
                        <td class='jshide'><input class='form-control text-end' step='any' type='number' id='totalamount_" + i + "' name='totalamount[]' value='"+(Number(pin.taxableamount) + Number(pin.vatamount))+"'></td>\
                        </tr>";
                        $('#row-count').val(i+1);
                        $('#lpo_number').val(pin.lpo_number);
                        $('#po_id').val(pin.po_id);
                        // $('#lpo_date').val(pin.lpo_date);
                        $('#lpo_date').val(pin.lpo_date ? pin.lpo_date.split('-').reverse().join('/') : '');

                        $('#payment_terms').val(pin.payment_terms);
                        $('#currency').val(pin.currency);
                        $('#bill_number').val(pin.bill_number);
                        // $('#bill_date').val(pin.bill_date);
                        $('#bill_date').val(pin.bill_date ? pin.bill_date.split('-').reverse().join('/') : '');

                        $('#awbno').val(pin.awbno);
                        $('#boeno').val(pin.boeno);
                        $('#warehouse').val(pin.warehouse);
                        
                        $('#grn_no').val(pin.doc_number);
                        // $('#grn_date').val(pin.grn_date);
                        $('#grn_date').val(pin.grn_date ? pin.grn_date.split('-').reverse().join('/') : '');
                        
                        $('#sales_person').val(pin.sales_person).trigger('change');
                        
                        $('#reference').val(pin.reference);
                        $('#narration').val(pin.narration);
                        
                        $('#deal_id').val(pin.deal_id);

                        $('#shipping_name').val(pin.shipping_name);
                        $('#shipping_address_1').val(pin.shipping_address_1);
                        $('#shipping_address_2').val(pin.shipping_address_2);
                        $('#shipping_contact_no').val(pin.shipping_contact_no);
                        $('#supplier_type').val(pin.supplier_type);
                        $('#purchase_type').val(pin.purchase_type);
                        $('#country').val(pin.supplier_country).trigger('change');
                        $('#state').val(pin.supplier_state);

                        qty_total += Number(pin.grn_qty);
                        unitprice_total += Number(pin.unitprice);
                        value_total += Number(pin.value);
                        discount_total += Number(pin.discount);
                        taxableamount_total += Number(pin.taxableamount);
                        vatamount_total += Number(pin.vatamount);
                        totalamount_total += (Number(pin.taxableamount) + Number(pin.vatamount));

                        $('#qty_total').html(qty_total);
                        $('#unitprice_total').html(unitprice_total);
                        $('#value_total').html(value_total);
                        $('#discount_total').html(discount_total);
                        $('#taxableamount_total').html(taxableamount_total);
                        $('#vatamount_total').html(vatamount_total);
                        $('#totalamount_total').html(totalamount_total);
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
                get_deal_code();
            });
            console.log(a);
            $('#myTable tbody').empty();
            $("#myTable tbody").append(tr);
            $(".jshide").show();
            $(".jshide1").hide();
            update_totals();
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });

});
</script>
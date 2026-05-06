    <?php try { ?>

            @if(isset($select_cart))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' =>  'delivery-note-create-form']) }}
            <input type="hidden" name="store_id" value="cart" />
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'delivery-note-create-form']) }}
            <input type="hidden" name="store_id" value="sales" />
            @endif
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
            <input type="hidden" id="net_vat" name="net_vat">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New ({{isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_delivery_note','DN', 'doc_number')}})
        </h4>
        <div class="purchase-order-content-header-right">
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>
             <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item"><i class="ico icon-outline-document-medicine text-success"></i> Save & Download</button></li>
                </ul>
            </div>
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                        <div class="col-4">
                                            <label class="form-label">Customer</label>
                                            <div class="form-group">
                                                <select class="form-control js-account-select" name="customer_id" id="customer_id" required onchange="get_pending_si_list()">
                                                @if(isset($deal_acc))
                                                <option value="{{ $deal_acc->id }}">
 @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                    {{ $deal_acc->account_name }} ({{ $deal_acc->account_code }})
                                @else
                                    {{ $deal_acc->account_name }}
                                @endif

                                               
                                                </option>
                                                @endif
                                                <option value=""></option>
                                                {{-- @foreach ($vendors as $value)
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->account_name }}
                                                </option>
                                                @endforeach --}}
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">DLN Number</label>
                                            <div class="form-group">
                                <input
                                                    class="form-control"
                                                    type="text" name="doc_number" autocomplete="off" id="doc_number"
                                                    value="{{ isset($edit) ? (!empty(@$edit->title) ? @$edit->title : old('doc_number')) : @App\SysHelper::get_new_code('sys_delivery_note','DN', 'doc_number') }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">DLN Date</label>
                                            <div class="form-group">
                                            @php
                                                            $value =date('d/m/Y');
                                                        @endphp
                                                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                                                            name="doc_date" value="{{ @$value }}" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group">
                                                <select class="form-control" name="currency" id="currency">
                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if($company->currency_id == $value->id) selected @endif>
                                                            {{ @$value->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By</label>
                                            <div class="form-group">
                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="created_by" value="{{  Auth::user()->full_name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-tab" data-bs-toggle="tab" data-bs-target="#shipping-details" type="button" role="tab" aria-controls="shipping-details" aria-selected="true">Shipping Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details" type="button" role="tab" aria-controls="vat-details" aria-selected="true">VAT Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="end-user-details-tab" data-bs-toggle="tab" data-bs-target="#end-user-details" type="button" role="tab" aria-controls="end-user-details" aria-selected="true">End User Details</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">


                <div class="col-2 mb-2">
                    <div class="input-effect">
                        <label class="txtlbl">Pending list</label>
                        <div id="plist" style="width: 100%; height: 130px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;"></div>
                        <a id="addDNPending"></a>
                        <input type="hidden" id="si_id" name="si_id" value="0" >
                        <input type="hidden" id="hd_pending_dn_id" name="hd_pending_dn_id" value="0" >
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>
                </div>    
                <div class="col-lg-10 mb-2">
                                    <div class="row">
                                         <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">  @lang('SIV No') <span>*</span> </label>
                                              @php
                                                $invoice_no = '';
                                                $invoice_date = date('d/m/Y'); // default today in d/m/Y

                                                if (isset($siv_det)) {
                                                    $invoice_no = $siv_det->doc_number;
                                                    $invoice_date = date('d/m/Y', strtotime($siv_det->doc_date));
                                                }

                                                if (isset($si_no) && !empty($si_no)) {
                                                    $invoice_no = $si_no;
                                                }

                                                if (isset($si_date) && !empty($si_date)) {
                                                    $invoice_date = date('d/m/Y', strtotime($si_date));
                                                }
                                            @endphp

                                                <input class="form-control" type="text" id="invoice_no" name="invoice_no"
                                                value="{{ $invoice_no }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('SIV Date')</label>
                                                <input class="form-control date-picker" id="invoice_date" type="text" name="invoice_date" value="{{ @$invoice_date }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">  @lang('LPO No') <span>*</span> </label>
                                                @php
    $lpoValue = old('lpo_no');

    if (!empty($lpo_no)) {
        $lpoValue = $lpo_no;
    } elseif (!empty($select_cart[0]->reference_no ?? null)) {
        $lpoValue = $select_cart[0]->reference_no;
    }
@endphp
                                                <input class="form-control" type="text" id="lpo_no" name="lpo_no"
                                                value="{{ $lpoValue }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('LPO Date')</label>
                                               @php
                                                    $value = date('d/m/Y'); // default today in dmy

                                                    if (isset($select_cart) && !empty($select_cart[0]->reference_date)) {
                                                        $value = date('d/m/Y', strtotime($select_cart[0]->reference_date));
                                                    }

                                                    if (isset($lpo_date) && !empty($lpo_date)) {
                                                        $value = date('d/m/Y', strtotime($lpo_date));
                                                    }
                                                @endphp

                                                <input class="form-control date-picker" id="lpo_date" type="text" name="lpo_date" value="{{ $value }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('Payment Terms') <span>*</span></label>
                                                <div class="form-group">
                                                    <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms" onchange="" required>
                                                    <option  value="" ></option>
                                                    @foreach($paymentterms as $value)
                                                         <option value="{{@$value->id}}"
                                                            {{isset($select_cart)? !empty(@$select_cart[0]->payment_terms)? @$select_cart[0]->payment_terms == @$value->id ? 'selected':'':'':''}}
                                                            {{ isset($payment_terms) ? (!empty(@$payment_terms) ? (@$payment_terms == @$value->id ? 'selected' : '') : '') : '' }} 
                                                            >{{@$value->title}}</option>
                                                    @endforeach
                                                </select>
                                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                                </div>
                                                
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('Sales Person Name')<span>*</span></label>
                                                <div class="form-group">
                                                      <select class="form-control js-example-basic-single" name="sales_man" id="sales_man" required>
                                                    <option value=""></option>
                                                    @foreach ($staff as $value)
                                                    <option value="{{ @$value->user_id }}"
                                                        <?php
                                                            if (@$sales_man == $value->user_id) {
                                                                ?> selected <?php
                                                            } elseif (isset($deal_det) && $deal_det->owner == $value->user_id) {
                                                                ?> selected <?php
                                                            } /*elseif ($value->user_id == Auth::id()) {
                                                                ?> selected
                                                            }*/
                                                        ?> >{{ @$value->full_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                    
                                                </div>
                                              
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">  @lang('Warehouse') <span>*</span> </label>
                                                <input class="form-control" type="text" id="warehouse" name="warehouse"
                                                value="{{ isset($editData) ? (!empty(@$editData->warehouse) ? @$edit->warehouse : old('warehouse')) : 'Taken from stock' }}" required>
                                            </div>
                                        </div>
                                        <script>
                                            $('#warehouse').val($('#main_company_id  option:selected').text());
                                        </script>

                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">  @lang('Driver') <span></span> </label>
                                                <input class="form-control" type="text" id="driver" name="driver"
                                                value="{{ isset($editData) ? (!empty(@$editData->driver) ? @$edit->driver : old('driver')) : 'Salman' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">  @lang('Vehicle No') <span>*</span> </label>
                                                <input class="form-control" type="text" id="vehicleno" name="vehicleno"
                                                value="{{ isset($editData) ? (!empty(@$editData->vehicleno) ? @$edit->vehicleno : old('vehicleno')) : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                @php
                                                $supplier_name=@$supp_name;
                                                if(isset($sup_name) && !empty(@$sup_name->supplier_name)){
                                                    $supplier_name=$sup_name->supplier_name;
                                                }
                                                @endphp
                                                <label class="form-label">  @lang('Supplier Name') <span>*</span> </label>
                                                <input class="form-control" type="text" id="supplier_name" name="supplier_name"
                                                value="@if($supplier_name=="") Taken from stock @else {{ $supplier_name }} @endif" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">  @lang('Deal Id') <span>*</span> </label>
                                                <?php 
                                                    $dealid=0;
                                                    if(isset($deal_id)){
                                                        $dealid = @App\SysHelper::get_code_from_dealid($deal_id);
                                                    }
                                                    else{
                                                        if(isset($select_cart)){
                                                            $dealid = @App\SysHelper::get_code_from_dealid($select_cart[0]->deal_id);
                                                        }
                                                    }
                                                ?>
                                                <input class="form-control" type="text" id="deal_id" name="deal_id"
                                                value="{{ $dealid }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('Narration') <span>*</span></label>
                                                <input class="form-control" data-bs-toggle="modal" data-bs-target="#narrationModal" type="text" name="narration" autocomplete="off" value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}" id="narration">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                            
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="shipping-details" role="tabpanel" aria-labelledby="shipping-details-tab">
                                        <div class="row gap-rows">
                                            <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Name') <span></span></label>
                                        <input type="text" class="form-control" value="@if(isset($deal_det)) {{ $deal_det->delivery_company }} @endif" id="shipping_name" name="shipping_name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" value="@if(isset($deal_det)) {{ $deal_det->delivery_address }} @endif" id="shipping_address" name="shipping_address">
                                    </div>
                                </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row gap-rows">

                                             <div class="col-lg-2 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Customer Country') <span></span></label>
                                            <select class="form-control js-example-basic-single" name="customer_country" id="country">
                                               <option value=""></option>
                                                @foreach ($countries as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        <?php try{?>                                                        
                                                        @if (isset($deal_cust)) @if (@$deal_cust->vat_country == $value->id) selected @endif
                                                        @endif
                                                        <?php } catch (\Throwable $th) {} ?>
                                                        >{{ @$value->name }} </option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>

                                    <div class="col-lg-2 mb-2">
    <div class="input-effect">
        <label class="form-label">@lang('Customer State')</label>

        <div id="sectionStateDiv">
            <select class="form-control js-example-basic-single" name="customer_state" id="state">
                <option value=""></option>

                @foreach ($states as $state)
                    <option value="{{ $state->id }}"
                        {{ (isset($deal_cust) && $deal_cust->vat_state == $state->id) ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>



                                         <div class="col-2">
                        <label class="form-label">VAT %</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_percent" id="vat_percent" value="">
                        </div>
                    </div>

                     <div class="col-2">
                        <label class="form-label">VAT Number</label>
                        <div class="form-group">
                          
                           <input class="form-control" type="number"  name="vat_number" id="vat_number" value="">
                        </div>
                    </div>


                                            <div class="col-lg-2 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Customer Type')</label>
                                            <div class="form-group">
                                                 <select class="form-control" name="customer_type" id="customer_type">
                                                <option value="0" ></option>
                                                @foreach($customertype as $value)
                                                        <option value="{{@$value->id}}" {{isset($deal_cust)? !empty(@$deal_cust->customer_type)? @$deal_cust->customer_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Sale Type')</label>
                                            <div class="form-group">
                                                 <select class="form-control" name="sale_type" id="sale_type">
                                                <option value="0" ></option>
                                                @foreach($saletype as $value)
                                                        <option value="{{@$value->id}}" {{isset($deal_cust)? !empty(@$deal_cust->sale_type)? @$deal_cust->sale_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                           
                                        </div>
                                    </div>
                                   
                                    
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="end-user-details" role="tabpanel" aria-labelledby="end-user-details-tab">
                                        <div class="row gap-rows">
                                            <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('End User Name') <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="@if(isset($deal_enduser)) {{ $deal_enduser->end_user_company_name }} @endif" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="@if(isset($deal_enduser)) {{ $deal_enduser->end_user_contact_person }} @endif">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="@if(isset($deal_enduser)) {{ $deal_enduser->email }} @endif">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Contact Person No') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" id="contact_person_no" autocomplete="off" value="@if(isset($deal_enduser)) {{ $deal_enduser->mobile_no }} @endif">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2" id="device_serial_container" style="display:none;">
                                        <div class="mb-3">
                                            <label class="form-label">Device Serial</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="device_serial" id="device_serial" readonly style="cursor:pointer;" placeholder="Click to enter serial numbers..." />
                                                <button type="button" class="btn btn-light border" id="device_serial_btn_modal">
                                                    <i class="ico icon-outline-list-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="table-container" style="border: solid 1px #d9d9d9;">
                                <table class="table table-hover form-item-table" id="myTable">
                                    <thead>                                                            
                                        <tr>
                                            <th class="resizable text-center" width="30px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="210px">@lang('Part No') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="280px">@lang('Description')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="30px">@lang('Tax')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="30px">@lang('Qty')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px">@lang('Price')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px">@lang('Value')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Dis <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#discountModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px">@lang('Taxable')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px">@lang('VAT')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('SRL No')<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @php $qty_total=0; $value_total=0; $discount_total=0; $taxableamount_total=0; $vatamount_total=0; $total_amount=0; @endphp
                                    @if (count($select_cart)>0)
                                    @php $i=0; @endphp
                                        @foreach ($select_cart as $cart)
                                        @php                                        
                                        $value = @App\SysHelper::com_curr_format($cart->qty * $cart->unitprice, 2, '.', ',');
                                        $taxamount=@App\SysHelper::com_curr_format($cart->value - $cart->discount, 2, '.', ',');
                                        $vatamount = @App\SysHelper::com_curr_format(($cart->value - $cart->discount)*$cart->tax/100, 2, '.', ',');
                                        $totalamount = (($cart->qty * $cart->unitprice) - $cart->discount)+(($cart->qty * $cart->unitprice) - $cart->discount)*$cart->tax/100;
                                        @endphp
                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i+1 }}" /></td>
                                            <td>
                                                <select class="form-control noborder " name="part_number[]">
                                                    <option value="{{ $cart->part_number }}">{{ $cart->part_number_txt }}</option>
                                                </select></td>
                                            <td>
                                                <textarea class="form-control" name="description[]" rows="1">{{ $cart->description }}</textarea></td>
                                            <td><input class="form-control qty rc" type="number" id="tax_{{ $i }}" name="tax[]" autocomplete="off" min="0" value="{{ $cart->tax }}" onchange="calc_change({{ $i }})"></td>
                                            <td><input class="form-control qty rc" type="number" id="qty_{{ $i }}" data-enter-skip name="qty[]" autocomplete="off" min="0" value="{{ $cart->qty }}" onchange="calc_change({{ $i }})" onkeypress="set_license_key_normal()"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="unitprice_{{ $i }}" value="{{ @App\SysHelper::com_curr_format( $cart->unitprice, 2, '.', ',')}}" name="unitprice[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})" onblur="formatCurrency(this)"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="value_{{ $i }}" value="{{ $value }}" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="discount_{{ $i }}" value=" {{ @App\SysHelper::com_curr_format( $cart->discount , 2, '.', ',') }}" name="discount[]" autocomplete="off" min="0" onchange="calc_change({{ $i }})" onblur="formatCurrency(this)"></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="taxableamount_{{ $i }}" value="{{ $taxamount }}" name="taxableamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="vatamount_{{ $i }}" value="{{ $vatamount }}" name="vatamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control text-right" type="text" id="totalamount_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($totalamount , 2, '.', ',') }}" name="totalamount[]" readonly></td>
                                            <td class="jshide"><input class="form-control srl" type="test" id="srl_{{ $i }}" name="serial_no[]" onclick="srlno_add({{ $i }})" ></td>
                                            </tr>
                                        @php $i++;
                                        @endphp
                                        @endforeach                                        
                                        
                                    @endif
                                    
                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $select_cart ? count($select_cart) + 1 : 1 }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control noborder " name="part_number[]">
                                                </select>
                                                {{-- on focus add this class and its funcanalities js-product-select --}}
                                            </td> 
                                            <td>
                                                <textarea class="form-control" name="description[]" rows="1"></textarea>
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden>                                            
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-center" data-enter-skip type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="set_license_key_normal()"></td>
                                            <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="serial_no[]"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" scope="col" >Total</th>
                                            <th class="text-center"><label id="lbl_total_qty" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_price" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_value" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_discount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_taxableamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_vatamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_totalamount" >0</label></th>
                                            <th class="text-end" scope="col" ></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>
                            {{ Form::close() }}


{{-- Models  --}}
<!-- <a data-bs-toggle="modal" data-bs-target="#editModal"></a> -->

					@include('backEnd.inventory.itemAddModal')

        <div class="modal side-panel fade" id="discountModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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

        <div class="modal side-panel fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" style="height: 279px !important;"> 
              	<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="editModalLabel">Serial No</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body m-0 p-0">
						<div class="card mb-0 mt-0">
							<div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <label class="form-label">Serial No</label>
                                        <div class="form-group">
                                            <textarea type="text" class="form-control" id="add_serial_no" style="height: 150px;"></textarea>
                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light add-btn ms-2" onclick="addSerialNo()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add
						</button>
					</div>
              	</div>
            </div>
        </div>
    
{{-- Models  --}}

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
                                    <textarea type="text" class="form-control" id="add_description" style="height: 150px;"></textarea>
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
<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Narration</h4>
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
$(document).on("keydown", 'input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]', function(e) {
    if (e.key === "Enter") {
        e.preventDefault(); // prevent form submit

        let row = $(this).closest("tr"); // get current row
        let name = $(this).attr("name");
        
        if (name === "qty[]") {
            row.find('input[name="unitprice[]"]').focus();
        } else if (name === "unitprice[]") {
            row.find('input[name="discount[]"]').focus();
        } else if (name === "discount[]") {
            row.find('input[name="serial_no[]"]').focus();
        }
    }
});
$('#delivery-note-create-form').on('keypress', function (e) {
    if (e.which === 13 && !$(e.target).is('input[name="qty[]"]') && !$(e.target).is('input[name="unitprice[]"]')) {
      e.preventDefault();
      return false;
    }
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('narration');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('shown.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
            setTimeout(() => narrationTextarea.focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });
</script>

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
    let serialNoModal;
    document.addEventListener("DOMContentLoaded", function () {
        const modalElement = document.getElementById('serialNoModal');
        serialNoModal = new bootstrap.Modal(modalElement);
    });
    let currentSerialInput = null;
    
    $(document).on('click', 'input[name="serial_no[]"]', function () {
        currentSerialInput = $(this);
        $('#add_serial_no').val(currentSerialInput.val());
        serialNoModal.show();
        setTimeout(() => $('#add_serial_no').focus(), 500);
    });
    function addSerialNo() {
        if (currentSerialInput) {
            const val = $('#add_serial_no').val();
            currentSerialInput.val(val);
            serialNoModal.hide();
            currentSerialInput = null;
        }
    }
</script>

<script>
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function() {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'textarea[name="description[]"]', function() {
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

<script>
    function calc_change_new(el) {
    $("#loading_bg").css("display", "block");

    // Get the current row
    var $row = $(el).closest('tr');

    // Read values from the current row
    var net_vat = $row.find('input[name="tax[]"]').val() || '0';

    var qty = $row.find('input[name="qty[]"]').val() || '0';
    var unitprice = $row.find('input[name="unitprice[]"]').val().replace(/,/g, '') || '0';
    var discount = $row.find('input[name="discount[]"]').val().replace(/,/g, '') || '0';
    var fright = 0;
    var customcharges = 0;

    var decimal_point = @json(session('logged_session_data.decimal_point'));

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
}
</script>
<script>
                                            update_totals();
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
                             let text = "";

                                if (SHOW_CUSTOMER_CODE) {
                                    text = item.account_name + " (" + item.account_code +
                                        ")";
                                } else {
                                    text = item.account_name; // no code
                                }

                                return {
                                    id: item.id,
                                    text: text
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
        $(this).select2('open');
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


    // When any .js-account-select select2 opens, prefill the search box with the currently selected value
        $(document).on('select2:open', function(e) {
            // Find the select2 element that triggered the event
            var $select = $(document.activeElement).closest('.js-account-select');
            if ($select.length === 0) {
                // fallback: try to get the open dropdown's select
                $select = $('.js-account-select').filter(function() {
                    return $(this).data('select2') && $(this).data('select2').isOpen();
                });
            }
            if ($select.length > 0) {
                var sel = $select.select2('data');
                if (sel && sel.length && sel[0].text) {
                    setTimeout(function() {
                        const searchInput = document.querySelector(
                            '.select2-container--open .select2-search__field');
                        if (searchInput) {
                            // Put current selected text into search box so user can edit / refine
                            searchInput.value = sel[0].text.trim();
                            // trigger input so select2 filters on prefilling
                            var event = new Event('input', {
                                bubbles: true
                            });
                            searchInput.dispatchEvent(event);

                            // Move cursor to end of the text
                            try {
                                var len = searchInput.value.length;
                                searchInput.setSelectionRange(len, len);
                            } catch (err) {
                                // ignore if not supported
                            }
                        }
                    }, 0);
                }
            }
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
            $row.find('textarea[name="description[]"]').val(selectedData.description || '');
            $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
            $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
            $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
            $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
            // $row.find('input[name="discount[]"]').val(0);
            $row.find('input[name="fright[]"]').val(0);
            $row.find('input[name="customcharges[]"]').val(0);
            $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
                $row.find('input[name="qty[]"]').focus();
            
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
    const pageHeight = window.innerHeight-65;
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
        function without_po(id) {
            $("#loading_bg").css("display", "block");
            $("#si_id").val(id);
            $("#table_id2").css("display", "");
            $("#loading_bg").css("display", "none");
        }
        function popup_si_pending(id){
            $("#loading_bg").css("display", "block");
            $("#hd_pending_dn_id").val(id);
            $("#si_id").val(id);
            $("#addDNPending").click();
            $("#addDNPending").prop("disabled", true);
            $("#loading_bg").css("display", "none");
        }

        function get_pending_si_list() {
        var cus_id = $("#customer_id").val();
        get_vat(cus_id);
        get_dn_list(cus_id);
        get_cust_details(cus_id);
        }


        function get_cust_details(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-customer-details') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                console.log("dataResult",dataResult)
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        if (dataResult['data'][i].status == 3) {
                           
                          
                        } else {
                            $('#btnSubmit').css('display', '');
                        }
                        $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');
                        $('#shipping_name').val(dataResult['data'][i].contcat_person);
                        $('#shipping_address').val(dataResult['data'][i].address);
                        $('#customer_type').val(dataResult['data'][i].customer_type).trigger('change');
                        $('#sale_type').val(dataResult['data'][i].sale_type).trigger('change');
                        $('#country').val(dataResult['data'][i].vat_country).trigger('change');
                        $('#state').val(dataResult['data'][i].vat_state).trigger('change');
                        console.log("cat=",dataResult['data'][i].vat_percentage);
                        $('#net_vat').val(dataResult['data'][i].vat_percentage);
                        $('.vat').val(dataResult['data'][i].vat_percentage);
                         $('#vat_percent').val(dataResult['data'][i].vat_percentage);
                            $('#vat_number').val(dataResult['data'][i].vat_number);
                    }
                } else {
                    $('#payment_terms').val('');
                    $('#shipping_name').val('');
                    $('#shipping_address').val('');
                    $('#customer_type').val('');
                    $('#sale_type').val('');
                    $('#country').val('');
                    $('#state').val('');
                    $('#net_vat').val('');
                    $('.vat').val('');
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

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
                    
                    var innerHtml ="<input type='radio' onclick='without_po(0)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without SIV</label><br />";
                    $("#plist").append(innerHtml);

                    $("#loading_bg").css("display", "none");
            }
        });
    }


    </script>
    <!-- Modal License Key-->
        <button id="btn_ModalLicenseKey" data-bs-target="#ModalLicenseKey" data-bs-toggle="modal" hidden></button>    
        <div class="modal side-panel fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1" aria-labelledby="ModalLicenseKey" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select License Key (<label id="ModalLabelHeading" ></label> )</h5>
                    <input type="hidden" id="part_no" />
                    <input type="hidden" id="update_id" />
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="popup_close"></button>
                </div>
                
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="lk-table" class="table table-hover table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">Select</th>
                                        <th style="width: 15%;">Expiry Date</th>
                                        <th style="width: 50%;">Licence Key</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <label id="selected_key">0</label> Keys Selected out of <label id="total_key">0</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
						<button onclick="set_license_key()" type="button" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Selected
						</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        
        function set_license_key_normal() {
    $(document).on("keypress", 'input[name="qty[]"]', function (e) {
        if (e.which === 13) { // Enter key
            let $row = $(this).closest("tr"); // current row
            let pt = $row.find('input[name="product_type[]"]').val();
            

            if (pt == 2) {
                $('#part_no').val($row.find('select[name="part_number[]"]').val());
                $("#ModalLabelHeading").text($row.find('select[name="part_number[]"] option:selected').text());
                //$("#license_qty").val($(this).val()); // qty value from current input
                $("#btn_ModalLicenseKey").click();
                get_license_key($('#part_no').val());
                e.preventDefault();
                return false;
            }

            return true;
        }
    });
}
// function set_license_key_normal(){
//     $('#qty').keypress(function (e) {
//         var key = e.which;
//         if(key === 13) { //the enter key code
//             var pt = 2;
//             if(pt == 2) {
//                 var part_id =$('#part_number_new').val();
//                 $('#ModalLabelHeading').text($('#part_number_new').val());    
//                 $('#part_no').val(part_id);
//                 $('#btn_ModalLicenseKey').click();
//                 get_license_key(part_id);
//             }
//             return true;
//         }
//     });
// }

        function set_license_key_po(rowid,producttype){
            $('#qty_'+rowid).keypress(function (e) {
                var key = e.which;
                if(key === 13) { //the enter key code
                    var pt = producttype;
                    if(pt == 2) {
                        var part_id =$('#part_id_'+rowid).val();
                        $('#ModalLabelHeading').text($('#part_number_'+rowid).val());    
                        $('#part_no').val(part_id);
                        $('#btn_ModalLicenseKey').click();
                        get_license_key(part_id);
                    }
                    return true;
                }
            });
        }
        function get_license_key(part_id){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('dn-get-grn-license-key') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    item_id : part_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows="";
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                            $('#total_key').text(len);
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                getSelectedRows +="<tr>\
                                    <td><input class='chk_key' type='checkbox' id='select_key_"+ Number(i+1) +"' onclick='key_select_change("+ Number(i+1) +")'  /><input type='hidden' id='item_key_id_"+ Number(i+1) +"' value='"+dataResult['data'][i].id+"' /></td>\
                                    <td>"+get_format_date(dataResult['data'][i].exp_date)+"</td>\
                                    <td>"+dataResult['data'][i].license_key+"</td>\
                                    </tr>";                                    
                            }
                            $('#license_key').val('');
                            $('#exp_date').val('');
                            $('#lk-table tbody').empty();
                            $("#lk-table tbody").append(getSelectedRows); 
                        }
                        else{
                            $('#lk-table tbody').empty();
                        }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function key_select_change(id){
            $('#select_key_'+id).on('change', function() { 
                if (this.checked) {
                }
            });

            var a = 0;
            var b = 1;
            var itm_id = 0;
            $(".chk_key").each(function() {
                if(this.checked){
                    a = Number(a+1);
                    if(itm_id == 0){
                        itm_id = $('#item_key_id_'+b).val();
                    }
                    else{
                        itm_id += ','+$('#item_key_id_'+b).val();
                    }
                }
                b++;
            });
            $('#update_id').val(itm_id);
            $('#selected_key').text(a);
        }
        
        function set_license_key(){
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('dn-update-grn-license-key') }}";
            var myArray = $('#update_id').val(); 
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id : myArray,
                    item_id : $('#part_no').val(),
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    $('#popup_close').click();
                }
            });
            $("#loading_bg").css("display", "none");
        }

    </script>




{{-- Device Serial Modal (populated dynamically when pending SI is loaded) --}}
<div class="modal fade" id="DeviceSerialModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="DeviceSerialModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 22rem;">
        <div class="modal-content">
            <div class="modal-header mb-2">
                <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serial Numbers</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="serial-parts-container">
                    {{-- Dynamically built by JS after pending SI items are loaded --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn_save_all_serials" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success" style="font-size:16px"></i> Save All
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    // Initialize DeviceSerialModal
    (function () {
        const _el = document.getElementById('DeviceSerialModal');
        let _modal = null;
        try {
            if (_el && window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                _modal = new bootstrap.Modal(_el);
            }
        } catch (e) { console.warn('DeviceSerialModal init failed', e); }

        window._deviceSerialModal = _modal;

        $(document).on('click', '#device_serial, #device_serial_btn_modal', function () {
            if (_modal) { _modal.show(); return; }
        });
    })();

    // --- Device serial modal UX & validation ---
    (function(){
        const normalize = s => (s || '').toString().trim().toLowerCase();

        window.updateSerialCountForSection = function($section) {
            const qty = parseInt($section.attr('data-qty')) || 0;
            const filled = $section.find('.part-serial-input').filter(function(){ return $(this).val().trim() !== ''; }).length;
            const $display = $section.find('.serial-count-display');
            $display.text(filled + ' of ' + qty);
            $display.toggleClass('complete', filled === qty && qty > 0);
            $display.toggleClass('incomplete', filled > 0 && filled < qty);
        };

        // Enter navigation
        $(document).on('keydown', '.part-serial-input', function(e){
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const $current = $(this).closest('.serial-input-row');
            const $next = $current.next().find('.part-serial-input');
            if ($next.length) { $next.focus(); return; }
            const $section = $(this).closest('.part-serial-section');
            const $nextSection = $section.nextAll('.part-serial-section').first();
            if ($nextSection.length) {
                const $first = $nextSection.find('.part-serial-input').first();
                if ($first.length) { $first.focus(); return; }
            }
            $('#btn_save_all_serials').focus();
        });

        // clear invalid state while typing
        $(document).on('input', '.part-serial-input', function(){
            $(this).removeClass('is-invalid');
        });

        // Duplicate check on blur/change
        $(document).on('blur change', '.part-serial-input', function(){
            const $this = $(this);
            const val = $this.val().trim();
            if (!val) { updateSerialCountForSection($this.closest('.part-serial-section')); $this.removeClass('is-invalid'); return; }
            const key = normalize(val);
            let duplicate = false;
            $('.part-serial-input').not($this).each(function(){
                if (normalize($(this).val()) === key) { duplicate = true; return false; }
            });
            if (!duplicate) {
                $('input[name="serial_no[]"]').each(function(){
                    const v = $(this).val() || '';
                    v.split(',').map(s => s.trim()).forEach(function(tok){ if (!duplicate && normalize(tok) === key && tok !== '') { duplicate = true; } });
                    if (duplicate) return false;
                });
            }
            if (duplicate) {
                toastr.error('Duplicate serial detected — remove or change it.');
                $this.addClass('is-invalid');
                $this.val('');
                $this.focus();
            } else {
                $this.removeClass('is-invalid');
            }
            updateSerialCountForSection($this.closest('.part-serial-section'));
        });

        // Final validation & save
        $(document).on('click', '#btn_save_all_serials', function(){
            const seen = {};
            let dupFound = false;
            function markSeen(str) {
                const k = normalize(str);
                if (!k) return true;
                if (seen[k]) { dupFound = true; return false; }
                seen[k] = true; return true;
            }
            $('input[name="serial_no[]"]').each(function(){
                const v = $(this).val() || '';
                v.split(',').map(s => s.trim()).forEach(s => { if (s) markSeen(s); });
            });
            $('.part-serial-input').each(function(){
                const v = $(this).val().trim();
                if (!v) return;
                if (!markSeen(v)) return false;
            });
            if (dupFound) {
                toastr.error('Duplicate serial numbers found. Remove duplicates before saving.');
                return;
            }
            const summary = [];
            $('.part-serial-section').each(function(){
                const $sec = $(this);
                const part = $sec.attr('data-part-number');
                const serials = [];
                $sec.find('.part-serial-input').each(function(){
                    const v = $(this).val().trim(); if (v) serials.push(v);
                });
                if (serials.length) {
                    summary.push(part + ': ' + serials.join(', '));
                    // update matching table rows
                    $('#myTable tbody tr').each(function(){
                        const partTxt = $(this).find('input[name="part_number_txt[]"]').val();
                        if (partTxt && partTxt.trim() === part) {
                            $(this).find('input[name="serial_no[]"]').val(serials.join(', '));
                        }
                    });
                }
                updateSerialCountForSection($sec);
            });
            $('#device_serial').val(summary.join(' | '));
            if (typeof bootstrap !== 'undefined' && $('#DeviceSerialModal').hasClass('show')) {
                $('#DeviceSerialModal').modal('hide');
            }
            toastr.success('Serial numbers saved');
        });

        // Initialize counts when modal opens
        $(document).on('shown.bs.modal', '#DeviceSerialModal', function(){
            $('.part-serial-section').each(function(){ updateSerialCountForSection($(this)); });
            const $firstEmpty = $('.part-serial-input').filter(function(){ return $(this).val().trim() === ''; }).first();
            if ($firstEmpty.length) $firstEmpty.focus();
        });
    })();

});
</script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
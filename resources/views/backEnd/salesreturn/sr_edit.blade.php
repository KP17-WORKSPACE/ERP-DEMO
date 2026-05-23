    <?php try { ?>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-update/'.$edit->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'sales-return-update']) }}
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                <input type="hidden" id="sr_id" value="{{ isset($edit) ? $edit->id : '' }}">                
                <input type="hidden" id="net_vat" name="net_vat">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
             Edit - {{ @$edit->doc_number }}
        </h4>
        <div class="purchase-order-content-header-right">
            <a type="button" class="btn btn-light text-dark" href="{{url('sales-return/'.$edit->id.'?sr_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
             <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{url('sales-return/'.$edit->id.'/delete')}}"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Cancel SR</a></li>
                    <li><a class="dropdown-item" href="{{url('sales-return/'.$edit->id.'/download')}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li>
                    
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#adj_popup_win"><i class="ico icon-outline-calculator-minimalistic text-danger"></i> Adjustment</button></li>

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
                                                <select class="form-control js-account-select" name="customer" id="customer" required>
                                                    <option value=""></option>
                                                    @foreach ($customer as $value)
                                                        <option value="{{ @$value->id }}"
                                                            {{ isset($edit) ? (!empty($edit->customer) ? (@$edit->customer == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->account_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">
                                                <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ isset($edit) ? (!empty(@$edit->doc_number) ? @$edit->doc_number : old('doc_number')) : '' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Doc Date</label>
                                            <div class="form-group">
                                            @php
                                                $value = date('d/m/Y'); // default today in dmy
                                                if(isset($edit) && !empty($edit->doc_date)){
                                                    $value = date('d/m/Y', strtotime($edit->doc_date));
                                                }
                                            @endphp

                                                    <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                                                        name="doc_date" value="{{ @$value }}" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency<a style="float: right;" data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                                            <div class="form-group">
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    @foreach ($currency as $value)
                                                    @if($edit->currency == @$value->id)
                                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By</label>
                                            <div class="form-group">
                                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->created_by) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
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
                        <div id="plist"
                                                style="width: 100%; height: 180px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                                            </div>
                                            <a data-modal-size="modal-md" data-target="#dn_pending_popup_win" id="addDnPending"
                                                data-toggle="modal"></a>
                                            <input type="hidden" id="dn_id" name="dn_id">
                                            <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>
                </div>    
                <div class="col-lg-10 mb-2">
                                        <div class="row">
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('SIV') @lang('Number')<span>*</span></label>
                                                    <input class="form-control" type="text" name="si_doc_number" autocomplete="off" id="si_doc_number" value="{{ isset($edit) ? (!empty(@$edit->si_doc_number) ? @$edit->si_doc_number : old('si_doc_number')) : '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">SIV Date</label>
                                                   @php
                                                        $value = date('d/m/Y'); // default today in dmy
                                                        if(isset($edit) && !empty($edit->si_doc_date)){
                                                            $value = date('d/m/Y', strtotime($edit->si_doc_date));
                                                        }
                                                    @endphp

                                                    <input class="form-control date-picker" id="si_doc_date" type="text" autocomplete="off" name="si_doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('DLN') @lang('Number')<span>*</span></label>
                                                    <input class="form-control" type="text" name="dn_doc_number" autocomplete="off" id="dn_doc_number" value="{{ isset($edit) ? (!empty(@$edit->dn_doc_number) ? @$edit->dn_doc_number : old('dn_doc_number')) : '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">DLN Date</label>
                                                   @php
                                                        $value = date('d/m/Y'); // default today in dmy
                                                        if(isset($edit) && !empty($edit->dn_doc_date)){
                                                            $value = date('d/m/Y', strtotime($edit->dn_doc_date));
                                                        }
                                                    @endphp
                                                    <input class="form-control date-picker" id="dn_doc_date" type="text" autocomplete="off" name="dn_doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
                                            
                                              <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference No<span>*</span></label>
                                                    <input class="form-control" type="text" name="reference_no" autocomplete="off" id="reference_no" value="{{ @$edit->lpo_number }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">LPO/Reference Date<span>*</span></label>
                                                    <input class="form-control date-picker" type="text" name="reference_date" autocomplete="off" id="reference_date" value="{{ !empty($edit->lpo_date) ? date('d/m/Y', strtotime($edit->lpo_date)) : '' }}" required>
                                                </div>
                                            </div>

                                             <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Payment Terms')<span>*</span></label>
                                                    <div class="form-group js-example-basic-single">
  <select class="form-control" name="payment_terms" id="payment_terms"  required>
                                                        <option value="" ></option>
                                                        @foreach($paymentterms as $value)
                                                             <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->payment_terms)? @$edit->payment_terms==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                        @endforeach
                                                    </select>
                                                    <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                                                    </div>
                                                  
                                                </div>
                                            </div>

                                             <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Sales Person Name')<span>*</span></label>
                                                    <select class="form-control js-example-basic-single" name="sales_man" id="sales_man" required>
                                                        <option value=""></option>
                                                        @foreach ($staff as $value)
                                                        <option value="{{ @$value->user_id }}"
                                                            @if(isset($edit)) @if($edit->sales_man == $value->user_id) selected @endif @else @if($value->user_id == Auth::user()->id) selected  @endif @endif
                                                            >{{ @$value->full_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                                     <div class="col-lg-3 mb-2">
                            <div class="input-effect">
                                <label class="form-label">Supplier Name<span>*</span></label>
                                @php
            $supplier_reference_list = @App\SysHelper::get_supplierlist_charofaccounts();

$selectedIds = $edit->ref_supplier_id
    ? explode(',', $edit->ref_supplier_id)
    : [];
@endphp


                                <select class="form-control js-example-basic-single" name="ref_supplier_id[]"
                                    id="ref_supplier_id" multiple>
                                    <option value="">-Select-</option>
                                    <option value="TFS" @if(in_array('TFS', $selectedIds)) selected @endif>TAKEN FROM STOCK</option>

                                    @foreach ($supplier_reference_list as $value)
                                    <option value="{{ @$value->id }}" @if(in_array($value->id, $selectedIds)) selected @endif >{{ @$value->account_name }}
                                        @if(@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                        ({{ @$value->account_code }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>

                                <input class="form-control" type="hidden" name="supplier_name" autocomplete="off"
                                    id="supplier_name" value="{{ $edit->supplier_name }}" required>
                            </div>
                        </div>
                                            
                                            
                                            <div class="col-lg-3 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Delivery Terms')<span>*</span></label>
                                                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off" id="delivery_terms" value="{{ isset($edit) ? (!empty(@$edit->delivery_terms) ? @$edit->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">@lang('Printed Invoice Number')<span></span></label>
                                                    <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off" id="printed_invoice_number" value="{{ isset($edit) ? (!empty(@$edit->printed_invoice_number) ? @$edit->printed_invoice_number : old('printed_invoice_number')) : '' }}">
                                                </div>
                                            </div>
                                           
                                           
                                          
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Deal ID<span>*</span></label>
                                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ @App\SysHelper::get_code_from_dealid($edit->deal_id) }}" required>
                                                </div>
                                            </div>
                                   
                                                <div class="col-lg-3 mb-2">
                                          
                                            <label class="form-label" for="">Attachment</label>
                                            <input type="file" class="form-control" name="doc" id="doc">

               
                                            </div>

                                            <div class="col-2">
                                                <label class="form-label">Credit Note</label>
                                                <div class="form-group">
                               

                                     <div class="form-group">
                                  
                                         <select class="form-control js-example-basic-single" required name="credit_note" id="credit_note">
                                       
                           
                                            <option value="CN" @if($edit->credit_note == 'CN') selected @endif>Credit Note</option>
                                            <option value="SR" @if($edit->credit_note == 'SR') selected @endif>Sales Return</option>
                                  
                                        
                                    </select>
                                </div>

                                                </div>
                                            </div>

                                            
                                            <div class="col mb-2">
                                                <div class="input-effect">
                                                    <label class="dynamicslbl">Narration<span></span></label>
                                                    <input class="form-control" type="text" name="narration" autocomplete="off" id="narration" value="{{ @$edit->narration }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                            
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="shipping-details" role="tabpanel" aria-labelledby="shipping-details-tab">
                                        <div class="row gap-rows">
                               

             <div class="col-3">

             @php
                    $customer = @App\SysHelper::get_customer_supplier_list($company_id);
                    
                @endphp
                        
                        <label class="form-label">Company (Ship To)</label>
                        <div class="form-group">
                            <select class="form-control js-example-basic-single" name="shipping_supplier"
                                id="shipping_supplier" required style="width: 100%;">
                                <option value=""></option>
                                @foreach ($customer as $value)
                                            <option value="{{ @$value->id }}"
                                                @if(isset($edit))
                                                    @if(!empty($edit->shipping_supplier))
                                                        @if ($edit->shipping_supplier == @$value->id)
                                                            selected
                                                        @endif
                                                       
                                                    @endif
                                                @endif                                                                                                
                                                >{{ @$value->account_name }}   @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->account_code }})
                                        @endif</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Name</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                value="{{ isset($edit) ? (!empty(@$edit->shipping_name) ? @$edit->shipping_name : '') : old('shipping_name') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ isset($edit) ? (!empty(@$edit->shipping_email) ? @$edit->shipping_email : '') : old('shipping_email') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no" id="shipping_contact_no"
                                value="{{ isset($edit) ? (!empty(@$edit->shipping_contact_no) ? @$edit->shipping_contact_no : '') : old('shipping_contact_no') }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" value="{{ isset($edit) ? (!empty(@$edit->shipping_address) ? @$edit->shipping_address : '') : old('shipping_address_1') }}" name="shipping_address_1" id="shipping_address_1" />
                        </div>
                    </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row gap-rows">
                                  <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Customer Type')</label>
                                            <select class="form-control js-example-basic-single" name="customer_type" id="customer_type">
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
                                            <select class="form-control js-example-basic-single" name="sale_type" id="sale_type">
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
                                            <select class="form-control js-example-basic-single" name="customer_country" id="country">
                                                <option data-display="" value="0"></option>
                                                @foreach ($countries as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        <?php try{?>                                                        
                                                        @if (isset($edit)) @if (@$edit->customer_country == $value->id) selected @endif @endif
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
                                                <select class="form-control js-example-basic-single" name="customer_state" id="state">
                                                    <option data-display="" value="0"></option>
                                                        @foreach ($states as $key => $value)
                                                            <option value="{{ $value->id }}"
                                                                @if (isset($edit)) @if (@$edit->customer_state == $value->id) selected @endif @endif
                                                                >{{ $value->name }}</option>
                                                        @endforeach
                                                </select>
                                            </div>
    
                                        </div>
                                    </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show" id="end-user-details" role="tabpanel" aria-labelledby="end-user-details-tab">
                                        <div class="row gap-rows">
                                     <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('End User Name') <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->end_user_name) ? @$edit->end_user_name : '') : old('end_user_name') }}" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_name) ? @$edit->contact_person_name : '') : old('contact_person_name') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_email) ? @$edit->contact_person_email : '') : old('contact_person_email') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="dynamicslbl">@lang('Contact Person No') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_no" id="contact_person_no" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_no) ? @$edit->contact_person_no : '') : old('contact_person_no') }}">
                                            
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
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Part No') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="250px">@lang('Description')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Tax')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Qty')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Price')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Value')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Dis <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#discountModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Taxable')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('SRL No')<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($edit_list) && count($edit_list) > 0)
                                         <?php $qty = 0; $unitprice = 0; $value = 0; $discount = 0; $taxableamount = 0; $vatamount = 0; $totalamount = 0; $i=1; $deal_discount_sum_amount=0; ?>
                    @if (count($edit_list)>0)
                        @foreach ($edit_list as $dt)
                        <tr>
                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" /></td>
                            <td><select class="form-control noborder " name="part_number[]">
                                                    <option value="{{ $dt->part_number }}">{{ $dt->product->part_number }}</option>
                                                </select></td>
                                                
                                            <td>
                                                <textarea class="form-control" name="description[]" rows="1">{{ $dt->description }}</textarea>
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" value="{{ $dt->product->part_number }}" hidden>
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" value="{{ @$dt->product->product_type }}" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" value="{{ @$dt->product->description }}" hidden>                                            
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" value="{{ @App\SysHelper::com_curr_format($dt->tax,2,'.',',') }}" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0" data-enter-skip value="{{ $dt->qty }}" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)" onkeydown="return set_license_key(this, event)"></td>
                                            <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" value="{{ @App\SysHelper::com_curr_format($dt->unitprice,2,'.',',') }}" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" value="{{ @App\SysHelper::com_curr_format($dt->value,2,'.',',') }}" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="discount[]" autocomplete="off" min="0" value="{{ @App\SysHelper::com_curr_format($dt->discount,2,'.',',') }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="taxableamount[]" autocomplete="off" min="0" value="{{ @App\SysHelper::com_curr_format($dt->taxableamount,2,'.',',') }}" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="vatamount[]" autocomplete="off" min="0" value="{{ @App\SysHelper::com_curr_format($dt->vatamount,2,'.',',') }}" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="totalamount[]" autocomplete="off" min="0" value="{{ @App\SysHelper::com_curr_format($dt->taxableamount+$dt->vatamount,2,'.',',') }}" readonly></td>
                                            <td><input class="form-control text-end" type="text" name="serial_no[]" value="{{ $dt->serial_no }}"></td>
                            
                        </tr>
                        
                        <?php $i++; $qty += $dt->qty; $unitprice += $dt->unitprice; $value += $dt->value; $discount += $dt->discount; $taxableamount += $dt->taxableamount; $vatamount += $dt->vatamount; $totalamount += ($dt->taxableamount+$dt->vatamount); ?>
                        @endforeach
                    @endif
                    @endif
                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $i }}" />
                                            </td>
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
                                            <td><input class="form-control text-center" type="number" data-enter-skip name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)" onkeydown="return set_license_key(this, event)"></td>
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
                                    <button type="button" id="deleteRow" onclick="update_totals()">Delete Row</button>
                                </div>
                            </div>
                            {{ Form::close() }}

                            
                            
                            <div class="table-container" style="border: solid 1px #d9d9d9; background: white;">
                        @if(count($editDataAdjustments)>0)
                        <b>Adjusted Items</b>
                                <table class="table table-hover form-item-table" id="myTable">
<thead>
                                    <tr>
                                        <th style="width:50px;" class="text-center">@lang('#')</th>
                                        <th style="width:100px;" class="text-center">@lang('Doc Number')</th>
                                        <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                        <th style="width:100px;" class="text-center">@lang('DLN NO')</th>
                                        <th style="width:100px;" class="text-end">Total</th>
                                        <th style="width:100px;" class="text-end">Paid</th>
                                        <th style="width:100px;" class="text-end">Balance</th>
                                        <th style="width:100px;" class="text-end">Adjusted</th>
                                        <th style="width:100px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($editDataAdjustments as $item)
                                    <tr>
                                        <td class="text-center">{{ @$loop->iteration }}</td>
                                        <td class="text-center">{{ @$item->siv_no }}</td>
                                        <td class="text-center">{{ @App\SysHelper::normalizeToDmy($item->doc_date) }}</td>
                                        <td class="text-center">{{ @$item->dln_no }}</td>
                                        <td class="text-end">{{ @$item->total_amount }}</td>
                                        <td class="text-end">{{ @$item->paid_amount }}</td>
                                        <td class="text-end">{{ @$item->balance_amount }}</td>
                                        <td class="text-end">{{ @$item->paid_amount }}</td>
                                        <td class="text-center"><a class="btn-sm btn-light" href="{{url('delete-sales-return-adjustment/'.$item->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"> <i class="ico icon-outline-trash-bin-minimalistic"></i> </a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                                </table>
                        @endif
                            </div>
                    <div class="row mt-40">
                    <div class="col-lg-12 text-left mb-2">
                            <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                
                            </table>
                    </div>
                </div>

                            
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


<button id="btn_ModalLicenseKey" data-bs-target="#ModalLicenseKey" data-bs-toggle="modal" hidden></button>
<div class="modal side-panel fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1" aria-labelledby="ModalLicenseKey" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    Add License  <label style="margin-left: 117px" id="ModalLabelHeading"></label>
                </h4>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#ModalExcelQuote" title="Import license keys from CSV or Excel">
                        Import
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body mt-2">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Qty</label>
                        <input type="hidden" id="item_id" />
                        <input type="hidden" id="license_row_index" value="" />
                        <input type="hidden" id="edit_license_id" value="" />
                        <input type="number" class="form-control" name="license_qty" id="license_qty" value="1" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">License Key (<span id="licenseCountSummary" class="text-muted small mt-2">Selected: 0 of 0</span>)</label>
                        <input type="text" class="form-control" name="license_key" id="license_key" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Expiry Date</label>
                        <input type="text" class="form-control date-picker" name="exp_date" id="exp_date" autocomplete="off" />
                    </div>
                    <div class="col-md-2"><br />
                        <button type="button" id="license_add" class="btn btn-light" onclick="return add_license_key()"><i class="ico icon-outline-add-square text-success me-1"></i>Add</button>
                        <button type="button" id="license_cancel_edit" class="btn btn-sm btn-outline-secondary ms-1" onclick="cancel_license_edit()" style="display:none;" title="Cancel edit">&#x2715;</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <div id="licenseKeyMessage" class="text-danger small mb-2" style="display:none;"></div>
                        <table id="lk-table" class="table table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">Sr.No</th>
                                    <th style="width: 57%;">License Key</th>
                                    <th style="width: 20%;">Expiry Date</th>
                                    <th style="width: 15%;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="return save_license_keys()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save & Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">License Excel Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Select File (.csv)</label>
                            <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv, .xls, .xlsx" />
                            <div class="form-text">
                                Supported formats:
                                <a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}" target="_blank">Download sample file</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" onclick="return excel_license_key()">Import</button>
            </div>
        </div>
    </div>
</div>

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
$(document).on("keydown", 'input[name="unitprice[]"], input[name="discount[]"], input[name="serial_no[]"]', function(e) {
    if (e.key === "Enter") {
        e.preventDefault(); // prevent form submit

        let row = $(this).closest("tr"); // get current row
        let name = $(this).attr("name");
        
        if (name === "unitprice[]") {
            row.find('input[name="discount[]"]').focus();
        } else if (name === "discount[]") {
            row.find('input[name="serial_no[]"]').focus();
        }
    }
});
</script>
<script>
$('#sales-return-update').on('keydown', function(e) {
    if (e.key === 'Enter' && !$(e.target).is('textarea')) {
        const $target = $(e.target);
        if ($target.is('input[name="qty[]"]')) {
            const productType = parseInt(String($target.closest('tr').find('input[name="product_type[]"]').first().val() || '').trim(), 10);
            if (productType === 2) {
                return true;
            }
        }
        e.preventDefault();
        return false;
    }
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
          modalElement.addEventListener('shown.bs.modal', function () {
            $('#add_serial_no').focus();
        });
    });
    let currentSerialInput = null;

    function normalizeSerials(text) {
        if (!text) return '';
        const parts = text.replace(/\r/g, '\n').split(/[\n,]+/).map(p => p.trim()).filter(Boolean);
        return parts.join(', ');
    }
    
    $(document).on('click', 'input[name="serial_no[]"]', function () {
        currentSerialInput = $(this);
        const formatted = normalizeSerials(currentSerialInput.val());
        $('#add_serial_no').val(formatted);
        serialNoModal.show();
    });
    function addSerialNo() {
        if (currentSerialInput) {
            const val = normalizeSerials($('#add_serial_no').val());
            currentSerialInput.val(val);
            $('#add_serial_no').val(val);
            serialNoModal.hide();
            currentSerialInput = null;
        }
    }
</script>

<script>
    let srLicenseDrafts = [];

    function showLicenseKeyMessage(message, type = 'danger') {
        const $msg = $('#licenseKeyMessage');
        $msg.removeClass('text-danger text-warning text-success');
        if (!message) {
            $msg.hide();
            return;
        }
        $msg.text(message).addClass(type === 'success' ? 'text-success' : (type === 'warning' ? 'text-warning' : 'text-danger')).show();
    }

    function isLicenseProductType(pt) {
        return parseInt(String(pt == null ? '' : pt).trim(), 10) === 2;
    }

    function parseLineQty($row) {
        const raw = ($row.find('input[name="qty[]"]').val() || '').toString().replace(/,/g, '').trim();
        const qty = parseFloat(raw);
        return isNaN(qty) ? 0 : qty;
    }

    function getLicenseQty() {
        const qty = parseInt($('#license_qty').val(), 10);
        return isNaN(qty) ? 0 : qty;
    }

    function normalizeLicenseDateForStore(value) {
        const raw = (value || '').toString().trim();
        if (!raw || raw === '0000-00-00') return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;

        const parts = raw.replace(/\./g, '/').replace(/-/g, '/').split('/');
        if (parts.length !== 3) return '';
        let [day, month, year] = parts;
        day = day.padStart(2, '0');
        month = month.padStart(2, '0');
        if (year.length === 2) year = '20' + year;
        if (!/^\d{4}$/.test(year)) return '';
        return `${year}-${month}-${day}`;
    }

    function formatLicenseDateForDisplay(value) {
        const ymd = normalizeLicenseDateForStore(value);
        if (!ymd) return '';
        const parts = ymd.split('-');
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    function getActiveLicenseTargetRow(itemId) {
        const $rows = $('#myTable > tbody > tr');
        const rowIndex = parseInt($('#license_row_index').val(), 10);
        if (!isNaN(rowIndex) && rowIndex >= 0 && rowIndex < $rows.length) {
            const $row = $rows.eq(rowIndex);
            if ($row.length && (!itemId || String($row.find('select[name="part_number[]"]').val()) === String(itemId))) {
                return $row;
            }
        }
        const $matches = $rows.filter(function () {
            return String($(this).find('select[name="part_number[]"]').val()) === String(itemId);
        });
        return $matches.length ? $matches.first() : $();
    }

    function getDraftRowId(row, index) {
        return String(row.local_id || row.id || `draft-${index}`);
    }

    function getExistingLicenseKeys() {
        return srLicenseDrafts.map(row => (row.license_key || '').toString().trim().toLowerCase()).filter(Boolean);
    }

    function getCommaSeparatedLicenseKeys(rows) {
        const seen = {};
        return (rows || []).map(row => (row.license_key || '').toString().trim()).filter(key => {
            if (!key) return false;
            const normalized = key.toLowerCase();
            if (seen[normalized]) return false;
            seen[normalized] = true;
            return true;
        });
    }

    function getLicenseKeyTokensFromSerial(serialVal) {
        const seen = {};
        const keys = [];
        (serialVal || '').toString().split(',').forEach(part => {
            const key = part.trim();
            if (!key) return;
            const normalized = key.toLowerCase();
            if (seen[normalized]) return;
            seen[normalized] = true;
            keys.push(key);
        });
        return keys;
    }

    function applyLicenseQtyHighlightForRow($row, keyCountOverride) {
        if (!$row || !$row.length) return;
        const $qty = $row.find('input[name="qty[]"]');
        if (!isLicenseProductType($row.find('input[name="product_type[]"]').first().val())) {
            $qty.css('color', '');
            return;
        }
        const qty = parseLineQty($row);
        const keyCount = (typeof keyCountOverride === 'number' && !isNaN(keyCountOverride))
            ? keyCountOverride
            : getLicenseKeyTokensFromSerial($row.find('input[name="serial_no[]"]').val()).length;
        $qty.css('color', (qty > 0 && keyCount < qty) ? '#dc3545' : '');
    }

    function applyLicenseKeysToSerialInput(itemId, rows) {
        const $targetRow = getActiveLicenseTargetRow(itemId);
        if (!$targetRow.length) return;
        $targetRow.find('input[name="serial_no[]"]').val(getCommaSeparatedLicenseKeys(rows).join(', '));
    }

    function setDraftLicenseRows(rows) {
        srLicenseDrafts = (rows || []).map((row, index) => ({
            local_id: String(row.local_id || row.id || `draft-${index}-${Math.random().toString(36).substr(2, 5)}`),
            id: row.id || null,
            license_key: (row.license_key || '').toString().trim(),
            exp_date: normalizeLicenseDateForStore(row.exp_date || ''),
        }));
        cancel_license_edit();
        renderLicenseRows(srLicenseDrafts);
    }

    function setLicenseAddButtonMode(mode) {
        if (mode === 'update') {
            $('#license_add').html('<i class="ico icon-outline-pen-2 me-1"></i>Update');
            return;
        }
        $('#license_add').html('<i class="ico icon-outline-add-square text-success me-1"></i>Add');
    }

    function updateLicenseAddState() {
        const maxQty = getLicenseQty();
        const currentCount = getExistingLicenseKeys().length;
        $('#license_add').prop('disabled', maxQty <= 0 || currentCount >= maxQty);
        $('#licenseCountSummary').text(`Selected: ${currentCount} of ${maxQty}`);
    }

    function findDraftRowIndex(localId) {
        return srLicenseDrafts.findIndex((row, index) => getDraftRowId(row, index) === localId);
    }

    function renderLicenseRows(rows) {
        let tr = '';
        let serial = 0;
        const seen = {};
        (rows || []).forEach((row, index) => {
            const licenseKey = (row.license_key || '').toString().trim();
            if (!licenseKey) return;
            const normalized = licenseKey.toLowerCase();
            if (seen[normalized]) return;
            seen[normalized] = true;
            serial += 1;
            const rowId = getDraftRowId(row, index);
            tr += `<tr data-local-id="${rowId}">
                <td>${serial}</td>
                <td>${$('<div>').text(licenseKey).html()}</td>
                <td>${formatLicenseDateForDisplay(row.exp_date)}</td>
                <td style="white-space:nowrap;">
                    <a onclick="edit_license_key_mode('${rowId}', this)" class="btn-sm btn-light me-1" title="Edit"><i class="ico icon-outline-pen-2"></i></a>
                    <a onclick="delete_license_key('${rowId}')" class="btn-sm btn-light" title="Delete"><i class="ico icon-outline-trash-bin-trash"></i></a>
                </td>
            </tr>`;
        });
        if (!serial) {
            tr = '<tr><td colspan="4" class="text-center text-muted">No keys added.</td></tr>';
        }
        $('#lk-table tbody').html(tr);
        updateLicenseAddState();
    }

    function edit_license_key_mode(localId, btn) {
        const index = findDraftRowIndex(localId);
        if (index === -1) return;
        const row = srLicenseDrafts[index];
        $('#edit_license_id').val(localId);
        $('#license_key').val(row.license_key).focus();
        $('#exp_date').val(formatLicenseDateForDisplay(row.exp_date));
        setLicenseAddButtonMode('update');
        $('#license_cancel_edit').show();
        $('#lk-table tbody tr').removeClass('table-warning');
        $(btn).closest('tr').addClass('table-warning');
    }

    function cancel_license_edit() {
        $('#edit_license_id').val('');
        $('#license_key').val('');
        $('#exp_date').val('');
        setLicenseAddButtonMode('add');
        $('#license_cancel_edit').hide();
        $('#lk-table tbody tr').removeClass('table-warning');
    }

    function add_license_key() {
        showLicenseKeyMessage('');
        const licenseKey = ($('#license_key').val() || '').toString().trim();
        const expDate = normalizeLicenseDateForStore($('#exp_date').val());
        const maxQty = getLicenseQty();
        const editId = $('#edit_license_id').val();
        const existing = getExistingLicenseKeys();

        if (!licenseKey) {
            showLicenseKeyMessage('Enter a license key.', 'danger');
            $('#license_key').focus();
            return false;
        }

        if (editId) {
            const idx = findDraftRowIndex(editId);
            if (idx === -1) {
                showLicenseKeyMessage('Unable to find selected key for update.', 'danger');
                return false;
            }
            const duplicateAt = existing.indexOf(licenseKey.toLowerCase());
            if (duplicateAt !== -1 && getDraftRowId(srLicenseDrafts[duplicateAt], duplicateAt) !== editId) {
                showLicenseKeyMessage('This license key has already been added.', 'danger');
                return false;
            }
            srLicenseDrafts[idx].license_key = licenseKey;
            srLicenseDrafts[idx].exp_date = expDate;
            cancel_license_edit();
            renderLicenseRows(srLicenseDrafts);
            return false;
        }

        if (existing.length >= maxQty) {
            showLicenseKeyMessage(`Cannot add more than ${maxQty} license keys.`, 'danger');
            return false;
        }
        if (existing.indexOf(licenseKey.toLowerCase()) !== -1) {
            showLicenseKeyMessage('This license key has already been added.', 'danger');
            return false;
        }

        srLicenseDrafts.push({
            local_id: `draft-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`,
            license_key: licenseKey,
            exp_date: expDate,
        });
        $('#license_key').val('');
        $('#exp_date').val('');
        renderLicenseRows(srLicenseDrafts);
        return false;
    }

    function delete_license_key(id) {
        const idx = findDraftRowIndex(id);
        if (idx === -1) return;
        srLicenseDrafts.splice(idx, 1);
        renderLicenseRows(srLicenseDrafts);
    }

    function view_license_key() {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');
        $.ajax({
            url: "{{ URL::to('view-grn-license-key-cart') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                item_id: $('#item_id').val(),
                sales_return_id: $('#sr_id').val(),
                context: 'sr',
            },
            cache: false,
            success: function(dataResult) {
                try {
                    const response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                    setDraftLicenseRows(response.data || []);
                    applyLicenseKeysToSerialInput($('#item_id').val(), response.data || []);
                } catch (e) {
                    showLicenseKeyMessage('Unable to load current license keys.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to load current license keys.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function excel_license_key() {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');

        const maxQty = getLicenseQty();
        const itemId = $('#item_id').val();
        const fileInput = $('#import_file')[0];

        if (!itemId) {
            showLicenseKeyMessage('Select a product before importing license keys.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        if (maxQty <= 0) {
            showLicenseKeyMessage('License quantity must be greater than zero before importing.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            $('#import_file').focus();
            showLicenseKeyMessage('Select a valid CSV or Excel file to import.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        const fileName = fileInput.files[0].name.toLowerCase();
        const allowedExtensions = ['csv', 'xls', 'xlsx'];
        const extension = fileName.split('.').pop();
        if ($.inArray(extension, allowedExtensions) === -1) {
            showLicenseKeyMessage('Unsupported file type. Use .csv, .xls, or .xlsx.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('item_id', itemId);
        formData.append('license_qty', maxQty);
        formData.append('import_file', fileInput.files[0]);
        formData.append('context', 'sr');

        $.ajax({
            url: "{{ URL::to('add-grn-license-key-cart-excel') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                try {
                    const response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                    if (response.error) {
                        showLicenseKeyMessage(response.error, 'danger');
                        return;
                    }

                    const currentKeys = getExistingLicenseKeys();
                    const duplicates = [];
                    (response.data || []).forEach(row => {
                        const key = (row.license_key || '').toString().trim();
                        if (!key) return;
                        if (currentKeys.indexOf(key.toLowerCase()) !== -1) {
                            duplicates.push(key);
                            return;
                        }
                        if (getExistingLicenseKeys().length + 1 > maxQty) {
                            return;
                        }
                        srLicenseDrafts.push({
                            local_id: `draft-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`,
                            license_key: key,
                            exp_date: normalizeLicenseDateForStore(row.exp_date || ''),
                        });
                    });

                    renderLicenseRows(srLicenseDrafts);
                    applyLicenseQtyHighlightForRow(getActiveLicenseTargetRow(itemId), getExistingLicenseKeys().length);
                    $('#license_key').val('');
                    $('#exp_date').val('');
                    $('#import_file').val('');
                    $('#ModalExcelQuote').modal('hide');

                    if (duplicates.length) {
                        showLicenseKeyMessage('Imported keys saved in the draft list. Duplicate entries were skipped: ' + duplicates.join(', '), 'warning');
                    } else {
                        showLicenseKeyMessage('Imported license keys added to the draft list.', 'success');
                    }
                } catch (err) {
                    showLicenseKeyMessage('Unable to import license keys. Please try again.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to import license keys. Please try again.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
        return false;
    }

    function save_license_keys() {
        $("#loading_bg").css("display", "block");
        showLicenseKeyMessage('');
        const itemId = $('#item_id').val();
        const maxQty = getLicenseQty();

        if (!itemId) {
            showLicenseKeyMessage('Select a product before saving license keys.', 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }
        if (srLicenseDrafts.length > maxQty) {
            showLicenseKeyMessage(`Cannot save more than ${maxQty} license keys.`, 'danger');
            $("#loading_bg").css("display", "none");
            return false;
        }

        const rows = srLicenseDrafts.map(row => ({
            license_key: row.license_key,
            exp_date: normalizeLicenseDateForStore(row.exp_date),
        }));

        $.ajax({
            url: "{{ URL::to('add-grn-license-key-cart') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                item_id: itemId,
                license_qty: maxQty,
                rows: JSON.stringify(rows),
                context: 'sr',
            },
            cache: false,
            success: function(dataResult) {
                try {
                    const response = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                    if (response.error) {
                        showLicenseKeyMessage(response.error, 'danger');
                        return;
                    }
                    if (response.duplicate || (response.duplicate_keys && response.duplicate_keys.length)) {
                        const duplicateText = response.message || ('Duplicate license keys were skipped: ' + (response.duplicate_keys || []).join(', '));
                        showLicenseKeyMessage(duplicateText, 'warning');
                        toastr.warning(duplicateText);
                    }
                    setDraftLicenseRows(response.data || []);
                    applyLicenseKeysToSerialInput(itemId, response.data || []);
                    const $target = getActiveLicenseTargetRow(itemId);
                    const savedCount = getCommaSeparatedLicenseKeys(response.data || []).length;
                    const lineQty = parseLineQty($target);
                    applyLicenseQtyHighlightForRow($target, savedCount);
                    if (lineQty > 0 && savedCount < lineQty) {
                        toastr.warning(`All qty license keys are not added. Added ${savedCount} of ${lineQty}.`);
                    }
                    $('#ModalLicenseKey').modal('hide');
                } catch (e) {
                    showLicenseKeyMessage('Unable to save license keys. Please try again.', 'danger');
                }
            },
            error: function() {
                showLicenseKeyMessage('Unable to save license keys. Please try again.', 'danger');
            },
            complete: function() {
                $("#loading_bg").css("display", "none");
            }
        });
        return false;
    }

    function set_license_key(el, e) {
        const key = e.which || e.keyCode;
        if (key !== 13) return true;

        const $row = $(el).closest('tr');
        const pt = $row.find('input[name="product_type[]"]').first().val();
        if (isLicenseProductType(pt)) {
            $('#item_id').val($row.find('select[name="part_number[]"]').val());
            $('#license_row_index').val($('#myTable > tbody > tr').index($row));
            $('#ModalLabelHeading').text($row.find('select[name="part_number[]"] option:selected').text());
            $('#license_qty').val($(el).val());
            $('#btn_ModalLicenseKey').click();
            view_license_key();
            e.preventDefault();
            return false;
        }
        return true;
    }

    $(function() {
        $('#myTable > tbody > tr').each(function() {
            applyLicenseQtyHighlightForRow($(this));
        });
        $(document).on('change', '#myTable tbody input[name="qty[]"]', function() {
            applyLicenseQtyHighlightForRow($(this).closest('tr'));
        });
        $(document).on('change input', '#myTable tbody input[name="serial_no[]"]', function() {
            applyLicenseQtyHighlightForRow($(this).closest('tr'));
        });
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
update_totals();
function getNumber($input) {
    let val = $input.val();
    if (!val) return 0;
    return parseFloat(val.toString().replace(/,/g, '')) || 0;
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
            total_qty           += getNumber($row.find('input[name="qty[]"]'));
            total_price         += getNumber($row.find('input[name="unitprice[]"]'));
            total_value         += getNumber($row.find('input[name="value[]"]'));
            total_discount      += getNumber($row.find('input[name="discount[]"]'));
            total_taxableamount += getNumber($row.find('input[name="taxableamount[]"]'));
            total_vatamount     += getNumber($row.find('input[name="vatamount[]"]'));
            total_totalamount   += getNumber($row.find('input[name="totalamount[]"]'));
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
                $row.find('input[name="qty[]"]').focus();
            
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
    function get_pending_si_list() {
            var id = $("#customer").val();
            alert(id);
            get_cust_details(id);
            get_cust_details_arabic(id);
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
                alert(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            if(dataResult['data'][i].status==3){
                                alert("Customer Information is incompleated! Please Update Customer.");
                                $('#btnSubmit').css('display','none');
                            } else { $('#btnSubmit').css('display',''); }
                            $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');
                            // $('#shipping_name').val(dataResult['data'][i].contcat_person);
                            // $('#shipping_address').val(dataResult['data'][i].address);
                            $('#customer_type').val(dataResult['data'][i].customer_type).trigger('change');
                            $('#sale_type').val(dataResult['data'][i].sale_type).trigger('change');;
                            $('#country').val(dataResult['data'][i].vat_country).trigger('change');;
                            // $('#state').val(dataResult['data'][i].vat_state);
window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;

                            $('#net_vat').val(dataResult['data'][i].vat_percentage);
                            $('.vat').val(dataResult['data'][i].vat_percentage);
                        }                        
                    }
                    else{
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
    function get_cust_details_arabic(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-customer-details-arabic') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                alert(dataResult);
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            $('#company_name_ar').val(dataResult['data'][i].company_name_ar);
                            $('#contact_person_ar').val(dataResult['data'][i].contact_person_ar);
                            $('#address_ar').val(dataResult['data'][i].address_ar);
                        }
                    }
                    else{
                        $('#company_name_ar').val('');
                        $('#contact_person_ar').val('');
                        $('#address_ar').val('');
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
    function get_profo_list(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-proforma-invoice-for-si') }}";
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
                        for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var doc_number = dataResult['data'][i].doc_number;
                                var option = "<option value='" + id + "'>" + doc_number +
                                    "</option>";
                                var innerHtml =
                                    "<input type='radio' onclick='popup_profo_pending(" + id +
                                    ")' id='pending_grn_" + i +
                                    "' name='pending_grn' value='" + doc_number +
                                    "'> <label for='pending_grn_" + i + "'> " + doc_number +
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
    </script>
        <div class="modal side-panel fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Attachments - <label id="att_cust_name"></label></h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="hd_pending_dn_id"/>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Attach File') <span>*</span> </label>
                                    <input class="form-control" type="file" id="att_file" name="att_file" onchange="updateDocName()"/>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('Date') <span>*</span> </label>
                                    <input class="form-control" type="date" id="att_date" name="att_date" value="{{ date('Y-m-d') }}"/>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label class="dynamicslbl">  @lang('File Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="doc_name" name="doc_name" value=""/>
                                </div>
                            </div>
                            <script>
                                function updateDocName() {
                                    var fileInput = document.getElementById('att_file');
                                    var fileName = fileInput.files[0] ? fileInput.files[0].name : '';
                                    var fileNameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                                    document.getElementById('doc_name').value = fileNameWithoutExtension;
                                }
                            </script>
                        </div>
                        
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <table id="att-table" class="table table-hover form-item-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 30%;">Date</th>
                                        <th style="width: 50%;">Attachment</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
                
					<div class="modal-footer">
						<button type="button" class="btn btn-light add-btn ms-2" onclick="add_attachment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Attachment
						</button>
					</div>
            </div>
        </div>
    </div>

<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-sales-invoice-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('siv_id', $('#si_id').val());
        formData.append('att_date', $('#att_date').val()); // Append other form data
        formData.append('att_file', $('#att_file')[0].files[0]); 
        formData.append('doc_name', $('#doc_name').val());


        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        $('#att_cust_name').text($('#customer :selected').text() + " " + $('#doc_number').val());
        var action = "{{ URL::to('view-sales-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                siv_id : $('#si_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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
    function delete_attachment(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-sales-invoice-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                siv_id : $('#si_id').val(),
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger text-white'>Delete</a></td>\
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
    
        <div class="modal side-panel fade" id="adj_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: 500px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bill Wise Adjustment</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-3">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-add-adjestment', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'sales-return-add-adjestment']) }}
                    <input type="hidden" value="{{ $edit->doc_number }}" name="adj_srn_no">
                    <input type="hidden" value="{{ $edit->dn_doc_number }}" name="adj_dn_doc_number">
                    <input type="hidden" value="{{ $edit->doc_date }}" name="edit_adj_doc_date">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <input type="text" id="act_srn_adj_amount" value="{{ ($edit_list->sum('taxableamount')+$edit_list->sum('vatamount')) }}" hidden/>
                                    <input type="text" id="srn_adj_amount" value="{{ ($edit_list->sum('taxableamount')+$edit_list->sum('vatamount')) }}" hidden />

                                    <table class="table table-hover form-item-table" cellspacing="0" width="100%" id="table_adjestment">
                                        <thead>
                                            <tr>
                                                {{-- <th style="width:100px;">@lang('Doc Date')</th>
                                                <th style="width:100px;">@lang('SIV NO')</th>
                                                <th style="width:100px;">@lang('Total')</th>
                                                <th style="width:100px;">@lang('Paid')</th>
                                                <th style="width:100px;">@lang('Balance')</th> --}}

                                                
                                                <th style="width:100px;" class="text-center">@lang('Doc No')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                                <th style="width:100px;" class="text-center">@lang('LPO NO')</th>
                                           
                                                <th style="width:120px;" class="text-center">@lang('Deal ID')</th>
                                                <th style="width:100px;" class="text-end">@lang('Total')</th>
                                                <th style="width:100px;" class="text-end">@lang('Paid')</th>
                                                <th style="width:100px;" class="text-end">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-end">@lang('Adjustment')</th>
                                                <th style="width:200px;" class="text-start">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i=0; @endphp
                                            @if (count($srn_adjestment)>0)
                                            @foreach ($srn_adjestment as $dt)
                                            @php
                                            
                                            $current_doc_adjustment = $editDataAdjustments->where('siv_no', $dt->doc_number)->sum('paid_amount');
                                            if($dt->total_paid_amount==""){$paid_amount = 0;} else {$paid_amount = $dt->total_paid_amount;}
                                            $balance_amount = max($dt->total_amount - $paid_amount, 0);
                                            $previous_paid_amount = max($paid_amount - $current_doc_adjustment, 0);

                                            @endphp
                                            @if($balance_amount > 0 || $current_doc_adjustment > 0)
                                            <tr class="js-sr-adj-row" data-row="{{ $i }}">
                                                <td style="width:100px;" class="text-center">{{ $dt->doc_number }}<input type="hidden" name="adj_siv_no[]" id="adj_siv_no_{{ $i }}" value="{{ $dt->doc_number }}" readonly /></td>
                                                <td style="width:100px;" class="text-center">{{ @App\SysHelper::normalizeToDmy($dt->doc_date) }}<input type="hidden" name="adj_doc_date[]" id="adj_doc_date_{{ $i }}" value="{{ @App\SysHelper::normalizeToDmy($dt->doc_date) }}" readonly /></td>
                                                <td style="width:100px;" class="text-center">{{ $dt->lpo_number }}<input type="hidden" name="lpo_number[]" id="lpo_number_{{ $i }}" value="{{ $dt->lpo_number }}" readonly /></td>
                                               
                                                <td style="width:120px;" class="text-center">{{ empty($dt->deal_id) ? '' : App\SysHelper::get_code_from_dealid($dt->deal_id) }}<input type="hidden" id="deal_id_{{ $i }}" value="{{ empty($dt->deal_id) ? '' : App\SysHelper::get_code_from_dealid($dt->deal_id) }}" readonly /></td>
                                                <td style="width:100px;" class="text-end">{{ @App\SysHelper::com_curr_format($dt->total_amount,2,'.',',') }}<input type="hidden" name="adj_total[]" id="adj_total_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($dt->total_amount,2,'.',',') }}" readonly /></td>
                                                <td style="width:100px;" class="text-end">{{ @App\SysHelper::com_curr_format($previous_paid_amount,2,'.',',') }}<input type="hidden" class="js-sr-adj-previous-paid" id="adj_previous_paid_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($previous_paid_amount,2,'.',',') }}" readonly /></td>
                                                <td style="width:100px;" class="text-end"><span id="adj_balance_display_{{ $i }}">{{ @App\SysHelper::com_curr_format($balance_amount,2,'.',',') }}</span><input type="hidden" name="adj_balance[]" id="adj_balance_{{ $i }}" value="{{ @App\SysHelper::com_curr_format($balance_amount,2,'.',',') }}" data-actual-balance="{{ $balance_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control text-end class_adj_paid" name="adj_paid[]" id="adj_paid_{{ $i }}" value="{{ $current_doc_adjustment > 0 ? @App\SysHelper::com_curr_format($current_doc_adjustment,2,'.',',') : @App\SysHelper::com_curr_format(0,2,'.',',') }}" data-current-amount="{{ $current_doc_adjustment }}" onclick="get_set_amount({{ $i }})"  /></td>
                                                {{-- <td style="width:100px;"><input type="text" class="form-control" name="bi_amount[]" id="bi_amount_{{ $i }}" value="{{ $balance_amount }}" readonly /></td> --}}
                                                <td style="width:100px;"><input type="text" class="form-control" name="narration[]" id="narration_{{ $i }}" value="{{ $dt->narration }}" /></td>
                                            </tr>
                                            @php $i++; @endphp
                                            @endif
                                            {{--  <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ @App\SysHelper::normalizeToDmy($dt->doc_date) }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_dln_no" value="{{ $dt->dln_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_siv_no" value="{{ $dt->siv_no }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total" id="adj_total" value="{{ $dt->total_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control class_adj_paid" name="adj_paid" id="adj_paid" value="{{ $dt->paid_amount }}" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance" id="adj_balance" value="{{ $dt->balance_amount }}" readonly /></td>
                                            </tr>  --}}
                                            @endforeach
                                            <?php /*
                                            @else
                                            <tr>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_doc_date" value="{{ $edit->doc_date }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_dln_no" value="{{ $edit->dn_doc_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_siv_no" value="{{ $edit->si_doc_number }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_total" id="adj_total" value="{{ $invoice_amount }}" readonly /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_paid" id="adj_paid" value="" onchange="get_set_amount()" required /></td>
                                                <td style="width:100px;"><input type="text" class="form-control" name="adj_balance" id="adj_balance" value="" readonly /></td>
                                            </tr>
                                            */ ?>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                              
                                                <th></th>
                                                <th></th>
                                                <th class="text-end"><label id="footer_total" /></th>
                                                <th class="text-end"><label id="footer_paid" /></th>
                                                <th class="text-end"><label id="footer_balance" /></th>
                                                <th class="text-end"><label id="footer_adjustment" /></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            function parseSrAdjustmentAmount(value) {
                                var amount = parseFloat(String(value || '0').replace(/,/g, ''));
                                return isNaN(amount) ? 0 : amount;
                            }

                            function formatSrAdjustmentAmount(value) {
                                if (typeof formatAmount === 'function') {
                                    return formatAmount(parseSrAdjustmentAmount(value));
                                }

                                return parseSrAdjustmentAmount(value).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }

                            function getSrAdjustmentRowBalance(id) {
                                var $balance = $('#adj_balance_' + id);
                                if ($balance.data('actual-balance') === undefined) {
                                    $balance.data('actual-balance', parseSrAdjustmentAmount($balance.val()));
                                }
                                return parseSrAdjustmentAmount($balance.data('actual-balance'));
                            }

                            function getSrAdjustmentCurrentAmount(id) {
                                return parseSrAdjustmentAmount($('#adj_paid_' + id).data('current-amount'));
                            }

                            function updateSrAdjustmentTotals() {
                                var total = 0;
                                var balance = 0;
                                var paid = 0;
                                var adjustment = 0;

                                $('#table_adjestment tbody tr').each(function () {
                                    total += parseSrAdjustmentAmount($(this).find('input[name="adj_total[]"]').val());
                                    balance += parseSrAdjustmentAmount($(this).find('input[name="adj_balance[]"]').val());
                                    paid += parseSrAdjustmentAmount($(this).find('.js-sr-adj-previous-paid').val());
                                    adjustment += parseSrAdjustmentAmount($(this).find('input[name="adj_paid[]"]').val());
                                });

                                $('#footer_total').text(formatSrAdjustmentAmount(total));
                                $('#footer_paid').text(formatSrAdjustmentAmount(paid));
                                $('#footer_balance').text(formatSrAdjustmentAmount(balance));
                                $('#footer_adjustment').text(formatSrAdjustmentAmount(adjustment));
                            }

                            $(document).on('click', '#table_adjestment tbody tr.js-sr-adj-row', function (event) {
                                if ($(event.target).is('input, textarea, select, button, a, label')) {
                                    return;
                                }

                                var id = $(this).data('row');
                                get_set_amount(id);
                                $('#adj_paid_' + id).trigger('focus');
                            });

                            $(document).on('input', '#table_adjestment .class_adj_paid', function () {
                                var id = ($(this).attr('id') || '').replace('adj_paid_', '');
                                var paid = parseSrAdjustmentAmount($(this).val());
                                var available = getSrAdjustmentRowBalance(id) + getSrAdjustmentCurrentAmount(id);

                                if (paid > available) {
                                    paid = available;
                                    $(this).val(formatSrAdjustmentAmount(paid));
                                }

                                $('#adj_balance_' + id).val(formatSrAdjustmentAmount(Math.max(available - paid, 0)));
                                $('#adj_balance_display_' + id).text(formatSrAdjustmentAmount(Math.max(available - paid, 0)));
                                updateSrAdjustmentTotals();
                            });

                            $(document).on('blur', '#table_adjestment .class_adj_paid', function () {
                                $(this).val(formatSrAdjustmentAmount($(this).val()));
                                updateSrAdjustmentTotals();
                            });

                            function get_set_amount(id)
                            {
                                if (id === undefined || id === null || id === '') {
                                    updateSrAdjustmentTotals();
                                    return;
                                }

                                set_adjestment(id);
                                var adj_total = getSrAdjustmentRowBalance(id) + getSrAdjustmentCurrentAmount(id);
                                var adj_paid = parseSrAdjustmentAmount($('#adj_paid_'+id).val());
                                $('#adj_balance_'+id).val(formatSrAdjustmentAmount(Math.max(adj_total - adj_paid, 0)));
                                $('#adj_balance_display_'+id).text(formatSrAdjustmentAmount(Math.max(adj_total - adj_paid, 0)));
                                updateSrAdjustmentTotals();
                            }

                            function set_adjestment(id){
                                var sum = parseSrAdjustmentAmount($('#act_srn_adj_amount').val());
                                var numItems = $('.class_adj_paid').length;
                                var adj=0;
                                for(i=0; i < numItems; i++){
                                    if(i!=id){
                                        adj += parseSrAdjustmentAmount($('#adj_paid_'+i).val());
                                    }
                                }
                                var adj2 = sum - adj;
                                

                                if(adj2 > 0){
                                    $('#srn_adj_amount').val(adj2);
                                }
                                else { $('#srn_adj_amount').val(0); }

                                var adj3 = parseSrAdjustmentAmount($('#srn_adj_amount').val());

                                if(adj3 > 0){
                                    var adj_total = getSrAdjustmentRowBalance(id) + getSrAdjustmentCurrentAmount(id);
                                    if(adj3 >= adj_total){
                                        $('#adj_paid_'+id).val(formatSrAdjustmentAmount(adj_total));
                                    }
                                    else{
                                        $('#adj_paid_'+id).val(formatSrAdjustmentAmount(adj3));
                                    }
                                } else {
                                    $('#adj_paid_'+id).val(formatSrAdjustmentAmount(0));
                                }
                            }

                            updateSrAdjustmentTotals();

                        </script>

                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-light add-btn">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    {{-- popup --}}


<script>
function set_adjust(amt,id) {
    let maxAdjustable = parseFloat($("input[name='adj_siv_amount_actual']").val());
    let currentAdjusted = 0;

    // Sum up all currently adjusted values
    $("input[id^='set_amt_']").each(function () {
        let val = parseFloat($(this).val());
        if (!isNaN(val)) {
            currentAdjusted += val;
        }
    });

    let remaining = maxAdjustable - currentAdjusted;

    if (remaining <= 0) {
        alert("No more amount left to adjust.");
        return;
    }

    // Check how much is available for this line
    let adjustAmount = parseFloat(amt);
    if (adjustAmount > remaining) {
        adjustAmount = remaining;
    }

    $('#set_amt_' + id).val(adjustAmount);

    // Optional: update hidden adjusted total
    $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted + adjustAmount);
}
</script>

<!-- Modal Change Currancy-->
        <div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Currancy</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currency as $value)
                                        @if($edit->currency == $value->id)
                                            <option value="{{ @$value->id }}" >{{ @$value->code }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy To</label>
                                <select class="form-control" name="to_currency_id" id="to_currency_id" required onchange="set_rate()">
                                    <option value="">Select</option>
                                    @foreach ($currencylist2 as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                                @foreach ($currencylist2 as $value)
                                    <input type="hidden" id="rate_{{ @$value->id }}" name="rate_{{ @$value->id }}" value="{{ @$value->rate }}" />
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Default Currency Conversion Rate</label>
                                <input type="text" class="form-control" id="to_currency_rate" name="to_currency_rate" required />
                            </div>
                        </div>
                        <script>
                            function set_rate(){
                                var id = $('#to_currency_id').val();
                                var rate = $('#rate_'+id).val();

                                $('#to_currency_rate').val(rate);
                            }

                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="cur_sr_id" value="{{ @$edit->id }}"/>
                    <input type="hidden" name="cur_sr_doc_no" value="{{ @$edit->doc_number }}"/>
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Change
						</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Change Currancy-->

    <script>


    $(document).ready(function() {


     $(document).on("change", "#shipping_supplier", function() {
            console.log("changed");
            var id = $("#shipping_supplier").val();
            get_shipping_supplier_detail2(id);
        });

        function get_shipping_supplier_detail2(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-chartofaccounts-info') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    console.log(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#shipping_name").val(dataResult['data'][i].contact_person);
                            //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                            // $("#shipping_address_1").val(dataResult['data'][i].address + '\n' + dataResult['data'][i].address2);
                            $("#shipping_address_1").val(dataResult['data'][i].shipping_address);
                            $("#shipping_email").val(dataResult['data'][i].email);
                            $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                        }
                    } else {
                        $("#shipping_name").val("");
                        $("#shipping_address_1").val("");
                        $("#shipping_email").val("");
                        $("#shipping_contact_no").val("");
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function get_shipping_supplier_detail(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-chartofaccounts-info') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    console.log(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#shipping_name").val(dataResult['data'][i].customer_salutation +
                                '. ' + dataResult['data'][i].first_name + ' ' + dataResult[
                                    'data'][i].last_name);
                            //$("#shipping_name").val(dataResult['data'][i].contcat_person);
                            $("#shipping_address_1").val(dataResult['data'][i].address + '\n' +
                                dataResult['data'][i].address2);
                            $("#shipping_email").val(dataResult['data'][i].email);
                            $("#shipping_contact_no").val(dataResult['data'][i].contcat_number);
                        }
                    } else {
                        $("#shipping_name").val("");
                        $("#shipping_address_1").val("");
                        $("#shipping_email").val("");
                        $("#shipping_contact_no").val("");
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

});
</script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

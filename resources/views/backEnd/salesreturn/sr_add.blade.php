    <?php try { ?>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-return-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' =>  'sales-return-store']) }}
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                <input type="hidden" id="net_vat" name="net_vat">
        
 <?php
                                                        $invno=@App\SysHelper::get_new_code('sys_sales_return','SR','doc_number');
                                                    ?>


    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            New ({{isset($edit) ? (!empty(@$edit->doc_number) ? @$edit->doc_number : old('doc_number')) : $invno }})
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
                    <li><button type="button" class="dropdown-item" onclick="add_set_adjestment()"><i class="ico icon-outline-calculator-minimalistic text-danger"></i> Adjustment</button></li>
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
                                                <select class="form-control js-account-select" name="customer" id="customer" required onchange="get_pending_si_list(this)">
                                                <option value=""></option>
                                                {{-- @foreach ($vendors as $value)
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->vendor_id) ? (@$edit->vendor_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                    {{ @$value->account_name }}
                                                </option>
                                                @endforeach --}}
                                            </select>
                                            <input type="hidden" id="selected_customer_id" value="">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">
                              

                                                    <input class="form-control" type="text" name="doc_number" autocomplete="off" id="doc_number" value="{{ isset($edit) ? (!empty(@$edit->doc_number) ? @$edit->doc_number : old('doc_number')) : $invno }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Invoice Date</label>
                                            <div class="form-group">
                                         @php
                                            $value = date('d/m/Y');
                                            if (isset($edit) && !empty($edit->doc_date)) {
                                                $value = date('d/m/Y', strtotime($edit->doc_date));
                                            }
                                        @endphp

                                                    <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                                                        name="doc_date" value="{{ @$value }}" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group"><?php
                                                    $currency1=1;
                                                    if(session('logged_session_data.company_id')==8){
                                                        $currency1=2;
                                                    }
                                                ?>
                                                <select class="form-control js-example-basic-single" name="currency" id="currency">
                                                    {{-- <option data-display="@lang('Currency') *" value="">@lang('Currency') *</option> --}}

                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if($company->currency_id == $value->id) selected @endif>
                                                            {{ @$value->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By</label>
                                            <div class="form-group">
                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->number) ? @$edit->number : old('createdby')) : Auth::user()->full_name }}" readonly>
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
                        <div id="plist" style="width: 100%; height: 180px; border: solid 1px #dfe1d7; border-radius: 5px; padding: 5px 5px; overflow-y: scroll;">
                        </div>
                        <a id="addSRPending"></a>
                        <input type="hidden" id="dn_id" name="dn_id">
                        <input type="hidden" id="hd_pending_dn_id" name="hd_pending_dn_id">
                        <input type="hidden" id="vat_percentage" name="vat_percentage" value="5">
                    </div>
                </div>    
                <div class="col-10 mb-2">
                    <div class="row">
                           <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('SIV') @lang('Number')<span>*</span></label>
                                                    <input class="form-control" type="text" name="si_doc_number" autocomplete="off" id="si_doc_number" value="{{ isset($edit) ? (!empty(@$edit->si_doc_number) ? @$edit->si_doc_number : old('si_doc_number')) : '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">SIV Date</label>
                                                  @php
    $value = date('d/m/Y');
    if (isset($edit) && !empty($edit->si_doc_date)) {
        $value = date('d/m/Y', strtotime($edit->si_doc_date));
    }
@endphp

                                                    <input class="form-control date-picker" id="si_doc_date" type="text" autocomplete="off" name="si_doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('DLN') @lang('Number')<span>*</span></label>
                                                    <input class="form-control" type="text" name="dn_doc_number" autocomplete="off" id="dn_doc_number" value="{{ isset($edit) ? (!empty(@$edit->si_doc_number) ? @$edit->si_doc_number : old('si_doc_number')) : '' }}" >
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">DLN Date</label>
                                                   @php
                                                        $value = date('d/m/Y'); 
                                                        if (isset($edit) && !empty($edit->doc_date)) {
                                                            $value = date('d/m/Y', strtotime($edit->doc_date));
                                                        }
                                                    @endphp

                                                    <input class="form-control date-picker" id="dn_doc_date" type="text" autocomplete="off"
                                                        name="dn_doc_date" value="{{ @$value }}" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">LPO/Reference No<span>*</span></label>
                                                    <input class="form-control" type="text" name="reference_no" autocomplete="off" id="reference_no" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">LPO/Reference Date<span>*</span></label>
                                                    <input class="form-control date-picker" type="text" name="reference_date" autocomplete="off" id="reference_date" value="{{ date('d/m/Y') }}" required>
                                                </div>
                                            </div>

                                             <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Payment Terms')<span>*</span></label>
                                                    <div class="form-group">
                                                         <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms"  required>
                                                        <option value="" ></option>
                                                        @foreach($paymentterms as $value)
                                                             <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->payment_terms)? @$edit->payment_terms==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                            
                                         

                                            <div class="col-lg-3 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Delivery Terms')<span>*</span></label>
                                                    <input class="form-control" type="text" name="delivery_terms" autocomplete="off" id="delivery_terms" value="{{ isset($edit) ? (!empty(@$edit->delivery_terms) ? @$edit->delivery_terms : old('delivery_terms')) : 'Ex-Dubai' }}">
                                                </div>
                                            </div>
                                           
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Sales Person Name')<span>*</span></label>
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
                                    
                                @endphp

                                <select class="form-control js-example-basic-single" name="ref_supplier_id[]"
                                    id="ref_supplier_id" multiple>
                                    <option value="">-Select-</option>

                                    @foreach ($supplier_reference_list as $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->account_name }}
                                            @if(App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                ({{ $value->account_code }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input class="form-control" type="hidden" name="supplier_name" autocomplete="off"
                                    id="supplier_name" value="TAKEN FROM STOCK" required>
                            </div>
                        </div>
                         <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Printed Invoice Number')<span></span></label>
                                                    <input class="form-control" type="text" name="printed_invoice_number" autocomplete="off" id="printed_invoice_number" value="{{ isset($edit) ? (!empty(@$edit->printed_invoice_number) ? @$edit->printed_invoice_number : old('printed_invoice_number')) : '' }}">
                                                </div>
                                            </div>
                                           
                                            
                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Deal ID<span>*</span></label>
                                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="" required>
                                                </div>
                                            </div>
                                            <!-- <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Supplier Name<span>*</span></label>
                                                    <input class="form-control" type="text" name="supplier_name" autocomplete="off" id="supplier_name" value="" required>
                                                </div>
                                            </div> -->
                                          
                                            <div class="col-lg-3 mb-2">
                                          
                                            <label class="form-label" for="">Attachment</label>
                                            <input type="file" class="form-control" name="doc" id="doc">

               
                                            </div><div class="col-2">
                                                <label class="form-label">Credit Note</label>
                                                <div class="form-group">
                               

                                     <div class="form-group">
                                  
                                         <select class="form-control js-example-basic-single" required name="credit_note" id="credit_note">
                                       
                           
                                            <option value="CN">Credit Note</option>
                                            <option value="SR" selected>Sales Return</option>
                                  
                                        
                                    </select>
                                </div>

                                                </div>
                                            </div>
                                            <div class="col mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Narration<span></span></label>
                                                    <input class="form-control" type="text" name="narration" autocomplete="off" id="narration" value="">
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
                                    {{-- @php $s = @App\SysHelper::internal_transfer_customer_id(@$value->id, session('logged_session_data.company_id')); @endphp --}}
                                    
                                    <option value="{{ @$value->id }}" 
                                        
                                        >
                                        {{ @$value->account_name }} 
                                        @if(@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                        ({{ @$value->account_code }})
                                        @endif
                                       
                                    </option>
                                @endforeach
                            </select>

                            

                            
                        </div>
                       <script>
    $(document).ready(function () {
        setTimeout(function () {
            $("#shipping_supplier").trigger("change");
        }, 300);
    });
</script>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Name</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_name" id="shipping_name"
                                value="{{ session('logged_session_data.full_name') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact Email</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_email" id="shipping_email"
                                value="{{ session('logged_session_data.email') }}" />
                        </div>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Contact No</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_contact_no"
                                id="shipping_contact_no" value="{{ session('logged_session_data.mobile') }}" />
                        </div>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Shipping Address</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="shipping_address_1"
                                id="shipping_address_1" />
                        </div>
                    </div>
                                            <!-- <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Name') <span></span></label>
                                        <input type="text" class="form-control" value="" id="shipping_name" name="shipping_name">
                                    </div>
                                </div>
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Address') <span></span></label>
                                        <input type="text" class="form-control" value="" id="shipping_address" name="shipping_address">
                                    </div>
                                </div> -->
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
                                                        @if (isset($edit)) @if (@$edit->customer_country == $value->id) selected @endif
                                                        @endif
                                                        <?php } catch (\Throwable $th) {} ?>
                                                        >{{ @$value->name }} </option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Customer State') <span></span></label>
    
                                            <div id="sectionStateDiv">
                                                <select class="form-control js-example-basic-single" name="customer_state" id="state">
                                                    <option  value=""></option>
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

                                     <div class="col-2">
                    <label class="form-label">VAT %</label>
                    <div class="form-group">

                        <input class="form-control" type="number" name="vat_percent" id="vat_percent" value="">
                    </div>
                </div>

                <div class="col-2">
                    <label class="form-label">VAT Number</label>
                    <div class="form-group">

                        <input class="form-control" type="number" name="vat_number" id="vat_number" value="">
                    </div>
                </div>


                                         <div class="col-lg-2 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Customer Type')</label>
                                            <div class="form-group">
                                                 <select class="form-control js-example-basic-single" name="customer_type" id="customer_type">
                                                <option value="0" ></option>
                                                @foreach($customertype as $value)
                                                        <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->customer_type)? @$edit->customer_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                                @endforeach
                                            </select>
                                           
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Sale Type')</label>
                                            <div class="form-group">
                                                  <select class="form-control js-example-basic-single" name="sale_type" id="sale_type">
                                                <option value="0" ></option>
                                                @foreach($saletype as $value)
                                                        <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->sale_type)? @$edit->sale_type==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
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
                                            <label class="form-label">@lang('End User Name') <span></span></label>
                                            <input type="text" class="form-control" name="end_user_name" id="end_user_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->end_user_name) ? @$edit->end_user_name : '') : old('end_user_name') }}" />
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Contact Person Name') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_name" id="contact_person_name" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_name) ? @$edit->contact_person_name : '') : old('contact_person_name') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Contact Person Email') <span></span></label>
                                            <input type="text" class="form-control" name="contact_person_email" id="contact_person_email" autocomplete="off" value="{{ isset($edit) ? (!empty(@$edit->contact_person_email) ? @$edit->contact_person_email : '') : old('contact_person_email') }}">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-2">
                                        <div class="input-effect">
                                            <label class="form-label">@lang('Contact Person No') <span></span></label>
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
                                            <th class="resizable text-center" width="50px">@lang('Description')<div class="resizer"></div></th>
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
                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="1" /></td>
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
                                            <td><input class="form-control text-center" data-enter-skip type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onkeypress="return set_license_key(this, event)"></td>
                                            <td><input class="form-control text-end" type="text" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
                                            <td><input class="form-control text-end" type="text" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="text" step="Any" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)" onblur="formatCurrency(this)"></td>
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
$('#sales-return-store').on('keydown', function(e) {
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
    
{{-- Models  --}}



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
                                    text = item.account_name + " (" + item.account_code + ")";
                                } else {
                                    text = item.account_name;  // no code
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

    $(document).on('select2:select change', '#customer', function (event) {
        var customerId = $(this).val();

        if (!customerId && event.params && event.params.data && event.params.data.id) {
            customerId = event.params.data.id;
        }

        $('#selected_customer_id, #adj_customer_id').val(customerId || '');
        $(this).attr('data-selected-customer-id', customerId || '');
    });

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
    function get_pending_si_list(el) {
        var id = $(el).val();
        get_dn_list(id);
        get_cust_details(id);
    }
    // function get_pending_si_list(id) {
    //     alert(id);
    //     var id = $("#customer").val();
    //     get_dn_list(id);
    //     get_cust_details(id);
    // }
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
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){

                            // $('#shipping_name').val(dataResult['data'][i].contcat_person);
                            // $('#shipping_address').val(dataResult['data'][i].address);
                            $('#customer_type').val(dataResult['data'][i].customer_type).trigger('change');
                            $('#sale_type').val(dataResult['data'][i].sale_type).trigger('change');
                            $('#country').val(dataResult['data'][i].vat_country).trigger('change');
                        $('#shipping_supplier').val(dataResult['data'][i].account_id).trigger('change');

                            // $('#state').val(dataResult['data'][i].vat_state).trigger('change');
                        window.SELECTED_STATE_ID = dataResult['data'][i].vat_state;

                            $('#sales_man').val(dataResult['data'][i].sales_person).trigger('change');
                            $('#net_vat').val(dataResult['data'][i].vat_percentage);
                        $('#vat_percent').val(dataResult['data'][i].vat_percentage);
                        $('#vat_number').val(dataResult['data'][i].vat_number);
                            
                            $('#tax').val(dataResult['data'][i].vat_percentage);
                            
                        }
                    }
                    else{
                        $('#shipping_name').val();
                        $('#shipping_address').val();
                        $('#customer_type').val();
                        $('#sale_type').val();
                        $('#country').val();
                        $('#state').val();
                        $('#net_vat').val();
                        $('#tax').val();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
    
    function get_dn_list(id) {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-si-list') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                $("#plist").empty();
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
                                "<input type='radio' onclick='popup_dn_pending(" + id +
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
                    var innerHtml ="<input type='radio' onclick='without_si(0)' id='pending_po_0' name='pending_po' value='0'> <label for='pending_po_0'> Without SI</label><br />";
                    $("#plist").append(innerHtml);
                    $("#loading_bg").css("display", "none");
            }
        });
    }

    function popup_dn_pending(id) {
        $("#loading_bg").css("display", "block");
        $("#hd_pending_dn_id").val(id);
        $("#dn_id").val(id);
        document.getElementById('addSRPending').click();
        get_adjestments(id);
        $("#loading_bg").css("display", "none");
    }
    
    function without_si(id) {
        $("#loading_bg").css("display", "block");

        $("#dn_id").val(id);
        $("#table_id").css("display", "");

        $("#loading_bg").css("display", "none");
    }

    function adjestments() {
        $("#loading_bg").css("display", "block");
        var id = $("#customer").val();
        get_adjestments(id);
        $("#adjestments_click").click();
        $("#loading_bg").css("display", "none");
    }
    
    function get_adjestments(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-sales-return-adjestment-list') }}";
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
                var tblrow="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){

                            var paid_amount = dataResult['data'][i].paid_amount;
                            if(paid_amount == null){paid_amount=0;}
                            var balance_amount = dataResult['data'][i].total_amount-Number(paid_amount);

                       /* tblrow += "<tr>";
                        tblrow += "<td><input type='text' class='form-control' name='adj_siv_no[]' id='adj_siv_no_"+ i +"' value='"+ dataResult['data'][i].doc_number +"' readonly /></td>";
                        tblrow += "<td><input type='text' class='form-control' name='adj_doc_date[]' id='adj_doc_date_"+ i +"' value='"+ dataResult['data'][i].doc_date +"' readonly /></td>";                        
                        tblrow += "<td><input type='text' class='form-control' name='adj_total[]' id='adj_total_"+ i +"' value='"+ dataResult['data'][i].total_amount +"' readonly /></td>";
                        tblrow += "<td><input type='text' class='form-control' name='adj_balance[]' id='adj_balance_"+ i +"' value='"+ balance_amount +"' readonly /></td>";
                        
                        if(paid_amount == 0){
                            tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required/></td>";
                            } else {
                                if(dataResult['data'][i].adj_status == 5){                                            
                                    tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required/></td>";
                                } else {
                                tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid2[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required readonly /></td><input type='hidden' name='adj_paid[]' value='0'/>";
                                }
                            }

                        {{--  tblrow += "<td><input type='text' class='form-control class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ paid_amount +"' onclick='get_set_amount("+ i +")' required /></td>";  --}}

                        tblrow += "</tr>";*/


                        }
                        
                        $('#table_adjestment tbody').empty();
                        $("#table_adjestment tbody").append(tblrow); 

                    }
                    else{
                        $('#table_adjestment tbody').empty();

                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
    function getSrAdjustmentCustomerId() {
        var customerId = $('#customer').val()
            || $('#selected_customer_id').val()
            || $('#adj_customer_id').val()
            || $('#customer').attr('data-selected-customer-id');

        if (!customerId && $('#customer').data('select2')) {
            var selectedCustomer = $('#customer').select2('data');
            if (selectedCustomer.length > 0) {
                customerId = selectedCustomer[0].id;
            }
        }

        if (!customerId) {
            var selectedOptionValue = $('#customer option:selected').val();
            customerId = selectedOptionValue || '';
        }

        $('#selected_customer_id, #adj_customer_id').val(customerId || '');

        return customerId || '';
    }

    function get_adjestments_add(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-sales-return-adjestment-list-add') }}";
        var customerId = id || getSrAdjustmentCustomerId();
        $('#adj_customer_id').val(customerId);

        if (!customerId) {
            $("#loading_bg").css("display", "none");
            alert('Please select customer before adjustment.');
            return;
        }

        $.ajax({
            url: action,
            type: "get",
            data: {
                _token: '{{ csrf_token() }}',
                id: customerId,
                customer: customerId,
                customer_id: customerId,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var invoiceRows = Array.isArray(dataResult['data']) ? dataResult['data'] : [];
                var unadjustedRows = Array.isArray(dataResult['positive_unadjusted']) ? dataResult['positive_unadjusted'] : [];
                var j = 0;
                var invoiceHtml = "";
                var unadjustedHtml = "";

                for (var i = 0; i < invoiceRows.length; i++) {
                    var invoiceRow = invoiceRows[i];
                    var paid_amount = parseSrAdjustmentAmount(invoiceRow.total_paid_amount);
                    var total_amount = parseSrAdjustmentAmount(invoiceRow.total_amount);
                    var balance_amount = total_amount - paid_amount;
                    if (balance_amount <= 0) {
                        continue;
                    }

                    invoiceHtml += "<tr class='js-sr-adj-row' data-row='"+ j +"'>";
                    invoiceHtml += "<td class='text-center'>"+ escapeSrAdjustmentValue(invoiceRow.doc_number) +"<input type='hidden' name='adj_siv_no[]' id='adj_siv_no_"+ j +"' value='"+ escapeSrAdjustmentValue(invoiceRow.doc_number) +"' readonly /></td>";
                    invoiceHtml += "<td class='text-center'>"+ formatSrAdjustmentDate(invoiceRow.doc_date) +"<input type='hidden' name='adj_doc_date[]' id='adj_doc_date_"+ j +"' value='"+ formatSrAdjustmentDate(invoiceRow.doc_date) +"' readonly /></td>";
                    invoiceHtml += "<td class='text-center'>"+ escapeSrAdjustmentValue(invoiceRow.lpo_number) +"<input type='hidden' name='adj_lpo_number[]' id='adj_lpo_number_"+ j +"' value='"+ escapeSrAdjustmentValue(invoiceRow.lpo_number) +"' readonly /></td>";
                    invoiceHtml += "<td class='text-end'>"+ formatSrAdjustmentAmount(total_amount) +"<input type='hidden' name='adj_total[]' id='adj_total_"+ j +"' value='"+ formatSrAdjustmentAmount(total_amount) +"' readonly /></td>";
                    invoiceHtml += "<td class='text-end'>"+ formatSrAdjustmentAmount(paid_amount) +"<input type='hidden' class='js-sr-adj-previous-paid' id='adj_previous_paid_"+ j +"' value='"+ formatSrAdjustmentAmount(paid_amount) +"' readonly /></td>";
                    invoiceHtml += "<td class='text-end'><span id='adj_balance_display_"+ j +"'>"+ formatSrAdjustmentAmount(balance_amount) +"</span><input type='hidden' name='adj_balance[]' id='adj_balance_"+ j +"' value='"+ formatSrAdjustmentAmount(balance_amount) +"' data-actual-balance='"+ balance_amount +"' readonly /></td>";
                    invoiceHtml += "<td><input type='text' class='form-control text-end class_adj_paid' name='adj_paid[]' id='adj_paid_"+ j +"' value='"+ formatSrAdjustmentAmount(0) +"' data-current-amount='0' onclick='get_set_amount("+ j +")' required /></td>";
                    invoiceHtml += "<td><input type='text' class='form-control' name='adj_narration[]' id='adj_narration_"+ j +"' value='"+ escapeSrAdjustmentValue(invoiceRow.narration)+"' /></td>";
                    invoiceHtml += "</tr>";
                    j++;
                }

                for (var k = 0; k < unadjustedRows.length; k++) {
                    var unadjustedRow = unadjustedRows[k];
                    var unadjustedPaid = parseSrAdjustmentAmount(unadjustedRow.paid);
                    var unadjustedTotal = parseSrAdjustmentAmount(unadjustedRow.total);
                    var unadjustedBalance = parseSrAdjustmentAmount(unadjustedRow.balance);
                    var unadjustedCurrent = parseSrAdjustmentAmount(unadjustedRow.bi_amount);
                    var rowCurrentValue = unadjustedCurrent > 0 ? unadjustedCurrent : 0;

                    unadjustedHtml += "<tr class='js-sr-adj-row' data-row='"+ j +"'>";
                    unadjustedHtml += "<td class='text-center'>"+ escapeSrAdjustmentValue(unadjustedRow.doc_number) +"<input type='hidden' name='adj_siv_no[]' id='adj_siv_no_"+ j +"' value='"+ escapeSrAdjustmentValue(unadjustedRow.doc_number) +"' readonly /></td>";
                    unadjustedHtml += "<td class='text-center'>"+ formatSrAdjustmentDate(unadjustedRow.doc_date) +"<input type='hidden' name='adj_doc_date[]' id='adj_doc_date_"+ j +"' value='"+ formatSrAdjustmentDate(unadjustedRow.doc_date) +"' readonly /></td>";
                    unadjustedHtml += "<td class='text-center'>"+ escapeSrAdjustmentValue(unadjustedRow.lpo_number) +"<input type='hidden' name='adj_lpo_number[]' id='adj_lpo_number_"+ j +"' value='"+ escapeSrAdjustmentValue(unadjustedRow.lpo_number) +"' readonly /></td>";
                    unadjustedHtml += "<td class='text-end'>"+ formatSrAdjustmentAmount(unadjustedTotal) +"<input type='hidden' name='adj_total[]' id='adj_total_"+ j +"' value='"+ formatSrAdjustmentAmount(unadjustedTotal) +"' readonly /></td>";
                    unadjustedHtml += "<td class='text-end'>"+ formatSrAdjustmentAmount(unadjustedPaid) +"<input type='hidden' class='js-sr-adj-previous-paid' id='adj_previous_paid_"+ j +"' value='"+ formatSrAdjustmentAmount(unadjustedPaid) +"' readonly /></td>";
                    unadjustedHtml += "<td class='text-end'><span id='adj_balance_display_"+ j +"'>"+ formatSrAdjustmentAmount(unadjustedBalance) +"</span><input type='hidden' name='adj_balance[]' id='adj_balance_"+ j +"' value='"+ formatSrAdjustmentAmount(unadjustedBalance) +"' data-actual-balance='"+ unadjustedBalance +"' readonly /></td>";
                    unadjustedHtml += "<td><input type='text' class='form-control text-end class_adj_paid' name='adj_paid[]' id='adj_paid_"+ j +"' value='"+ formatSrAdjustmentAmount(rowCurrentValue) +"' data-current-amount='"+ rowCurrentValue +"' onclick='get_set_amount("+ j +")' required /></td>";
                    unadjustedHtml += "<td><input type='text' class='form-control' name='adj_narration[]' id='adj_narration_"+ j +"' value='"+ escapeSrAdjustmentValue(unadjustedRow.remarks || '')+"' /></td>";
                    unadjustedHtml += "</tr>";
                    j++;
                }

                $('#table_adjestment tbody').empty().append(invoiceHtml);
                if (unadjustedHtml !== "") {
                    $('#table_adjestment_unadjusted tbody').empty().append(unadjustedHtml);
                } else {
                    $('#table_adjestment_unadjusted tbody').empty().append("<tr class='text-muted'><td colspan='8' class='text-center'>No positive unadjusted balance found</td></tr>");
                }
                updateSrAdjustmentTotals();
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function save_adjestments() {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('sales-return-add-adjestment3') }}";

        var id = getSrAdjustmentCustomerId();
        $('#adj_customer_id').val(id);
        var adj_srn_no = $('#adj_srn_no').val();

        if (!id) {
            $("#loading_bg").css("display", "none");
            alert('Please select customer before adjustment.');
            return;
        }

        var adj_doc_date = [];
        $('input[name="adj_doc_date[]"]').each(function() { adj_doc_date.push($(this).val()); });

        var adj_siv_no = [];
        $('input[name="adj_siv_no[]"]').each(function() { adj_siv_no.push($(this).val()); });

        var adj_total = [];
        $('input[name="adj_total[]"]').each(function() { adj_total.push($(this).val()); });

        var adj_paid = [];
        $('input[name="adj_paid[]"]').each(function() { adj_paid.push($(this).val()); });

        var adj_balance = [];
        $('input[name="adj_balance[]"]').each(function() { adj_balance.push($(this).val()); });
        
        var adj_lpo_number = [];
        $('input[name="adj_lpo_number[]"]').each(function() { adj_lpo_number.push($(this).val()); });
        
        var adj_narration = [];
        $('input[name="adj_narration[]"]').each(function() { adj_narration.push($(this).val()); });
        
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id:id,
                customer: id,
                customer_id: id,
                adj_srn_no: adj_srn_no,
                adj_doc_date: adj_doc_date,
                doc_date: $('#doc_date').val(),
                dn_doc_number: $('#dn_doc_number').val(),
                adj_siv_no: adj_siv_no,
                adj_total: adj_total,
                adj_paid: adj_paid,
                adj_balance: adj_balance,
                adj_lpo_number: adj_lpo_number,
                adj_narration: adj_narration,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                var tblrow="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){

                            var paid_amount = parseSrAdjustmentAmount(dataResult['data'][i].paid_amount);
                            var total_amount = parseSrAdjustmentAmount(dataResult['data'][i].total_amount);
                            var balance_amount = total_amount - paid_amount;
                            var current_amount = paid_amount;
                            var previous_paid_amount = Math.max(paid_amount - current_amount, 0);

                            tblrow += "<tr class='js-sr-adj-row' data-row='"+ i +"'>";
                            tblrow += "<td class='text-center'>"+ escapeSrAdjustmentValue(dataResult['data'][i].doc_number) +"<input type='hidden' name='adj_siv_no[]' id='adj_siv_no_"+ i +"' value='"+ escapeSrAdjustmentValue(dataResult['data'][i].doc_number) +"' readonly /></td>";
                            tblrow += "<td class='text-center'>"+ formatSrAdjustmentDate(dataResult['data'][i].doc_date) +"<input type='hidden' name='adj_doc_date[]' id='adj_doc_date_"+ i +"' value='"+ formatSrAdjustmentDate(dataResult['data'][i].doc_date) +"' readonly /></td>";
                            tblrow += "<td class='text-center'>"+ escapeSrAdjustmentValue(dataResult['data'][i].lpo_number) +"<input type='hidden' name='adj_lpo_number[]' id='adj_lpo_number_"+ i +"' value='"+ escapeSrAdjustmentValue(dataResult['data'][i].lpo_number) +"' readonly /></td>";
                            tblrow += "<td class='text-end'>"+ formatSrAdjustmentAmount(total_amount) +"<input type='hidden' name='adj_total[]' id='adj_total_"+ i +"' value='"+ formatSrAdjustmentAmount(total_amount) +"' readonly /></td>";
                            tblrow += "<td class='text-end'>"+ formatSrAdjustmentAmount(previous_paid_amount) +"<input type='hidden' class='js-sr-adj-previous-paid' id='adj_previous_paid_"+ i +"' value='"+ formatSrAdjustmentAmount(previous_paid_amount) +"' readonly /></td>";
                            tblrow += "<td class='text-end'><span id='adj_balance_display_"+ i +"'>"+ formatSrAdjustmentAmount(balance_amount) +"</span><input type='hidden' name='adj_balance[]' id='adj_balance_"+ i +"' value='"+ formatSrAdjustmentAmount(balance_amount) +"' data-actual-balance='"+ balance_amount +"' readonly /></td>";
                            tblrow += "<td><input type='text' class='form-control text-end class_adj_paid' name='adj_paid[]' id='adj_paid_"+ i +"' value='"+ formatSrAdjustmentAmount(current_amount) +"' data-current-amount='"+ current_amount +"' onclick='get_set_amount("+ i +")' required /></td>";
                            tblrow += "<td><input type='text' class='form-control' name='adj_narration[]' id='adj_narration_"+ i +"' value='"+ escapeSrAdjustmentValue(dataResult['data'][i].narration)+"' /></td>";
                            tblrow += "</tr>";

                        }
                        
                        $('#table_adjestment tbody').empty();
                        $("#table_adjestment tbody").append(tblrow); 
                        updateSrAdjustmentTotals();
                        alert('Adjustment Added Successfully');
                        $('#adj_popup_win').modal('hide');

                    }
                    else{
                        $('#table_adjestment tbody').empty();

                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }

    function add_adjestments(){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('sales-return-add-adjestment2') }}";
        $.ajax({
            url: action,
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                srn_no: $('#adj_srn_no').val(),
                dln_no: $('#adj_dln_no').val(),
                siv_no: $('#adj_siv_no').val(),
                doc_date: $('#adj_doc_date').val(),
                total_amount: $('#adj_total').val(),
                paid_amount: $('#adj_paid').val(),
                balance_amount: $('#adj_balance').val(),
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
                            $('#adj_srn_no').val(dataResult['data'][i].srn_no);
                            $('#adj_dln_no').val(dataResult['data'][i].dln_no);
                            $('#adj_siv_no').val(dataResult['data'][i].siv_no);
                            $('#adj_total').val(dataResult['data'][i].total_amount);
                            $('#adj_paid').val(dataResult['data'][i].paid_amount);
                            $('#adj_balance').val(dataResult['data'][i].balance_amount);
                        }
                        alert("Adjestment added successfully");
                    }
                    else{

                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
</script>
<script>
        function add_set_adjestment() {
            
            var csid = getSrAdjustmentCustomerId();
            $('#adj_srn_no').val($('#doc_number').val());
            $('#adj_customer_id').val(csid);

            if (!csid) {
                alert('Please select customer before adjustment.');
                return;
            }

            var amt = $('#lbl_total_totalamount').text();
            
            $('#act_srn_adj_amount').val(amt);
            $('#srn_adj_amount').val(amt);

            get_adjestments_add(csid);
    
            $('#btn_adj_popup_win').click();
        }
    </script>
    
        <button type="button" id="btn_adj_popup_win" data-bs-toggle="modal" data-bs-target="#adj_popup_win" hidden></button>
        <div class="modal side-panel fade" id="adj_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="height: 500px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bill Wise Adjustment</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" name="adj_srn_no" id="adj_srn_no">
                    <input type="hidden" name="adj_customer_id" id="adj_customer_id">
                    <div class="card-body" style="height: 420px; overflow-y: scroll;">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <input type="text" id="act_srn_adj_amount" hidden />
                                    <input type="text" id="srn_adj_amount" hidden />

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
                                                <th style="width:100px;" class="text-end">@lang('Total')</th>
                                                <th style="width:100px;" class="text-end">@lang('Paid')</th>
                                                <th style="width:100px;" class="text-end">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-end">@lang('Adjustment')</th>
                                                <th style="width:200px;" class="text-start">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
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
                                    <h6 class="mt-3 mb-2">Positive Unadjusted Balance</h6>
                                    <table class="table table-hover form-item-table" cellspacing="0" width="100%" id="table_adjestment_unadjusted">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;" class="text-center">@lang('Doc No')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                                <th style="width:100px;" class="text-center">@lang('LPO NO')</th>
                                                <th style="width:100px;" class="text-end">@lang('Total')</th>
                                                <th style="width:100px;" class="text-end">@lang('Paid')</th>
                                                <th style="width:100px;" class="text-end">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-end">@lang('Adjustment')</th>
                                                <th style="width:200px;" class="text-start">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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

                            function escapeSrAdjustmentValue(value) {
                                if (typeof escapeErpHtml === 'function') {
                                    return escapeErpHtml(value);
                                }

                                return String(value || '')
                                    .replace(/&/g, '&amp;')
                                    .replace(/"/g, '&quot;')
                                    .replace(/'/g, '&#039;')
                                    .replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;');
                            }

                            function formatSrAdjustmentDate(value) {
                                if (!value) {
                                    return '';
                                }

                                var dateValue = String(value).split(' ')[0];
                                var parts = dateValue.split('-');

                                if (parts.length === 3 && parts[0].length === 4) {
                                    return escapeSrAdjustmentValue(parts[2] + '/' + parts[1] + '/' + parts[0]);
                                }

                                return escapeSrAdjustmentValue(dateValue);
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

                                $('#table_adjestment tbody tr, #table_adjestment_unadjusted tbody tr').each(function () {
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

                            $(document).on('click', '#table_adjestment tbody tr.js-sr-adj-row, #table_adjestment_unadjusted tbody tr.js-sr-adj-row', function (event) {
                                if ($(event.target).is('input, textarea, select, button, a, label')) {
                                    return;
                                }

                                var id = $(this).data('row');
                                get_set_amount(id);
                                $('#adj_paid_' + id).trigger('focus');
                            });

                            $(document).on('input', '#table_adjestment .class_adj_paid, #table_adjestment_unadjusted .class_adj_paid', function () {
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

                            $(document).on('blur', '#table_adjestment .class_adj_paid, #table_adjestment_unadjusted .class_adj_paid', function () {
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

                        </script>

                        <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn btn-light add-btn ms-2" onclick="save_adjestments()">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Adjust
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


    $(document).ready(function() {


     $(document).on("change", "#shipping_supplier", function() {
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

          // Auto-open vendors dropdown on page load
        setTimeout(function () {
            $('#customer').select2('open');
        }, 500);

});
</script>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

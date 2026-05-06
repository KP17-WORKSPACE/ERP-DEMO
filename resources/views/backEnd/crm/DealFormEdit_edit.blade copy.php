    <?php try { ?>

                @if (isset($edit))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-deals-form']) }}
                    @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deals-form']) }}
                    @endif
                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                    <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                    <input type="hidden" name="quote_id" value="{{ $quote_id }}">
                    


    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ $edit->code }}
        </h4>
        <div class="purchase-order-content-header-right">
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
            </button>
             <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    
                    {{-- <li><a class="dropdown-item" href="{{url('crm-quote/'.$edit->id.'/download/'.$edit->quote_id)}}"><i class="ico icon-outline-document-medicine text-success"></i> Download</a></li> --}}

        

                    
                    

                </ul>
            </div>
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                         <div class="col-3">
                                            <label class="form-label">Deal Name</label>
                                            <div class="form-group">
                                    <input class="form-control" type="text" name="deal_name" autocomplete="off" id="deal_name" value="{{ isset($edit) ? (!empty(@$edit->deal_name) ? @$edit->deal_name : old('deal_name')) : old('deal_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <label class="form-label">Customer</label>
                                            <div class="form-group">
                                                <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required onchange="change_cust_id()">
                                                <option value=""></option>
                                                @foreach ($vendors as $value)
                                                <option value="{{ @$value->id }}" @if(@$edit->cust_id == $value->id) selected @endif>{{ @$value->code }} - {{ @$value->name }}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-2">
                                            <label class="form-label">Est. Closing Date *</label>
                                            <div class="form-group">

                                                
                                    @php
                                        @$value = @$edit->estimated_close_date;

                                    @endphp
                                    <input class="form-control date-picker" id="estimated_close_date" type="text" autocomplete="off" name="estimated_close_date" value="{{ @App\SysHelper::normalizeToDmy(@$value) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By </label>
                                            <div class="form-group">                                                    
                                                <select class="form-control" name="owner" id="owner" required>
                                                    <option value="{{ @$edit->owner }}">{{ @$edit->ownername->full_name }}</option>
                                                </select>
                                            </div>
                                        </div>



                            
                                        
                                        <div class="col-2">
                                            <label class="form-label">Company</label>
                                            <div class="form-group">
                                                <select class="form-control" name="company" id="company" required>
                                                    @if (session('logged_session_data.company_id') == 1)
                                                    <option value="">Select</option>
                                                    @foreach ($company as $value)
                                                    <option value="{{ @$value->id }}" @if(session('logged_session_data.company_id') == @$value->id) selected @endif>{{ @$value->company_name }}</option>
                                                    @endforeach
                                                    @else
                                                    <option value="{{ session('logged_session_data.company_id') }}">{{ $deal_company }}</option>
                                                    @endif
                                                </select>
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
                                        <button class="nav-link " id="delivery-fields-tab" data-bs-toggle="tab" data-bs-target="#delivery-fields" type="button" role="tab" aria-controls="delivery-fields" aria-selected="true">Delivery Location</button>
                                    </li>
                                    
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="quote-fields-tab" data-bs-toggle="tab" data-bs-target="#quote-fields" type="button" role="tab" aria-controls="quote-fields" aria-selected="true">Quote</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="editfullfill-fields-tab" data-bs-toggle="tab" data-bs-target="#editfullfill-fields" type="button" role="tab" aria-controls="editfullfill-fields" aria-selected="true">Edit Fullfill</button>
                                    </li>
                                     <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="internal-fields-tab" data-bs-toggle="tab" data-bs-target="#internal-fields" type="button" role="tab" aria-controls="internal-fields" aria-selected="true">Internal Note</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">

                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Contact Person Name<span>*</span></label>
                                                    <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Designation</label>
                                                   <input class="form-control" type="text" name="designation" autocomplete="off" id="designation" value="{{ isset($edit) ? (!empty(@$edit->designation) ? @$edit->designation : old('designation')) : old('designation') }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Mobile<span>*</span></label>
                                                    <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Email</label>
                                                  <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Address<span></span></label>
                                                    <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Brand')<span>*</span></label>
                                                    <select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple>
                                                        @foreach ($brand as $value)
                                                        <option value="{{ @$value->title }}"
                                                            @if(isset($edit))
                                                                @if(!empty($edit->tags))
                                                                    @if(str_contains($edit->tags, $value->title)) selected @endif
                                                                @endif
                                                            @endif >{{ @$value->title }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">Value<span>*</span></label>
                                                    <div class="form-group">
                                                         <input class="form-control" type="number" step="any" name="deal_value" autocomplete="off" id="deal_value" value="{{ isset($edit) ? (!empty(@$edit->deal_value) ? @App\SysHelper::currancy_format_deal_no($edit->deal_value, $edit->company_id) : old('deal_value')) : old('deal_value') }}">
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Date<span>*</span></label>
                                                    @php
                                                    $value = date('Y-m-d');
                                                    if (isset($edit) && !empty($edit->date)) {
                                                        $value = date('Y-m-d', strtotime(@$edit->date));
                                                    }
                                                    @endphp
                                                    <input class="form-control date-picker" id="date" type="text" name="date" value="{{ @App\SysHelper::normalizeToDmy(@$value) }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">Stage<span>*</span></label>
                                                    <select class="form-control" name="stage" id="stage">
                                                        <option value="1" @if(@$edit->stage == 1) selected @endif >Prospecting</option>
                                                        <option value="2" @if(@$edit->stage == 2) selected @endif >Quote</option>
                                                        <option value="3" @if(@$edit->stage == 3) selected @endif >Closure</option>
                                                        <option value="4" @if(@$edit->stage == 4) selected @endif >Won</option>
                                                        <option value="5" @if(@$edit->stage == 5) selected @endif >Lost</option>
                                                    </select>
                                                    <textarea class="primary-input dynamicstxt_s w-100 form-control" name="lost_comments" rows="4" style="height: 50px !important; display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                                    <script>
                                                        $('#stage').on('change', function(e) {
                                                            if ($('#stage').val() == 5) {
                                                                $('#lost_comments').css("display", "block");
                                                                $('#lost_comments').prop('required', true);
                                                            } else {
                                                                $('#lost_comments').css("display", "none");
                                                                $('#lost_comments').prop('required', false);
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">Source<span>*</span></label>
                                                    <select class="form-control" name="source" id="source">
                                                        <option value="">-Select-</option>
                                                        <option value="Chat" @if(@$edit->source == "Chat") selected @endif >Chat</option>
                                                        <option value="Call" @if(@$edit->source == "Call") selected @endif >Call</option>
                                                        <option value="Mail" @if(@$edit->source == "Mail") selected @endif @if(!isset($edit)) selected @endif>Mail</option>
                                                        <option value="Website" @if(@$edit->source == "Website") selected @endif >Website</option>
                                                        <option value="Gitex 2023" @if(@$edit->source == "Gitex 2023") selected @endif >Gitex 2023</option>
                                                        <option value="Gitex" @if(@$edit->source == "Gitex") selected @endif >Gitex</option>
                                                        <option value="Fulfillment" @if(@$edit->source == "Fulfillment") selected @endif >Fulfillment</option>
                                                        <option value="Ecommerce" @if(@$edit->source == "Ecommerce") selected @endif >Ecommerce</option>
                                                        <option value="Other" @if(@$edit->source == "Other") selected @endif >Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2" id="sourcediv" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">Other Source<span>*</span></label>
                                                    <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o" value="{{ isset($edit) ? (!empty(@$edit->source_o) ? @$edit->source_o : old('source_o')) : old('source_o') }}" style="display: none;" placeholder="Source">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Deal Type<span></span></label>
                                                    <select class="form-control" name="isproject" id="isproject">
                                                        {{--  <option value="4" @if(@$edit->isproject == "4") selected @endif >Project</option>  --}}
                                                        <option value="1" @if(@$edit->isproject == "1") selected @endif >Reseller</option>
                                                        <option value="2" @if(@$edit->isproject == "2") selected @endif >Enduser</option>
                                                        <option value="3" @if(@$edit->isproject == "3") selected @endif >E-Commerece</option>
                                                        <option value="5" @if(@$edit->isproject == "5") selected @endif >Marketing</option>
                                                    </select>
                                                    <script>
                                                        $('#isproject').on('change', function(e) {
                                                            if ($('#isproject').val() == 4) {
                                                                $('#is_professional_service').prop( "checked", true );
                                                            } else {
                                                                $('#is_professional_service').prop( "checked", false );
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2" style="display: none;">
                                                <div class="input-effect">
                                                    <label class="form-label">Status<span>*</span></label>
                                                    <select class="form-control" name="status" id="status" required>
                                        <option value="1" @if(@$edit->status == 1) selected @endif >New</option>
                                        <option value="2" @if(@$edit->status == 2) selected @endif >Qualified</option>
                                        <option value="3" @if(@$edit->status == 3) selected @endif >Unqualified </option>
                                    </select>
                                    <textarea class="form-control" name="lost_comments" rows="4" style="display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                    <script>
                                        $('#status').on('change', function(e) {
                                            if ($('#status').val() == 3) {
                                                $('#lost_comments').css("display", "block");
                                                $('#lost_comments').prop('required', true);
                                            } else {
                                                $('#lost_comments').css("display", "none");
                                                $('#lost_comments').prop('required', false);
                                            }
                                        });
                                    </script>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Project Service<span>*</span></label>
                                                    <div class="form-control">
                                                        <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="is_professional_service" name="is_professional_service" checked>
                                                        <label class="form-check-label ml-4 mt-1" for="is_professional_service">Yes, Project Service</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Attach<span>*</span></label>
                                    <input type="file" class="form-control" name="doc" id="doc">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-2">
                                                <div class="input-effect">
                                                    <label class="form-label">Notes<span>*</span></label>
                                    <textarea class="form-control" name="note" rows="3" autocomplete="off" id="note">@if(isset($edit)) {{$edit->note}} @endif</textarea>
                                                </div>
                                            </div>


                                            
                                        </div>
                                    </div>

                                    

                                       <div class="tab-pane fade" id="delivery-fields" role="tabpanel" aria-labelledby="delivery-fields-tab">
                                       

                                    
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <div class="row">
                                                        <input type="hidden" name="cust_deal_id" value="{{ $edit->id ?? '' }}" />
                                                        <input type="hidden" name="cust_id" value="{{ $edit->cust_id ?? '' }}" />

                                                        {{-- Customer Name --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Customer Name</label>
                                                                <select class="form-control js-example-basic-single" name="delivery_company" id="delivery_company" required>
                                                                    <option value="">-Select-</option>
                                                                    @foreach ($cust_supp ?? [] as $value)
                                                                        <option value="{{ $value->name }}"
                                                                            {{ (isset($edit->delivery_company) && $edit->delivery_company == $value->name) ? 'selected' : '' }}>
                                                                            {{ $value->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- Address 1 --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address 1</label>
                                                                <input class="form-control" type="text" id="delivery_address1" name="delivery_address1"
                                                                    value="{{ old('delivery_address1', $edit->delivery_address1 ?? $addressbook->address ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- Contact Person --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Contact Person</label>
                                                                <input type="text" class="form-control" name="delivery_name" id="delivery_name"
                                                                    value="{{ old('delivery_name', $edit->delivery_name ?? $leads->cust_name ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- Address 2 --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Address 2</label>
                                                                <input class="form-control" type="text" id="delivery_address2" name="delivery_address2"
                                                                    value="{{ old('delivery_address2', $edit->delivery_address2 ?? $addressbook->address2 ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- Contact Number --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Contact Number</label>
                                                                <input type="text" class="form-control" name="delivery_number" id="delivery_number"
                                                                    value="{{ old('delivery_number', $edit->delivery_number ?? $leads->cust_no ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- City --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">City</label>
                                                                <input class="form-control" type="text" id="delivery_city" name="delivery_city"
                                                                    value="{{ old('delivery_city', $edit->delivery_city ?? $addressbook->city ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- Email --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Contact Email</label>
                                                                <input type="email" class="form-control" name="delivery_email" id="delivery_email"
                                                                    value="{{ old('delivery_email', $edit->delivery_email ?? $leads->cust_email ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- Country --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Country</label>
                                                                <select class="form-control js-example-basic-single" id="country_n_e" name="delivery_country" required>
                                                                    <option value="">Select Country</option>
                                                                    @foreach ($countries ?? [] as $value)
                                                                        <option value="{{ $value->id }}"
                                                                            {{ ($edit->delivery_country ?? $addressbook->country ?? '') == $value->id ? 'selected' : '' }}>
                                                                            {{ $value->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- PO Box --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">PO Box</label>
                                                                <input class="form-control" type="text" name="delivery_zip_code"
                                                                    value="{{ old('delivery_zip_code', $edit->delivery_zip_code ?? $addressbook->zip_code ?? '') }}" required>
                                                            </div>
                                                        </div>

                                                        {{-- State --}}
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">State</label>
                                                                <select class="form-control js-example-basic-single" id="state_n_e" name="delivery_state" required>
                                                                    <option value="">Select State</option>
                                                                    @foreach ($states ?? [] as $st)
                                                                        <option value="{{ $st->id }}"
                                                                            {{ ($edit->delivery_state ?? $addressbook->state ?? '') == $st->id ? 'selected' : '' }}>
                                                                            {{ $st->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>


                                                
                                                  
                                        </div>

                                       <div class="tab-pane fade" id="quote-fields" role="tabpanel" aria-labelledby="quote-fields-tab">
                                            
                                        <h4 class="mb-1 color-sub-head font-size-13 mb-2">Quote</h4>
                                           

                                            @if (count($quotationitems) > 0)
                                            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote/' . $edit->id . '/download/' . $edit->quote_id, 'method' => 'POST', 'id' => 'crm-quote-download-form']) }} --}}

                                            
                                                <div id="quote-download-form">
   <div class="mb-3 d-flex align-items-center flex-wrap gap-3">

                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input" type="checkbox" id="withPartNumber" name="with_partnumber" value="1">
                                                        <label class="form-check-label" for="withPartNumber">Include Part Numbers</label>
                                                    </div>

                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input" type="checkbox" id="excludeVat" name="without_vat" value="1">
                                                        <label class="form-check-label" for="excludeVat">Exclude VAT</label>
                                                    </div>

                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input" type="checkbox" id="withoutTotal" name="without_total" value="1">
                                                        <label class="form-check-label" for="withoutTotal">Hide Total</label>
                                                    </div>

                                                    <button type="button" id="btnDownloadQuote"
                                                            class=" btn-sm btn-success text-white d-flex align-items-center gap-2" 
                                                            style="padding: 0px 8px 0px 8px;border-radius:4px">
                                                        <i class="ico icon-bold-download-minimalistic text-white" style="font-size: 16px;"></i>
                                                        <span>Download</span>
                                                    </button>

                                                </div>
                                                </div>
                                         

                                                {{-- {{ Form::close() }} --}}
                                            @else
                                                <div class="alert alert-warning mt-3 mb-0">
                                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                                    No quotation items available to generate a quote.
                                                </div>
                                            @endif

                                    <script>
                                        $(document).ready(function() {
    $('#btnDownloadQuote').on('click', function(e) {
        e.preventDefault();

        // Collect checkbox values
        let params = [];
        if ($('#withPartNumber').is(':checked')) params.push('with_partnumber=1');
        if ($('#excludeVat').is(':checked')) params.push('without_vat=1');
        if ($('#withoutTotal').is(':checked')) params.push('without_total=1');

        // Build URL with query string
        let url = '/crm-quote/{{ $edit->id }}/download/{{ $edit->quote_id }}';
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        // Open the URL in a new tab to trigger PDF download
        window.open(url, '_blank');
    });
});

                                    </script>


                                            <h4 class="mb-1 color-sub-head font-size-13 mb-2 mt-3">Quote Revisions</h4>

                                           <?php $editcheck = App\SysHelper::deal_edit_disable($edit->id); ?>

                                          

                                    
                                            <table class="table table-hover table-striped align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Quote No</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        @$quote_no = App\SysCrmQuoteItems::select('quote_id')
                                                                        ->where('deal_id', $edit->id)
                                                                        ->groupBy('quote_id')
                                                                        ->orderBy('quote_id', 'asc')
                                                                        ->get(); 
                                                    ?>
                                                    @foreach (@$quote_no as $item)
                                                        <tr>
                                                            <!-- Quote Number -->
                                                            <td>
                                                                <strong>{{ $edit->deal_code->code }} - {{ $item->quote_id }}</strong>
                                                            </td>

                                                            <!-- Action Buttons -->
                                                            <td class="d-flex justify-content-start align-items-center gap-2">
                                                                <!-- Download -->
                                                                <a class="btn btn-sm btn-light  text-dark" style="padding: 0px 8px 0px 8px;border-radius:4px" 
                                                                href="{{ url('crm-quote/'.$edit->id.'/download/'.$item->quote_id) }}">
                                                                <i class="ico icon-bold-download-minimalistic text-success" style="font-size:16px"></i>    Download
                                                                </a>

                                                                @if($editcheck == 0)
                                                                    <!-- Edit -->
                                                                    <a class="btn btn-sm btn-light  text-dark" 
                                                                    href="{{ url('crm-deals/' . $edit->id . '/edit/'.$item->quote_id) }}">
                                                                      <i class="ico icon-outline-pen-2 text-success" style="font-size:16px"></i>  Edit
                                                                    </a>

                                                                    <!-- Create Copy -->
                                                                    <a class="btn btn-sm btn-light text-dark" 
                                                                    href="{{ url('crm-quote/'.$edit->id.'/createcopy/'.$item->quote_id) }}">
                                                                       <i class="ico icon-outline-copy text-success" style="font-size:16px"></i> Create Copy
                                                                    </a>
                                                                @endif

                                                                <!-- Set as Final Quote / Final Quote Label -->
                                                                @if ($item->quote_id != $edit->quote_id)
                                                                    @if($editcheck == 0)
                                                                        <a class="btn btn-sm btn-light text-dark" 
                                                                        href="{{ url('crm-quote/'.$edit->id.'/setprimary/'.$item->quote_id) }}">
                                                                         <i class="ico icon-outline-check-square text-success" style="font-size:16px"></i>   Set as Final Quote
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <span class="btn btn-sm btn-light text-white bg-success "  style="padding: 0px 8px 0px 8px;border-radius:4px">Final Quote</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                   
                                                </tbody>
                                            </table>

                                        </div>

                                        <div class="tab-pane fade" id="editfullfill-fields" role="tabpanel" aria-labelledby="editfullfill-fields-tab">
                                           
                                               <?php $data = App\SysHelper::deal_track_status($edit->id); ?>
                                                    @if(App\SysHelper::set_track($edit->id)==1)
                                                    @if($data=="Fulfill" )
                                                <div class="">
                                                    <h4 class="mb-1 color-sub-head font-size-13 mb-2">Deal Track</h4>
                                                    @if (App\SysHelper::get_company_status($edit->customername)==0)
                                                    
                                                  <div style="padding: 60px 20px; max-width: 500px; margin: 50px auto; text-align: center; border: 1px solid #ddd; border-radius: 8px; ">
                                                       <!-- Warning Icon -->
                                                        <div style="font-size: 50px; color: #ffc107; margin-bottom: 20px;">
                                                            <i class="ico icon-outline-shield-warning"></i>
                                                        </div>
                                                    <h4 style="margin-bottom: 20px; color: #333;" class="title-15">Customer Information Incomplete</h4>
                                                        <p style="margin-bottom: 30px; color: #555;" >
                                                            Some required information for this customer is missing. Please update the customer details to continue.
                                                        </p>
                                                        <a    href="{{ url('customers/'.$edit->customername->id.'?customer_action=edit') }}" target="_blank" class="btn-sm text-center btn-primary btn-lg text-white">
                                                            Update Customer Information
                                                        </a>
                                                    </div>
             
                                                    @else
                                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-submit-form']) }}
                                                    
                                                    
                                                    @php 
                                                    $delivery_date="";
                                                    $payment_terms="";
                                                    $payment_mode="";
                                                    $purchease_required="";
                                                    $partial_delivery="";
                                                    $technical="";
                                                    $technical_detail="";
                                                    $lpo="";
                                                    $cheque_copy="";
                                                    $purchease_quote="";
                                                    $remarks="";
                                                    $reference_no="";
                                                    $reference_date="";
                                                    $purchease_approval=0;
                                                    $invoice_approval=1;
                                                    $delivery_approval=1;
                                                    $receivables_approval=1;
                                                    $start_date="";
                                                    $end_date="";

                                                    if(isset($deal_track_temp)){
                                                        $delivery_date=$deal_track_temp->delivery_date;
                                                        $payment_terms=$deal_track_temp->payment_terms;
                                                        $payment_mode=$deal_track_temp->payment_mode;
                                                        $purchease_required=$deal_track_temp->purchease_required;
                                                        $partial_delivery=$deal_track_temp->partial_delivery;
                                                        $technical=$deal_track_temp->technical;
                                                        $technical_detail=$deal_track_temp->technical_detail;
                                                        $lpo=$deal_track_temp->lpo;
                                                        $cheque_copy=$deal_track_temp->cheque_copy;
                                                        $purchease_quote=$deal_track_temp->purchease_quote;
                                                        $remarks=$deal_track_temp->remarks;
                                                        $reference_no=$deal_track_temp->reference_no;
                                                        $reference_date=$deal_track_temp->reference_date;
                                                        $purchease_approval=$deal_track_temp->purchease_approval;
                                                        $invoice_approval=$deal_track_temp->invoice_approval;
                                                        $delivery_approval=$deal_track_temp->delivery_approval;
                                                        $receivables_approval=$deal_track_temp->receivables_approval;
                                                        $start_date=$deal_track_temp->start_date;
                                                        $end_date=$deal_track_temp->end_date;
                                                        $invoicing = $deal_track_temp->invoicing;
                                                    }
                                                    @endphp
                                                    <div class="">
                                                        <div class="row">
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="no-gutters input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label class="form-label">@lang('Expected Delivery Date')<span></span></label>
                                                                            
                                                                            <input class="form-control date-picker" id="delivery_date1" type="text" autocomplete="off" required name="delivery_date" value="{{ @App\SysHelper::normalizeToDmy($delivery_date)  }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">Payment Terms<span></span></label>
                                                                    <select class="form-control" name="payment_terms" id="payment_terms1" required>
                                                                        <option value="">-Select-</option>
                                                                    @foreach ($paymentterms as $key => $value)
                                                                        <option value="{{ @$value->id }}"
                                                                            @if($payment_terms !="") @if (@$payment_terms == @$value->id) selected @endif 
                                                                            @else
                                                                            @if (isset($quotationitems)) @if (@$quotationitems[0]->payment_terms == @$value->id) selected @endif @endif
                                                                            @endif
                                                                            >{{ @$value->title }}</option>
                                                                    @endforeach                                                    
                                                                    </select>
                                                                    <script>
                                                                        $('#payment_terms1').on('change', function(e) {
                                                                            if ($('#payment_terms1').val() == 20 || $('#payment_terms1').val() == 21) {
                                                                                $('#payment_mode_sec_div').css("display", "none");
                                                                                //$('#payment_mode_sec').prop('required', true);
                                                                            } else {
                                                                                $('#payment_mode_sec_div').css("display", "none");
                                                                                //$('#payment_mode_sec').prop('required', false);
                                                                            }

                                                                            if($('#payment_terms1').val() == 1 || $('#payment_terms1').val() == 2){
                                                                                $('#payment_mode').val(1);
                                                                            } else { $('#payment_mode').val(2); }
                                                                            
                                                                            if ($('#payment_terms1').val() == 22) {
                                                                                $('#payment_terms1_txt').css("display", "block");
                                                                                $('#payment_terms1_txt').prop('required', true);
                                                                            } else {
                                                                                $('#payment_terms1_txt').css("display", "none");
                                                                                $('#payment_terms1_txt').prop('required', false);
                                                                            }
                                                                        });
                                                                    </script>
                                                                    <input class="form-control" id="payment_terms1_txt1" type="text" value="" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                                                                </div>
                                                            </div>
                                                            @php
                                                            $mode_sel=0;
                                                            if(@$quotationitems[0]->payment_terms== 1 || @$quotationitems[0]->payment_terms== 2){ $mode_sel=1;} else { $mode_sel=2;} 

                                                            @endphp
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">Payment Mode<span></span></label>
                                                                    <select class="form-control" name="payment_mode" id="payment_mode1" required>
                                                                        <option value="">-Select-</option>
                                                                        <option value="1" @if($payment_mode==1) selected @else @if($mode_sel==1) selected @endif @endif>Cash</option>
                                                                        <option value="2" @if($payment_mode==2) selected @else @if($mode_sel==2) selected @endif @endif>Cheque</option>
                                                                        <option value="3" @if($payment_mode==3) selected @endif>Bank Transfer</option>
                                                                        <option value="4" @if($payment_mode==4) selected @endif>Open Credit</option>
                                                                        <option value="5" @if($payment_mode==5) selected @endif>Credit Card</option>
                                                                        <option value="6" @if($payment_mode==6) selected @endif>Bank TT</option>
                                                                        <option value="7" @if($payment_mode==7) selected @endif>Letter of Credit</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3" id="payment_mode_sec_div" style="display: none;">
                                                                <div class="input-effect">
                                                                    <label class="form-label">Payment Mode<span></span></label>
                                                                    <select class="form-control" name="payment_mode_sec" id="payment_mode_sec1" >
                                                                        <option value="">-Select-</option>
                                                                        <option value="1">Cash</option>
                                                                        <option value="2">Cheque</option>
                                                                        <option value="3">Bank Transfer</option>
                                                                        <option value="4">Open Credit</option>
                                                                        <option value="5">Credit Card</option>
                                                                        <option value="6">Bank TT</option>
                                                                        <option value="7">Letter of Credit</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                       
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect ">
                                                                    <label class="form-label">Purchase Required<span></span></label>
                                                                    <div class="form-control">
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_required1" name="purchease_required" @if($purchease_required==1) checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="purchease_required1">Yes, Required</label></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">Partial Delivery<span></span></label>
                                                                    <div class="form-control">
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="partial1" name="partial_delivery" @if($partial_delivery==1) checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="partial1">Yes, Partial Delivery</label></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">Professional Service<span></span></label>
                                                                    <div class="form-control">
                                                                    <input type="hidden" name="technical" value="0" />
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="technical1" name="technical" @if($technical==1 ||$edit->is_professional_service == 1 ) checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="technical1">Yes, Professional Service</label></div>
                                                                </div>
                                                                <script>
                                                                    $('#technical1').on('change', function(e) {
                                                                        if ($('#technical1').prop('checked') == true) {
                                                                            $('#technical_div').css("display", "block");
                                                                            $('#technical_detail').prop('required', true);
                                                                            $('#technical_detail').val($('#technical_detail_hide').val());
                                                                        } else {
                                                                            $('#technical_div').css("display", "none");
                                                                            $('#technical_detail').prop('required', false);
                                                                        }
                                                                    });
                                                                </script>
                                                            </div>
                                                            @if($is_amc_item >0 )
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="no-gutters input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label class="form-label">@lang('Start Date')<span></span></label>
                                                                            <input class="form-control" id="start_date1" type="date" autocomplete="off" required name="start_date" value="{{ $start_date }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3 mb-3">
                                                                <div class="no-gutters input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label class="form-label">@lang('End Date')<span></span></label>
                                                                            <input class="form-control" id="end_date1" type="date" autocomplete="off" required name="end_date" value="{{ $end_date }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-3 mb-3">
                                                                <div class="form-group">
                                                                    <label for="">Invoicing</label>
                                                                    <select class="form-control" type="text" name="amc_invoice" id="amc_invoice1" required>
                                                                        <option value="">-Select-</option>
                                                                        <option value="Monthly">Monthly</option>
                                                                        <option value="Quarterly">Quarterly</option>
                                                                        <option value="Half Yearly">Half Yearly</option>
                                                                        <option value="Yearly" selected>Yearly</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            @endif
                                                            <div class="col-lg-3 mb-3" id="technical_div" style="display: none;">
                                                                <div class="input-effect">
                                                                    <label class="form-label">Professional Service Note<span></span></label>
                                                                    <textarea class="dynamicstxt_s w-100 form-control" style="height: 35px !important" name="technical_detail" rows="4" autocomplete="off" id="technical_detail1" placeholder="Remarks">{{ $technical_detail }}</textarea>
                                                                </div>
                                                            </div>
                                                            @if($technical==1 ||$edit->is_professional_service == 1 )
                                                            <script>
                                                                $('#technical_div').css("display", "block");
                                                                $('#technical_detail').prop('required', true);
                                                            </script>
                                                            @endif
                                                       
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect ">
                                                                    <label class="form-label">Purchase Approval<span></span></label>
                                                                    <div class="form-control">
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_approval1" name="purchease_approval" @if($purchease_approval==0) @else checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="purchease_approval1">Yes, Required</label></div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $(document).ready(function() {
                                                                    $('#purchease_required').change(function() {
                                                                        if(this.checked) {
                                                                            $('#purchease_approval').attr("checked", true);
                                                                            $('#purchease_required').attr("checked", true);                                        
                                                                        }
                                                                        else{
                                                                            $('#purchease_approval').attr("checked", false);
                                                                            $('#purchease_required').attr("checked", false);                                        
                                                                        }
                                                                    });
                                                                });
                                                                
                                                                $('#purchease_required').change(function() {
                                                                    if(this.checked == true) {
                                                                        $('#purchease_approval').attr("checked", true);
                                                                        $('#purchease_required').attr("checked", true);                                        
                                                                    }
                                                                    else{
                                                                        $('#purchease_approval').attr("checked", false);
                                                                        $('#purchease_required').attr("checked", false);                                        
                                                                    }
                                                                });
                                                                $('#purchease_approval').change(function() {
                                                                        if(this.checked == true) {
                                                                            $('#purchease_approval').attr("checked", true);
                                                                            $('#purchease_required').attr("checked", true);                                        
                                                                        }
                                                                        else{
                                                                            $('#purchease_approval').attr("checked", false);
                                                                            $('#purchease_required').attr("checked", false);                                        
                                                                        }
                                                                });
                                                            </script>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect ">
                                                                    <label class="form-label">Invoice Approval<span></span></label>
                                                                    <div class="form-control">
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault1" name="invoice_approval" @if($invoice_approval==0) @else checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault1">Yes, Required</label></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect ">
                                                                    <label class="form-label">Delivery Approval<span></span></label>
                                                                    <div class="form-control">
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault2" name="delivery_approval" @if($delivery_approval==0) @else checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault2">Yes, Required</label></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect ">
                                                                    <label class="form-label">Receivables Approval<span></span></label>
                                                                    <div class="form-control">
                                                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault3" name="receivables_approval" @if($receivables_approval==0) @else checked @endif>
                                                                    <label class="form-check-label ml-4 mt-1" for="flexCheckDefault3">Yes, Required</label></div>
                                                                </div>
                                                            </div>
                                                      
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">@lang('LPO')<span></span></label>
                                                                    @if($lpo!="")
                                                                    <?php $file = explode("|",$lpo); ?>
                                                                    @foreach ($file as $f)
                                                                    <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                                                    @endforeach
                                                                    @endif
                                                                    
                                                                    <div class="form-group files">
                                                                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" id="lpo1" name="lpo[]">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">@lang('Cheque/TT Copy')<span></span></label>
                                                                    @if($cheque_copy!="")
                                                                    <?php $file = explode("|",$cheque_copy); ?>
                                                                    @foreach ($file as $f)
                                                                    <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                                                    @endforeach
                                                                    @endif
                                                                                
                                                                    <div class="form-group files">
                                                                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" id="cheque_copy1" name="cheque_copy[]">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="input-effect">
                                                                    <label class="form-label">@lang('Purchase Quote')<span></span></label>
                                                                    @if($purchease_quote!="")
                                                                    <?php $file = explode("|",$purchease_quote); ?>
                                                                    @foreach ($file as $f)
                                                                    <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                                                    @endforeach
                                                                    @endif
                                                                    
                                                                    <div class="form-group files">
                                                                        <input type="file" class="form-control dynamicstxt_s" multiple="multiple" id="purchease_quote1" name="purchease_quote[]">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                          
                                        
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="no-gutters input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label class="form-label">@lang('LPO/Reference No')<span></span></label>
                                                                            <input class="form-control" id="reference_no1" type="text" autocomplete="off" required name="reference_no" value="{{ $reference_no }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 mb-3">
                                                                <div class="no-gutters input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label class="form-label">@lang('LPO/Reference Date')<span></span></label>
                                                                            <input class="form-control" id="reference_date1" type="date" autocomplete="off" required name="reference_date" value="{{ $reference_date }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                              <div class="col-lg-3 mb-3">
                                                                <div class="no-gutters input-right-icon">
                                                                    <div class="col">
                                                                        <div class="input-effect">
                                                                            <label class="form-label">@lang('Remarks')<span></span></label>
                                                                            <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks1" placeholder="Remarks">{{ $remarks }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" id="deal_id1" name="deal_id" value="{{ $edit->id }}"/>
                                                        <button type="submit" class="btn btn-light add-btn ms-2" value="save" name="btnSubmit" id="btnSave"><span class="ti-check"></span><i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                                                        <button type="submit" class="btn btn-light add-btn ms-2" value="approve" name="btnSubmit" id="btnApprove"><span class="ti-check"></span><i class="ico icon-outline-bookmark-opened text-success"></i> Submit For Approval</button>
                                                    </div>
                                                    {{ Form::close() }}

                                               <script>
$(document).ready(function() {

    // Function to handle the form submission
    function submitDealTrack(action) {
        var formData = new FormData();

        // Collect all inputs by ID
        formData.append('delivery_date', $('#delivery_date1').val());
        formData.append('payment_terms', $('#payment_terms1').val());
        formData.append('payment_terms_txt', $('#payment_terms1_txt1').val());
        formData.append('payment_mode', $('#payment_mode1').val());
        formData.append('payment_mode_sec', $('#payment_mode_sec1').val());
        formData.append('purchease_required', $('#purchease_required1').is(':checked') ? 1 : 0);
        formData.append('partial_delivery', $('#partial1').is(':checked') ? 1 : 0);
        formData.append('technical', $('#technical1').is(':checked') ? 1 : 0);
        formData.append('start_date', $('#start_date1').val() ? $('#start_date1').val() : '');
        formData.append('end_date', $('#end_date1').val() ? $('#end_date1').val() : '');
        formData.append('amc_invoice', $('#amc_invoice1').val());
        formData.append('technical_detail', $('#technical_detail1').val());
        formData.append('purchease_approval', $('#purchease_approval1').is(':checked') ? 1 : 0);
        formData.append('invoice_approval', $('input[name="invoice_approval"]').is(':checked') ? 1 : 0);
        formData.append('delivery_approval', $('input[name="delivery_approval"]').is(':checked') ? 1 : 0);
        formData.append('receivables_approval', $('input[name="receivables_approval"]').is(':checked') ? 1 : 0);
        formData.append('reference_no', $('#reference_no1').val());
        formData.append('reference_date', $('#reference_date1').val() ? $('#reference_date1').val() : null);
        formData.append('remarks', $('#remarks1').val());
        formData.append('deal_id', $('#deal_id1').val());
        formData.append('btnSubmit', action); // save or approve

        // Attach multiple files
        var lpo_files = $('#lpo1')[0].files;
        for (var i = 0; i < lpo_files.length; i++) {
            formData.append('lpo[]', lpo_files[i]);
        }

        var cheque_files = $('#cheque_copy1')[0].files;
        for (var i = 0; i < cheque_files.length; i++) {
            formData.append('cheque_copy[]', cheque_files[i]);
        }

        var purchase_files = $('#purchease_quote1')[0].files;
        for (var i = 0; i < purchase_files.length; i++) {
            formData.append('purchease_quote[]', purchase_files[i]);
        }

        // AJAX call
        $.ajax({
            url: "{{ url('crm-deal-track-submit') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                // optional: disable buttons to prevent double submit
                $('#btnSave, #btnApprove').prop('disabled', true);
            },
            success: function(response) {
                alert(response.message || 'Submitted successfully');
                // optional: reload page or redirect
                // location.reload();
            },
            error: function(xhr) {
                $('#btnSave, #btnApprove').prop('disabled', false);
                var err = xhr.responseJSON;
                if(err && err.errors){
                    var msg = '';
                    $.each(err.errors, function(key, value){
                        msg += value[0] + "\n";
                    });
                    alert(msg);
                } else {
                    alert('An error occurred, please try again.');
                }
            }
        });
    }

    // Save button
    $('#btnSave').click(function(e){
        e.preventDefault();
        submitDealTrack('save');
    });

    // Submit for Approval button
    $('#btnApprove').click(function(e){
        e.preventDefault();
        submitDealTrack('approve');
    });

});
</script>


                                                    @endif
            
                                                            {{-- <li><button type="button" class="dropdown-item" data-modal-size="modal-md" data-bs-target="#ModalDealTrack" data-bs-toggle="modal"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Click to Fullfill</button></li> --}}
                                                    @else
                                                                           
                                                    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || $check_edit_fullfill == 0)
                                                    {{-- <li><button type="button" class="dropdown-item" data-modal-size="modal-md" data-bs-target="#ModalDealTrackEdit" data-bs-toggle="modal"><i class="ico icon-outline-calculator-minimalistic text-warning"></i> Edit Fulfill</button></li> --}}
                                 <div class="">
                                      <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="mb-0" style="font-size: 1.1rem;">Edit Deal Track</h4>
                                        <a target="__blank" href="{{ url('crm-deal-track/'.$edit->id.'/view') }}" 
                                        class=" btn-success btn-sm text-white">
                                            View Deal Track
                                        </a>
                                    </div>

                
                @if (App\SysHelper::get_company_status($edit->customername)==0)
                  <div style="padding: 60px 20px; max-width: 500px; margin: 50px auto; text-align: center; border: 1px solid #ddd; border-radius: 8px; ">
                                                       <!-- Warning Icon -->
                                                        <div style="font-size: 50px; color: #ffc107; margin-bottom: 20px;">
                                                            <i class="ico icon-outline-shield-warning"></i>
                                                        </div>
                                                    <h4 style="margin-bottom: 20px; color: #333;" class="title-15">Customer Information Incomplete</h4>
                                                        <p style="margin-bottom: 30px; color: #555;" >
                                                            Some required information for this customer is missing. Please update the customer details to continue.
                                                        </p>
                                                        <a    href="{{ url('customers/'.$edit->customername->id.'?customer_action=edit') }}" target="_blank" class="btn-sm text-center btn-primary btn-lg text-white">
                                                            Update Customer Information
                                                        </a>
                                                    </div>
                @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-submit-edit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                
                @php 
                $edit_delivery_date="";
                $edit_payment_terms="";
                $edit_payment_mode="";
                $edit_purchease_required="";
                $edit_partial_delivery="";
                $edit_technical="";
                $edit_technical_detail="";
                $edit_lpo="";
                $edit_cheque_copy="";
                $edit_purchease_quote="";
                $edit_remarks="";
                $edit_reference_no="";
                $edit_reference_date="";
                $edit_purchease_approval=1;
                $edit_invoice_approval=1;
                $edit_delivery_approval=1;
                $edit_receivables_approval=1;
                $start_date="";
                $end_date="";

                if(isset($deal_track)){
                    $edit_delivery_date=$deal_track->delivery_date;
                    $edit_payment_terms=$deal_track->payment_terms;
                    $edit_payment_mode=$deal_track->payment_mode;
                    $edit_purchease_required=$deal_track->purchease_required;
                    $edit_partial_delivery=$deal_track->partial_delivery;
                    $edit_technical=$deal_track->technical;
                    $edit_technical_detail=$deal_track->technical_detail;
                    $edit_lpo=$deal_track->lpo;
                    $edit_cheque_copy=$deal_track->cheque_copy;
                    $edit_purchease_quote=$deal_track->purchease_quote;
                    $edit_remarks=$deal_track->remarks;
                    $edit_reference_no=$deal_track->reference_no;
                    $edit_reference_date=$deal_track->reference_date;
                    $edit_purchease_approval=$deal_track->purchease_approval;
                    $edit_invoice_approval=$deal_track->invoice_approval;
                    $edit_delivery_approval=$deal_track->delivery_approval;
                    $edit_receivables_approval=$deal_track->receivables_approval;
                    $start_date=$deal_track->start_date;
                    $end_date=$deal_track->end_date;
                    $invoicing = $deal_track->invoicing;
                }
                @endphp
                <div class="">
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Expected Delivery Date')<span></span></label>
                                        
                                        <input class="form-control" id="delivery_date" type="date" autocomplete="off" required name="delivery_date" value="{{ $edit_delivery_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">Payment Terms<span></span></label>
                                <select class="form-control" name="payment_terms" id="payment_terms2" required>
                                    <option value="">-Select-</option>
                                @foreach ($paymentterms as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if($edit_payment_terms !="") @if (@$edit_payment_terms == @$value->id) selected @endif 
                                        @else
                                        @if (isset($quotationitems)) @if (@$quotationitems[0]->payment_terms == @$value->id) selected @endif @endif
                                        @endif
                                        >{{ @$value->title }}</option>
                                @endforeach                                                    
                                </select>
                                <script>
                                    $('#payment_terms2').on('change', function(e) {
                                        if ($('#payment_terms2').val() == 20 || $('#payment_terms2').val() == 21) {
                                            $('#payment_mode_sec_div2').css("display", "none");
                                            //$('#payment_mode_sec').prop('required', true);
                                        } else {
                                            $('#payment_mode_sec_div2').css("display", "none");
                                            //$('#payment_mode_sec').prop('required', false);
                                        }

                                        if($('#payment_terms2').val() == 1 || $('#payment_terms2').val() == 2){
                                            $('#payment_mode2').val(1);
                                        } else { $('#payment_mode2').val(2); }

                                        if ($('#payment_terms2').val() == 22) {
                                            $('#payment_terms2_txt').css("display", "block");
                                            $('#payment_terms2_txt').prop('required', true);
                                        } else {
                                            $('#payment_terms2_txt').css("display", "none");
                                            $('#payment_terms2_txt').prop('required', false);
                                        }
                                    });
                                </script>
                                <input class="form-control" id="payment_terms2_txt" type="text" value="{{ @$quotationitems[0]->payment_terms_txt }}" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                            </div>
                        </div>
                        @php
                        $mode_sel=0;
                        if(@$quotationitems[0]->payment_terms== 1 || @$quotationitems[0]->payment_terms== 2){ $mode_sel=1;} else { $mode_sel=2;} 

                        @endphp
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">Payment Mode<span></span></label>
                                <select class="form-control" name="payment_mode" id="payment_mode2" required>
                                    <option value="">-Select-</option>
                                    <option value="1" @if($edit_payment_mode==1) selected @else @if($mode_sel==1) selected @endif @endif>Cash</option>
                                    <option value="2" @if($edit_payment_mode==2) selected @else @if($mode_sel==2) selected @endif @endif>Cheque</option>
                                    <option value="3" @if($edit_payment_mode==3) selected @endif>Bank Transfer</option>
                                    <option value="4" @if($edit_payment_mode==4) selected @endif>Open Credit</option>
                                    <option value="5" @if($edit_payment_mode==5) selected @endif>Credit Card</option>
                                    <option value="6" @if($edit_payment_mode==6) selected @endif>Bank TT</option>
                                    <option value="7" @if($edit_payment_mode==7) selected @endif>Letter of Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3" id="payment_mode_sec_div2" style="display: none;">
                            <div class="input-effect">
                                <label class="form-label">Payment Mode<span></span></label>
                                <select class="form-control" name="payment_mode_sec" id="payment_mode_sec" >
                                    <option value="">-Select-</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Cheque</option>
                                    <option value="3">Bank Transfer</option>
                                    <option value="4">Open Credit</option>
                                    <option value="5">Credit Card</option>
                                    <option value="6">Bank TT</option>
                                    <option value="7">Letter of Credit</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="form-label">Purchase Required<span></span></label>
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_required2" name="purchease_required" @if($edit_purchease_required==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="purchease_required2">Yes, Required</label></div>
                            </div>
                        </div>
                        <script>
                            $('#payment_terms2').change();
                            $(document).ready(function() {
                                $('#purchease_required2').change(function() {
                                    if(this.checked) {
                                        $('#purchease_approval2').attr("checked", true);
                                    }
                                    else{
                                        $('#purchease_approval2').attr("checked", false);
                                    }
                                });
                            });
                        </script>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">Partial Delivery<span></span></label>
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="partial2" name="partial_delivery" @if($edit_partial_delivery==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="partial2">Yes, Partial Delivery</label></div>
                            </div>

                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">Professional Service<span></span></label>
                                <div class="form-control">
                                <input type="hidden" name="technical" value="0" />
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="technical2" name="technical" @if($edit_technical==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="technical2">Yes, Professional Service</label></div>
                            </div>
                            <script>
                                $('#technical2').on('change', function(e) {
                                    if ($('#technical2').prop('checked') == true) {
                                        $('#technical_div2').css("display", "block");
                                        $('#technical_detail2').prop('required', true);
                                    } else {
                                        $('#technical_div2').css("display", "none");
                                        $('#technical_detail2').prop('required', false);
                                        alert('Project service will be delete!!');
                                    }
                                });
                            </script>
                        </div>
                        @if($is_amc_item >0 )
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Start Date')<span></span></label>
                                        <input class="form-control" id="start_date" type="date" autocomplete="off" required name="start_date" value="{{ $start_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('End Date')<span></span></label>
                                        <input class="form-control" id="end_date" type="date" autocomplete="off" required name="end_date" value="{{ $end_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="col-lg-3 mb-3">
                            <div class="form-group">
                                <label for="">Invoicing</label>
                                <select class="form-control" type="text" name="amc_invoice" id="amc_invoice" required>
                                    <option value="">-Select-</option>
                                    <option @if($invoicing=="Monthly") selected @endif value="Monthly">Monthly</option>
                                    <option @if($invoicing=="Quarterly") selected @endif value="Quarterly">Quarterly</option>
                                    <option @if($invoicing=="Half Yearly") selected @endif value="Half Yearly">Half Yearly</option>
                                    <option @if($invoicing=="Yearly") selected @endif value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>

                        @endif
                        <div class="col-lg-3 mb-3" id="technical_div2" style="display: none;">
                            <div class="input-effect">
                                <label class="form-label">Professional Service Note<span></span></label>
                                <textarea class="dynamicstxt_s w-100 form-control" style="height: 35px !important" name="technical_detail" rows="4" autocomplete="off" id="technical_detail2" placeholder="Remarks">{{ $edit_technical_detail }}</textarea>
                            </div>
                        </div>
                        @if($edit_technical==1) 
                        <script>
                            $('#technical_div2').css("display", "block");
                            $('#technical_detail2').prop('required', true);
                        </script>
                        @endif
                   
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="form-label">Purchase Approval<span></span></label>
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_approval2" name="purchease_approval" @if($edit_purchease_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="purchease_approval2">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="form-label">Invoice Approval<span></span></label>
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="invoice_approval" @if($edit_invoice_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="form-label">Delivery Approval<span></span></label>
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="delivery_approval" @if($edit_delivery_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="form-label">Receivables Approval<span></span></label>
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="receivables_approval" @if($edit_receivables_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
      
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">@lang('LPO')<span></span></label>
                                @if($edit_lpo!="")
                                <?php $file = explode("|",$edit_lpo); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="lpo[]">
                                  </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">@lang('Cheque/TT Copy')<span></span></label>
                                @if($edit_cheque_copy!="")
                                <?php $file = explode("|",$edit_cheque_copy); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                            
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="cheque_copy[]">
                                  </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="form-label">@lang('Purchase Quote')<span></span></label>
                                @if($edit_purchease_quote!="")
                                <?php $file = explode("|",$edit_purchease_quote); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="purchease_quote[]">
                                  </div>
                            </div>
                        </div>
                        
                       
    
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('LPO/Reference No')<span></span></label>
                                        <input class="form-control" id="reference_no" type="text" autocomplete="off" required name="reference_no" value="{{ $edit_reference_no }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('LPO/Reference Date')<span></span></label>
                                        <input class="form-control" id="reference_date" type="date" autocomplete="off" required name="reference_date" value="{{ $edit_reference_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="form-label">@lang('Remarks')<span></span></label>
                                        <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks">{{ $edit_remarks }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}"/>
                    <button type="submit" class="btn btn-light add-btn ms-2" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span><i class="ico icon-outline-bookmark-opened text-success"></i> Update</button>
                </div>
                {{ Form::close() }}
                @endif
            </div>
                                                            @endif
                                                            @endif
                                                    @endif

                                        </div>
                                        </div>
                                <div class="tab-pane fade" id="internal-fields" role="tabpanel" aria-labelledby="internal-fields-tab">
                                           
                                                    
                                            <div class="row">

                                                <div class="col-7">
                                                    @if($edit->note != "")<b>Deal Notes :- </b>
                                                    
                                                          <div class="card">
                                                            <div class="card-body">
                                                                <div class="fw-semibold"> {!! nl2br($edit->note) !!} </div>

                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(count($comments)>0)
                                                    <div class="mt-3" style="">
                                                        @foreach ($comments as $cmts)
                                                        <div class="card border-0 rounded-3 mb-3">
                                                            {{-- @if ($cmts->created_by == Auth::user()->id)
                                                            <a href="{{url('crm-deals-comments-delete/'.$cmts->id.'')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-window-close text-sm text-danger float-right" aria-hidden="true"></i></a>
                                                            @endif
                                                            <p class="mb-0">{!! nl2br($cmts->comments) !!}
                                                                @if ($cmts->commentsdoc!="")
                                                                    <a class="text-info p-0" href="{{asset('public/uploads/crm_deal_doc/')}}/{{ $cmts->commentsdoc }}" target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                                                                @endif
                                                                <span class="text-muted text-right">{{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($cmts->created_at))}}</span>
                                                            </p> --}}



                                                            <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            
                                            <!-- Left Section -->
                                            <div class="flex-grow-1 pe-3">
                                                <!-- Comment -->
                                                <p class="mb-2 fw-semibold @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif">
                                                    {!! $cmts->comments !!}
                                                </p>

                                                <!-- Metadata -->
                                                <div class="d-flex flex-wrap align-items-center gap-2 small text-muted">
                                                    <span>
                                                        <i class="ico icon-bold-user me-1"></i>
                                                        {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}
                                                    </span>
                                                    <span>•</span>
                                                    <span>
                                                        <i class="ico icon-bold-clock me-1"></i>
                                                        {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                                    </span>
                                                    @if ($cmts->deleted_at)
                                                        <span class="text-danger ms-2">
                                                        
                                                            Deleted: {{ date('d/m/Y h:i A', strtotime($cmts->deleted_at)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Right Section -->
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($cmts->commentsdoc)
                                                    <a href="{{ asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-light">
                                                        <i class="ico icon-bold-paperclip" style="font-size:16px"></i>
                                                        
                                                    </a>
                                                @endif

                                                @if ($cmts->created_by == Auth::user()->id)
                                                    @if ($cmts->deleted_at)
                                                        <a href="{{ url('crm-leads-comments-restore/' . $cmts->id) }}"
                                                        onclick="return confirm('Are you sure you want to restore this comment?')"
                                                        class="btn btn-sm btn-light">
                                                            <i class="ico icon-bold-restart" style="font-size:16px"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ url('crm-leads-comments-delete/' . $cmts->id) }}"
                                                        onclick="return confirm('Are you sure you want to delete this comment?')"
                                                        class="btn btn-sm btn-light">
                                                            <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:16px"> </i>
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                                            
                                                        </div>
                                                        
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </div>

                                                <div class="col-5">
                                                   <label class="font-weight-bold form-label">Internal Note</label>
                                               <div id="deal-comments-form">
                                                    <textarea name="comments" class="form-control" cols="10" rows="3" required></textarea>
                                                    <input type="file" class="form-control mt-2" name="commentsdoc" id="commentsdoc">
                                                    <input type="hidden" name="commentsid" value="{{ $edit->id }}" />

                                                    <div class="mt-3 d-flex justify-content-start">
                                                        <button type="button" id="submitComment" class="btn btn-light text-success d-flex align-items-center gap-2">
                                                            <i class="ico icon-outline-add-square fs-5 text-success"></i>
                                                            <span>Add Note</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                </div>

                                            </div>


                                                    <script>
                                                        
                                                        
                                                    $(document).on('click', '#submitComment', function (e) {
                                                        e.preventDefault();

                                                        let formData = new FormData();
                                                        formData.append('comments', $('textarea[name="comments"]').val());
                                                        formData.append('commentsid', $('input[name="commentsid"]').val());

                                                        let fileInput = $('#commentsdoc')[0];
                                                        if (fileInput.files.length > 0) {
                                                            formData.append('commentsdoc', fileInput.files[0]);
                                                        }

                                                    
                                                        $.ajax({
                                                            url: '{{ url("crm-deals-comments-add") }}',
                                                            type: 'POST',
                                                            data: formData,
                                                            processData: false,
                                                            contentType: false,
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            beforeSend: function () {
                                                                $('#submitComment').prop('disabled', true).text('Saving...');
                                                            },
                                                            success: function (response) {
                                                                console.log("response",response); // Debugging line to check response
                                                                alert('Comment added successfully!');
                                                                $('#submitComment').prop('disabled', false).html('<i class="ico icon-outline-add-square fs-5 text-success"></i><span>Add Note</span>');
                                                                $('textarea[name="comments"]').val('');
                                                                $('#commentsdoc').val('');
                                                                // Optionally append new comment to comment list
                                                            },
                                                            error: function (xhr) {
                                                                $('#submitComment').prop('disabled', false).text('Add Note');
                                                                alert('Something went wrong: ' + xhr.responseText);
                                                            }
                                                        });
                                                    });

                                                    </script>
						

                                        </div>

                                </div>
                            </div>

                           
                            
                            <div class="deal-list-content-header">
                                <table width="100%">
                                    <tbody>
                                        
                                        <tr>
                                            <td class="text-end float-end">
                                                <input type="hidden" name="quotation_generated" id="quotation_generated" value="{{ count($quotationitems) > 0 ? 1 : 0 }}">
                                                <button class="btn btn-sm btn-light add-btn" type="button" href="#" onclick="quote_generate()">
                                                    <i class="ico icon-bold-document-add text-success" style="font-size: 16px"></i>
                                                    <span>Generate Quotation</span>
                                                </button>
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
                                            <div id="generate-quotation" style="height: {{ count($quotationitems) > 0 ? 'auto' : '0px' }}; overflow: hidden; transition: all 0.5s ease;">

                                                <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Quotation</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">
                                        <div class="col-2">
                                            <label class="form-label">Quote Validity:</label>
                                            <div class="form-group">
                                                <input class="form-control" id="quote_validity" type="text" autocomplete="off" placeholder="Quote Validity" name="quote_validity" value="2 Weeks" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Payment Terms:</label>
                                            <div class="form-group">
                                                <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}" @if(count($quotationitems) > 0) @if($quotationitems[0]->payment_terms==$value->id) selected @endif @endif>{{ @$value->title }}</option>
                                    @endforeach
                                    
                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text" value=""
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
                                        <div class="col-2">
                                            <label class="form-label">Delivery Time:</label>
                                            <div class="form-group">
                                                <input class="form-control" id="delivery_time" type="text" autocomplete="off" placeholder="Delivery Time" name="delivery_time" value="2 Weeks" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency:<a style="float: right;" data-bs-target="#ModalChangeCurrancy" data-bs-toggle="modal"><i class="ico icon-outline-pen-2"></i></a></label>
                                            <div class="form-group">
                                                <select class="form-control" name="currency_id" id="currency_id" required>
                                                    <option value="">-Select-</option>
                                                    @foreach ($currency as $value)
                                                        <option value="{{ @$value->id }}" @if(@$edit->deal_currency == $value->id) selected @endif>{{ @$value->code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>           
                                                               
                                        <div class="col-4">
                                            <label class="form-label">Terms and Condition:</label>
                                            <div class="form-group">
                                                <textarea class="form-control" rows="3" data-bs-toggle="modal" data-bs-target="#narrationModal" id="terms_and_condition" autocomplete="off" name="terms_and_condition">{{ @$edit->terms_and_condition }}</textarea>
                                            </div>
{{-- <script>
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
                                            <th class="resizable text-center" width="200px">@lang('Description')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Cost')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Tax')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="50px">@lang('Qty')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Price')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Value')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="80px" scope="col" >Dis <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#discountModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Taxable')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('VAT')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="100px">@lang('Total')<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          
                                        @if(count($quotationitems) > 0)
                                        @foreach($quotationitems as $item)
                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $item->sort_id }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control noborder " name="part_number[]">
                                                        <option value="{{ $item->product_id }}">{{ $item->productname->part_number }}</option>
                                                </select>
                                            </td>
                                            <td><input class="form-control" type="text" name="description[]" value="{{ $item->description }}"></td>
                                            <td>                                                                    
                                                <input class="form-control text-end" type="number" name="cost[]" autocomplete="off" value="{{ $item->cost }}">
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden>                                            
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)" value="{{ $item->vat }}"></td>
                                            <td><input class="form-control text-center" type="number" id="qty_{{ $item->id }}" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)" value="{{ $item->qty }}"></td>
                                            <td><input class="form-control text-end" type="number" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)" value="{{ $item->price }}"></td>
                                            <td><input class="form-control text-end" type="number" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="number" step="Any" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)" value="{{ $item->discount }}"></td>
                                            <td><input class="form-control text-end" type="number" name="taxableamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="number" name="vatamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="number" name="totalamount[]" autocomplete="off" min="0" readonly></td>
                                        </tr>
                                        @endforeach
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function() {
                                                // trigger change on all qty fields once
                                                document.querySelectorAll('input[name="qty[]"]').forEach(function(el) {
                                                    el.dispatchEvent(new Event("change"));
                                                });
                                            });
                                        </script>
                                        @endif
                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ count($quotationitems)+1 }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control noborder " name="part_number[]">
                                                </select>
                                                {{-- on focus add this class and its funcanalities js-product-select --}}
                                            </td> 
                                            <td><input class="form-control" type="text" name="description[]"></td>
                                            <td>                                                                    
                                                <input class="form-control text-end" type="number" name="cost[]" autocomplete="off" >
                                                <input class="form-control" type="text" name="part_number_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="hscode_txt[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type[]" autocomplete="off" readonly="true" hidden>
                                                <input class="form-control" type="text" name="product_type_part_number_text[]" autocomplete="off" readonly="true" hidden>                                            
                                            </td>
                                            <td><input type="number" class="form-control text-center" name="tax[]" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-center" type="number" name="qty[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-end" type="number" name="unitprice[]" step="any" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-end" type="number" name="value[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="number" step="Any" name="discount[]" autocomplete="off" min="0" onchange="calc_change_new(this)"></td>
                                            <td><input class="form-control text-end" type="number" name="taxableamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="number" name="vatamount[]" autocomplete="off" min="0" readonly></td>
                                            <td><input class="form-control text-end" type="number" name="totalamount[]" autocomplete="off" min="0" readonly></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" scope="col" >Total</th>
                                            <th class="text-center"><label id="lbl_total_qty" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_price" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_value" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_discount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_taxableamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_vatamount" >0</label></th>
                                            <th class="text-end" scope="col" ><label id="lbl_total_totalamount" >0</label></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>

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
                                            <td><select class="form-control noborder" name="cfc_name[]" id="cfc_name_1">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->selling_exp_account)? @$edit_cfc[0]->selling_exp_account==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select></td>
                                            <td> <select class="form-control noborder" name="cfc_credit_account[]" id="cfc_credit_account_1"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[0])? !empty(@$edit_cfc[0]->credit_account)? @$edit_cfc[0]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select></td>
                                            <td><input class="form-control text-end" type="number" id="cfc_amount_1" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(1)" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->amount) ? @$edit_cfc[0]->amount : old('')) : old('') }}" step="any" ></td>
                                            <td><input class="form-control" type="text" id="cfc_remarks_1" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[0]) ? (!empty(@$edit_cfc[0]->remarks) ? @$edit_cfc[0]->remarks : old('')) : old('') }}"></td>
                                        </tr>
                                        <tr>
                                            <td><select class="form-control noborder" name="cfc_name[]" id="cfc_name_2">
                                <option value=""></option>
                                @foreach ($customs_freight_account as $key => $value)
                                    <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->selling_exp_account)? @$edit_cfc[1]->selling_exp_account==$value->id ? 'selected':'':'':''}} >{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select></td>
                                            <td><select class="form-control noborder" name="cfc_credit_account[]" id="cfc_credit_account_2"
                                readonly="true">
                                <option value="none"></option>
                                @foreach ($supplier as $key => $value)
                                <option value="{{ @$value->id }}" {{isset($edit_cfc[1])? !empty(@$edit_cfc[1]->credit_account)? @$edit_cfc[1]->credit_account==@$value->id ? 'selected':'':'':''}}>{{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                @endforeach
                            </select></td>
                                            <td><input class="form-control text-end" type="number" id="cfc_amount_2" name="cfc_amount[]"
                                autocomplete="off" min="0" onchange="cfc_amount_change(2)" value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->amount) ? @$edit_cfc[1]->amount : old('')) : old('') }}" step="any" ></td>
                                            <td><input class="form-control" type="text" id="cfc_remarks_2" name="cfc_remarks[]"
                                autocomplete="off" value="{{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->remarks) ? @$edit_cfc[1]->remarks : old('')) : old('') }}"></td>
                                        </tr>
                                    </tbody>
                                </table>
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

        <script>
    let descriptionModal;
    document.addEventListener("DOMContentLoaded", function() {
        const descriptionElement = document.getElementById('descriptionModal');
        descriptionModal = new bootstrap.Modal(descriptionElement);
    });
    let currentDescriptionInput = null;

    $(document).on('click', 'input[name="description[]"]', function() {
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
    });
    let currentSerialInput = null;
    
    $(document).on('click', 'input[name="serial_no[]"]', function () {
        currentSerialInput = $(this);
        $('#add_serial_no').val(currentSerialInput.val());
        serialNoModal.show();
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
    function calc_change_new(el) {
    $("#loading_bg").css("display", "block");

    // Get the current row
    var $row = $(el).closest('tr');

    // Read values from the current row
    var net_vat = $row.find('input[name="tax[]"]').val() || '0';

    var qty = $row.find('input[name="qty[]"]').val() || '0';
    var unitprice = $row.find('input[name="unitprice[]"]').val() || '0';
    var discount = $row.find('input[name="discount[]"]').val() || '0';
    var fright = 0;
    var customcharges = 0;

    var decimal_point = @json(session('logged_session_data.decimal_point'));

    // Calculate value
    var fin_value = parseFloat(unitprice) * parseFloat(qty);
    $row.find('input[name="value[]"]').val(fin_value.toFixed(decimal_point));

    // Calculate taxable amount
    var fin_taxableamount = fin_value + parseFloat(customcharges) + parseFloat(fright) - parseFloat(discount);
    $row.find('input[name="taxableamount[]"]').val(fin_taxableamount.toFixed(decimal_point));

    // Calculate VAT
    var fin_vatamount = fin_taxableamount * (parseFloat(net_vat) / 100);
    $row.find('input[name="vatamount[]"]').val(fin_vatamount.toFixed(decimal_point));

    // Calculate total amount
    var total_amount = fin_taxableamount + fin_vatamount;
    $row.find('input[name="totalamount[]"]').val(total_amount.toFixed(decimal_point));

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
        total_price += parseFloat($row.find('input[name="unitprice[]"]').val()) || 0;
        total_value += parseFloat($row.find('input[name="value[]"]').val()) || 0;
        total_discount += parseFloat($row.find('input[name="discount[]"]').val()) || 0;
        //total_fright += parseFloat($row.find('input[name="fright[]"]').val()) || 0;
        //total_customcharges += parseFloat($row.find('input[name="customcharges[]"]').val()) || 0;
        total_taxableamount += parseFloat($row.find('input[name="taxableamount[]"]').val()) || 0;
        total_vatamount += parseFloat($row.find('input[name="vatamount[]"]').val()) || 0;
        total_totalamount += parseFloat($row.find('input[name="totalamount[]"]').val()) || 0;
    });

    $('#lbl_total_qty').text(total_qty);
    $('#lbl_total_price').text(total_price.toFixed(decimal_point));
    $('#lbl_total_value').text(total_value.toFixed(decimal_point));
    $('#lbl_total_discount').text(total_discount.toFixed(decimal_point));
    //$('#lbl_total_fright').text(total_fright.toFixed(decimal_point));
    //$('#lbl_total_customcharges').text(total_customcharges.toFixed(decimal_point));
    $('#lbl_total_taxableamount').text(total_taxableamount.toFixed(decimal_point));
    $('#lbl_total_vatamount').text(total_vatamount.toFixed(decimal_point));
    $('#lbl_total_totalamount').text(total_totalamount.toFixed(decimal_point));
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
            placeholder: 'Select Product',
            minimumInputLength: 2,
            dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
        });

        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            var $row = $(this).closest('tr'); // find the closest row

            // Set values using "name" attribute selectors inside the same row
            //$row.find('input[name="description[]"]').val(selectedData.description || '');
            $row.find('input[name="description[]"]').val(selectedData.description || '');
            $row.find('input[name="part_number_txt[]"]').val(selectedData.text || '');
            $row.find('input[name="hscode_txt[]"]').val(selectedData.hscode || '');
            $row.find('input[name="product_type[]"]').val(selectedData.product_type || '');
            $row.find('input[name="product_type_part_number_text[]"]').val(selectedData.description || '');
            $row.find('input[name="discount[]"]').val(0);
            $row.find('input[name="tax[]"]').val(parseInt($('#net_vat').val()));
            
        });

        
    }

    initAccountSelect2('.js-product-select');

    // Re-initialize on focus if needed
    $(document).on('focus', '.js-product-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
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
            $(document).ready(function(){
                if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
                else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
            });

            $(document).on("change", "#source", function () {
            if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
            else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
            });

            function change_cust_id() {
                var id = $("#cust_id").val();
                var user = $("#user_id").val();
                get_cust_name(id);
                get_sales_person(id,user);
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
                    success: function(dataResult) {
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                            if(dataResult['data'] != null){
                                len = dataResult['data'].length;
                            }
                            if(len > 0){
                                for(var i=0; i<len; i++){
                                    var name = dataResult['data'][i].customer_salutation +' '+ dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                    var address = dataResult['data'][i].address +', '+dataResult['data'][i].address2 +', '+dataResult['data'][i].city +', '+dataResult['data'][i].statename +', '+dataResult['data'][i].name;
                                    $("#cust_name").val(name.replace('null ','').replace('null',''));
                                    $("#designation").val(dataResult['data'][i].designation);
                                    $("#cust_no").val(dataResult['data'][i].mobile);
                                    $("#cust_email").val(dataResult['data'][i].email);
                                    $("#address").val(address);
                                    $('#payment_terms').val(dataResult['data'][i].payment_terms).trigger('change');

                                    //1.Reseller
                                    if(dataResult['data'][i].account_type == 1){
                                        $("#isproject").val(1);
                                        $('#is_professional_service').prop( "checked", false );
                                    }//2.Enduser
                                    if(dataResult['data'][i].account_type == 2){
                                        $("#isproject").val(2);
                                        $('#is_professional_service').prop( "checked", false );
                                    }//3.Ecommerce
                                    if(dataResult['data'][i].account_type == 3){
                                        $("#isproject").val(3);
                                        $('#is_professional_service').prop( "checked", false );
                                    }
                                }
                            }
                            else{
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
            function get_sales_person(id,user) {
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
                    success: function(dataResult) {
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                            if(dataResult['data'] != null){
                                len = dataResult['data'].length;
                            }
                            if(len > 0){
                                $('#owner').find('option').remove();
                                for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var name = dataResult['data'][i].full_name;
                                    var sele='';
                                    if(user == id) { sele='selected'; }
                                    var option = "<option value='"+id+"' "+sele+">"+name+"</option>";
                                    $("#owner").append(option);
                                }
                            }
                            else{
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
                    success: function(dataResult) {
                        //alert(dataResult);
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        if(dataResult['data']=="ERROR")
                        {
                            alert("Error found in something!!");
                            $("#btn_add_company").css("display", "block");
                        }
                        else if(dataResult['data']=="ERROR2")
                        {
                            alert("Company Name already exists!! Please Contact Support");
                            $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                            $("#btn_add_company").css("display", "block");
                        }
                        else{
                            if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                            }
                            if(len > 0){

                                $('#cust_id').find('option').not(':first').remove();
                               var newCompanyId = dataResult['new_company_id'];

                                for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var name = dataResult['data'][i].name;
                                    var name2 = dataResult['data'][i].code;
                                    var option = "<option value='"+id+"'>"+name+"</option>";
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

            $(document).ready(function() {
                // Trigger change event only if a country is selected by default
                if ($('#country_ship').val() !== '') {
                    $('#country_ship').trigger('change');
                }
            });

          
        </script>
{{-- 
        <div class="modal side-panel fade" id="ModalNote" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-comments-add', 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                    <textarea name="comments" class="form-control" id="comments" cols="10" rows="3"></textarea>
                    <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                    <input type="hidden" id="commentsid" name="commentsid" value="{{ $edit->id }}" />
                    <div class="mt-2 justify-content-end d-flex">
						<button type="submit" class="btn btn-light add-btn ms-2">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Internal Note
						</button>
                    </div>                        
                    {{ Form::close() }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-2">
                    @if($edit->note != "")<b>Deal Notes :- </b>
                    <div class="notes border-bottom mt-2"> {!! nl2br($edit->note) !!} </div>
                    @endif
                    @if(count($comments)>0)
                    <div class="notes border-bottom mt-3">
                        @foreach ($comments as $cmts)
                        <div>
                            @if ($cmts->created_by == Auth::user()->id)
                            <a href="{{url('crm-deals-comments-delete/'.$cmts->id.'')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-window-close text-sm text-danger float-right" aria-hidden="true"></i></a>
                            @endif
                            <p class="mb-0">{!! nl2br($cmts->comments) !!}
                                @if ($cmts->commentsdoc!="")
                                    <a class="text-info p-0" href="{{asset('public/uploads/crm_deal_doc/')}}/{{ $cmts->commentsdoc }}" target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                                @endif
                                <span class="text-muted text-right">{{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($cmts->created_at))}}</span>
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
        <div class="modal side-panel fade" id="ModalChangeCurrancy" data-bs-backdrop="false" tabindex="-1" aria-labelledby="ModalChangeCurrancy" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Currancy</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-update-currency', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Currancy From</label>
                                <select class="form-control" name="from_currency_id" required>
                                    @foreach ($currencylist as $value)
                                        @if(@$currency_id == $value->id)
                                            <option value="{{ @$value->id }}" @if(@$currency_id == $value->id) selected @endif>{{ @$value->code }}</option>
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
                    <input type="hidden" name="cur_quote_id" value="{{ $quote_id }}"/>
                    <input type="hidden" name="cur_deal_id" value="{{ $edit->id }}"/>
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
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('terms_and_condition');
        const narrationTextarea = document.getElementById('narrationTextarea');
        const insertButton = document.getElementById('insertNarration');
        const narrationModal = document.getElementById('narrationModal');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('show.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
        });

        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });
</script>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
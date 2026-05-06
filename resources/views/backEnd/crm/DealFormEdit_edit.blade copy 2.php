<?php try { ?>

  

@if (isset($edit))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-deals-form', 'novalidate' => true]) }}
@else
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deals-form', 'novalidate' => true]) }}
@endif
<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
<input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
<input type="hidden" name="quote_id" value="{{ $quote_id }}">
<input type="hidden" name="net_vat" id="net_vat" value="{{ $edit->customername->vat_percentage }}">


<style>
.form-item-table .select2-container--default .select2-selection--single{ border: none !important;}
.form-item-table.select2-container--default .select2-selection--single .select2-selection__arrow b { display: none !important; }
</style>

<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        Edit - {{ $edit->code }}
    </h4>
    <div class="purchase-order-content-header-right">

         <a class="btn btn-light text-dark" href="{{url('crm-deals-add')}}">
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

                {{-- <li><a class="dropdown-item" href="{{url('crm-quote/'.$edit->id.'/download/'.$edit->quote_id)}}"><i
                            class="ico icon-outline-document-medicine text-success"></i> Download</a></li> --}}

                @if($edit->stage == 4 || $edit->stage == 1)
                    @if (count($support)==0)
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalSupport" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center  text-dark"><i class="ico icon-outline-add-square text-success title-15 me-2"></i> Add Pre-Sales Request</a></li>
                    @else
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalSupportCmt" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> Add Pre-Sales Request Comments</a></li>
                    @endif
                @endif
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalCollaboration" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> Add Collaboration</a></li>
        @if ($quotationitems->where('product_type', 2)->count() < 1)
                   
                    <li><a type="button"  data-modal-size="modal-md" data-bs-target="#ModalEndUserDetails" data-bs-toggle="modal" class="dropdown-item d-flex align-items-center  text-dark"><i class="ico icon-outline-add-square text-success  title-15 me-2"></i> End User Details</a></li>
        @endif

   @if (!empty($edit->track) && !empty($edit->track->id))
                        <li>
                            <a target="__blank" 
                            href="{{ url('crm-deal-track-approval-list/' . $edit->track->id) }}" 
                            class="dropdown-item d-flex align-items-center text-dark">
                                <i class="ico icon-outline-document-text text-success title-15 me-2"></i>
                                Deal Track
                            </a>
                        </li>
                    @endif


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
                    <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required
                        onchange="change_cust_id()">
                        <option value=""></option>
                        @foreach ($vendors as $value)
                              <option value="{{ @$value->id }}" @if (@$edit->cust_id == $value->id) selected @endif>{{ trim(@$value->name) }}@if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'] == 1) ({{ trim(@$value->code) }})@endif</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-2">
                <label class="form-label">Deal Name</label>
                <div class="form-group">
                    <input class="form-control" type="text" name="deal_name" autocomplete="off" id="deal_name"
                        value="{{ isset($edit) ? (!empty(@$edit->deal_name) ? @$edit->deal_name : old('deal_name')) : old('deal_name') }}"
                        required>
                </div>
            </div>

            <script>
                $(document).ready(function () {

    $(document).on("input", "#deal_name", function () {
        let val = $(this).val();

        // Capitalize first letter of every word
        val = val.replace(/\b\w/g, function (char) {
            return char.toUpperCase();
        });

        $(this).val(val);
    });

});

            </script>
          

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
           


                <div class="col-lg-2">
                    <div class="input-effect">
                        <label class="form-label">Value<span>*</span></label>
                        <div class="form-group">
                                <input class="form-control" type="text" step="any" name="deal_value" autocomplete="off"
                                id="deal_value" readonly disabled
                                value="{{ isset($edit) ? (!empty(@$edit->deal_value) ? @App\SysHelper::currancy_format($edit->deal_value, $edit->currency_id) : old('deal_value')) : old('deal_value') }}">
                        </div>

                    </div>
                </div>
            <div class="col-2 ">
                <label class="form-label">Deal Profit</label>
                
                     <input class="form-control" type="text" autocomplete="off"
                        value="{{ @App\SysHelper::currancy_format($edit->deal_profit, $edit->currency_id)}}" readonly disabled>
               
            </div>


            {{-- <div class="col-2">
                <label class="form-label">Created By </label>
                <div class="form-group">
                    <select class="form-control" name="owner" id="owner" required>
                        <option value="{{ @$edit->owner }}">{{ @$edit->ownername->full_name }}</option>
                    </select>
                </div>
            </div> --}}





         


        </div>
    </div>
</div>


 <style>
                            .col-5-custom {
                                flex: 0 0 auto;
                                width: 20%;
                            }
                        </style>
<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields"
                type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="delivery-fields-tab" data-bs-toggle="tab" data-bs-target="#delivery-fields"
                type="button" role="tab" aria-controls="delivery-fields" aria-selected="true">Delivery Location</button>
        </li>

        


                   <li class="nav-item" role="presentation">
            <button class="nav-link " id="salesperson-fields-tab" data-bs-toggle="tab" data-bs-target="#salesperson-fields"
                type="button" role="tab" aria-controls="salesperson-fields" aria-selected="true">Sales Person</button>
        </li>


        <li class="nav-item" role="presentation">
            <button class="nav-link " id="quote-fields-tab" data-bs-toggle="tab" data-bs-target="#quote-fields"
                type="button" role="tab" aria-controls="quote-fields" aria-selected="true">Quote</button>
        </li>

        @if ($quotationitems->where('product_type', 2)->count() > 0)
        
      <li class="nav-item" role="presentation">
            <button class="nav-link " id="enduser-fields-tab" data-bs-toggle="tab" data-bs-target="#enduser-fields"
                type="button" role="tab" aria-controls="enduser-fields" aria-selected="true">End User Details</button>
        </li>
        @endif

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="editfullfill-fields-tab" data-bs-toggle="tab"
                data-bs-target="#editfullfill-fields" type="button" role="tab" aria-controls="editfullfill-fields"
                aria-selected="true">Edit Fullfill</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link " id="internal-fields-tab" data-bs-toggle="tab" data-bs-target="#internal-fields"
                type="button" role="tab" aria-controls="internal-fields" aria-selected="true">Internal Note</button>
        </li>
    </ul>
    <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
        <div class="tab-pane fade " id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
            <div class="row gap-rows">

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Contact Person Name<span>*</span></label>
                        <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name"
                            value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}"
                            required>
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Designation</label>
                        <input class="form-control" type="text" name="designation" autocomplete="off" id="designation"
                            value="{{ isset($edit) ? (!empty(@$edit->designation) ? @$edit->designation : old('designation')) : old('designation') }}">
                    </div>
                </div>

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Mobile<span>*</span></label>
                        <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no"
                            value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Email</label>
                        <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" data-bs-target="#EmailModal" data-bs-toggle="modal"
                            value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Address<span></span></label>
                        <input class="form-control" type="text" name="address" autocomplete="off" id="address" data-bs-target="#AddressModal" data-bs-toggle="modal"
                            value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                    </div>
                </div>
                <div class="col-lg-2 mb-2">
                
                    <div class="input-effect">
                        <label class="form-label">@lang('Brand') <span>*</span></label>
                      @php
    // Convert "aruba,aio,cisco wifi" into ['aruba', 'aio', 'cisco wifi']
    $selectedTags = !empty($edit->tags) ? array_map('trim', explode(',', $edit->tags)) : [];
@endphp

<select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple>
    @foreach ($brand as $value)
        <option value="{{ $value->title }}"
            @if(in_array($value->title, $selectedTags)) selected @endif>
            {{ $value->title }}
        </option>
    @endforeach
</select>
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
                        <input class="form-control date-picker" id="date" type="text" name="date"
                            value="{{ @App\SysHelper::normalizeToDmy(@$value) }}">
                    </div>
                </div>
            
                <div class="col-lg-2 mb-2" style="display: none;">
                    <div class="input-effect">
                        <label class="form-label">Source<span>*</span></label>
                        <select class="form-control js-example-basic-single" name="source" id="source">
                            <option value="">-Select-</option>
                            <option value="Chat" @if (@$edit->source == 'Chat') selected @endif>Chat</option>
                            <option value="Call" @if (@$edit->source == 'Call') selected @endif>Call</option>
                            <option value="Mail" @if (@$edit->source == 'Mail') selected @endif @if (!isset($edit))
                            selected @endif>Mail</option>
                            <option value="Website" @if (@$edit->source == 'Website') selected @endif>Website
                            </option>
                            <option value="Gitex 2023" @if (@$edit->source == 'Gitex 2023') selected @endif>Gitex
                                2023</option>
                            <option value="Gitex" @if (@$edit->source == 'Gitex') selected @endif>Gitex
                            </option>
                            <option value="Fulfillment" @if (@$edit->source == 'Fulfillment') selected @endif>
                                Fulfillment</option>
                            <option value="Ecommerce" @if (@$edit->source == 'Ecommerce') selected @endif>Ecommerce
                            </option>
                            <option value="Other" @if (@$edit->source == 'Other') selected @endif>Other
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 mb-2" id="sourcediv" style="display: none;">
                    <div class="input-effect">
                        <label class="form-label">Other Source<span>*</span></label>
                        <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o"
                            value="{{ isset($edit) ? (!empty(@$edit->source_o) ? @$edit->source_o : old('source_o')) : old('source_o') }}"
                            style="display: none;" placeholder="Source">
                    </div>
                </div>


                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Deal Type<span></span></label>
                        <select class="form-control js-example-basic-single" name="isproject" id="isproject">
                            {{-- <option value="4" @if (@$edit->isproject == '4') selected @endif >Project</option> --}}
                            <option value="1" @if (@$edit->isproject == '1') selected @endif>Reseller
                            </option>
                            <option value="2" @if (@$edit->isproject == '2') selected @endif>Enduser
                            </option>
                            <option value="3" @if (@$edit->isproject == '3') selected @endif>E-Commerece
                            </option>
                            <option value="5" @if (@$edit->isproject == '5') selected @endif>Marketing
                            </option>
                        </select>
                        <script>
                            $('#isproject').on('change', function (e) {
                                if ($('#isproject').val() == 4) {
                                    $('#is_professional_service').prop("checked", true);
                                } else {
                                    $('#is_professional_service').prop("checked", false);
                                }
                            });
                        </script>
                    </div>
                </div>

                
             

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Project Service<span>*</span></label>
                        <div class="form-control d-flex justify-content-center align-items-center">
                            <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                id="is_professional_service" name="is_professional_service" @if($edit->is_professional_service == 1) checked @endif>
                            <label class="form-label ml-4" for="is_professional_service">Yes, Project
                                Service</label>
                        </div>
                    </div>
                </div>

          

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Stage<span>*</span></label>
                        <select class="form-control js-example-basic-single" name="stage" id="stage">
                            <option value="1" @if (@$edit->stage == 1) selected @endif>Prospecting
                            </option>
                            <option value="2" @if (@$edit->stage == 2) selected @endif>Quote</option>
                            <option value="3" @if (@$edit->stage == 3) selected @endif>Closure
                            </option>
                            <option value="4" @if (@$edit->stage == 4) selected @endif>Won</option>
                            <option value="5" @if (@$edit->stage == 5) selected @endif>Lost</option>
                        </select>
                        <textarea class="primary-input dynamicstxt_s w-100 form-control" name="lost_comments" rows="4"
                            style="height: 50px !important; display: none;" autocomplete="off" id="lost_comments"
                            placeholder="Reason"></textarea>
                        <script>
                            $('#stage').on('change', function (e) {
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

                   <div class="col-2" id="followup_date_div">
                        <label class="form-label">FollowUp Date<span>*</span></label>
                  @php


$followupDate = @$edit->followup_date;

// If value exists, convert from DB (UTC or system timezone) to Dubai time
if (!empty($followupDate)) {
    try {
        $followupDate = Carbon\Carbon::parse($followupDate)
           
            ->format('d/m/Y h:i A'); // Match Flatpickr
    } catch (\Exception $e) {
        // Fallback: in case parsing fails
        $followupDate = Carbon\Carbon::now()
            ->addDays(3)
            ->setTime(11, 0)
            ->format('d/m/Y h:i A');
    }
} else {
    // If not set, default = +3 days 11 AM Dubai
    $followupDate = Carbon\Carbon::now()
        ->addDays(3)
        ->setTime(11, 0)
        ->format('d/m/Y h:i A');
}
@endphp
                        <input type="text" class="form-control date-time-picker" name="followup_date" id="followup_date" value="{{ $followupDate }}">
                </div>

                  <div class="col-2">
                <label class="form-label">Company</label>
                <div class="form-group">
                    <select class="form-control js-example-basic-single" name="company" id="company" required>
                       
                           
                            @foreach ($company as $value)
                                <option value="{{ @$value->id }}" @if ($edit->company_id == @$value->id)
                                selected @endif>{{ @$value->company_name }}
                                </option>
                            @endforeach
                       
                    </select>
                </div>
            </div>

                <div class="col-lg-2 mb-2">
                    <div class="input-effect">
                        <label class="form-label">Attach<span>*</span></label>
                        <input type="file" class="form-control" name="doc" id="doc">
                    </div>
                </div>
                <div class="col mb-2">
                    <div class="input-effect">
                        <label class="form-label">Notes<span>*</span></label>
                        <input class="form-control" name="note" rows="3" autocomplete="off" id="note" data-bs-toggle="modal" value="@if(isset($edit)) {{$edit->note}} @endif" data-bs-target="#NoteModal">


                    </div>
                </div>


                 <div class="col-auto    ms-auto" style="margin-top:1.5rem">

                     <input type="hidden" name="quotation_generated" id="quotation_generated"
    value="{{ request()->query('new') == 'yes' ? 1 : ((count($quotationitems) < 1) ? 0 : 1) }}">

                @if(count($quotationitems) < 1 )
                        
                        
                    <button class="btn btn-sm btn-light add-btn" type="button" href="#" onclick="quote_generate()">
                        <i class="ico icon-bold-document-add text-success" style="font-size: 16px"></i>
                        <span>Generate Quotation</span>
                    </button>
                    @endif

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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <select class="form-control js-example-basic-single" name="delivery_company"
                                    id="delivery_company" >
                                    <option value="">-Select-</option>
                                    @foreach ($cust_supp ?? [] as $value)
                                        <option value="{{ $value->name }}" {{ isset($edit->delivery_company) && $edit->delivery_company == $value->name ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                      

                        {{-- Contact Person --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" class="form-control" name="delivery_name" id="delivery_name"
                                    value="{{ old('delivery_name', $edit->delivery_name ?? ($leads->cust_name ?? '')) }}"
                                    >
                            </div>
                        </div>

                      

                        {{-- Contact Number --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="delivery_number" id="delivery_number"
                                    value="{{ old('delivery_number', $edit->delivery_number ?? ($leads->cust_no ?? '')) }}"
                                    >
                            </div>
                        </div>

                      

                        {{-- Email --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="delivery_email" id="delivery_email"
                                    value="{{ old('delivery_email', $edit->delivery_email ?? ($leads->cust_email ?? '')) }}"
                                    >
                            </div>
                        </div>

                        {{-- Country --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select class="form-control js-example-basic-single" id="country_n_e"
                                    name="delivery_country" >
                                    <option value="">Select Country</option>
                                    @foreach ($countries ?? [] as $value)
                                        <option value="{{ $value->id }}" {{ ($edit->delivery_country ?? ($addressbook->country ?? '')) == $value->id ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                      

                        {{-- State --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <select class="form-control js-example-basic-single" id="state_n_e"
                                    name="delivery_state" >
                                    <option value="">Select State</option>
                                    @foreach ($states ?? [] as $st)
                                        <option value="{{ $st->id }}" {{ ($edit->delivery_state ?? ($addressbook->state ?? '')) == $st->id ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                          {{-- Address 1 --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Address 1</label>
                                <input class="form-control" type="text" id="delivery_address1" name="delivery_address1"
                                    value="{{ old('delivery_address1', $edit->delivery_address1 ?? ($addressbook->address ?? '')) }}"
                                    >
                            </div>
                        </div>

                       
                        {{-- Address 2 --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Address 2</label>
                                <input class="form-control" type="text" id="delivery_address2" name="delivery_address2"
                                    value="{{ old('delivery_address2', $edit->delivery_address2 ?? ($addressbook->address2 ?? '')) }}"
                                    >
                            </div>
                        </div>

                       

                        {{-- City --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input class="form-control" type="text" id="delivery_city" name="delivery_city"
                                    value="{{ old('delivery_city', $edit->delivery_city ?? ($addressbook->city ?? '')) }}"
                                    >
                            </div>
                        </div>

                        
                

                        {{-- PO Box --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">PO Box</label>
                                <input class="form-control" type="text" name="delivery_zip_code" id="delivery_zip_code"
                                    value="{{ old('delivery_zip_code', $edit->delivery_zip_code ?? ($addressbook->zip_code ?? '')) }}"
                                    >
                            </div>
                        </div>


                    </div>
                </div>
            </div>




        </div>

          <div class="tab-pane fade" id="salesperson-fields" role="tabpanel" aria-labelledby="salesperson-fields-tab">
          <div class="row text-start">

                    <!-- Sales Person -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Sales Person</p>
                        {{-- {{ @$edit->ownername->first_name }} {{ @$edit->ownername->middle_name }} {{ @$edit->ownername->last_name }} --}}

                           <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                        <option value="">-Select-</option>

                                        @foreach ($sales_person as $value)
                                            <option value="{{ $value->user_id }}" @if($edit->owner == $value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                    </div>

                    <!-- Mobile -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Mobile</p>
                        {{ @$edit->ownername->mobile }}
                    </div>

                    <!-- Email -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Email</p>
                        {{ @$edit->ownername->email }}
                    </div>

                    <!-- Ext No -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Ext No</p>
                        {{ @$edit->ownername->ext_no ?? '--' }}
                    </div>

                    <!-- Source -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Source</p>
                        @if (@$edit->source != '')
                            {{ @$edit->source }} @if(@$edit->source_o != '') - {{ @$edit->source_o }} @endif
                        @endif
                    </div>

                    <!-- Close Date -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Close Date</p>
                        {{ date('d/m/Y', strtotime(@$edit->estimated_close_date)) }}
                    </div>

                    <!-- Added By -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Added By</p>
                        {{ @$edit->createdby->full_name }}
                    </div>

                    <!-- Added On -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Added On</p>
                        {{ date('d/m/Y h:i A', strtotime(@$edit->created_at)) }}
                    </div>

                    <!-- Updated On -->
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <p class="font-weight-600 mb-0">Updated On</p>
                        {{ date('d/m/Y h:i A', strtotime(@$edit->updated_at)) }}
                    </div>

            </div>
        </div>

        <div class="tab-pane fade" id="quote-fields" role="tabpanel" aria-labelledby="quote-fields-tab">

           
                     <script>
$(document).ready(function() {
    $('.btnDownloadQuote').on('click', function(e) {
        e.preventDefault();

        const row = $(this).closest('td');
        const id = $(this).data('id');
        const quote = $(this).data('quote');

        // Collect checkbox values within the same row
        let params = [];
        if (row.find('.withPartNumber').is(':checked')) params.push('with_partnumber=1');
        if (row.find('.excludeVat').is(':checked')) params.push('without_vat=1');
        if (row.find('.withoutTotal').is(':checked')) params.push('without_total=1');


        // Build the final URL
        let url = `/crm-quote/${id}/download/${quote}`;
        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        console.log(url); // For debugging
        window.open(url, '_blank'); // Open in a new tab to download
    });
});
</script>


            <h4 class="mb-1 color-sub-head font-size-13 mb-2">Quote Revisions
            
                 
            </h4> 

            <?php    $editcheck = App\SysHelper::deal_edit_disable($edit->id); ?>




            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:120px">Quote No</th>
                        <th  class="text-center">Actions</th>
                        <th style="width:80px">  
                            <a class="btn btn-sm btn-light text-center text-dark"  href="{{ url('crm-deals-create-quote/'.$edit->id ) }}" style="padding: 0px 8px 0px 8px;border-radius:4px">
                                             <svg style="height:14px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M352 128C352 110.3 337.7 96 320 96C302.3 96 288 110.3 288 128L288 288L128 288C110.3 288 96 302.3 96 320C96 337.7 110.3 352 128 352L288 352L288 512C288 529.7 302.3 544 320 544C337.7 544 352 529.7 352 512L352 352L512 352C529.7 352 544 337.7 544 320C544 302.3 529.7 288 512 288L352 288L352 128z"></path></svg>
                                 Quotation
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
    @$quote_no = App\SysCrmQuoteItems::select('quote_id')->where('deal_id', $edit->id)->groupBy('quote_id')->orderBy('quote_id', 'asc')->get();
                        ?>
                    @foreach (@$quote_no as $item)
                        <tr>
                            <!-- Quote Number -->
                            <td>
                                <strong>{{ $edit->deal_code->code }} @if($item->quote_id != 1) - {{ $item->quote_id - 1 }} @endif</strong>
                            </td>

                            <!-- Action Buttons -->
                            <td class="d-flex justify-content-start align-items-center gap-2">

                                     <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input withPartNumber" type="checkbox" id="withPartNumber{{ $item->quote_id }}" name="with_partnumber"
                                            value="1">
                                        <label class="form-label" for="withPartNumber{{ $item->quote_id }}">Include Part Numbers</label>
                                    </div>

                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input excludeVat" type="checkbox" id="excludeVat{{ $item->quote_id }}" name="without_vat" value="1">
                                        <label class="form-label" for="excludeVat{{ $item->quote_id }}">Exclude VAT</label>
                                    </div>

                                    <div class="form-check form-check-inline me-4">
                                        <input class="form-check-input withoutTotal" type="checkbox" id="withoutTotal{{ $item->quote_id }}" name="without_total"
                                            value="1">
                                        <label class="form-label" for="withoutTotal{{ $item->quote_id }}">Hide Total</label>
                                    </div>


                                <!-- Download -->
                                <a class="btn btn-sm btn-light btnDownloadQuote text-dark"
                                    style="padding: 0px 8px 0px 8px;border-radius:4px"  data-id="{{ $edit->id }}" 
                                    data-quote="{{ $item->quote_id }}">
                                    <i class="ico icon-bold-download-minimalistic text-success" style="font-size:16px"></i>
                                    Download
                                </a>

                                @if ($editcheck == 0)
                                    <!-- Edit -->
                                    <a class="btn btn-sm btn-light  text-dark"
                                        href="{{ url('crm-deals/show/' . $edit->id . '?deal_action=edit&quote=' . $item->quote_id) }}">
                                        <i class="ico icon-outline-pen-2 text-success" style="font-size:16px"></i>
                                        Edit
                                    </a>

                                    <!-- Create Copy -->
                                    <a class="btn btn-sm btn-light text-dark me-4"
                                        href="{{ url('crm-quote/' . $edit->id . '/createcopy/' . $item->quote_id) }}">
                                        <i class="ico icon-outline-copy text-success" style="font-size:16px"></i>
                                        Create Copy
                                    </a>
                                @endif

                                <!-- Set as Final Quote / Final Quote Label -->
                                @if ($item->quote_id != $edit->quote_id)
                                    @if ($editcheck == 0)
                                        <a class="btn btn-sm btn-light text-dark"
                                            href="{{ url('crm-quote/' . $edit->id . '/setprimary/' . $item->quote_id) }}">
                                            <i class="ico icon-outline-check-square text-success" style="font-size:16px"></i> Set as
                                            Final Quote
                                        </a>
                                    @endif
                                @else
                                    <span class="btn btn-sm btn-light text-dark "
                                        style="padding: 0px 8px 0px 8px;border-radius:4px">Final Quote</span>
                                @endif
                            </td>

                            <td></td>
                        </tr>
                    @endforeach


                </tbody>
            </table>

        </div>

        @if ($quotationitems->where('product_type', 2)->count() > 0)

           <div class="tab-pane fade" id="enduser-fields" role="tabpanel" aria-labelledby="enduser-fields-tab">

           
                 @if ($enduser=="")
                    <div id="enduser-form">

                    <input type="hidden" id="end_user_deal_id" value="{{ $edit->id }}">

                    <div class="row">

                        <div class="col-md-3">
                            <label class="form-label">Company Name *</label>
                            <input type="text" class="form-control" id="end_user_company_name">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Device Serial</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="device_serial" readonly onclick="openDeviceSerialModal()">
                                <button type="button" class="btn btn-light border" onclick="openDeviceSerialModal()">
                                    <i class="ico icon-outline-list-down"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Contact Person *</label>
                            <input type="text" class="form-control" id="end_user_contact_person">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Mobile No</label>
                            <input type="text" class="form-control" id="mobile_no">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="project_name">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Brief description about this project</label>
                            <input class="form-control" id="project_description">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">When it is expected to Close</label>
                            <input type="text" class="form-control date-picker" id="expected_close_date">
                        </div>

                    </div>

                    <div class="modal-footer p-0 mt-2">
                        <button type="button" id="saveEndUser" class="btn btn-light add-btn ms-2">
                            <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                        </button>
                    </div>

                </div>

                <script>
                    $(document).on("click", "#saveEndUser", function () {

    let data = {
        end_user_deal_id: $("#end_user_deal_id").val(),
        end_user_company_name: $("#end_user_company_name").val(),
        device_serial: $("#device_serial").val(),
        end_user_contact_person: $("#end_user_contact_person").val(),
        mobile_no: $("#mobile_no").val(),
        email: $("#email").val(),
        project_name: $("#project_name").val(),
        project_description: $("#project_description").val(),
        expected_close_date: $("#expected_close_date").val(),
        _token: "{{ csrf_token() }}"
    };

    // basic validation
    if (data.end_user_company_name.trim() === "") {
        toastr.error("Company Name is required"); return;
    }
    if (data.end_user_contact_person.trim() === "") {
        toastr.error("Contact Person is required"); return;
    }

    $.ajax({
        url: "/crm-deal-add-end-user",
        type: "POST",
        data: data,
        beforeSend: function () {
            $("#saveEndUser").prop("disabled", true).html("Saving...");
        },
        success: function (resp) {
            toastr.success("End User details saved successfully");

            // refresh or reload tab
            setTimeout(() => location.reload(), 1000);
        },
        error: function (xhr) {
            toastr.error("Error saving data");
            console.log(xhr.responseText);
        },
        complete: function () {
            $("#saveEndUser")
                .prop("disabled", false)
                .html(`<i class="ico icon-outline-bookmark-opened text-success"></i> Save`);
        }
    });

});

                </script>
     
                @else
                  
                            <div class="row">

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Company Name</p> <br>
                                <span class="truncate-text-custom">{{ $enduser->end_user_company_name }}</span> 
                                </div>

                                <div class="col-2">
                                       @php
        $count_serial = ($enduser->device_serial)
            ? count(array_filter(explode(',', $enduser->device_serial)))
            : 0;
    @endphp
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Device Serial ({{ $count_serial }})</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->device_serial }}</span> 
                                </div>

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Contact Person</p><br>
                                <span class="truncate-text-custom">{{ $enduser->end_user_contact_person }}</span> 
                                </div>

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Mobile No</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->mobile_no }}</span> 
                                </div>

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Email</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->email }}</span>
                                </div>

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Project Name</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->project_name }}</span>
                                </div>

                                <div class="col-2 mb-3">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Brief description about this project</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->project_description }}</span>
                                </div>

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">When it is expected to Close</p><br>
                                    <span class="truncate-text-custom">{{ date('d/m/Y', strtotime($enduser->expected_close_date)) }}</span>
                                </div>

                            </div>
                      


                @endif

        </div>
        @endif

        <div class="tab-pane fade show active" id="editfullfill-fields" role="tabpanel" aria-labelledby="editfullfill-fields-tab">

            <?php    $data = App\SysHelper::deal_track_status($edit->id); ?>
            @if (App\SysHelper::set_track($edit->id) == 1)
                @if ($data == 'Fulfill')

                    
                   
                            @if (App\SysHelper::get_company_status($edit->customername) == 0)
                    

                                @php
                                    $validation = @App\SysHelper::get_customer_incomplete_fields($edit->customername);
                                @endphp

                                  @php
                                        $editDoc = @App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)->get();
                                    @endphp

                                 

                                        @php
                                        $ids = array_column($validation['errors'], 'id');
                                        @endphp

                                    <div class="row">

                                        @if (in_array('vat_number', $ids))
                                         <div class="col">
                                        <label for="" class="form-label">VAT Number</label>
                                            <div class=""><input class="form-control" type="text" name="vat_number" id="ci_vat_number"
                                                    value="{{ $edit->customername->vat_number }}">
                                                </div>
                                        </div>
                                        @endif
                                     
                                       
                                        @if (in_array('mobile', $ids))
                                        <div class="col">
                                            <label for="" class="form-label">Customer Mobile</label>
                                            <input class="form-control" type="text" name="mobile" id="ci_mobile" placeholder="Mobile"
                                                value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">

                                        </div>
                                        @endif


                                        @if (in_array('email', $ids))

                                        <div class="col">
                                            <label for="" class="form-label">Customer Email</label>
                                            <input class="form-control" type="text" name="email" id="ci_email" placeholder="Email"
                                                value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}" >
                                        </div>

                                        @endif

                                        @if (in_array('first_name', $ids))

                                          <!-- First Name -->
                                        <div class="col">
                   
                                            <label class="form-label mb-0 me-3" style="min-width: 120px;">Primary
                                                Contact:</label>

                                            <input type="text" class="form-control" id="ci_firstName"
                                                name="first_name" placeholder="First Name"
                                                value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}">
                                        </div>
                                            
                                        @endif

                                        @if (in_array('contact_number', $ids))

                                             <div class="col ">
                                            <label for="" class="form-label">Customer Phone</label>
                                            <input class="form-control" type="text" name="mobile_code" id="ci_mobile_code" placeholder="Work Phone"
                                                value="{{ $edit->customername->contcat_number }}" >
                                        </div> 
                                            
                                        @endif
                                      

                                        @php
                                            $exists = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'Trade License/Commercial Registration')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted
                                                        ->exists();

                                                            $existsVat = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'VAT Certificate')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted
                                                        ->exists();

                                                 
                                        @endphp                         
                                        @if (!$exists)
                                   
                                        
                                        <div class="col">
                                            <input class="form-control" type="hidden" name="doc_name[]"
                                            value="Trade License/Commercial Registration" readonly />
                                                <label for="" class="form-label">Trade License/Commercial Registration</label>
                                                  <input class="form-control" type="file" name="customer_documents_1" id="ci_trade_doc" />
                                                   <input class="form-control date-picker" type="text" id="ci_trade_exp_date" name="doc_exp_date[]"
                                            placeholder="Expiry Date" />
                                        </div>

                                        @endif

                                        @if (!$existsVat)

                                         <div class="col ">
                                           <input class="form-control" type="hidden" name="doc_name[]"
                                            value="VAT Certificate" readonly />
                                                <label for="" class="form-label">VAT Certificate</label>
                                                 <input class="form-control" type="file" name="customer_documents_2" id="ci_vat_doc" />
                                        </div>
                                            
                                        @endif

                                       
                                        

                                   
{{--                                  
                                  <div class="modal fade side-panel" 
                                        id="INCSMODAL" 
                                        data-bs-backdrop="false" 
                                        tabindex="-1" 
                                        aria-labelledby="INCSMODAL" 
                                        aria-hidden="true">

                        <div class="modal-dialog modal-lg" style="width:29rem">
                            <div class="modal-content">

                                <!-- Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title" style="padding-left:0" id="INCSMODAL">{{  ucwords($edit->customername->name) }}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Body -->
                                <div class="modal-body p-0">
                                    <div class="card m-0">
                                        <div class="card-body p-0">

                                            <div class="table-responsive">
                                    <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                <th style="width: 80px;" class="text-center">@lang('Date')</th>
                                                <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            

                                            @forelse ($editDoc as $doc)

                                            <tr>
                                                <td class="text-start">{{ $doc->doc_name }}</td>
                                                <td class="text-center">{{  date('d/m/Y', strtotime(@$doc->doc_exp_date))  }}</td>
                                                <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/cust-suppl/') }}/{{ $doc->doc_file }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                </td>

                                            </tr>
                                            @empty
                                                <p class="text-muted">No files uploaded.</p>
                                            @endforelse

                                        </tbody>
                                    </table>
                                    </div>


                                    

                                            

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                                </div>

                                <div class="col-3">
                                 <button type="button" class="btn btn-sm btn-light font-weight-500 text-success" data-bs-toggle="modal" data-bs-target="#INCSMODAL">View Documents ({{ count($editDoc) }})</button>

                                </div> --}}

                                        {{-- <div class="mt-3">
                                            <div class="d-flex align-items-center gap-2">
                                                   @if (count($editDoc) > 0)
                                        @foreach ($editDoc as $doc)
                                            <a class="btn btn-sm btn-light rounded-0 font-weight-500 truncate-text-custom" target="__blank" href="{{ asset('public/uploads/cust-suppl/') }}/{{ $doc->doc_file }}">
                                                      <i class="ico icon-bold-download-minimalistic text-success" style="font-size:16px"></i>  {{ $doc->doc_name }} ({{ date('d/m/Y', strtotime(@$doc->doc_exp_date)) }})
                                            </a>

                                           
                                              @endforeach
                                    @endif
                                        </div>
                                        </div> --}}

                                       
                                     



                                    </div>

                                    
                              <script>
$(document).ready(function () {

    function updateCustomerEdit() {
        let fd = new FormData();


// inline DOM checks and appends (no helper functions)
let el;

el = document.getElementById('customer_edit_id'); if (el) fd.append('cust_id', el.value);
el = document.getElementById('ci_vat_number');    if (el) fd.append('vat_number', el.value);
el = document.getElementById('ci_mobile');        if (el) fd.append('mobile', el.value);
el = document.getElementById('ci_email');         if (el) fd.append('email', el.value);
el = document.getElementById('ci_salutation');    if (el) fd.append('customer_salutation', el.value);
el = document.getElementById('ci_firstName');     if (el) fd.append('first_name', el.value);
el = document.getElementById('ci_mobile_code');   if (el) fd.append('mobile_code', el.value);

// document names (only if related input exists in DOM)
if (document.getElementById('ci_trade_doc')) fd.append('doc_name[0]', 'Trade License/Commercial Registration');
if (document.getElementById('ci_vat_doc'))   fd.append('doc_name[1]', 'VAT Certificate');

// expiry dates
el = document.getElementById('ci_trade_exp_date'); if (el) fd.append('doc_exp_date[0]', el.value);

// files (check existence and length)
el = document.getElementById('ci_trade_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_1', el.files[0]);

el = document.getElementById('ci_vat_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_2', el.files[0]);

// fd is ready to send via fetch / $.ajax / XHR


        fd.append("_token", "{{ csrf_token() }}");

        $.ajax({
            url: "{{ url('customer-update-deal-track') }}",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loading_bg").show();
            },
            success: function (res) {
                $("#loading_bg").hide();

                if (res.status) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function (xhr) {
                $("#loading_bg").hide();
                toastr.error("Something went wrong!");
            }
        });
    }

    $("#btnupdateCustomer").on("click", function (e) {
        e.preventDefault();
        updateCustomerEdit();
    });

});
                            </script>


                            


                                  

                                        

                            <div class="row pt-3" style="border-top: 1px solid #dee2e6">
                                <div class="col-4">
                                  
                                </div>
                                <div class="col-4 d-flex justify-content-center">
            <input type="hidden" id="customer_edit_id" name="customer_edit_id" value="{{ $edit->customername->id }}" />
                                    <button type="button" class="btn btn-light add-btn ms-2" 
                                        id="btnupdateCustomer"><span class="ti-check"></span><i
                                            class="ico icon-outline-bookmark-opened text-success"></i> Update Customer</button>
                                </div>
                                <div class="col-4"></div>
                            </div>
                                        

                <style>
                   .deal-track-wrapper {
    position: relative;
}

/* More transparent overlay */
.deal-track-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.09);  /* <--- much lighter */
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(1px);  /* light blur */
}

/* Visible background text under message */
.deal-track-overlay-text {
    font-size: 20px;
    color: #fff;
    padding: 15px 25px;
    background: rgba(0,0,0,0.30);  /* <--- lighter text box */
    border-radius: 10px;
  
    text-align: center;
}

                </style>
                @endif
                
<div class="deal-track-wrapper position-relative mt-0">

@if (App\SysHelper::get_company_status($edit->customername) == 0)

       <div class="deal-track-overlay">
        <div class="deal-track-overlay-text">
            ⚠️ Please update customer to submit for deal approval
        </div>
    </div>
@endif

                <h4 class="color-sub-head font-size-13">Deal Track</h4>

                        @php
                            $delivery_date = '';
                            $payment_terms = '';
                            $payment_mode = '';
                            $purchease_required = '';
                            $partial_delivery = '';
                            $technical = '';
                            $technical_detail = '';
                            $lpo = '';
                            $cheque_copy = '';
                            $purchease_quote = '';
                            $remarks = '';
                            $reference_no = '';
                            $reference_date = '';
                            $purchease_approval = 0;
                            $invoice_approval = 1;
                            $delivery_approval = 1;
                            $receivables_approval = 1;
                            $start_date = '';
                            $end_date = '';

                            if (isset($deal_track_temp)) {

                                $delivery_date = $deal_track_temp->delivery_date;
                                $payment_terms = $deal_track_temp->payment_terms;
                                $payment_mode = $deal_track_temp->payment_mode;
                                $purchease_required = $deal_track_temp->purchease_required;
                                $partial_delivery = $deal_track_temp->partial_delivery;
                                $technical = $deal_track_temp->technical;
                                $technical_detail = $deal_track_temp->technical_detail;
                                $lpo = $deal_track_temp->lpo;
                                $cheque_copy = $deal_track_temp->cheque_copy;
                                $purchease_quote = $deal_track_temp->purchease_quote;
                                $remarks = $deal_track_temp->remarks;
                                $reference_no = $deal_track_temp->reference_no;
                                $reference_date = $deal_track_temp->reference_date;
                                $purchease_approval = $deal_track_temp->purchease_approval;
                                $invoice_approval = $deal_track_temp->invoice_approval;
                                $delivery_approval = $deal_track_temp->delivery_approval;
                                $receivables_approval = $deal_track_temp->receivables_approval;
                                $start_date = $deal_track_temp->start_date;
                                $end_date = $deal_track_temp->end_date;
                                $invoicing = $deal_track_temp->invoicing;
                            }
                        @endphp
                       
                        <div class="">
                            <div class="row">
                                <div class="col-5-custom mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('Expected Delivery Date')<span></span></label>

                                                <input class="form-control date-picker" id="delivery_date1" type="text"
                                                    autocomplete="off"  
                                                    value="@if(!empty($delivery_date)) {{  @App\SysHelper::normalizeToDmy($delivery_date) }} @else {{ date('d/m/Y') }} @endif ">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('LPO/Reference No')<span></span></label>
                                                <input class="form-control" id="reference_no1" type="text" autocomplete="off"
                                                     name="reference_no" value="{{ $reference_no }}" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('LPO/Reference Date')<span></span></label>
                                                <input class="form-control date-picker" id="reference_date1" type="text"
                                                    autocomplete="off"  name="reference_date"
                                                    value="@if(!empty($reference_date)) {{ @App\SysHelper::normalizeToDmy($reference_date) }} @else {{ date('d/m/Y') }} @endif">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Payment Terms<span></span></label>
                                        <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms1" >
                                            <option value="">-Select-</option>
                                            @foreach ($paymentterms as $key => $value)
                                                <option value="{{ @$value->id }}" @if ($payment_terms != '') @if (@$payment_terms == @$value->id) selected @endif @else @if (isset($quotationitems))
                                                @if (@$quotationitems[0]->payment_terms == @$value->id) selected @endif @endif @endif>
                                                    {{ @$value->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <script>
                                            $(document).ready(function () {
                                                // Bind change event
                                                $('#payment_terms1').on('change', function () {
                                                    const val = $(this).val();

                                                    // Show/hide payment_mode_sec_div
                                                    if (val == 20 || val == 21) {
                                                        $('#payment_mode_sec_div').hide();
                                                        // $('#payment_mode_sec').prop('required', true);
                                                    } else {
                                                        $('#payment_mode_sec_div').hide();
                                                        // $('#payment_mode_sec').prop('required', false);
                                                    }

                                                    // Set payment_mode based on terms
                                                    if (val == 1 || val == 2) {
                                                        $('#payment_mode').val(1);
                                                    } else {
                                                        $('#payment_mode').val(2);
                                                    }

                                                    // Show/hide payment_terms1_txt
                                                    if (val == 22) {
                                                        $('#payment_terms1_txt').show().prop('required', true);
                                                    } else {
                                                        $('#payment_terms1_txt').hide().prop('required', false);
                                                    }
                                                });

                                                // Trigger once on load in case value is already selected
                                                $('#payment_terms1').trigger('change');
                                            });
                                        </script>
                                        <input class="form-control" id="payment_terms1_txt1" type="text" value="" autocomplete="off"
                                            placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                                    </div>
                                </div>
                                @php
                                    $mode_sel = 0;
                                    if (@$quotationitems[0]->payment_terms == 1 || @$quotationitems[0]->payment_terms == 2) {
                                        $mode_sel = 1;
                                    } else {
                                        $mode_sel = 2;
                                    }

                                @endphp
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Payment Mode<span></span></label>
                                        <select class="form-control js-example-basic-single" name="payment_mode" id="payment_mode1" >
                                            <option value="">-Select-</option>
                                            <option value="1" @if ($payment_mode == 1) selected @else @if ($mode_sel == 1) selected
                                            @endif @endif>Cash</option>
                                            <option value="2" @if ($payment_mode == 2) selected @else @if ($mode_sel == 2) selected
                                            @endif @endif>Cheque</option>
                                            <option value="3" @if ($payment_mode == 3) selected @endif>Bank Transfer
                                            </option>
                                            <option value="4" @if ($payment_mode == 4) selected @endif>Open Credit</option>
                                            <option value="5" @if ($payment_mode == 5) selected @endif>Credit Card</option>
                                            <option value="6" @if ($payment_mode == 6) selected @endif>Bank TT</option>
                                            <option value="7" @if ($payment_mode == 7) selected @endif>Letter of Credit
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3" id="payment_mode_sec_div" style="display: none;">
                                    <div class="input-effect">
                                        <label class="form-label">Payment Mode<span></span></label>
                                        <select class="form-control js-example-basic-single" name="payment_mode_sec" id="payment_mode_sec1">
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

                            

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Purchase Required<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="purchease_required1" name="purchease_required" checked @if ($purchease_required == 0)
                                                @else checked @endif>
                                            <label class="form-label ml-4 " for="purchease_required1">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Purchase Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="purchease_approval1" name="purchease_approval" checked @if ($purchease_approval == 0)
                                                @else checked @endif>
                                            <label class="form-label ml-4" for="purchease_approval1">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Invoice Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="flexCheckDefault1" name="invoice_approval" @if ($invoice_approval == 0) @else
                                                checked @endif>
                                            <label class="form-label ml-4 " for="flexCheckDefault1">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Delivery Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="flexCheckDefault2" name="delivery_approval" @if ($delivery_approval == 0) @else
                                                checked @endif>
                                            <label class="form-label ml-4 " for="flexCheckDefault2">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect ">
                                        <label class="form-label">Receivables Approval<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1"
                                                id="flexCheckDefault3" name="receivables_approval" @if ($receivables_approval == 0)
                                                @else checked @endif>
                                            <label class="form-label ml-4 " for="flexCheckDefault3">Yes,
                                                Required</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Partial Delivery<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input class="form-check-input me-2" style="margin-top:.20rem!important" type="checkbox" value="1" id="partial1"
                                                name="partial_delivery" @if ($partial_delivery == 1) checked @endif>

                                            <label class="form-label mb-0" for="partial1">Yes, Partial Delivery</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                        <label class="form-label">Professional Service<span></span></label>
                                        <div class="form-control d-flex align-items-center">
                                            <input type="hidden" name="technical" value="0" />
                                            <input class="form-check-input ml-2 me-2" style="margin-top:.20rem!important" type="checkbox" value="1" id="technical1"
                                                name="technical" @if ($technical == 1 || $edit->is_professional_service == 1) checked
                                                @endif>
                                            <label class="form-label ml-4 " for="technical1">Yes, Professional
                                                Service</label>
                                        </div>
                                    </div>
                                    <script>
                                        $('#technical1').on('change', function (e) {
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

                                <div class="col-5-custom mb-3" id="technical_div" style="display: none;">
                                    <div class="input-effect">
                                        <label class="form-label">Professional Service Note<span></span></label>
                                        <textarea class="dynamicstxt_s w-100 form-control"
                                            name="technical_detail" rows="1" autocomplete="off" id="technical_detail1"
                                            placeholder="Remarks">{{ $technical_detail }}</textarea>
                                    </div>
                                </div>
                                @if ($technical == 1 || $edit->is_professional_service == 1)
                                    <script>
                                        $('#technical_div').css("display", "block");
                                        $('#technical_detail').prop('required', true);
                                    </script>
                                @endif


                                <script>
                                    $(document).ready(function () {
                                        $('#purchease_required1').change(function () {
                                            if (this.checked) {
                                                $('#purchease_approval1').attr("checked", true);
                                                $('#purchease_required1').attr("checked", true);
                                            } else {
                                                $('#purchease_approval1').attr("checked", false);
                                                $('#purchease_required1').attr("checked", false);
                                            }
                                        });
                                    });

                                    $('#purchease_required1').change(function () {
                                        if (this.checked == true) {
                                            $('#purchease_approval1').attr("checked", true);
                                            $('#purchease_required1').attr("checked", true);
                                        } else {
                                            $('#purchease_approval1').attr("checked", false);
                                            $('#purchease_required1').attr("checked", false);
                                        }
                                    });
                                    $('#purchease_approval').change(function () {
                                        if (this.checked == true) {
                                            $('#purchease_approval1').attr("checked", true);
                                            $('#purchease_required1').attr("checked", true);
                                        } else {
                                            $('#purchease_approval1').attr("checked", false);
                                            $('#purchease_required1').attr("checked", false);
                                        }
                                    });
                                </script>


                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                           <label class="form-label d-flex justify-content-between">@lang('LPO')

                                                                                    @php
    $files = $lpo ? explode('|', $lpo) : [];
    $fileCount = count($files);
@endphp
@if($fileCount > 0)
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#SubmitLPOModal" data-bs-toggle="modal" style="cursor:pointer;">({{ $fileCount }} Files)</small>
@endif
                                        </label>

                                        <div class="form-group files">
                                            <input type="file" class="form-control dynamicstxt_s" multiple="multiple" id="lpo1"
                                                name="lpo[]">
                                        </div>
                                    </div>
                                </div>

                                 <div class="modal fade side-panel" 
                        id="SubmitLPOModal" 
                        data-bs-backdrop="false" 
                        tabindex="-1" 
                        aria-labelledby="SubmitLPOModalLabel" 
                        aria-hidden="true">

                        <div class="modal-dialog modal-lg" style="width:29rem">
                            <div class="modal-content">

                                <!-- Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title" style="padding-left:0" id="SubmitLPOModal">LPO</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <!-- Body -->
                                <div class="modal-body p-0">
                                    <div class="card m-0">
                                        <div class="card-body p-0">

                                            <div class="table-responsive">
                                    <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $files = $lpo ? explode('|', $lpo) : [];
                                            @endphp

                                            @forelse ($files as $f)

                                            <tr>
                                                <td class="text-start">{{ $f }}</td>
                                                <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/crm_deal_track_doc/' . $f) }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                </td>

                                            </tr>
                                            @empty
                                                <p class="text-muted">No files uploaded.</p>
                                            @endforelse

                                        </tbody>
                                    </table>
                                    </div>


                                    

                                            

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                                </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                         <label class="form-label d-flex justify-content-between">@lang('Cheque/TT Copy')

                                                   

                                                    @php
                                                        $files = $cheque_copy ? explode('|', $cheque_copy) : [];
                                                        $fileCount = count($files);
                                                    @endphp
                                                    @if($fileCount > 0)
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#SubmitChequeTT" data-bs-toggle="modal" style="cursor:pointer;">({{ $fileCount }} Files)</small>
                                                    @endif       
                                                </label>
                                        @if ($cheque_copy != '')
                                            <?php                    $file = explode('|', $cheque_copy); ?>
                                            @foreach ($file as $f)
                                                <a class="text-primary" href="{{ asset('public/uploads/crm_deal_track_doc/') }}/{{ $f }}"
                                                    target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                            @endforeach
                                        @endif

                                        <div class="form-group files">
                                            <input type="file" class="form-control dynamicstxt_s" multiple="multiple"
                                                id="cheque_copy1" name="cheque_copy[]">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade side-panel" 
                                                    id="SubmitChequeTT" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="SubmitChequeTT" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="SubmitChequeTT">Cheque/TT Copy</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $files = $cheque_copy ? explode('|', $cheque_copy) : [];
                                                                    @endphp

                                                                    @forelse ($files as $f)

                                                                    <tr>
                                                                        <td class="text-start">{{ $f }}</td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/crm_deal_track_doc/' . $f) }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    @empty
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    @endforelse

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>

                                <div class="col-5-custom mb-3">
                                    <div class="input-effect">
                                         <label class="form-label d-flex justify-content-between">@lang('Purchase Quote')

                                                     @php
                                                        $files = $purchease_quote ? explode('|', $purchease_quote) : [];
                                                        $fileCount = count($files);
                                                    @endphp
                                                    @if($fileCount > 0)
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#SubmitPurchaseQuote" data-bs-toggle="modal" style="cursor:pointer;">({{ $fileCount }} Files)</small>
                                                    @endif  
                                                </label>

                                        <div class="form-group files">
                                            <input type="file" class="form-control dynamicstxt_s" multiple="multiple"
                                                id="purchease_quote1" name="purchease_quote[]">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade side-panel" 
                                                    id="SubmitPurchaseQuote" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="SubmitPurchaseQuote" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="SubmitPurchaseQuote">Purchase Quote</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $files = $purchease_quote ? explode('|', $purchease_quote) : [];
                                                                    @endphp

                                                                    @forelse ($files as $f)

                                                                    <tr>
                                                                        <td class="text-start">{{ $f }}</td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/crm_deal_track_doc/' . $f) }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    @empty
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    @endforelse

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>



                                @if ($is_amc_item > 0)
                                    <div class="col-5-custom mb-3">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Start Date')<span></span></label>
                                                    <input class="form-control" id="start_date1" type="date" autocomplete="off" 
                                                        name="start_date" value="{{ $start_date }}" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-5-custom mb-3">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('End Date')<span></span></label>
                                                    <input class="form-control" id="end_date1" type="date" autocomplete="off" 
                                                        name="end_date" value="{{ $end_date }}" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-5-custom mb-3">
                                        <div class="form-group">
                                            <label for="">Invoicing</label>
                                            <select class="form-control js-example-basic-single" type="text" name="amc_invoice" id="amc_invoice1" >
                                                <option value="">-Select-</option>
                                                <option value="Monthly">Monthly</option>
                                                <option value="Quarterly">Quarterly</option>
                                                <option value="Half Yearly">Half Yearly</option>
                                                <option value="Yearly" selected>Yearly</option>
                                            </select>
                                        </div>
                                    </div>

                                @endif


                                <div class="col mb-3">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <label class="form-label">@lang('Remarks')<span></span></label>
                                                   <input class=" w-100 form-control" value="{{ $remarks }}" data-bs-toggle="modal" data-bs-target="#narrationModalremarks1"
                                                    name="remarks" rows="1" autocomplete="off" id="remarks1"
                                                    placeholder="Remarks">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const referenceInput1 = document.getElementById('remarks1');
                        const narrationTextarea1 = document.getElementById('narrationTextarearemarks1');
                        const insertButton1 = document.getElementById('insertNarrationremarks1');
                        const narrationModal1 = document.getElementById('narrationModalremarks1');

                        // Pre-fill textarea when modal opens
                        narrationModal1.addEventListener('shown.bs.modal', () => {
                            narrationTextarea1.value = referenceInput1.value;
                        setTimeout(() => $('#narrationTextarearemarks1').focus(), 500);


                        });

                        // On insert button click, update input and close modal
                        insertButton1.addEventListener('click', () => {
                            referenceInput1.value = narrationTextarea1.value;
                            bootstrap.Modal.getInstance(narrationModal1).hide();
                        });
                    });
                </script>

                <div class="modal side-panel fade" id="narrationModalremarks1" data-bs-backdrop="false" tabindex="-1"
                    aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body m-0 p-0">
                                <div class="card mb-0 mt-0">
                                    <div class="card-body">
                                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarearemarks1" rows="6"
                                            placeholder="Write remarks here..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="insertNarrationremarks1" class="btn btn-light add-btn ms-2">
                                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                

                        <div class="modal-footer">
                            <input type="hidden" id="deal_id1" name="deal_id" value="{{ $edit->id }}" />
                            <button type="submit" class="btn btn-light add-btn ms-2" value="save" name="btnSubmit"
                                id="btnSave"><span class="ti-check"></span><i
                                    class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                            <button type="submit" class="btn btn-light add-btn ms-2" value="approve" name="btnSubmit"
                                id="btnApprove"><span class="ti-check"></span><i
                                    class="ico icon-outline-bookmark-opened text-success"></i> Submit For Approval</button>
                        </div>


                        <script>
                            $(document).ready(function () {

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
                                    formData.append('receivables_approval', $('input[name="receivables_approval"]').is(':checked') ? 1 :
                                        0);
                                    
                                       let referenceNo = $('#reference_no1').val().trim();
                                    // If empty → show alert and stop request
                                    if (!referenceNo) {
                                        alert("Reference number is required!");
                                        return; // stop AJAX request
                                    }

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
                                        beforeSend: function () {
                                            $("#loading_bg").css("display", "block");

                                            // optional: disable buttons to prevent double submit
                                            // $('#btnSave, #btnApprove').prop('disabled', true);
                                        },
                                        success: function (response) {
                                            console.log(response)
                                            $("#loading_bg").css("display", "none");
                                            toastr.success("Deal Track Submitted successfully", "Success");

                                            // optional: reload page or redirect


                                            location.reload();

                                        },
                                        error: function (xhr) {
                                            $("#loading_bg").css("display", "none");


                                            var err = xhr.responseJSON;
                                            if (err && err.errors) {
                                                var msg = '';
                                                $.each(err.errors, function (key, value) {
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
                                $('#btnSave').click(function (e) {
                                    e.preventDefault();
                                    submitDealTrack('save');
                                });

                                // Submit for Approval button
                                $('#btnApprove').click(function (e) {
                                    e.preventDefault();
                                    submitDealTrack('approve');
                                });

                            });
                        </script>

</div>
                  
                @else
                
                    @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || $check_edit_fullfill == 0)
                        {{-- <li><button type="button" class="dropdown-item" data-modal-size="modal-md"
                                data-bs-target="#ModalDealTrackEdit" data-bs-toggle="modal"><i
                                    class="ico icon-outline-calculator-minimalistic text-warning"></i> Edit Fulfill</button>
                        </li> --}}
                        
                           


                            @if (App\SysHelper::get_company_status($edit->customername) == 0)

                

                             
                                @php
                                    $validation = @App\SysHelper::get_customer_incomplete_fields($edit->customername);
                                @endphp

                                  @php
                                        $editDoc = @App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)->get();
                                    @endphp

                                 

                                        @php
                                        $ids = array_column($validation['errors'], 'id');
                                        @endphp

                                    <div class="row">

                                        @if (in_array('vat_number', $ids))
                                         <div class="col">
                                        <label for="" class="form-label">VAT Number</label>
                                            <div class=""><input class="form-control" type="text" name="vat_number" id="ci_vat_number"
                                                    value="{{ $edit->customername->vat_number }}">
                                                </div>
                                        </div>
                                        @endif
                                     
                                       
                                        @if (in_array('mobile', $ids))
                                        <div class="col">
                                            <label for="" class="form-label">Customer Mobile</label>
                                            <input class="form-control" type="text" name="mobile" id="ci_mobile" placeholder="Mobile"
                                                value="{{ $edit->customername->mobile }}">

                                        </div>
                                        @endif


                                        @if (in_array('email', $ids))

                                        <div class="col">
                                            <label for="" class="form-label">Customer Email</label>
                                            <input class="form-control" type="text" name="email" id="ci_email" placeholder="Email"
                                                value="{{ $edit->customername->email }}" >
                                        </div>

                                        @endif

                                        @if (in_array('first_name', $ids))

                                          <!-- First Name -->
                                        <div class="col">
                   
                                            <label class="form-label mb-0 me-3" style="min-width: 120px;">Primary
                                                Contact:</label>

                                            <input type="text" class="form-control" id="ci_firstName"
                                                name="first_name" placeholder="First Name"
                                                value="{{ isset($edit->customername) ? @$edit->customername->first_name : '' }} {{ isset($edit->customername) ? @$edit->customername->last_name : '' }}">
                                        </div>
                                            
                                        @endif

                                        @if (in_array('contact_number', $ids))

                                             <div class="col ">
                                            <label for="" class="form-label">Customer Phone</label>
                                            <input class="form-control" type="text" name="mobile_code" id="ci_mobile_code" placeholder="Work Phone"
                                                value="{{ $edit->customername->contcat_number }}" >
                                        </div> 
                                            
                                        @endif
                                      

                                        @php
                                            $exists = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'Trade License/Commercial Registration')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted

                                                        ->exists();

                                                            $existsVat = App\SysCustSupplDoc::where('cust_suppl_id', $edit->customername->id)
                                                        ->where('doc_name', 'VAT Certificate')
                                                              ->whereNull('deleted_at') // <-- only consider not deleted

                                                        ->exists();

                                                 
                                        @endphp                         
                                        @if (!$exists)
                                   
                                        
                                        <div class="col">
                                            <input class="form-control" type="hidden" name="doc_name[]"
                                            value="Trade License/Commercial Registration" readonly />
                                                <label for="" class="form-label">Trade License/Commercial Registration</label>
                                                  <input class="form-control" type="file" name="customer_documents_1" id="ci_trade_doc" />
                                                   <input class="form-control date-picker" type="text" id="ci_trade_exp_date" name="doc_exp_date[]"
                                            placeholder="Expiry Date" />
                                        </div>

                                        @endif

                                        @if (!$existsVat)

                                         <div class="col ">
                                           <input class="form-control" type="hidden" name="doc_name[]"
                                            value="VAT Certificate" readonly />
                                                <label for="" class="form-label">VAT Certificate</label>
                                                 <input class="form-control" type="file" name="customer_documents_2" id="ci_vat_doc" />
                                        </div>

                                        
                                            
                                        @endif
{{-- 
                                         <div class="col">
                                 <button type="button" class="btn btn-sm btn-light font-weight-500 text-success" style="margin-top:1.5rem" data-bs-toggle="modal" data-bs-target="#INCSMODAL">View Documents ({{ count($editDoc) }})</button>

                                </div> --}}

                                       
                                        

                                   
                                 
                                  {{-- <div class="modal fade side-panel" 
                                        id="INCSMODAL" 
                                        data-bs-backdrop="false" 
                                        tabindex="-1" 
                                        aria-labelledby="INCSMODAL" 
                                        aria-hidden="true">

                        <div class="modal-dialog modal-lg" style="width:29rem">
                            <div class="modal-content">

                                <!-- Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title" style="padding-left:0" id="INCSMODAL">{{  ucwords($edit->customername->name) }}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                             
                                <div class="modal-body p-0">
                                    <div class="card m-0">
                                        <div class="card-body p-0">

                                            <div class="table-responsive">
                                    <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                        <thead class="text-center">
                                            <tr>
                                                <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                <th style="width: 80px;" class="text-center">@lang('Date')</th>
                                                <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            

                                            @forelse ($editDoc as $doc)

                                            <tr>
                                                <td class="text-start">{{ $doc->doc_name }}</td>
                                                <td class="text-center">{{  date('d/m/Y', strtotime(@$doc->doc_exp_date))  }}</td>
                                                <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/cust-suppl/') }}/{{ $doc->doc_file }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                </td>

                                            </tr>
                                            @empty
                                                <p class="text-muted">No files uploaded.</p>
                                            @endforelse

                                        </tbody>
                                    </table>
                                    </div>


                                    

                                            

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                                </div> --}}

                               

                                        {{-- <div class="mt-3">
                                            <div class="d-flex align-items-center gap-2">
                                                   @if (count($editDoc) > 0)
                                        @foreach ($editDoc as $doc)
                                            <a class="btn btn-sm btn-light rounded-0 font-weight-500 truncate-text-custom" target="__blank" href="{{ asset('public/uploads/cust-suppl/') }}/{{ $doc->doc_file }}">
                                                      <i class="ico icon-bold-download-minimalistic text-success" style="font-size:16px"></i>  {{ $doc->doc_name }} ({{ date('d/m/Y', strtotime(@$doc->doc_exp_date)) }})
                                            </a>

                                           
                                              @endforeach
                                    @endif
                                        </div>
                                        </div> --}}

                                       
                                     



                                    </div>

                                    
                              <script>
$(document).ready(function () {

    function updateCustomerEdit() {
        let fd = new FormData();


// inline DOM checks and appends (no helper functions)
let el;

el = document.getElementById('customer_edit_id'); if (el) fd.append('cust_id', el.value);
el = document.getElementById('ci_vat_number');    if (el) fd.append('vat_number', el.value);
el = document.getElementById('ci_mobile');        if (el) fd.append('mobile', el.value);
el = document.getElementById('ci_email');         if (el) fd.append('email', el.value);
el = document.getElementById('ci_salutation');    if (el) fd.append('customer_salutation', el.value);
el = document.getElementById('ci_firstName');     if (el) fd.append('first_name', el.value);
el = document.getElementById('ci_mobile_code');   if (el) fd.append('mobile_code', el.value);

// document names (only if related input exists in DOM)
if (document.getElementById('ci_trade_doc')) fd.append('doc_name[0]', 'Trade License/Commercial Registration');
if (document.getElementById('ci_vat_doc'))   fd.append('doc_name[1]', 'VAT Certificate');

// expiry dates
el = document.getElementById('ci_trade_exp_date'); if (el) fd.append('doc_exp_date[0]', el.value);

// files (check existence and length)
el = document.getElementById('ci_trade_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_1', el.files[0]);

el = document.getElementById('ci_vat_doc');
if (el && el.files && el.files.length > 0) fd.append('customer_documents_2', el.files[0]);

// fd is ready to send via fetch / $.ajax / XHR


        fd.append("_token", "{{ csrf_token() }}");

        $.ajax({
            url: "{{ url('customer-update-deal-track') }}",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loading_bg").show();
            },
            success: function (res) {
                $("#loading_bg").hide();

                if (res.status) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function (xhr) {
                $("#loading_bg").hide();
                toastr.error("Something went wrong!");
            }
        });
    }

    $("#btnupdateCustomer").on("click", function (e) {
        e.preventDefault();
        updateCustomerEdit();
    });

});
                            </script>


                            


                                  

                                        

                            <div class="row pt-3" style="border-top: 1px solid #dee2e6">
                                <div class="col-4">
                                  
                                </div>
                                <div class="col-4 d-flex justify-content-center">
            <input type="hidden" id="customer_edit_id" name="customer_edit_id" value="{{ $edit->customername->id }}" />
                                    <button type="button" class="btn btn-light add-btn ms-2" 
                                        id="btnupdateCustomer"><span class="ti-check"></span><i
                                            class="ico icon-outline-bookmark-opened text-success"></i> Update Customer</button>
                                </div>
                                <div class="col-4"></div>
                            </div>
                                        

                <style>
                   .deal-track-wrapper {
    position: relative;
}

/* More transparent overlay */
.deal-track-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.15);  /* <--- much lighter */
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(1px);  /* light blur */
}

/* Visible background text under message */
.deal-track-overlay-text {
    font-size: 20px;
    color: #fff;
    padding: 15px 25px;
    background: rgba(0,0,0,0.35);  /* <--- lighter text box */
    border-radius: 10px;
  
    text-align: center;
}

                </style>
                            @endif

                            <div class="deal-track-wrapper position-relative mt-1">

@if (App\SysHelper::get_company_status($edit->customername) == 0)

       <div class="deal-track-overlay">
        <div class="deal-track-overlay-text">
            ⚠️ Please update customer to submit for deal approval
        </div>
    </div>
@endif

                             <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0" style="font-size: 1.1rem;">Edit Deal Track</h4>
                                <a target="__blank" href="{{ url('crm-deal-track-approval-list/' . $edit->track->id) }}"
                                    class=" btn-light btn-sm ">
                                    View Deal Track
                                </a>
                            </div>

                                @php
                                    $edit_delivery_date = '';
                                    $edit_payment_terms = '';
                                    $edit_payment_mode = '';
                                    $edit_purchease_required = '';
                                    $edit_partial_delivery = '';
                                    $edit_technical = '';
                                    $edit_technical_detail = '';
                                    $edit_lpo = '';
                                    $edit_cheque_copy = '';
                                    $edit_purchease_quote = '';
                                    $edit_remarks = '';
                                    $edit_reference_no = '';
                                    $edit_reference_date = '';
                                    $edit_purchease_approval = 1;
                                    $edit_invoice_approval = 1;
                                    $edit_delivery_approval = 1;
                                    $edit_receivables_approval = 1;
                                    $start_date = '';
                                    $end_date = '';

                                    if (isset($deal_track)) {
                                        $edit_delivery_date = $deal_track->delivery_date;
                                        $edit_payment_terms = $deal_track->payment_terms;
                                        $edit_payment_mode = $deal_track->payment_mode;
                                        $edit_purchease_required = $deal_track->purchease_required;
                                        $edit_partial_delivery = $deal_track->partial_delivery;
                                        $edit_technical = $deal_track->technical;
                                        $edit_technical_detail = $deal_track->technical_detail;
                                        $edit_lpo = $deal_track->lpo;
                                        $edit_cheque_copy = $deal_track->cheque_copy;
                                        $edit_purchease_quote = $deal_track->purchease_quote;
                                        $edit_remarks = $deal_track->remarks;
                                        $edit_reference_no = $deal_track->reference_no;
                                        $edit_reference_date = $deal_track->reference_date;
                                        $edit_purchease_approval = $deal_track->purchease_approval;
                                        $edit_invoice_approval = $deal_track->invoice_approval;
                                        $edit_delivery_approval = $deal_track->delivery_approval;
                                        $edit_receivables_approval = $deal_track->receivables_approval;
                                        $start_date = $deal_track->start_date;
                                        $end_date = $deal_track->end_date;
                                        $invoicing = $deal_track->invoicing;
                                    }
                                @endphp
                                <div class="">
                                    <script>
                                    $(document).on('keydown', 'input, select, textarea', function (e) {
                                        if (e.key === 'Enter') {
                                            e.preventDefault(); // stop form submit
                                            
                                            let focusable = $('input, select, textarea')
                                                .filter(':visible:not([disabled])'); // all visible fields
                                            
                                            let index = focusable.index(this); // current field index
                                            
                                            if (index > -1 && index + 1 < focusable.length) {
                                                focusable.eq(index + 1).focus();
                                            }
                                        }
                                    });
                                    </script>

                                    <div class="row">
                                        <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Expected Delivery Date')<span></span></label>

                                                        <input class="form-control date-picker" id="delivery_date2" type="text" autofocus
                                                            autocomplete="off"  name="delivery_date"
                                                            value="{{ @App\SysHelper::normalizeToDmy($edit_delivery_date) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                           <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('LPO/Reference No')<span></span></label>
                                                        <input class="form-control" id="reference_no" type="text" autocomplete="off"
                                                             name="reference_no" value="{{ $edit_reference_no }}" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                           <div class="col-5-custom mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('LPO/Reference Date')<span></span></label>
                                                        <input class="form-control date-picker" id="reference_date" type="text"
                                                            autocomplete="off"  name="reference_date"
                                                            value="{{  @App\SysHelper::normalizeToDmy($edit_reference_date) }}" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label">Payment Terms<span></span></label>
                                                <select class="form-control js-example-basic-single" name="payment_terms" id="payment_terms2" >
                                                    <option value="">-Select-</option>
                                                    @foreach ($paymentterms as $key => $value)
                                                        <option value="{{ @$value->id }}" @if ($edit_payment_terms != '') @if (@$edit_payment_terms == @$value->id) selected @endif @else @if (isset($quotationitems)) @if (@$quotationitems[0]->payment_terms == @$value->id)
                                                        selected @endif @endif @endif>{{ @$value->title }}</option>
                                                    @endforeach
                                                </select>
                                                <script>
                                                    $('#payment_terms2').on('change', function (e) {
                                                        if ($('#payment_terms2').val() == 20 || $('#payment_terms2').val() == 21) {
                                                            $('#payment_mode_sec_div2').css("display", "none");
                                                            //$('#payment_mode_sec').prop('required', true);
                                                        } else {
                                                            $('#payment_mode_sec_div2').css("display", "none");
                                                            //$('#payment_mode_sec').prop('required', false);
                                                        }

                                                        if ($('#payment_terms2').val() == 1 || $('#payment_terms2').val() == 2) {
                                                            $('#payment_mode2').val(1);
                                                        } else {
                                                            $('#payment_mode2').val(2);
                                                        }

                                                        if ($('#payment_terms2').val() == 22) {
                                                            $('#payment_terms2_txt').css("display", "block");
                                                            $('#payment_terms2_txt').prop('required', true);
                                                        } else {
                                                            $('#payment_terms2_txt').css("display", "none");
                                                            $('#payment_terms2_txt').prop('required', false);
                                                        }
                                                    });
                                                </script>
                                                <input class="form-control" id="payment_terms2_txt" type="text"
                                                    value="{{ @$quotationitems[0]->payment_terms_txt }}" autocomplete="off"
                                                    placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                                            </div>
                                        </div>
                                        @php
                                            $mode_sel = 0;
                                            if (@$quotationitems[0]->payment_terms == 1 || @$quotationitems[0]->payment_terms == 2) {
                                                $mode_sel = 1;
                                            } else {
                                                $mode_sel = 2;
                                            }

                                        @endphp
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label">Payment Mode<span></span></label>
                                                <select class="form-control js-example-basic-single" name="payment_mode" id="payment_mode2" >
                                                    <option value="">-Select-</option>
                                                    <option value="1" @if ($edit_payment_mode == 1) selected @else @if ($mode_sel == 1)
                                                    selected @endif @endif>Cash</option>
                                                    <option value="2" @if ($edit_payment_mode == 2) selected @else @if ($mode_sel == 2)
                                                    selected @endif @endif>Cheque</option>
                                                    <option value="3" @if ($edit_payment_mode == 3) selected @endif>Bank Transfer
                                                    </option>
                                                    <option value="4" @if ($edit_payment_mode == 4) selected @endif>Open Credit
                                                    </option>
                                                    <option value="5" @if ($edit_payment_mode == 5) selected @endif>Credit Card
                                                    </option>
                                                    <option value="6" @if ($edit_payment_mode == 6) selected @endif>Bank TT</option>
                                                    <option value="7" @if ($edit_payment_mode == 7) selected @endif>Letter of Credit
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3" id="payment_mode_sec_div2" style="display: none;">
                                            <div class="input-effect">
                                                <label class="form-label">Payment Mode<span></span></label>
                                                <select class="form-control js-example-basic-single" name="payment_mode_sec" id="payment_mode_sec">
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

                                    

                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Purchase Required<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                                        id="purchease_required2" name="purchease_required" @if ($edit_purchease_required == 0) @else checked @endif>
                                                    <label class="form-label ml-4 " for="purchease_required2">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>



                                         <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Purchase Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                                        id="purchease_approval2" name="purchease_approval" @if ($edit_purchease_approval == 0) @else checked @endif @if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) @if ($deal_track->accounts == 1) disabled @endif
                                                        @endif>
                                                    <label class="form-label ml-4 " for="purchease_approval2">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Invoice Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                                        id="flexCheckDefaultinvoice" name="invoice_approval" @if ($edit_invoice_approval == 0) @else checked @endif @if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) @if ($deal_track->accounts == 1) disabled @endif
                                                        @endif>
                                                    <label class="form-label ml-4" for="flexCheckDefaultinvoice">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Delivery Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                                        id="flexCheckDefaultdel" name="delivery_approval" @if ($edit_delivery_approval == 0) @else checked @endif @if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) @if ($deal_track->accounts == 1) disabled @endif
                                                        @endif>
                                                    <label class="form-label ml-4 " for="flexCheckDefaultdel">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect ">
                                                <label class="form-label">Receivables Approval<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1"
                                                        id="flexCheckDefaultrec" name="receivables_approval" @if ($edit_receivables_approval == 0) @else checked @endif @if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) @if ($deal_track->accounts == 1) disabled @endif
                                                        @endif>
                                                    <label class="form-label ml-4" for="flexCheckDefaultrec">Yes,
                                                        Required</label>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            $('#payment_terms2').change();
                                            $(document).ready(function () {
                                                $('#purchease_required2').change(function () {
                                                    if (this.checked) {
                                                        $('#purchease_approval2').attr("checked", true);
                                                    } else {
                                                        $('#purchease_approval2').attr("checked", false);
                                                    }
                                                });
                                            });
                                        </script>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label">Partial Delivery<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1" id="partial2"
                                                        name="partial_delivery" @if ($edit_partial_delivery == 1) checked @endif>
                                                    <label class="form-label ml-4 " for="partial2">Yes, Partial
                                                        Delivery</label>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                <label class="form-label">Professional Service<span></span></label>
                                                <div class="form-control d-flex align-items-center">
                                                    <input type="hidden" name="technical" value="0" />
                                                    <input class="form-check-input ml-2 me-2" type="checkbox" value="1" id="technical2"
                                                        name="technical" @if ($edit_technical == 1) checked @endif>
                                                    <label class="form-label ml-4 " for="technical2">Yes, Professional
                                                        Service</label>
                                                </div>
                                            </div>
                                            <script>
                                                $('#technical2').on('change', function (e) {
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
                                        @if ($is_amc_item > 0)
                                            <div class="col-5-custom mb-3">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="input-effect">
                                                            <label class="form-label">@lang('Start Date')<span></span></label>
                                                            <input class="form-control" id="start_date" type="date" autocomplete="off"
                                                                 name="start_date" value="{{ $start_date }}" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-5-custom mb-3">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="input-effect">
                                                            <label class="form-label">@lang('End Date')<span></span></label>
                                                            <input class="form-control" id="end_date" type="date" autocomplete="off"
                                                                 name="end_date" value="{{ $end_date }}" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-5-custom mb-3">
                                                <div class="form-group">
                                                    <label for="">Invoicing</label>
                                                    <select class="form-control js-example-basic-single" type="text" name="amc_invoice" id="amc_invoice" >
                                                        <option value="">-Select-</option>
                                                        <option @if ($invoicing == 'Monthly') selected @endif value="Monthly">Monthly
                                                        </option>
                                                        <option @if ($invoicing == 'Quarterly') selected @endif value="Quarterly">
                                                            Quarterly
                                                        </option>
                                                        <option @if ($invoicing == 'Half Yearly') selected @endif value="Half Yearly">Half
                                                            Yearly
                                                        </option>
                                                        <option @if ($invoicing == 'Yearly') selected @endif value="Yearly">Yearly
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                        @endif
                                        <div class="col-5-custom mb-3" id="technical_div2" style="display: none;">
                                            <div class="input-effect">
                                                <label class="form-label">Professional Service Note<span></span></label>
                                                <textarea class="dynamicstxt_s w-100 form-control" 
                                                    name="technical_detail" rows="1" autocomplete="off" id="technical_detail2"
                                                    placeholder="Remarks">{{ $edit_technical_detail }}</textarea>
                                            </div>
                                        </div>
                                        @if ($edit_technical == 1)
                                            <script>
                                                $('#technical_div2').css("display", "block");
                                                $('#technical_detail2').prop('required', true);
                                            </script>
                                        @endif

                                       

                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                   <label class="form-label d-flex justify-content-between">
                                            <span>@lang('LPO')</span>
                                            @php
    $files = $edit_lpo ? explode('|', $edit_lpo) : [];
    $fileCount = count($files);
@endphp
@if($fileCount > 0)
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#LPOModal" data-bs-toggle="modal" style="cursor:pointer;">({{ $fileCount }} Files)</small>
@endif
                                        </label>

                                                <div class="form-group files">
                                                    <input type="file" id="lpo2" class="form-control dynamicstxt_s" multiple="multiple"
                                                        name="lpo[]">
                                                </div>
                                            </div>
                                        </div>

                                               <div class="modal fade side-panel" 
                                                        id="LPOModal" 
                                                        data-bs-backdrop="false" 
                                                        tabindex="-1" 
                                                        aria-labelledby="LPOModalLabel" 
                                                        aria-hidden="true">

                                                        <div class="modal-dialog modal-lg" style="width:29rem">
                                                            <div class="modal-content">

                                                                <!-- Header -->
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" style="padding-left:0" id="LPOModalLabel">LPO</h4>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <!-- Body -->
                                                                <div class="modal-body p-0">
                                                                    <div class="card m-0">
                                                                        <div class="card-body p-0">

                                                                            <div class="table-responsive">
                                                                    <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                        <thead class="text-center">
                                                                            <tr>
                                                                                <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                                                <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $files = $edit_lpo ? explode('|', $edit_lpo) : [];
                                                                            @endphp

                                                                            @forelse ($files as $f)

                                                                            <tr>
                                                                                <td class="text-start">{{ $f }}</td>
                                                                                <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/crm_deal_track_doc/' . $f) }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                                </td>

                                                                            </tr>
                                                                            @empty
                                                                                <p class="text-muted">No files uploaded.</p>
                                                                            @endforelse

                                                                        </tbody>
                                                                    </table>
                                                                    </div>


                                                                    

                                                                            

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                </div>


                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                               <label class="form-label d-flex justify-content-between">@lang('Cheque/TT Copy')

                                                   

                                                    @php
                                                        $files = $edit_cheque_copy ? explode('|', $edit_cheque_copy) : [];
                                                        $fileCount = count($files);
                                                    @endphp
                                                    @if($fileCount > 0)
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#ChequeTT" data-bs-toggle="modal" style="cursor:pointer;">({{ $fileCount }} Files)</small>
                                                    @endif       
                                                </label>
                                                <div class="form-group files">
                                                    <input type="file" id="cheque2" class="form-control dynamicstxt_s"
                                                        multiple="multiple" name="cheque_copy[]">
                                                </div>
                                            </div>
                                        </div>

                                            <div class="modal fade side-panel" 
                                                    id="ChequeTT" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="ChequeTT" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="ChequeTT">Cheque/TT Copy</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $files = $edit_cheque_copy ? explode('|', $edit_cheque_copy) : [];
                                                                    @endphp

                                                                    @forelse ($files as $f)

                                                                    <tr>
                                                                        <td class="text-start">{{ $f }}</td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/crm_deal_track_doc/' . $f) }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    @empty
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    @endforelse

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>
                                        <div class="col-5-custom mb-3">
                                            <div class="input-effect">
                                                  <label class="form-label d-flex justify-content-between">@lang('Purchase Quote')

                                                     @php
                                                        $files = $edit_purchease_quote ? explode('|', $edit_purchease_quote) : [];
                                                        $fileCount = count($files);
                                                    @endphp
                                                    @if($fileCount > 0)
                                            <small id="lpo-file-count" class="text-success" data-bs-target="#PurchaseQuote" data-bs-toggle="modal" style="cursor:pointer;">({{ $fileCount }} Files)</small>
                                                    @endif  
                                                </label>
                                             

                                                <div class="form-group files">
                                                    <input type="file" id="poquote2" class="form-control dynamicstxt_s"
                                                        multiple="multiple" name="purchease_quote[]">
                                                </div>
                                            </div>
                                        </div>

                                        
 <div class="modal fade side-panel" 
                                                    id="PurchaseQuote" 
                                                    data-bs-backdrop="false" 
                                                    tabindex="-1" 
                                                    aria-labelledby="PurchaseQuote" 
                                                    aria-hidden="true">

                                                <div class="modal-dialog modal-lg" style="width:29rem">
                                                    <div class="modal-content">

                                                        <!-- Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" style="padding-left:0" id="PurchaseQuote">Purchase Quote</h4>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="modal-body p-0">
                                                            <div class="card m-0">
                                                                <div class="card-body p-0">

                                                                    <div class="table-responsive">
                                                            <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th style="width: 80px;" class="text-start">@lang('Files')</th>
                                                                        <th style="width: 30px;" class="text-center"> <i class="ico icon-bold-paperclip"></i> </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $files = $edit_purchease_quote ? explode('|', $edit_purchease_quote) : [];
                                                                    @endphp

                                                                    @forelse ($files as $f)

                                                                    <tr>
                                                                        <td class="text-start">{{ $f }}</td>
                                                                        <td class="text-center"><a class="btn-sm btn-light text-dark btn-fixed" href="{{ asset('public/uploads/crm_deal_track_doc/' . $f) }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>
                                                                        </td>

                                                                    </tr>
                                                                    @empty
                                                                        <p class="text-muted">No files uploaded.</p>
                                                                    @endforelse

                                                                </tbody>
                                                            </table>
                                                            </div>


                                                            

                                                                    

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                        </div>

                                        <div class="col mb-3">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Remarks')<span></span></label>
                                                        <input class="w-100 form-control" data-bs-toggle="modal" data-bs-target="#narrationModalremarks"
                                                            name="remarks" rows="1" autocomplete="off"
                                                            id="remarks" placeholder="Remarks" value="{{ $edit_remarks }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" id="deal_id2" name="deal_id" value="{{ $edit->id }}" />
                                    <button type="button" class="btn btn-light add-btn ms-2" value="approve" name="btnSubmit"
                                        id="btneditSubmit"><span class="ti-check"></span><i
                                            class="ico icon-outline-bookmark-opened text-success"></i> Update</button>
                                </div>

                            
                        </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('remarks');
        const narrationTextarea = document.getElementById('narrationTextarearemarks');
        const insertButton = document.getElementById('insertNarrationremarks');
        const narrationModal = document.getElementById('narrationModalremarks');

        // Pre-fill textarea when modal opens
        narrationModal.addEventListener('shown.bs.modal', () => {
            narrationTextarea.value = referenceInput.value;
            setTimeout(() => $('#narrationTextarearemarks').focus(), 500);

        });


        // On insert button click, update input and close modal
        insertButton.addEventListener('click', () => {
            referenceInput.value = narrationTextarea.value;
            bootstrap.Modal.getInstance(narrationModal).hide();
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput1 = document.getElementById('remarks1');
        const narrationTextarea1 = document.getElementById('narrationTextarearemarks1');
        const insertButton1 = document.getElementById('insertNarrationremarks1');
        const narrationModal1 = document.getElementById('narrationModalremarks1');

        // Pre-fill textarea when modal opens
        narrationModal1.addEventListener('shown.bs.modal', () => {
            narrationTextarea1.value = referenceInput1.value;
            setTimeout(() => $('#narrationTextarearemarks1').focus(), 500);

        });

        // On insert button click, update input and close modal
        insertButton1.addEventListener('click', () => {
            referenceInput1.value = narrationTextarea1.value;
            bootstrap.Modal.getInstance(narrationModal1).hide();
        });
    });
</script>

<div class="modal side-panel fade" id="narrationModalremarks1" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarearemarks1" rows="6"
                            placeholder="Write remarks here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarrationremarks1" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal side-panel fade" id="narrationModalremarks" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Enter Remarks</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarearemarks" rows="6"
                            placeholder="Write remarks here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="insertNarrationremarks" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>


                        <script>
                            $(document).ready(function () {

                                // Core submission function
                                function submitEditDealTrack() {
                                    var formData = new FormData();

                                    formData.append('delivery_date', $('#delivery_date2').val());
                                    formData.append('payment_terms', $('#payment_terms2').val());
                                    formData.append('payment_terms_txt', $('#payment_terms2_txt').val());
                                    formData.append('payment_mode', $('#payment_mode2').val());
                                    formData.append('payment_mode_sec', $('#payment_mode_sec').val() || '');
                                    formData.append('purchease_required', $('#purchease_required2').is(':checked') ? 1 : 0);
                                    formData.append('partial_delivery', $('#partial2').is(':checked') ? 1 : 0);
                                    formData.append('technical', $('#technical2').is(':checked') ? 1 : 0);
                                    formData.append('start_date', $('#start_date').val() || '');
                                    formData.append('end_date', $('#end_date').val() || '');
                                    formData.append('amc_invoice', $('#amc_invoice').val());
                                    formData.append('technical_detail', $('#technical_detail2').val());
                                    formData.append('purchease_approval', $('#purchease_approval2').is(':checked') ? 1 : 0);
                                    formData.append('invoice_approval', $('#flexCheckDefaultinvoice').is(':checked') ? 1 : 0);
                                    formData.append('delivery_approval', $('#flexCheckDefaultdel').is(':checked') ? 1 : 0);
                                    formData.append('receivables_approval', $('#flexCheckDefaultrec').is(':checked') ? 1 : 0);

                                    let referenceNo = $('#reference_no').val().trim();
                                    // If empty → show alert and stop request
                                    if (!referenceNo) {
                                        alert("Reference number is required!");
                                        return; // stop AJAX request
                                    }

                                    formData.append('reference_no', $('#reference_no').val());
                                    formData.append('reference_date', $('#reference_date').val() || '');
                                    formData.append('remarks', $('#remarks').val());
                                    formData.append('deal_id', $('#deal_id2').val());
                                   

                                    // Attach multiple files
                                    $.each($('#lpo2')[0].files, function (i, file) { formData.append('lpo[]', file); });
                                    $.each($('#cheque2')[0].files, function (i, file) { formData.append('cheque_copy[]', file); });
                                    $.each($('#poquote2')[0].files, function (i, file) { formData.append('purchease_quote[]', file); });

                                    // AJAX submission
                                    $.ajax({
                                        url: "{{ url('crm-deal-track-submit-edit') }}",
                                        type: 'POST',
                                        data: formData,
                                        contentType: false,
                                        processData: false,
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                        beforeSend: function () {
                                            $("#loading_bg").show();
                                        },
                                        success: function (response) {
                                            $("#loading_bg").hide();
                                              
                                            toastr.success("Deal Track Updated successfully", "Success");
                                            location.reload();
                                        },
                                        error: function (xhr) {
                                            $("#loading_bg").hide();
                                            let msg = 'An error occurred';
                                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                                msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n");
                                            }
                                            alert(msg);
                                        }
                                    });
                                }

                                // Bind buttons
                                $('#btneditSubmit').on('click', function (e) {
                                    e.preventDefault();
                                    submitEditDealTrack();
                                });

                               
                            });
                        </script>


                    @endif
                @endif
            @endif

                    </div>





        <div class="tab-pane fade" id="internal-fields" role="tabpanel" aria-labelledby="internal-fields-tab">


            <div class="row">

                <div class="col-7">
            <div id="scrollBox"  style="max-height: 12rem; overflow-y: auto;">

                    @if ($edit->note != '')<b>Deal Notes :- </b>

                        <div class="card">
                            <div class="card-body">
                                <div class="fw-semibold" style="font-size:11px"> {!! nl2br($edit->note) !!} </div>

                            </div>
                        </div>
                    @endif
                    @if (count($comments) > 0)
                        <div class="mt-2" style="">
                            @foreach ($comments as $cmts)
                                     <div class="card  rounded-3 mb-2 comments-card">
                                                        <div class="card-body py-0">

                                                        

                                                            <!-- Top Row: Right-Aligned Icons -->
                                                            <div class="d-flex justify-content-between mb-0">


                                                                <!-- Comment -->
                                                                        <p class="mb-0 text-break fw-semibold @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif" style="font-size:11px">
                                                                                {!!   nl2br($cmts->comments) !!}
                                                                        </p>


                                                                        <div class="d-flex align-items-baseline gap-2">
                                                                                @if ($cmts->commentsdoc)
                                                                                                <a href="{{ asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc) }}"
                                                                                                target="_blank" class="btn btn-sm btn-light me-1"  style="min-height:17px">
                                                                                                    <i class="ico icon-bold-paperclip" style="font-size:11px"></i>
                                                                                                </a>
                                                                                            @endif

                                                                                            @if ($cmts->created_by == Auth::user()->id)
                                                                                                @if ($cmts->deleted_at)
                                                                                                    <a href="{{ url('crm-deals-comments-restore/' . $cmts->id) }}"
                                                                                                    onclick="return confirm('Are you sure you want to restore this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-bold-restart" style="font-size:11px"></i>
                                                                                                    </a>
                                                                                                @else
                                                                                                    <a href="{{ url('crm-deals-comments-delete/' . $cmts->id) }}"
                                                                                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:11px"></i>
                                                                                                    </a>
                                                                                                @endif
                                                                                    @endif
                                                                        </div>


                                                            

                                                            </div>

                                                            <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                                            <div class="text-end small text-muted">

                                                                <span style="font-size:10px">
                                                                   
                                                                    {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}
                                                                </span>

                                                                <span>•</span>

                                                                <span style="font-size:10px">
                                                                    <i class="ico icon-bold-clock me-1"></i>
                                                                    {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                                                </span>

                                                                @if ($cmts->deleted_at)
                                                                <span>•</span>
                                                                    
                                                                    <span class="text-danger" style="font-size:10px">
                                                                        Deleted: {{ date('d/m/Y h:i A', strtotime($cmts->deleted_at)) }}
                                                                    </span>
                                                                @endif

                                                            </div>

                                                        </div>
                                                </div>

                            @endforeach
                        </div>
                    @endif
                </div>
                </div>

                <div class="col-5">
                        <div class="d-flex justify-content-between align-items-center mb-2 w-100">
    
                            <label class="font-weight-bold form-label mb-0 w-50">Internal Note</label>

                            <button type="button" id="viewbnotemodal" data-bs-toggle="modal" data-bs-target="#ViewNotesModal"
                                class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size:11px">
                                <i class="ico icon-outline-notebook text-success" style="font-size:15px"></i>
                                <span>View Notes</span>
                            </button>

                            <!-- Modal Account-->
                            <div class="modal side-panel fade" id="ViewNotesModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header"><h4 class="modal-title" id="exampleModalLongTitle">Internal Note</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
                                                    <div id="scrollBox"  style="max-height: 420px; overflow-y: auto;">


                                    @if ($edit->note != '')

                        <div class="card">
                            <div class="card-body">
                                <div class="fw-semibold" style="font-size:12px"> {!! nl2br($edit->note) !!} </div>

                            </div>
                        </div>
                    @endif
                    @if (count($comments) > 0)
                        <div class="mt-2" style="">
                            @foreach ($comments as $cmts)
                                     <div class="card  rounded-3 mb-2 comments-card">
                                                        <div class="card-body py-0">

                                                        

                                                            <!-- Top Row: Right-Aligned Icons -->
                                                            <div class="d-flex justify-content-between mb-0">


                                                                <!-- Comment -->
                                                                        <p class="mb-0 text-break fw-semibold @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif" style="font-size:12px">
                                                                                {!!   nl2br($cmts->comments) !!}
                                                                        </p>


                                                                        <div class="d-flex align-items-baseline gap-2">
                                                                                @if ($cmts->commentsdoc)
                                                                                                <a href="{{ asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc) }}"
                                                                                                target="_blank" class="btn btn-sm btn-light me-1"  style="min-height:17px">
                                                                                                    <i class="ico icon-bold-paperclip" style="font-size:12px"></i>
                                                                                                </a>
                                                                                            @endif

                                                                                            @if ($cmts->created_by == Auth::user()->id)
                                                                                                @if ($cmts->deleted_at)
                                                                                                    <a href="{{ url('crm-deals-comments-restore/' . $cmts->id) }}"
                                                                                                    onclick="return confirm('Are you sure you want to restore this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-bold-restart" style="font-size:12px"></i>
                                                                                                    </a>
                                                                                                @else
                                                                                                    <a href="{{ url('crm-deals-comments-delete/' . $cmts->id) }}"
                                                                                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                                                                                    class="btn btn-sm btn-light"  style="min-height:17px">
                                                                                                        <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:12px"></i>
                                                                                                    </a>
                                                                                                @endif
                                                                                    @endif
                                                                        </div>


                                                            

                                                            </div>

                                                            <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                                            <div class="text-end small text-muted">

                                                                <span style="font-size:11px">
                                                                   
                                                                    {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}
                                                                </span>

                                                                <span>•</span>

                                                                <span style="font-size:11px">
                                                                    <i class="ico icon-bold-clock me-1"></i>
                                                                    {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                                                </span>

                                                                @if ($cmts->deleted_at)
                                                                <span>•</span>
                                                                    
                                                                    <span class="text-danger" style="font-size:11px">
                                                                        Deleted: {{ date('d/m/Y h:i A', strtotime($cmts->deleted_at)) }}
                                                                    </span>
                                                                @endif

                                                            </div>

                                                        </div>
                                                </div>

                            @endforeach
                        </div>
                    @endif



                                </div>
                                            </div>

                                        </div>
                                    
                                    </div>
                                </div>
                                </div>
                            <!-- Modal Account-->

                        </div>
                    <div id="deal-comments-form">
                        <textarea name="comments" class="form-control" cols="10" rows="3"></textarea>
                        {{-- <input type="file" class="form-control mt-2" name="commentsdoc" id="commentsdoc"> --}}
                        <input type="hidden" name="commentsid" value="{{ $edit->id }}" />

                          <div class="row mt-2">
                                                        <div class="col-md-4 d-flex justify-content-start align-items-center">
                                                        <button type="button" id="submitComment"
                                                            class="btn btn-light d-inline-flex align-items-center gap-2">
                                                            <i class="ico icon-outline-add-square fs-5 text-success"></i>
                                                            <span>Add Note</span>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                                                    </div>

                                                    
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
                        url: '{{ url('crm-deals-comments-add') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function () {
                           $("#loading_bg").css("display", "block");
                        },
                        success: function (response) {
                            console.log("response", response); // Debugging line to check response
                           
                           
                            $('textarea[name="comments"]').val('');
                            $('#commentsdoc').val('');
                            $("#loading_bg").css("display", "none");
                            location.reload();
                            // Optionally append new comment to comment list
                        },
                        error: function (xhr) {
                            $("#loading_bg").css("display", "none");
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
                            <label class="form-label">Payment Terms:</label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="payment_terms"
                                    id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}" @if (count($quotationitems) > 0) @if ($quotationitems[0]->payment_terms == $value->id) selected @endif @elseif(@$edit->customername->payment_terms == $value->id) selected @endif>
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
                        <th class="resizable text-center" width="150px">@lang('Part No') <a
                                class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#addproductModal"></a>
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="150px">@lang('Description')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px">@lang('Cost')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px">@lang('Tax')
                            <div class="resizer"></div>
                        </th>
                        <th class="resizable text-center" width="65px">@lang('Qty')
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
                                        value="{{ $item->cost }}" onchange="calc_change_new(this)" onblur="formatCurrency(this)">
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
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // trigger change on all qty fields once
                                document.querySelectorAll('input[name="qty[]"]').forEach(function (el) {
                                    el.dispatchEvent(new Event("change"));
                                });
                            });
                        </script>
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

          <table class="table form-item-table mb-3">
    <tr class="align-middle text-center"> <!-- centers vertically + horizontally -->
        <td class="text-end"><b>Additional Discount :</b></td>
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

                                                    @if ($showCode)
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
                                    {{ @$value->account_code }} - {{ @$value->account_name }}
                                </option>
                            @endforeach
                        </select></td>
                    <td><select class="form-control js-example-basic-single noborder" name="cfc_credit_account[]" id="cfc_credit_account_2"
                            readonly="true">
                            <option value="none"></option>
                            @foreach ($supplier as $key => $value)
                                <option value="{{ @$value->id }}" {{ isset($edit_cfc[1]) ? (!empty(@$edit_cfc[1]->credit_account) ? (@$edit_cfc[1]->credit_account == @$value->id ? 'selected' : '') : '') : '' }}>
                                    {{ @$value->account_code }} - {{ @$value->account_name }}
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
    </div>

</div>


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
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea2" rows="6"
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
                                    @if (@$currency_id == $value->id)
                                        <option value="{{ @$value->id }}" @if (@$currency_id == $value->id) selected @endif>
                                            {{ @$value->code }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Currancy To</label>
                            <select class="form-control" name="to_currency_id" id="to_currency_id" required
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
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name *</label>
                                <input type="text" class="form-control" name="end_user_company_name" id="end_user_company_name" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Device Serial</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="device_serial" id="device_serial" readonly style="cursor:pointer;" onclick="openDeviceSerialModal()" />
                                    <button type="button" class="btn btn-light border" onclick="openDeviceSerialModal()">
                                        <i class="ico icon-outline-list-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
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
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person *</label>
                                <input type="text" class="form-control" name="end_user_contact_person" id="end_user_contact_person" required />
                            </div>
                        </div>
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="job_title" id="job_title" />
                            </div>
                        </div>  --}}
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" />
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        </div>
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

        <div class="col-3">
            <p class="font-weight-600 mb-0 truncate-text-custom">Device Serial</p><br>
             <span class="truncate-text-custom">{{ $enduser->device_serial }}</span> 
        </div>

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

        <div class="col-3">
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
        </div>

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
                        <textarea style="height: 109px !important;" class="form-control" id="narrationTextarea2Address" rows="6"
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


<!-- Device Serial Modal -->
<div class="modal fade" id="DeviceSerialModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="DeviceSerialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" style="left:25%;max-width:49rem;width:49rem;top:39%">
        <div class="modal-content">
            <div class="modal-header mb-2">
                <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serial Numbers</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="device_serial_container">
                    <div class="device-serial-row d-flex align-items-center mb-2" data-index="1">
                        <span class="me-2 text-muted" style="min-width:10px;">1.</span>
                        <input type="text" class="form-control device-serial-input" placeholder="Enter serial number" />
                        <button type="button" class="btn btn-sm btn-light border ms-2 btn-remove-serial" style="display:none;">
                            <i class="ico icon-outline-minus-circle text-danger"></i>
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="button" style="margin-left: 1.2rem;" class="btn btn-sm btn-light border" id="btn_add_device_serial">
                        <i class="ico icon-outline-add-square text-success me-1"></i> Add Serial
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn_save_device_serials" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to properly open DeviceSerialModal from within ModalEndUserDetails
function openDeviceSerialModal() {
    // Store the parent modal state
    var $parentModal = $('#ModalEndUserDetails');
    
    // Hide parent modal temporarily (without animation)
    // $parentModal.addClass('modal-static-hidden');
    // $parentModal.css('opacity', '0.5');
    
    // Show device serial modal
    var deviceSerialModal = new bootstrap.Modal(document.getElementById('DeviceSerialModal'));
    deviceSerialModal.show();
}

// When DeviceSerialModal is hidden, restore parent modal
$(document).on('hidden.bs.modal', '#DeviceSerialModal', function() {
    var $parentModal = $('#ModalEndUserDetails');
    // $parentModal.removeClass('modal-static-hidden');
    // $parentModal.css('opacity', '1');
});

$(document).ready(function() {
    var maxSerials = 500;
    var serialIndex = 1;

    // Update row numbers and toggle remove buttons
    function updateSerialRows() {
        var $rows = $('#device_serial_container .device-serial-row');
        $rows.each(function(i) {
            $(this).attr('data-index', i + 1);
            $(this).find('span').first().text((i + 1) + '.');
        });
        // Show/hide remove buttons (hide if only 1 row)
        if ($rows.length <= 1) {
            $rows.find('.btn-remove-serial').hide();
        } else {
            $rows.find('.btn-remove-serial').show();
        }
        serialIndex = $rows.length;
    }

    // Add new serial row
    $('#btn_add_device_serial').on('click', function() {
        var $rows = $('#device_serial_container .device-serial-row');
        if ($rows.length >= maxSerials) {
            toastr.warning('Maximum ' + maxSerials + ' serial numbers allowed.');
            return;
        }

        // Check if last row is empty
        var $lastInput = $rows.last().find('.device-serial-input');
        if ($lastInput.val().trim() === '') {
            $lastInput.focus();
            return;
        }

        serialIndex++;
        var newRow = `
            <div class="device-serial-row d-flex align-items-center mb-2" data-index="${serialIndex}">
                <span class="me-2 text-muted" style="min-width:10px;">${serialIndex}.</span>
                <input type="text" class="form-control device-serial-input" placeholder="Enter serial number" />
                <button type="button" class="btn btn-sm btn-light border ms-2 btn-remove-serial">
                    <i class="ico icon-outline-minus-circle text-danger"></i>
                </button>
            </div>
        `;
        $('#device_serial_container').append(newRow);
        updateSerialRows();
        $('#device_serial_container .device-serial-row').last().find('.device-serial-input').focus();
    });

    // Remove serial row
    $(document).on('click', '.btn-remove-serial', function() {
        var $row = $(this).closest('.device-serial-row');
        $row.fadeOut(150, function() {
            $(this).remove();
            updateSerialRows();
        });
    });

    // Handle Enter key to add new row
    $(document).on('keydown', '.device-serial-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var $rows = $('#device_serial_container .device-serial-row');
            var $currentRow = $(this).closest('.device-serial-row');
            var currentIndex = $rows.index($currentRow);

            // If this is the last row, add new one
            if (currentIndex === $rows.length - 1) {
                $('#btn_add_device_serial').trigger('click');
            } else {
                // Move to next row
                $rows.eq(currentIndex + 1).find('.device-serial-input').focus();
            }
        }
    });

    // Save serials to the main input
    $('#btn_save_device_serials').on('click', function() {
        var serials = [];
        $('#device_serial_container .device-serial-input').each(function() {
            var val = $(this).val().trim();
            if (val !== '') {
                serials.push(val);
            }
        });
        $('#device_serial').val(serials.join(', '));
        $('#DeviceSerialModal').modal('hide');
    });

    // Pre-fill modal when opening
    $('#DeviceSerialModal').on('show.bs.modal', function() {
        var currentValue = $('#device_serial').val().trim();
        var $container = $('#device_serial_container');
        $container.empty();

        if (currentValue === '') {
            // Add one empty row
            var emptyRow = `
                <div class="device-serial-row d-flex align-items-center mb-2" data-index="1">
                    <span class="me-2 text-muted" style="min-width:10px;">1.</span>
                    <input type="text" class="form-control device-serial-input" placeholder="Enter serial number" />
                    <button type="button" class="btn btn-sm btn-light border ms-2 btn-remove-serial" style="display:none;">
                        <i class="ico icon-outline-minus-circle text-danger"></i>
                    </button>
                </div>
            `;
            $container.append(emptyRow);
        } else {
            // Split by comma and create rows
            var serials = currentValue.split(',');
            serials.forEach(function(serial, i) {
                var trimmedSerial = serial.trim();
                var row = `
                    <div class="device-serial-row d-flex align-items-center mb-2" data-index="${i + 1}">
                        <span class="me-2 text-muted" style="min-width:10px;">${i + 1}.</span>
                        <input type="text" class="form-control device-serial-input" placeholder="Enter serial number" value="${trimmedSerial}" />
                        <button type="button" class="btn btn-sm btn-light border ms-2 btn-remove-serial">
                            <i class="ico icon-outline-minus-circle text-danger"></i>
                        </button>
                    </div>
                `;
                $container.append(row);
            });
        }
        updateSerialRows();
    });

    // Focus first input when modal is shown
    $('#DeviceSerialModal').on('shown.bs.modal', function() {
        $('#device_serial_container .device-serial-input').first().focus();
    });
});
</script>

 

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
                                                descriptionInput.value = row[1].trim();
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
                                $("#delivery_address1").val(dataResult['data'][i].address);
                                $("#delivery_address2").val(dataResult['data'][i].address2);
                                
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
    // Initialize form validation for crm-deals-form
    FormValidator.init('crm-deals-form', {
        showAllErrors: true,
        scrollToFirst: true,
        highlightFields: true,
        toastrPosition: 'toast-top-right',
        toastrTimeout: 6000
    });
});
</script>
<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
<?php try { ?>






{{ Form::open([
    'class' => 'form-horizontal',
    'files' => true,
    'url' => 'crm-leads',
    'method' => 'POST',
    'enctype' => 'multipart/form-data',
    'id' => 'crm-leads-form',
]) }}


<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New (<span class="font-weight-600"
                id="new_code">{{ App\SysHelper::get_new_code_lead('sys_crm_leads', 'LD', 'code', session('logged_session_data.company_id')) }}</span>)
        </h4>

        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">
            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">

                </ul>
            </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">

            <div class="row" id="top-row">

                <div class="col-4 mb-2">
                    <label class="form-label">Customer
                        <a style="float: right; cursor: pointer;" class="text-success" data-bs-toggle="modal"
                            data-bs-target="#addcompany"><i class="ico icon-bold-buildings-2" aria-hidden="true"></i>
                            Add
                        </a> </label>
                    <select class="form-control js-example-basic-single" name="company_name" id="company_name" required>

                        <option value="">Select</option>
                        @foreach ($vendors as $value)
                            <option value="{{ @$value->id }}"
                                {{ isset($edit)
                                    ? (!empty($edit->company_name)
                                        ? (@$edit->company_name == @$value->id
                                            ? 'selected'
                                            : '')
                                        : '')
                                    : '' }}>
                                {{ trim(@$value->customer_name_display) }}@if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code']) ({{ trim(@$value->code) }})@endif

                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label">Lead Name</label>

                    <input class="form-control capitalize-title" type="text" name="lead_name" autocomplete="off" id="lead_name"
                        value="{{ isset($edit) ? (!empty(@$edit->lead_name) ? @$edit->lead_name : old('lead_name')) : old('lead_name') }}"
                        required>
                </div>


              



                <div class="col-2 mb-2">
                    <label class="form-label" for="">
                        Brand
                        <a href="#" class="text-success ms-2" style="float: right;" data-bs-toggle="modal" data-bs-target="#addBrand" title="Add Brand">
                            <i class="ico icon-outline-add-square"></i>
                        </a>
                    </label>
                    <select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple required>
                        @foreach ($brand as $value)
                            <option value="{{ @$value->title }}"
                                @if (isset($edit)) @if (!empty($edit->tags))
                                        @if (str_contains($edit->tags, $value->title)) selected @endif
                                @endif
                        @endif >{{ @$value->title }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Sales Person</label>
                    <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                        <option value="">-Select-</option>
                    </select>
                </div>

                {{-- Company select is populated by controller based on staff company_access --}}

                    <div class="col-2 mb-2">
                        <label class="form-label" for="">Company</label>
                        <select class="form-control js-example-basic-single" name="company" id="company" required>

                            @foreach ($company as $value)
                                <option value="{{ @$value->id }}" @if (session('logged_session_data.company_id') == $value->id) selected @endif>
                                    {{ @$value->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                {{-- @else
                    <input type="hidden" name="company" id="company"
                        value="{{ session('logged_session_data.company_id') }}" />

                @endif --}}


              




            </div>





        </div>
    </div>
    <div class="tab-wrap mb-3">
        <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab"
                    data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields"
                    aria-selected="true">Extra Fields</button>
            </li>
        </ul>
        <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
            <div class="tab-pane fade show active" id="extra-fields" role="tabpanel"
                aria-labelledby="extra-fields-tab">

                <div class="row gap-rows">
                <div class="col-2 mb-2">
                    <label class="form-label" for="">Contact Person Name</label>
                    <input class="form-control capitalize-title" type="text" name="cust_name" autocomplete="off" id="cust_name"
                        value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}"
                        required>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Designation</label>

                    <select class="form-control js-example-basic-single" name="cust_designation" id="cust_designation"
                        required>
                        <option value="">--Designation--</option>
                        @if (count($designation) > 0)
                            @foreach ($designation as $val)
                                <option value="{{ $val->title }}"
                                    {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}
                                    aria-describedby="">{{ $val->title }}</option>
                            @endforeach
                        @endif
                    </select>

                    {{-- <input class="form-control" type="text" name="cust_designation" autocomplete="off"
                        id="cust_designation"
                        value="{{ isset($edit) ? (!empty(@$edit->cust_designation) ? @$edit->cust_designation : old('cust_designation')) : old('cust_designation') }}"> --}}

                </div>


                <div class="col-2 mb-2">
                    <label class="form-label" for="">Mobile</label>
                    <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no"
                        value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">

                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Email</label>
                    <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email"  data-bs-toggle="modal" data-bs-target="#EmailModal"
                        value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">

                </div>


                <div class="col-2 mb-2">
                    <label class="form-label" for="">Address</label>
                    <input class="form-control" type="text" name="address" autocomplete="off" id="address"  data-bs-toggle="modal" data-bs-target="#AddressModal"
                        value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                </div>





                <div class="col-2 mb-2">
                    <label class="form-label" for="">Source</label>
                    <select class="form-control js-example-basic-single" name="source" id="source">
                        <option value="">-Select-</option>
                        <option value="Chat" @if (@$edit->source == 'Chat') selected @endif>Chat
                        </option>
                        <option value="Call" @if (@$edit->source == 'Call') selected @endif>Call
                        </option>
                        <option value="Mail" @if (@$edit->source == 'Mail') selected @endif
                            @if (!isset($edit)) selected @endif>Mail</option>
                        <option value="Website" @if (@$edit->source == 'Website') selected @endif>Website
                        </option>
                        {{-- <option value="Gitex 2023" @if (@$edit->source == 'Gitex 2023') selected @endif
                                        >Gitex 2023</option> --}}
                        <option value="Gitex" @if (@$edit->source == 'Gitex') selected @endif>Gitex
                        </option>
                        <option value="Ecommerce" @if (@$edit->source == 'Ecommerce') selected @endif>
                            Ecommerce
                        </option>
                        <option value="Other" @if (@$edit->source == 'Other') selected @endif>Other
                        </option>
                    </select>
                </div>


                <div class="col-2 mb-2" id="sourcediv" style="display: none;">
                    <label class="form-label" for="">Other Source</label>
                    <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o"
                        value="{{ isset($edit) ? (!empty(@$edit->source_o) ? @$edit->source_o : old('source_o')) : old('source_o') }}"
                        style="display: none;" placeholder="Source">

                </div>

                {{-- <div class="col-2 mb-2"> --}}
                {{-- <label class="form-label" for="">Created By</label> --}}
                <input class="form-control" type="hidden" name="createdby" autocomplete="off" id="createdby"
                    value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}"
                    readonly>
                {{-- </div> --}}

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Date</label>
                    @php
                        $value = Carbon\Carbon::now()->format('d/m/Y');
                        if (isset($edit) && !empty($edit->date)) {
                            $value = Carbon\Carbon::parse($edit->date)->format('d/m/Y');
                        }
                    @endphp
                    <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                        name="date" value="{{ @$value }}" required>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Lead Type</label>
                    <select class="form-control js-example-basic-single" name="isproject" id="isproject">
                        <option value="4" @if (@$edit->isproject == '4') selected @endif>Project
                        </option>
                        <option value="1" @if (@$edit->isproject == '1') selected @endif>Reseller
                        </option>
                        <option value="2" @if (@$edit->isproject == '2') selected @endif>Enduser
                        </option>
                        <option value="3" @if (@$edit->isproject == '3') selected @endif>
                            E-Commerce
                        </option>
                    </select>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Status</label>
                    <select class="form-control js-example-basic-single" name="status" id="status" required>
                        <option value="1" @if (@$edit->status == 1) selected @endif>New
                        </option>
                        <option value="2" @if (@$edit->status == 2) selected @endif>
                            Qualified
                        </option>
                        <option value="3" @if (@$edit->status == 3) selected @endif>
                            Unqualified
                        </option>
                        <option value="4" @if (@$edit->status == 4) selected @endif>Pending
                            Response</option>
                    </select>

                    <script>
                        $('#status').on('change', function(e) {

                            if ($('#status').val() == "3") {

                                $('#statusdiv').css("display", "block");

                            } else {
                                $('#statusdiv').css("display", "none");

                            }
                        });
                    </script>
                </div>

                <div class="col-5-custom" id="statusdiv" style="display: none">
                    <label class="form-label" for="">Reason</label>

                    <textarea class="form-control" name="lost_comments" rows="4" autocomplete="off" id="lost_comments"
                        placeholder="Reason"></textarea>
                </div>

                <div class="col-2 mb-2">
                    <label class="form-label" for="">Attachment</label>
                    <input type="file" class="form-control" name="doc[]" id="doc" multiple="multiple">

                </div>



                <div class="col-4 mb-2" >
                    <label class="form-label" for="">Notes</label>
                    <input class="form-control" name="note" rows="3" data-bs-toggle="modal" data-bs-target="#narrationModal" autocomplete="off" id="note">
                </div>
                </div>
               
            </div>
        </div>
    </div>


</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('note');
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

<div class="modal side-panel fade" id="narrationModal" data-bs-backdrop="false" tabindex="-1"
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
                        <textarea style="height: 109px !important;" class="form-control capitalize-title" id="narrationTextarea" rows="6"
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


{{ Form::close() }}





<div class="modal side-panel fade" id="addcompany" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="top:25%;max-width:1000px!important;left: 37%;">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Add Customer</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">

                        <div class="row gap-rows row-cols-5">
                    

                            <div class="col">
                                <label for="" class="form-label">Customer Name</label>
                                <input class="form-control" type="text" aria-describedby=""
                                    autocomplete="off" id="company_name_add" required placeholder="">
                                     <style>
                                         #company_name_add_list ul {
                                                width: 380px;
                                                left:16rem

                                            }
                                    </style>
                                <div id="company_name_add_list">
                                </div>
                                <script>
                                    $(document).ready(function() {

                                        $('#company_name_add').keyup(function() {
                                            var query = $(this).val();
                                            if (query != '') {
                                                var _token = $('input[name="_token"]').val();
                                                $.ajax({
                                                    url: "{{ route('autocomplete.customer_name') }}",
                                                    method: "POST",
                                                    data: {
                                                        query: query,
                                                        _token: _token
                                                    },
                                                    success: function(data) {
                                                        $('#company_name_add_list').fadeIn();
                                                        $('#company_name_add_list').html(data);
                                                    }
                                                });
                                            }
                                        });

                                        $(document).on('click', 'li', function() {
                                            $('#customer_name').val('');
                                        $('#customer_name_display').val('');
                                        // toastr.info('Customer Already Exists.', 'Info');



                                        $('#company_name_add_list').fadeOut();
                                        });

                                        $(document).click(function(e) {
                                            if (!$(e.target).closest('#company_name_add, #company_name_add_list').length) {
                                                $('#company_name_add_list').fadeOut();
                                            }
                                        });

                                    });
                                </script>
                            </div>


                           

                              

                            <div class="col">
                                <label for="" class="form-label">Contact Person</label>
                                <div class="d-flex gap-2 align-items-end">
                                    <div style="min-width: 54px; max-width: 54px;">
                                        <select class="form-control js-example-basic-single" id="salutation_cust" name="customer_salutation_add" required>
                                            <option value="Mr.">Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Miss.">Miss.</option>
                                        </select>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input class="form-control" type="text" autocomplete="off" id="cust_name_add" name="cust_name_add" required placeholder="Name">
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <label for="" class="form-label">Mobile</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_no_add"
                                    required>
                            </div>

                            <div class="col">
                                <label for="" class="form-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_email_add"
                                    required>
                            </div>



                            <div class="col">
                                <label for="" class="form-label">Country</label>
                                <select class="form-control js-example-basic-single" name="country_ship"
                                    id="country_ship">
                                    <option value="">-Select-</option>
                                    @foreach ($country as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($value->iso3 == 'ARE') selected @endif>
                                            {{ @$value->name }}</option>
                                    @endforeach
                                </select>

                                <div style="display:none">

                                           <select class="form-select js-example-basic-single" style="width:30px;display:none" name="country_telephone" id="country_telephone" required>
            <option value="" disabled selected>Select Country</option>
            @foreach ($country as $key => $value)
                <option value="{{ @$value->iso2 }}|{{ @$value->id }}">{{ @$value->name }}</option>
            @endforeach
        </select>
                                </div>
                                
                            </div>

                                <div class="col">
                                <label for="" class="form-label">State</label>
                                <div id="sectionStateDiv_ship">
                                    <select class="form-control js-example-basic-single" name="state_ship"
                                        id="state_ship">
                                        <option data-display="" value=""></option>
                                        <?php try { ?>
                                        @if (isset($editData) && $editData->vat_state != '')
                                            <option data-display="{{ $editData->vatstate->name }}"
                                                value="{{ $editData->vat_state }}" selected>
                                                {{ $editData->vatstate->name }}</option>
                                        @endif
                                        <?php } catch (\Exception $e) {
                                        } ?>
                                    </select>
                                </div>

                            </div>

                            <div class="col">
                                <label for="" class="form-label">City</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_city"
                                    required>

                            </div>

                             <div class="col">
                                <label for="" class="form-label">Area</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_area"
                                    required>

                            </div>

                              <div class="col">
                                <label for="" class="form-label">Building Name</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_building_name"
                                    required>

                            </div>

                              <div class="col">
                                <label for="" class="form-label">Flat/Office No</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_flat_office_no"
                                    required>

                            </div>

                            {{-- <div class="col">
                                <label for="" class="form-label">Address 1</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add"
                                    required>

                            </div> --}}

                            {{-- <div class="col">
                                <label for="" class="form-label">Address 2</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add2"
                                    required>
                            </div> --}}

                            

                            <div class="col">
                                <label for="" class="form-label">PO Box</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_pobox"
                                    required>

                            </div>

                        

                            <div class="col">
                                <label for="" class="form-label">Payment Terms</label>
                                <select class="form-control js-example-basic-single" id="payment_terms" required>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($value->id == 3) selected @endif>{{ @$value->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                                    <div class="col">
                                <label for="" class="form-label">Customer Type</label>
                                <select class="form-control js-example-basic-single" id="account_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1" selected>Reseller</option>
                                    <option value="2">Enduser</option>
                                    <option value="3">Ecommerce</option>
                                </select>
                            </div>

                                <div class="col">
                                <label for="" class="form-label">Designation</label>
                                <select class="form-control js-example-basic-single" name="designation_add"
                                    id="designation_add" required>
                                    <option value="">--Designation--</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}"
                                                {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}
                                                aria-describedby="">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <input type="hidden" name="place_id" id="place_id" value="">
                            <input type="hidden" name="customer_website" id="customer_website" value="">
                            <input type="hidden" name="maps_location" id="maps_location" value="">


                            <div class="col">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" id="cust_sales_person" required>
                                    <option value="">-Select-</option>

                                    @foreach ($sales_person as $value)
                                        <option value="{{ $value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                      

                     

                            

                          
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="add_company_leads" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save 
                </button>
            </div>
        </div>


    </div>
</div>

<script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhC6fAWLvqoE4znv7d8ovf8y3pMR0OG7s&libraries=places&language=en">
</script>

<style>
    .pac-container {
        width: 500px !important;   /* Set your desired width */
        max-width: 600px;
    }
</style>

 <script>
        $(document).ready(function () {
            const input = document.getElementById("company_name_add");
            
            const autocomplete = new google.maps.places.Autocomplete(input, {
                types: ["establishment"],
                fields: [
                    "place_id",
                    "name",
                    "formatted_address",
                    "address_components",
                    "geometry",
                    "plus_code",
                    "international_phone_number",
                    "formatted_phone_number",
                    "website",
                    "url"
            ]
                  
            });
            
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                
                if (!place.place_id) {
                    console.error("No place details found");
                    return;
                }

                 $('#company_name_add_list').fadeOut();

                
                console.log("📍 COMPLETE PLACE DATA:", place);
                
                // Extract structured address
                let address = {
                    country: "",
                    country_code: "",
                    state: "",
                    city: "",
                    area: "",
                    building_name: "",
                    street_number: "",
                    route: "",
                    po_box: "",
                    postal_code: ""
                };
                
                if (place.address_components) {
                    place.address_components.forEach(component => {
                        const types = component.types;
                        
                        if (types.includes("country")) {
                            address.country = component.long_name;
                            address.country_code = component.short_name;
                        }
                        if (types.includes("administrative_area_level_1")) {
                            address.state = component.long_name;
                        }
                        if (types.includes("locality")) {
                            address.city = component.long_name;
                        }
                        if (types.includes("sublocality") || types.includes("sublocality_level_1")) {
                            address.area = component.long_name;
                        }
                        if (types.includes("premise") || types.includes("neighborhood")) {
                            address.building_name = component.long_name;
                        }
                        if (types.includes("street_number")) {
                            address.street_number = component.long_name;
                        }
                        if (types.includes("route")) {
                            address.route = component.long_name;
                        }
                        if (types.includes("post_box")) {
                            address.po_box = component.long_name;
                        }
                        if (types.includes("postal_code")) {
                            address.postal_code = component.long_name;
                            console.log("Postal Code:", address.postal_code);
                        }
                    });
                }
                
                // Extract country mobile code from phone number
                let mobileCode = "";
                if (place.international_phone_number) {
                    const match = place.international_phone_number.match(/^\+(\d{1,4})/);
                    if (match) {
                        mobileCode = "+" + match[1];
                    }
                }
                
                // Get coordinates
                const lat = place.geometry?.location?.lat() || "";
                const lng = place.geometry?.location?.lng() || "";
                
                // Fill all form fields
                setFieldvalue("company_name_add", place.name || "");
                setFieldvalue("cust_no_add", place.international_phone_number || "");
                // setFieldvalue("mobile_code", mobileCode);
                setFieldvalue("customer_website", place.website || "");
            
                
                setFieldvalue("maps_location", place.url || "");
                
                // setFieldvalue("country", address.country);
                var targetName = address.country || ""; // or any value you want to match


                function normalize(str) {
    return str.toLowerCase().replace(/\s+/g, '');
}

                // Select the option based on its text (the visible name)
                $('#country_ship option').each(function() {
                     if (normalize($(this).text()) === normalize(targetName)) {
        $(this).prop('selected', true);
          $('#country_ship').trigger('change');
    }
                });

                

                var targetName = address.state || ""; // or any value you want to match


              // Delay execution to ensure options are loaded
setTimeout(function() {
    var matched = false;

    $('#state_ship option').each(function() {
        if (normalize($(this).text()) === normalize(targetName)) {
            $(this).prop('selected', true);
            matched = true;
            return false; // break the loop
        }
    });

    if (matched) {
        $('#state_ship').trigger('change');
    }

    console.log("State selection attempted for:", targetName);
}, 600); // adjust 300ms as needed


                setFieldvalue("cust_city", address.city);
                setFieldvalue("cust_area", address.area);
                setFieldvalue("cust_building_name", address.building_name);
                setFieldvalue("cust_pobox", address.postal_code);
                setFieldvalue("place_id", place.place_id);

                setFieldvalue("longitude", lng);
                setFieldvalue("latitude", lat);


                // Show success message
                $("#successAlert").addClass("show");
                setTimeout(() => {
                    $("#successAlert").removeClass("show");
                }, 3000);
                
            });
            
            // Helper function to set field value and add visual feedback
            function setFieldvalue(fieldId, value) {
                const field = $("#" + fieldId);
                if (field.length) {
                    field.val(value);
                    if (value) {
                        field.addClass("filled");
                    } else {
                        field.removeClass("filled");
                    }
                }
            }
        });
</script>




<!-- External JS for country codes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<script>
    $(document).ready(function () {

    $('#country_ship').on('change', function () {

        console.log("!222121")

        let shipId = $(this).val();    // example: "5"

        if (!shipId) {
            $('#country_telephone').val("").trigger('change');
            return;
        }

        // Loop through telephone options
        $('#country_telephone option').each(function () {
            let value = $(this).val(); // example: "AE|5"

            if (!value) return;

            let parts = value.split('|');
            let telId = parts[1]; // country ID

            if (telId == shipId) {
                $('#country_telephone').val(value).trigger('change');
            }
        });

    });

});

</script>

<script>
$(document).ready(function() {
    // Map ISO2 → dial code
    var countryCodes = {};
    $.each(window.intlTelInputGlobals.getCountryData(), function(index, country) {
        countryCodes[country.iso2.toLowerCase()] = country.dialCode;
    });

  

    // When country changes, set country code in input
    $('#country_telephone').on('change', function() {
        // Extract ISO2 from value (before the | character)
        var fullValue = $(this).val(); // e.g. "US|1"
        var iso2 = fullValue ? fullValue.split('|')[0].toLowerCase() : '';
        console.log(iso2)
        console.log("!@1212KPPPP")
        var code = countryCodes[iso2] || '';
        var currentNumber = $('#cust_no_add').val().replace(/^\+\d+\s?/, ''); // remove previous code
        
        console.log("code = ", code)
        
        $('#cust_no_add').val(code ? '+' + code + ' ' + currentNumber : currentNumber);

         
    });
});
</script>

<script>
    $(document).ready(function() {
        $('#company').on('change', function() {
            let companyId = $(this).val();

            if (!companyId) {
                $('#new_code').text('');
                return;
            }

            $.ajax({
                url: "{{ url('/ajax/get-new-lead-code') }}",
                type: "GET",
                data: {
                    table: 'sys_crm_leads',
                    prefix: 'LD',
                    column: 'code',
                    company_id: companyId
                },
                success: function(response) {
                    if (response.new_code) {
                        $('#new_code').text(response.new_code);
                    } else {
                        $('#new_code').text('');
                        console.error('No code returned from server');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    $('#new_code').text('');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Auto-focus Customer dropdown on page load
        setTimeout(function() {
            $('#company_name').select2('open');
        }, 300);
        
        // Enter key navigation for #top-row inputs
        $('#top-row').on('keydown', 'input, select, textarea', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                
                // Get the current field's ID
                var currentId = $(this).attr('id');
                var $nextElement = null;
                
                // Define custom navigation flow
                switch(currentId) {
                    case 'company_name':
                        $nextElement = $('#lead_name');
                        break;
                    case 'lead_name':
                        $nextElement = $('#tags');
                        break;
                    case 'tags':
                        $nextElement = $('#owner');
                        break;
                    case 'owner':
                        $nextElement = $('#company');
                        break;
                    case 'company':
                        // Last field in top-row, blur or move to next section
                        $(this).blur();
                        return;
                    default:
                        // Fallback to sequential navigation
                        var $focusableElements = $('#top-row').find('input:visible:not([disabled]):not([readonly]), select:visible:not([disabled]), textarea:visible:not([disabled]):not([readonly])');
                        var currentIndex = $focusableElements.index(this);
                        if (currentIndex > -1 && currentIndex < $focusableElements.length - 1) {
                            $nextElement = $focusableElements.eq(currentIndex + 1);
                        }
                }
                
                // Focus or open the next element
                if ($nextElement && $nextElement.length > 0) {
                    if ($nextElement.hasClass('js-example-basic-single') || $nextElement.hasClass('select2-hidden-accessible')) {
                        // Open Select2 dropdown
                        $nextElement.select2('open');
                    } else {
                        // Focus regular input/textarea
                        $nextElement.focus();
                    }
                }
            }
        });
        
        // Auto-fill Lead Name when Customer is selected and focus on Lead Name
        $('#company_name').on('select2:select', function(e) {
            var selectedText = $(this).find('option:selected').text().trim();
            if (selectedText && selectedText !== 'Select') {
                // $('#lead_name').val(selectedText);
            }
            // Focus on Lead Name field
            setTimeout(function() {
                $('#lead_name').focus();
            }, 100);
        });
        
        // When Lead Name loses focus or Enter is pressed, open Brand dropdown
        $('#lead_name').on('blur', function() {
            if ($(this).val().trim() !== '') {
                setTimeout(function() {
                    $('#tags').select2('open');
                }, 100);
            }
        });
        
        // Brand is multiselect - do not auto-jump to next field
        // User can select multiple brands before manually moving to Sales Person
        
        // When Sales Person is selected, open Company dropdown
        $('#owner').on('select2:select', function(e) {
            setTimeout(function() {
                $('#company').select2('open');
            }, 100);
        });

        // When Company select2 opens, prefill the search box with the currently selected option
        // so the user can edit/change the selection easily.
        $('#company_name').on('select2:open', function() {
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
        
        // Handle Enter key on select2 dropdowns to close and move to next field
        $(document).on('keydown', '.select2-search__field', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                var $select2Container = $(this).closest('.select2-container');
                var $originalSelect = $('#' + $select2Container.attr('aria-owns').replace('-results', '').replace('select2-', '').replace('-container', ''));
                
                if ($originalSelect.length) {
                    // Close the dropdown
                    $originalSelect.select2('close');
                    
                    // Trigger the navigation based on which field it is
                    setTimeout(function() {
                        var selectId = $originalSelect.attr('id');
                        
                        if (selectId === 'company_name') {
                            $('#lead_name').focus();
                        } else if (selectId === 'tags') {
                            $('#owner').select2('open');
                        } else if (selectId === 'owner') {
                            $('#company').select2('open');
                        }
                    }, 50);
                }
            }
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput2Address = document.getElementById('address');
        const narrationTextarea2Address = document.getElementById('narrationTextarea2Address');
        const insertButton2Address = document.getElementById('insertNarration2Address');
        const narrationModal2Address = document.getElementById('AddressModal');

        // Pre-fill textarea when modal opens
        narrationModal2Address.addEventListener('show.bs.modal', () => {
            narrationTextarea2Address.value = referenceInput2Address.value;
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
        narrationModal2Email.addEventListener('show.bs.modal', () => {
            narrationTextarea2Email.value = referenceInput2Email.value;
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


  <div class="modal side-panel fade" id="addBrand" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 157px !important;">


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Add Brand</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{ Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'brand',
                    'method' => 'POST',
                    'enctype' => 'multipart/form-data',
                    'id' => 'addBrandForm',
                ]) }}


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label class="form-label">@lang('Brand') @lang('Name')
                                                <span>*</span></label>
                                            <input class="primary-input form-control" type="text" name="title" id="new_brand_title"
                                                autocomplete="off" value="">
                                            <input type="hidden" name="id" value="">
                                            <span class="focus-border"></span>
                                            <div id="new_brand_error" class="text-danger mt-1" style="display:none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="saveBrandAjax">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
                {{ Form::close() }}

            </div>




        </div>
    </div>

<script>
    $(document).ready(function() {
        $('#saveBrandAjax').on('click', function(e) {
            e.preventDefault();

            var title = $('#new_brand_title').val().trim();
            var $error = $('#new_brand_error');
            $error.hide().text('');

            if (!title) {
                $error.text('Brand name is required.').show();
                return;
            }

            var $button = $(this);
            $button.prop('disabled', true);

            $.ajax({
                url: "{{ url('brand') }}",
                type: 'POST',
                data: {
                    title: title,
                    _token: $('input[name="_token"]').first().val()
                },
                success: function(response) {
                    if (response.success && response.title) {
                        var newOption = new Option(response.title, response.title, true, true);
                        $('#tags').append(newOption).trigger('change');
                        $('#new_brand_title').val('');
                        $('#addBrand').modal('hide');
                        toastr.success('Brand added successfully');
                    } else {
                        $error.text(response.message || 'Unable to save brand.').show();
                    }
                },
                error: function(xhr) {
                    var message = 'Unable to save brand. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.title) {
                        message = xhr.responseJSON.errors.title[0];
                    }
                    $error.text(message).show();
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });

        $('#addBrandForm').on('submit', function(e) {
            e.preventDefault();
            $('#saveBrandAjax').trigger('click');
        });

        $('#addBrand').on('shown.bs.modal', function() {
            $('#new_brand_title').trigger('focus');
        });

        $('#addBrand').on('hidden.bs.modal', function() {
            $('#new_brand_title').val('');
            $('#new_brand_error').hide().text('');
        });
    });
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<?php try { ?>




{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'customer-save-form-edit', 'novalidate' => true]) }}


<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            Edit - {{ $editData->code }}
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">



            <a name="customer_action" href="customers?customer_action=add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>




            <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="catid" id="catid" value="2">

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Update
            </button>

             <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




            @if ($editData->supplier_id != null && $editData->supplier_id != '')
                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('suppliers/' . $editData->supplier_id) }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        View Supplier
                    </a>
                </li>
                
            @else
                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('suppliers?supplier_action=createsupplier&customer_id=' . $editData->id) }}">
                        <i class="ico icon-outline-add-square text-success  title-15 me-2"></i>
                        Create Supplier
                    </a>
                </li>
            @endif



            </ul>
        </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">

            <div class="row">
                <div class="col-12"style="">
                    <div class="row gap-rows g-2">
                        <div class="col-1">
                            <label for="" class="form-label">Cust. Type</label>

                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="account_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1" @if ($editData->account_type == 1) selected @endif>Reseller
                                    </option>
                                    <option value="2" @if ($editData->account_type == 2) selected @endif>Enduser
                                    </option>
                                    <option value="3" @if ($editData->account_type == 3) selected @endif>Ecommerce
                                    </option>
                                </select>
                                {{-- <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i> --}}

                            </div>
                        </div>


                        <div class="col-4">
                            <label for="" class="form-label">Customer Name</label>
                            <input class="form-control capitalize-words text-capitalize" type="text"
                                name="customer_name" id="customer_name" placeholder="Company Name"
                                value="{{ isset($editData) ? ucwords(strtolower($editData->name)) : '' }}" required>

                        </div>



                        <div class="col-4">
                            <label for="" class="form-label">Customer Display Name</label>
                            <input class="form-control" type="text" name="customer_name_display"
                                id="customer_name_display" placeholder="Customer Display Name"
                                value="{{ isset($editData) ? @$editData->customer_name_display : '' }}" required>
                        </div>

                        <script>
                            $('#customer_name').on('input', function() {
                                console.log('Input event triggered');
                                var txt = $('#customer_name').val();
                                $('#customer_name_display').val(txt.toUpperCase());
                                var txt2 = capitalizeFirstLetter(txt);
                                console.log(txt2)
                                $('#customer_name').val(txt2);
                            });

                            function capitalizeFirstLetter(string) {
                                console.log(string.charAt(0).toUpperCase() + string.slice(1))
                                return string.charAt(0).toUpperCase() + string.slice(1);
                            }
                        </script>

                        <div class="col-3">
                            <label for="" class="form-label">Customer Website</label>
                            <div class="form-group">
                                <input type="text" name="customer_website" id="customer_website"
                                    value="{{ @$editData->website }}" class="form-control" placeholder="Website URL">
                            </div>
                        </div>

                        <div class="col-1">
                            <label for="" class="form-label">Salutation</label>

                            <div class="form-group">
                                <select class="form-control select2 js-example-basic-single h-100" id="salutation"
                                    name="customer_salutation">

                                    <option value="Mr." @if ($editData->customer_salutation == 'Mr.') selected @endif>Mr.
                                    </option>
                                    <option value="Mrs." @if ($editData->customer_salutation == 'Mrs.') selected @endif>Mrs.
                                    </option>
                                    <option value="Miss." @if ($editData->customer_salutation == 'Miss.') selected @endif>
                                        Miss.</option>
                                </select>


                            </div>
                        </div>

                        <div class="col-2">
                            <label for="" class="form-label">First Name</label>

                            <input type="text" class="form-control capitalize-words" id="firstName" name="first_name"
                                placeholder="First Name" value="{{ isset($editData) ? @$editData->first_name : '' }}">
                        </div>

                        <!-- Last Name -->
                        <div class="col-2">
                            <label for="" class="form-label">Last Name</label>

                            <input type="text" class="form-control capitalize-words" id="lastName" name="last_name"
                                placeholder="Last Name" value="{{ isset($editData) ? @$editData->last_name : '' }}">
                        </div>




                        <div class="col-1">
                            <label class="form-label">Country</label>

                            <select class="form-select js-example-basic-single" style="width:30px"
                                name="country_telephone" id="country_telephone" required>
                                <option value="" disabled selected>Select Country</option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->iso2 }}|{{ @$value->id }}"
                                        @if ($editData->country_telephone == $value->iso2 . '|' . $value->id) selected @elseif($editData->vat_country == $value->id) selected @endif>
                                        {{ @$value->name }}</option>
                                @endforeach
                            </select>

                        </div>

                        <!-- External JS for country codes -->
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>


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

                                    var code = countryCodes[iso2] || '';
                                    var currentNumber = $('#mobile_code').val().replace(/^\+\d+\s?/,
                                        ''); // remove previous code
                                    var currentNumber = $('#company_mobile_phone').val().replace(/^\+\d+\s?/,
                                        ''); // remove previous code

                                    $('#mobile_code').val(code ? '+' + code + ' ' + currentNumber : currentNumber);
                                    $('#company_mobile_phone').val(code ? '+' + code + ' ' + currentNumber : currentNumber);

                                    if (code) {
                                        // Loop through all employee rows and update work_phone and mobile inputs
                                        $('input[name^="e_work_phone"], input[name^="e_mobile"]').each(function() {
                                            // Remove existing country code if present
                                            var currentNumber = $(this).val().replace(/^\+\d+\s?/, '');
                                            $(this).val('+' + code + ' ' + currentNumber);
                                        });
                                    }
                                });
                            });
                        </script>

                        <div class="col-3">
                            <label class="form-label">Customer Phone</label>

                            <input class="form-control" type="text" name="mobile_code" id="mobile_code"
                                placeholder="Work Phone" value="{{ $editData->contcat_number }}" required>
                        </div>

                        <div class="col-3">
                            <label for="" class="form-check-label">Customer Mobile</label>
                            <input class="form-control" type="text" name="mobile" id="company_mobile_phone"
                                placeholder="Mobile" value="{{ $editData->mobile }}">

                        </div>



                        <div class="col-3">
                            <label for="" class="form-label">Customer Email</label>
                            <input class="form-control" type="text" name="email" placeholder="Email"  id="company_email"
                                value="{{ $editData->email }}" required>
                        </div>

                        <div class="col-3">
                               <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Designation</span>
                                <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;" data-bs-toggle="modal" data-bs-target="#adddesignationModal2">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                </button>
                            </label>

                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="designation"
                                    id="company_designation" required>
                                    <option value="">Select</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}"
                                                @if ($editData->designation == $val->title) selected @endif>{{ $val->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>



                        </div>

                        <div class="col-3">
                            <label for="" class="form-label">Maps Location</label>
                            <div class="form-group">
                                <input type="text" name="maps_location" id="maps_location"
                                    value="{{ @$editData->maps_location }}" class="form-control"
                                    placeholder="Maps Location">
                            </div>
                        </div>


                        <div class="col-1-5">
                            <label for="" class="form-label">Company Type</label>

                            <div class="form-group">
                                <select class="form-control" name="type" id="type">
                                    <option value="1"
                                        @if (isset($editData)) @if (@$editData->type == 1) selected @endif
                                        @endif>Green</option>
                                    <option value="2"
                                        @if (isset($editData)) @if (@$editData->type == 2) selected @endif
                                        @endif>Orange</option>
                                    <option value="3"
                                        @if (isset($editData)) @if (@$editData->type == 3) selected @endif
                                        @endif>Red</option>
                                    <option value="4"
                                        @if (isset($editData)) @if (@$editData->type == 4) selected @endif
                                        @endif>Black</option>
                                </select>
                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                            </div>

                        </div>

                        <div class="col-1-5">
                            <label for="" class="form-check-label">Internal Customer</label>
                            <div class="form-group">
                                <select class="form-control" name="internal">
                                    <option value="0" @if (@$editData->internal == 0) selected @endif>No
                                    </option>
                                    <option value="1" @if (@$editData->internal == 1) selected @endif>Yes
                                    </option>
                                </select>
                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>
                            </div>
                        </div>







                        <script>
                            $(document).ready(function() {
                                // Live capitalize words for inputs with .capitalize-words (e.g., Customer Name)
                                $(document).on("input", ".capitalize-words", function() {
                                    let val = $(this).val();
                                    val = val.replace(/\b\w/g, char => char.toUpperCase());
                                    $(this).val(val);
                                });

                                // Keep display field uppercase while typing
                                $('#customer_name').on('input', function() {
                                    var txt = $(this).val();
                                    $('#customer_name_display').val(txt.toUpperCase());
                                });

                                // Prevent Enter from submitting the form; move to next input instead
                                $(document).on('keydown', '#customer-save-form-edit input, #customer-save-form-edit select, #customer-save-form-edit button', function(e) {
                                    if (e.key === 'Enter') {
                                        var tag = e.target.tagName.toLowerCase();
                                        if (tag === 'textarea') return;
                                        e.preventDefault();
                                        var $form = $(this).closest('form');
                                        var focusable = $form.find(':input:visible:not([disabled]):not([type=hidden])').filter(':not(button)');
                                        var idx = focusable.index(this);
                                        if (idx > -1 && idx + 1 < focusable.length) {
                                            focusable.eq(idx + 1).focus();
                                        }
                                        return false;
                                    }
                                });
                            });
                        </script>

















  <script>
                            $(document).ready(function() {

                                const $companySelect = $('#company_access');
                                const $salesSelect = $('#sales_person');

                                // Init select2 (if not already)
                                $companySelect.select2({
                                    placeholder: 'Select Company'
                                });

                                $salesSelect.select2({
                                    placeholder: 'Select Sales Persons'
                                });

                                function fetchSalesPersons(companyIds) {

                                    // 🔥 No company selected → clear sales persons
                                    if (!companyIds || companyIds.length === 0) {
                                        $salesSelect.empty().trigger('change');
                                        return;
                                    }

                                    // Preserve already selected values
                                    let selectedValues = $salesSelect.val() || [];

                                    $.ajax({
                                        url: "{{ url('get-sales-person-list') }}",
                                        type: "GET",
                                        data: {
                                            company_ids: companyIds
                                        },
                                        dataType: "json",
                                        beforeSend: function() {
                                            $('#loading_bg').css('display', 'block');

                                            // $salesSelect.prop('disabled', true);
                                        },
                                        success: function(res) {

                                            $salesSelect.empty();

                                            if (res.status === 'success' && Array.isArray(res.data)) {

                                                if (res.data.length === 0) {
                                                    $salesSelect.trigger('change');
                                                    return;
                                                }

                                                res.data.forEach(function(staff) {
                                                    const isSelected = selectedValues.includes(String(staff
                                                        .user_id));

                                                    const option = new Option(
                                                        staff.full_name,
                                                        staff.user_id,
                                                        isSelected,
                                                        isSelected
                                                    );

                                                    $salesSelect.append(option);
                                                });
                                            }

                                            $salesSelect.trigger('change');
                                        },
                                        error: function() {
                                            $salesSelect.empty().trigger('change');
                                        },
                                        complete: function() {
                                            $('#loading_bg').css('display', 'none');

                                            // $salesSelect.prop('disabled', false);
                                        }
                                    });
                                }

                                // On company change
                                $companySelect.on('change', function() {
                                    fetchSalesPersons($(this).val());
                                });

                                // 🔁 If edit page and company already selected
                                if ($companySelect.val()?.length) {
                                    fetchSalesPersons($companySelect.val());
                                }
                            });
                        </script>

                     



                        <div class="col-6">
                            <label for="" class="form-label">Company</label>
                            <select class="form-control js-example-basic-single" name="company_access[]"
                                id="company_access" multiple required>
                                @foreach ($company as $value)
                                    <option value="{{ @$value->id }}"
                                        @if (!empty($editData->company_access)) @if (str_contains($editData->company_access, $value->id)) selected @endif
                                        @endif>{{ @$value->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="" class="form-label">Sales Persons</label>
                            <select class="form-control js-example-basic-single" name="sales_person[]"
                                id="sales_person" multiple required>
                                <option data-display="" value="">Select</option>
                                @foreach ($staffs as $value)
                                    <option value="{{ @$value->user_id }}"
                                        @foreach ($editAssign as $sp)  @if (@$sp->user_id == @$value->user_id) selected @endif
                                        @endforeach
                                        
                               >{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>

                </div>



            </div>

        </div>
    </div>
</div>
<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="address-field-tab" data-bs-toggle="tab"
                data-bs-target="#address-field" type="button" role="tab" aria-controls="address-field"
                aria-selected="true">Address</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-person-info-tab" data-bs-toggle="tab"
                data-bs-target="#contact-person-info" type="button" role="tab"
                aria-controls="contact-person-info" aria-selected="false">Contact
                Person</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#vat-details"
                type="button" role="tab" aria-controls="vat-details" aria-selected="false">VAT
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-details-tab" data-bs-toggle="tab" data-bs-target="#payment-details"
                type="button" role="tab" aria-controls="payment-details" aria-selected="false">Payment
            </button>
        </li>


        <li class="nav-item" role="presentation">
            <button class="nav-link" id="document-details-tab" data-bs-toggle="tab"
                data-bs-target="#document-details" type="button" role="tab" aria-controls="document-details"
                aria-selected="false">Documents
            </button>
        </li>
    </ul>
    <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
        <div class="tab-pane fade show active" id="address-field" role="tabpanel"
            aria-labelledby="address-field-tab">

            <div class="row">


                <script>
                    $(document).ready(function() {

                        // When normal country changes → update VAT country + load states
                        $("#country").on("change", function() {

                            let countryId = $(this).val(); // ID value
                            let selectedValue = $(this).val(); // <-- YOU MISSED THIS LINE earlier

                            console.log("Country selected:", countryId);

                            // Set VAT Country same as normal country
                            // $("#country_vat").val(countryId).trigger('change');

                            // Set telephone country by matching either ISO2 OR ID
                            // $("#country_telephone option").each(function () {

                            //     let optionValue = $(this).val();   // like "AE|231"
                            //     if (!optionValue) return;

                            //     let parts = optionValue.split("|");

                            //     let iso2 = parts[0];   // AE
                            //     let id = parts[1];     // 231

                            //     // Match by ISO2 OR ID
                            //     if (selectedValue == iso2 || selectedValue == id) {
                            //         $("#country_telephone").val(optionValue).trigger("change");
                            //     }
                            // });

                        });

                        // When normal state changes → update VAT state
                        $("#state").on("change", function() {
                            let stateId = $(this).val();
                            $("#vat_state").val(stateId).trigger("change");
                        });



                    });
                </script>

                @php
                    $billingAddress = $editAddressbook->where('is_shipping', 0)->first();
                    $shippingAddress = $editAddressbook->where('is_shipping', 1)->first();

                @endphp

                <div class="col-md-6">
                    <p><b>Billing Address</b></p>
                    <input type="hidden" name="billing_address_id" value="{{ @$billingAddress->id }}">
                    <div class="row">
                        <div class="col-md-3">Country</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single" name="country"
                                id="country" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if (@$billingAddress->country == $value->id) selected @endif>{{ @$value->name }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">State</div>
                        <div class="col-md-8">
                            <div id="sectionStateDiv">
                                <select class="form-control js-example-basic-single" name="state" id="state">
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}"
                                            @if (isset($billingAddress) && $billingAddress->state == $state->id) selected @endif>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">City</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="city"
                                id="city" value="{{ @$billingAddress->city }}" placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Area</div>
                        <div class="col-md-8">
    <input class="form-control"
           type="text"
           name="billing_area"
           id="billing_area"
           value="{{ !empty($billingAddress->area) ? $billingAddress->area : $editData->address2 }}"
           required>
</div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Building Name</div>
                       <div class="col-md-8">
    <input class="form-control"
           type="text"
           name="billing_building_name"
           id="billing_building_name"
           value="{{ empty($billingAddress->building_name) ? $editData->address : $billingAddress->building_name }}"
           required>
</div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Flat/Office No</div>
                        <div class="col-md-8"><input class="form-control" type="text"
                                name="billing_flat_office_shop_no" id="billing_flat_office_shop_no"
                                value="{{ @$billingAddress->flat_office_no }}" placeholder="" required></div>
                    </div>

                    {{-- <div class="row mt-2">
                        <div class="col-md-3">Address 1</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Address 2</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address2"
                                placeholder="" required></div>
                    </div> --}}


                    <div class="row mt-2">
                        <div class="col-md-3">Po Box</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="zip_code"
                                value="{{ @$billingAddress->zip_code }}" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">


                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="mb-0 fw-bold">Shipping Address</p>
                        <div class="d-flex justify-content-end align-items-center mb-2" style="gap: 16px;">
                            <!-- Checkbox -->
                            <p class="mb-0 d-flex align-items-center" style="gap: 6px; cursor: pointer;"
                                onclick="document.getElementById('same_billing_address').click();">
                                <input type="checkbox" id="same_billing_address" name="same_billing_address"
                                    value="1" style="pointer-events: none;">
                                <span>Same as Billing Address</span>
                            </p>


                            <!-- Icon + Add More -->
                            <a data-bs-toggle="modal" data-bs-target="#ModalAddress"
                                class="d-flex align-items-center text-decoration-none"
                                style="gap: 6px;  cursor: pointer;">
                                <i class="ico icon-bold-book-2" style="font-size: 16px; line-height: 1;"></i>
                                <span>Add More</span>
                            </a>
                        </div>

                        <script>
                            $("#same_billing_address").click(function() {
                                if (this.checked) {
                                    // $('[name=address_ship]').val($('[name=address]').val());
                                    // $('[name=address2_ship]').val($('[name=address2]').val());
                                    $('[name=city_ship]').val($('[name=city]').val());
                                    // $('#select2-country_ship-container').html($('#country option:selected').text());
                                    //troigger countey shiip
                                    $('#country_ship').val($('[name=country]').val()).trigger('change');
                                    setTimeout(function () {
    $('#state_ship')
        .val($('[name=state]').val())
        .trigger('change');
}, 500);
                                    // $('#state_ship').append(new Option($('#state option:selected').text(), '0', true, true));
                                    $('[name=zip_code_ship]').val($('[name=zip_code]').val());
                                    $('#country_ship').removeAttr('required');
                                    // $('#address_ship').removeAttr('required');
                                    // $('#address2_ship').removeAttr('required');
                                    $('#city_ship').removeAttr('required');
                                    $('#state_ship').removeAttr('required');
                                    $('#zip_code_ship').removeAttr('required');
                                    $('#shipping_area').val($('[name=billing_area]').val());
                                    $('#shipping_building_name').val($('[name=billing_building_name]').val());
                                    $('#shipping_flat_office_shop_no').val($('[name=billing_flat_office_shop_no]').val());
                                }
                                if (!this.checked) {
                                    // $('[name=address_ship]').val('');
                                    // $('[name=address2_ship]').val('');
                                    $('[name=city_ship]').val('');
                                    $('[name=country_ship]').val('');
                                    $('[name=state_ship]').val('');
                                    $('[name=zip_code_ship]').val('');
                                    $('#country_ship').attr('required');
                                    // $('#address_ship').attr('required');
                                    // $('#address2_ship').attr('required');
                                    $('#city_ship').attr('required');
                                    $('#state_ship').attr('required');
                                    $('#zip_code_ship').attr('required');
                                    $('#shipping_area').val('');
                                    $('#shipping_building_name').val('');
                                    $('#shipping_flat_office_shop_no').val('');
                                }
                            });
                        </script>

                    </div>



                    <div class="row">
                        <div class="col-md-3">Country</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single"
                                name="country_ship" id="country_ship" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if (@$shippingAddress->country == $value->id) selected @endif>{{ @$value->name }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">State</div>
                        <div class="col-md-8">
                            <div id="sectionStateDiv_ship">
                                <select class="form-control js-example-basic-single" name="state_ship"
                                    id="state_ship">

                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}"
                                            @if (isset($shippingAddress) && $shippingAddress->state == $state->id) selected @endif>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">City</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="city_ship"
                                value="{{ @$shippingAddress->city }}" placeholder="" required></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">Area</div>
                       <div class="col-md-8">
    <input class="form-control"
           type="text"
           name="shipping_area"
           id="shipping_area"
           value="{{ empty($shippingAddress->area) ? $editData->address2 : $shippingAddress->area }}"
           required>
</div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Building Name</div>
                       <div class="col-md-8">
    <input class="form-control"
           type="text"
           name="shipping_building_name"
           id="shipping_building_name"
           value="{{ empty($shippingAddress->building_name) ? $editData->address : $shippingAddress->building_name }}"
           required>
</div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Flat/Office No</div>
                        <div class="col-md-8"><input class="form-control" type="text"
                                name="shipping_flat_office_shop_no" id="shipping_flat_office_shop_no"
                                value="{{ @$shippingAddress->flat_office_no }}" placeholder="" required></div>
                    </div>


                    {{-- <div class="row mt-2">
                        <div class="col-md-3">Address 1</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address_ship"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Address 2</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address2_ship"
                                placeholder="" required></div>
                    </div> --}}
                    <input type="hidden" name="shipping_address_id" value="{{ @$shippingAddress->id }}">


                    <div class="row mt-2">
                        <div class="col-md-3">Po Box</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="zip_code_ship"
                                value="{{ @$shippingAddress->zip_code }}" placeholder=""></div>
                    </div>
                </div>
            </div>



            <div class="row mt-2">
                <div class="row g-3 mt-4">
                    @php
                        $excludeIds = [optional($billingAddress)->id, optional($shippingAddress)->id];
                    @endphp
                    @foreach ($editAddressbook->whereNotIn('id', $excludeIds) as $itm)
                        <div class="col-md-4 col-lg-3">
                            <div class="card border h-100">
                                <div class="card-body p-3">

                                    <!-- Delete Button -->
                                    <a class="text-danger float-end"
                                        href="{{ url('delete-cust-suppl-address/' . $itm->id) }}">
                                        <i class="ico icon-bold-trash-bin-2"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a class="text-success float-end me-3"
                                        onclick="edit_popup_data({{ $itm->id }})" data-bs-toggle="modal"
                                        data-bs-target="#ModalAddressEdit" style="cursor: pointer;">
                                        <i class="ico icon-outline-pen-2"></i>
                                    </a>

                                    <!-- Title -->
                                    <p class="fw-bold mb-3">
                                        @if ($itm->is_shipping)
                                            Shipping Address
                                        @else
                                            Billing Address
                                        @endif
                                    </p>

                                    <!-- Address Table -->
                                    <table class="table table-sm table-borderless mb-0 small">
                                        <tr>
                                            <th>Country</th>
                                            <td>{{ $itm->countryname->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>State</th>
                                            <td>{{ $itm->statename->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>City</th>
                                            <td>{{ $itm->city }}</td>
                                        </tr>
                                        <tr>
                                            <th>Area</th>
                                            <td>{{ $itm->area }}</td>
                                        </tr>
                                        <tr>
                                            <th>Building</th>
                                            <td>{{ $itm->building_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Flat/Office</th>
                                            <td>{{ $itm->flat_office_no }}</td>
                                        </tr>
                                        <tr>
                                            <th>PO Box</th>
                                            <td>{{ $itm->zip_code }}</td>
                                        </tr>
                                    </table>

                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields for Edit Popup -->
                        <input type="hidden" id="country_n_e_{{ $itm->id }}" value="{{ $itm->country }}" />
                        <input type="hidden" id="address_type_n_e{{ $itm->id }}"
                            value="{{ $itm->is_shipping }}" />
                        {{-- <input type="hidden" id="address_n_e_{{ $itm->id }}" value="{{ $itm->address }}" />
        <input type="hidden" id="address2_n_e_{{ $itm->id }}" value="{{ $itm->address2 }}" /> --}}
                        <input type="hidden" id="area_n_e_{{ $itm->id }}" value="{{ $itm->area }}" />
                        <input type="hidden" id="building_name_n_e_{{ $itm->id }}"
                            value="{{ $itm->building_name }}" />
                        <input type="hidden" id="flat_office_shop_no_n_e_{{ $itm->id }}"
                            value="{{ $itm->flat_office_no }}" />
                        <input type="hidden" id="city_n_e_{{ $itm->id }}" value="{{ $itm->city }}" />
                        <input type="hidden" id="state_n_e_{{ $itm->id }}" value="{{ $itm->state }}" />
                        <input type="hidden" id="zip_code_n_e_{{ $itm->id }}" value="{{ $itm->zip_code }}" />
                        <input type="hidden" id="set_default_n_e_{{ $itm->id }}"
                            value="{{ $itm->set_default }}" />

                    @endforeach
                </div>

                <script>
                    function edit_popup_data(id) {
                        $('#cust_suppl_edit_id').val(id);
                        $('#country_n_e').val($('#country_n_e_' + id).val());
                        // $('#address_type_n_e').val($('#address_type_n_e' + id).val());
                        // $('#address_n_e').val($('#address_n_e_' + id).val());
                        $('#area_n_e').val($('#area_n_e_' + id).val());
                        $('#building_name_n_e').val($('#building_name_n_e_' + id).val());
                        $('#flat_office_shop_no_n_e').val($('#flat_office_shop_no_n_e_' + id).val());
                        $('#address2_n_e').val($('#address2_n_e_' + id).val());
                        $('#city_n_e').val($('#city_n_e_' + id).val());
                        $('#state_n_e').val($('#state_n_e_' + id).val());
                        $('#zip_code_n_e').val($('#zip_code_n_e_' + id).val());
                        $('#set_default_n_e').val($('#set_default_n_e_' + id).val());
                    }
                </script>
            </div>

        </div>

        <div class="tab-pane fade" id="contact-person-info" role="tabpanel"
            aria-labelledby="contact-person-info-tab">

            <script>
                $(document).ready(function() {
                    // 1. Target the element with ID 'salutation' and attach a 'change' event handler
                    $("#salutation").change(function() {

                        // 2. Get the new value of the changed element (#salutation)
                        const newSalutationValue = $(this).val();

                        // 3. Set the value of the target element (#e_salutation_1)
                        $("#e_salutation_1").val(newSalutationValue).trigger('change');

                        console.log("Salutation changed to:", newSalutationValue);
                        console.log("#e_salutation_1 is now set to:", $("#e_salutation_1").val());
                    });

                    $("#company_designation").change(function() {

                        // 2. Get the new value of the changed element (#salutation)
                        const newDesignationValue = $(this).val();

                        // 3. Set the value of the target element (#e_salutation_1)
                        $("#e_designation_1").val(newDesignationValue).trigger('change');

                        console.log("Designation changed to:", newDesignationValue);
                        console.log("#e_designation is now set to:", $("#e_designation").val());
                    });



                    $(document).on("input", "#firstName", function() {

                        let value = $(this).val();

                        // Optional: normalize spaces
                        value = value.replace(/\s+/g, ' ').trimStart();

                        // Set value word-by-word (live)
                        $("#e_first_name_1").val(value);

                        console.log("First Name typing:", value);
                    });

                    $(document).on("input", "#lastName", function() {

                        let value = $(this).val();

                        // Optional: normalize spaces
                        value = value.replace(/\s+/g, ' ').trimStart();

                        // Set value word-by-word (live)
                        $("#e_last_name_1").val(value);

                        console.log("Last Name typing:", value);
                    });

                    $(document).on("input", "#company_email", function() {

                        let value = $(this).val();

                        // Optional: normalize spaces
                        value = value.replace(/\s+/g, ' ').trimStart();

                        // Set value word-by-word (live)
                        $("#e_email_address_1").val(value);

                        console.log("Email Address typing:", value);
                    });


                    $(document).on("input", "#company_mobile_phone", function() {

                        let value = $(this).val();

                        // Optional: normalize spaces
                        value = value.replace(/\s+/g, ' ').trimStart();

                        // Set value word-by-word (live)
                        $("#e_mobile_1").val(value);

                        console.log("Mobile typing:", value);
                    });


                });
            </script>

            <table class="table table-hover" id="pi-ret-table" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                        <th>@lang('Salutation')</th>
                        <th>@lang('First Name')</th>
                        <th>@lang('Last Name')</th>
                        <th>@lang('Email Address')</th>
                        {{-- <th>@lang('Work Phone')</th> --}}
                        <th>@lang('Mobile')</th>
                        <th>@lang('Department') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#adddepartmentModal"></a></th>
                        <th>@lang('Designation') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal"
                                data-bs-target="#adddesignationModal"></a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @foreach ($editContact as $edt)
                        <tr id="pr_row_{{ $i }}">

                            <td><select class="form-control js-example-basic-single" name="e_salutation[]"
                                    id="e_salutation_{{ $i }}">
                                    <option value="">-Salutation-</option>
                                    <option value="Mr." @if ($edt->salutation == 'Mr.') selected @endif>Mr.
                                    </option>
                                    <option value="Mrs." @if ($edt->salutation == 'Mrs.') selected @endif>Mrs.
                                    </option>
                                    <option value="Miss." @if ($edt->salutation == 'Miss.') selected @endif>Miss.
                                    </option>
                                </select></td>
                            <td><input type="text" class="form-control capitalize-words" name="e_first_name[]"
                                    id="e_first_name_{{ $i }}" value="{{ ucfirst($edt->first_name) }}" />
                            </td>
                            <td><input type="text" class="form-control capitalize-words" name="e_last_name[]"
                                    id="e_last_name_{{ $i }}" value="{{ ucfirst($edt->last_name) }}" />
                            </td>
                            <td><input type="text" class="form-control" name="e_email_address[]"
                                    id="e_email_address_{{ $i }}" value="{{ $edt->email_address }}" />
                            </td>
                            {{-- <td><input type="text" class="form-control" name="e_work_phone[]"
                                    id="e_work_phone_{{ $i }}" value="{{ $edt->work_phone }}" /></td> --}}
                            <td><input type="text" class="form-control" name="e_mobile[]"
                                    id="e_mobile_{{ $i }}" value="{{ $edt->mobile }}" /></td>

                            <td>
                                <select class="form-control js-example-basic-single" name="e_department[]"
                                    id="e_department_{{ $i }}">
                                    <option value="">--Department--</option>
                                    @if (count($department) > 0)
                                        @foreach ($department as $val)
                                            <option value="{{ $val->name }}"
                                                @if ($edt->department == $val->name) selected @endif>{{ $val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_designation[]"
                                    id="e_designation_{{ $i }}">
                                    <option value="">--Designation--</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}"
                                                @if ($edt->designation == $val->title) selected @endif>{{ $val->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>

                        </tr>
                        <?php $i++; ?>
                    @endforeach
                    @for ($r = $i; $r <= 5; $r++)
                        <tr id="pr_row_{{ $r }}">
                            <td><select class="form-control js-example-basic-single" name="e_salutation[]"
                                    id="e_salutation_{{ $r }}">
                                    <option value="">-Salutation-</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Miss.">Miss.</option>
                                </select></td>
                            <td><input type="text" class="form-control" name="e_first_name[]"
                                    id="e_first_name_{{ $r }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_last_name[]"
                                    id="e_last_name_{{ $r }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_email_address[]"
                                    id="e_email_address_{{ $r }}" value="" /></td>
                            {{-- <td><input type="text" class="form-control" name="e_work_phone[]"
                                    id="e_work_phone_{{ $r }}" value="" /></td> --}}
                            <td><input type="text" class="form-control" name="e_mobile[]"
                                    id="e_mobile_{{ $r }}" value="" /></td>

                            <td>
                                <select class="form-control js-example-basic-single" name="e_department[]"
                                    id="e_department_{{ $r }}">
                                    <option value="">--Department--</option>
                                    @if (count($department) > 0)
                                        @foreach ($department as $val)
                                            <option value="{{ $val->name }}">{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_designation[]"
                                    id="e_designation_{{ $r }}">
                                    <option value="">--Designation--</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>

                        </tr>
                    @endfor
                    <input type="hidden" value="{{ $r - 1 }}" id="pr_row_count" />
                </tbody>

            </table>
        </div>
        <div class="tab-pane fade" id="vat-details" role="tabpanel" aria-labelledby="vat-details-tab">


            <div class="row">
                <div class="col-md-6">
                    <div class="row mt-2">
                        <div class="col-md-3">VAT Country</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single" name="country_vat"
                                id="country_vat" required>
                                <option data-display="" value=""></option>
                                @foreach ($vat as $key => $value)
                                    <option value="{{ @$value->vat_country }}"
                                        @if ($editData->vat_country == $value->vat_country) selected @endif>{{ @$value->name }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">VAT State</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single" name="vat_state"
                                id="vat_state" required>
                                <option data-display="" value=""></option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        @if (isset($editData) && $editData->vat_state == $state->id) selected @endif>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">VAT %</div>
                        <div class="col-md-2"><input class="form-control" type="number" name="vat_percentage"
                                id="vat_percentage" value="{{ $editData->vat_percentage }}" readonly required></div>
                        <div class="col-md-4 mt-2"><input type="checkbox" name="vat_percentage_fixed"
                                id="vat_percentage_fixed" value="1"
                                @if ($editData->vat_is_fixed == 1) checked @endif> Fixed Rate</div>
                        <script>
                            $("#vat_percentage_fixed").click(function() {
                                if (this.checked) {
                                    $('#vat_percentage').attr('readonly', false);
                                }
                                if (!this.checked) {
                                    $('#vat_percentage').attr('readonly', true);
                                }
                            });
                        </script>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Customer Type</div>
                        <div class="col-md-8"><select class="form-control" name="customer_type" id="customer_type"
                                required>
                                <option data-display="" value=""></option>
                                @foreach ($customer_type as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->id == $editData->customer_type) selected @endif>{{ @$value->title }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">Sale Type</div>
                        <div class="col-md-8"><select class="form-control" name="sale_type" id="sale_type" required>
                                <option data-display="" value=""></option>
                                @foreach ($sale_type as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->id == $editData->sale_type) selected @endif>{{ @$value->title }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">VAT Number</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="vat_number"
                                value="{{ $editData->vat_number }}"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="tab-pane fade" id="payment-details" role="tabpanel" aria-labelledby="payment-details-tab">

            <div class="row">
                <div class="col-md-6">
                    <div class="row mt-2">
                        <div class="col-md-3">Transaction Type</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single"
                                name="transaction_type" id="transaction_type" required>
                                <option value="">Select</option>
                                <option value="Cash"
                                    @if ($editData->transaction_type == 'Cash') selected @elseif($editData->transaction_type != 'Credit') selected @endif>
                                    Cash</option>
                                <option value="Credit" @if ($editData->transaction_type == 'Credit' || $editData->transaction_type == null) selected @endif>Credit
                                </option>
                            </select></div>
                    </div>

                    <div class="row mt-2 credit-fields">
                        <div class="col-md-3">Credit Limit</div>
                        <div class="col-md-8"><input class="form-control format-amount" type="text"
                                value="{{ number_format($editData->credit_limit, 2, '.', ',') }}" name="credit_limit" required></div>
                    </div>
                    <div class="row mt-2 credit-fields">
                        <div class="col-md-3">Credit Days</div>
                        <div class="col-md-8"><input class="form-control" type="number"
                                value="{{ $editData->credit_days }}" name="credit_days" required></div>
                    </div>





                    <script>
                        $(document).ready(function() {

                            function formatAmount(input) {
                                let inputStr = input.toString();
                                let number = parseFloat(inputStr.replace(/,/g, ''));

                                if (!isNaN(number)) {
                                    return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                } else {
                                    return '';
                                }
                            }

                            function unformatAmount(value) {
                                return value.toString().replace(/,/g, '');
                            }

                            $('.format-amount').on('blur', function() {
                                $(this).val(formatAmount($(this).val()));
                            }).on('focus', function() {
                                $(this).val(unformatAmount($(this).val()));
                            });

                            function toggleCreditFields() {
                                let type = $('#transaction_type').val();

                                if (type === 'Cash') {
                                    $('.credit-fields').hide();
                                    $('#payment_terms_cash').show();
                                    $('#payment_terms_normal').hide();
                                    $('.credit-fields input').prop('required', false).val('');
                                } else if (type === 'Credit') {
                                    $('.credit-fields').show();
                                    $('.credit-fields input').prop('required', true);
                                    $('#payment_terms_cash').hide();
                                    $('#payment_terms_normal').show();
                                }
                            }

                            // Run on page load (edit case)
                            toggleCreditFields();

                            // Run when user changes option
                            $('#transaction_type').on('change', function() {
                                console.log("Transaction type changed to:", $(this).val());
                                toggleCreditFields();
                            });
                        });
                    </script>

                    <div class="row mt-2">
                        <div class="col-md-3">Payment Terms</div>
                        <div class="col-md-8">


                            <div id="payment_terms_cash" style="display: none">
                                <select class="form-control js-example-basic-single" name="payment_terms_cash">
                                    <option @if ($editData->payment_terms == 1) selected @endif value="1">CASH ON DELIVERY</option>
                                    <option @if ($editData->payment_terms == 2) selected @endif value="2">CDC ON DELIVERY</option>
                                    <option @if ($editData->payment_terms == 3) selected @endif value="3">ADVANCE PAYMENT</option>
                                </select>
                            </div>


                            <div id="payment_terms_normal">
                                <select class="form-control js-example-basic-single" name="payment_terms"
                                    id="payment_terms">
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($editData->payment_terms == $value->id) selected @else @if ($value->id == 3) selected @endif
                                            @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>





                            <input class="form-control" id="payment_terms_txt" type="text" value=""
                                autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt"
                                style="display: none;">
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
                    <div class="row mt-2">
                        <div class="col-md-3">GRN</div>
                        <div class="col-md-8">
                            <select class="form-control js-example-basic-single" name="grn_select"
                                id="grn_select">
                                
                                    <option value="yes" @if ($editData->grn_select == 'yes') selected @endif>Yes</option>
                                    <option value="no" @if ($editData->grn_select == 'no') selected @endif>No</option>
                             
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="tab-pane fade" id="document-details" role="tabpanel" aria-labelledby="document-details-tab">
            <div class="row">
                <div class="col-md-8">
                    <div class="table-responsive mb-3">
                        <table id="long-list" class="table table-hover" style="table-layout: fixed; width:100%">

                            <thead class="text-start">
                                <tr>
                                    <th style="width: 100px;">Document Name</th>
                                    <th style="width: 40px;" class="text-center">Expiry Date</th>
                                    <th style="width: 60px;" class="text-center">Action</th>
                                    <th style="width: 100px;" class="text-center">Deleted Info</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($editDoc as $doc)
                                    <tr class="{{ $doc->deleted_at ? 'table-danger opacity-75' : '' }}">

                                        {{-- Document Name --}}
                                        <td>
                                            @if ($doc->deleted_at)
                                                <span class="text-muted" style="text-decoration: line-through;">
                                                    {{ $doc->doc_name }}
                                                </span>
                                            @else
                                                {{ $doc->doc_name }}
                                            @endif
                                        </td>

                                        {{-- Expiry Date --}}
                                        <td class="text-center">
                                            @if ($doc->deleted_at)
                                                <span class="text-muted" style="text-decoration: line-through;">
                                                    {{ date('d/m/Y', strtotime($doc->doc_exp_date)) }}
                                                </span>
                                            @else
                                                {{ date('d/m/Y', strtotime($doc->doc_exp_date)) }}
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-center no-toggle">

                                            <a class="btn-sm btn-light" title="Download Document"
                                                href="{{ asset('public/uploads/cust-suppl/' . $doc->doc_file) }}"
                                                target="_blank">
                                                Download
                                            </a>

                                            @if ($doc->deleted_at)
                                                <a class="btn-sm btn-light border-0" title="Restore Document"
                                                    href="{{ url('restore-cust-suppl-doc/' . $doc->id) }}">
                                                    <i class="ico  icon-bold-restart  text-dark"
                                                        style="font-size:14px;"></i>
                                                </a>
                                            @else
                                                <a class="btn-sm btn-light border-0" title="Delete Document"
                                                    href="{{ url('delete-cust-suppl-doc/' . $doc->id) }}">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size:14px;"></i>
                                                </a>
                                            @endif



                                        </td>

                                        {{-- Deleted Info --}}
                                        <td class="text-center">
                                            @if ($doc->deleted_at)
                                                {{-- <span class="badge bg-danger">DELETED</span> --}}

                                                <small class="text-muted">
                                                    By: {{ $doc->deletedby->full_name ?? 'Unknown' }} <br>
                                                    On: {{ date('d/m/Y H:i', strtotime($doc->deleted_at)) }}
                                                </small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            No documents found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-light float-end" style="cursor: pointer;" onclick="add_doc_row()"><i
                            class="ico icon-outline-add-square text-success" aria-hidden="true"></i> Add More</a>
                    <input type="hidden" id="doc_row" value="4" />
                    <script>
                        function add_doc_row() {
                            var r = $('#doc_row').val()
                            $('#d_' + r).css('display', '');
                            r++;
                            $('#doc_row').val(r);
                        }
                    </script>
                </div>
            </div>
            @for ($i = 1; $i <= 10; $i++)
                <div class="row pb-2" id="d_{{ $i }}"
                    @if ($i > 3) style="display:none;" @endif>
                    <div class="col-md-3">
                        <input class="form-control" type="text" name="doc_name[]"
                            value="@if ($i == 1) Trade License @elseif($i == 2) VAT Certificate @else Other Documents @endif"
                            @if ($i == 1) readonly @endif
                            @if ($i == 2) readonly @endif />
                    </div>
                    <div class="col-md-3">
                        <input class="form-control" type="file" name="customer_documents_{{ $i }}" />
                    </div>
                    @if ($i == 1)
                        <div class="col-md-3">
                            <input class="form-control date-picker" type="text" name="doc_exp_date[]"
                                placeholder="Expiry Date" />

                        </div>
                    @endif
                    <div class="col-md-3">&nbsp;</div>
                </div>
            @endfor
        </div>
    </div>
</div>


</div>

{{ Form::close() }}




<div class="modal side-panel fade" id="ModalAddress" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 369px !important;">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Add New Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-cust-suppl-address', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="cust_suppl_id" value="{{ $editData->id }}" />
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                        <div class="row">
                            <div class="col-md-3">Address Type</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    name="address_type_n">
                                    <option value="0">Billing Address</option>
                                    <option value="1">Shipping Address</option>
                                </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Country</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="country_n"
                                    name="country_n" required>
                                    <option data-display="" value=""></option>
                                    @foreach ($countries as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                    @endforeach
                                </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">State</div>
                            <div class="col-md-8">
                                <div id="sectionStateDiv_n">
                                    <select class="form-control js-example-basic-single" id="state_n"
                                        name="state_n" required>
                                        <option data-display="" value=""></option>
                                        <?php try { ?>
                                        @if (isset($editData) && $editData->vat_state != '')
                                            <option data-display="{{ $editData->vatstate->name }}"
                                                value="{{ $editData->vat_state }}" selected>
                                                {{ $editData->vatstate->name }}</option>
                                        @endif
                                        <?php }catch (\Exception $e) {   } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                         <div class="row mt-2">
                            <div class="col-md-3">City</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="city_n"
                                    placeholder="" required></div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">Area</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="area_n"
                                    id="area_n" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Building Name</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="building_name_n"
                                    id="building_name_n" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Flat/Office No</div>
                            <div class="col-md-8"><input class="form-control" type="text"
                                    name="flat_office_shop_no_n" id="flat_office_shop_no_n" placeholder="" required>
                            </div>
                        </div>
                        {{-- <div class="row mt-2">
                            <div class="col-md-3">Address 1</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="address_n"
                                    placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="address2_n"
                                    placeholder="" required></div>
                        </div> --}}
                       

                        <div class="row mt-2">
                            <div class="col-md-3">PO Box</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="zip_code_n"
                                    placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Set Default</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    id="set_default_n">
                                    <option value="0">None</option>
                                    <option value="1">Default Billing Address</option>
                                    <option value="1">Default Shipping Address</option>
                                </select></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>


<div class="modal side-panel fade" id="ModalAddressEdit" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-cust-suppl-address', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Edit Address</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <input type="hidden" id="cust_suppl_edit_id" name="cust_suppl_edit_id" />
            <input type="hidden" name="cust_suppl_edit" value="{{ $editData->id }}" />
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                        <div class="row">
                            <div class="col-md-3">Address Type</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    id="address_type_n_e" name="address_type_n_e">
                                    <option value="0">Billing Address</option>
                                    <option value="1">Shipping Address</option>
                                </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Country</div>
                            <div class="col-md-8"><select class="form-control" id="country_n_e" name="country_n_e"
                                    required>
                                    <option data-display="" value=""></option>
                                    @foreach ($countries as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                    @endforeach
                                </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">State</div>
                            <div class="col-md-8">
                                <div id="sectionStateDiv_n_e">
                                    <select class="form-control" id="state_n_e" name="state_n_e" required>
                                        <option data-display="" value=""></option>
                                        <?php try { ?>
                                        @if (isset($states))
                                            @foreach ($states as $st)
                                                <option data-display="{{ $st->name }}"
                                                    value="{{ $st->id }}" selected> {{ $st->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                        <?php }catch (\Exception $e) {   } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">City</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="city_n_e"
                                    name="city_n_e" placeholder="" required></div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-3">Area</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="area_n_e"
                                    id="area_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Building Name</div>
                            <div class="col-md-8"><input class="form-control" type="text"
                                    name="building_name_n_e" id="building_name_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Flat/Office No</div>
                            <div class="col-md-8"><input class="form-control" type="text"
                                    name="flat_office_shop_no_n_e" id="flat_office_shop_no_n_e" placeholder=""
                                    required></div>
                        </div>

                        {{-- <div class="row mt-2">
                            <div class="col-md-3">Address 1</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address_n_e"
                                    name="address_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address2_n_e"
                                    name="address2_n_e" placeholder="" required></div>
                        </div> --}}


                        <div class="row mt-2">
                            <div class="col-md-3">PO Box</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="zip_code_n_e"
                                    name="zip_code_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Set Default</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    id="set_default_n_e" name="set_default_n_e">
                                    <option value="0">None</option>
                                    <option value="1">Default Billing Address</option>
                                    <option value="1">Default Shipping Address</option>
                                </select></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btn_add_address" type="submit" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- Restore last active tab ---
        let lastTab = localStorage.getItem("active-customeredit-tab");
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
            btn.addEventListener("shown.bs.tab", function(e) {
                localStorage.setItem("active-customeredit-tab", e.target.getAttribute(
                    "data-bs-target"));
            });
        });

    });
</script>


<script>
    function add_address() {
        if ($("#country_n").val() == "") {
            $("#country_n").focus();
            return false;
        }
        if ($("#address_n").val() == "") {
            $("#address_n").focus();
            return false;
        }
        if ($("#address2_n").val() == "") {
            $("#address2_n").focus();
            return false;
        }
        if ($("#city_n").val() == "") {
            $("#city_n").focus();
            return false;
        }
        if ($("#state_n").val() == "") {
            $("#state_n").focus();
            return false;
        }

        $("#loading_bg").css("display", "block");
        var address_type_n = $("#address_type_n").val();
        var country_n = $("#country_n").val();
        var address_n = $("#address_n").val();
        var address2_n = $("#address2_n").val();
        var city_n = $("#city_n").val();
        var state_n = $("#state_n").val();
        var zip_code_n = $("#zip_code_n").val();
        var set_default_n = $("#set_default_n").val();

        var action = "{{ URL::to('add-supplier-script') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                address_type: address_type_n,
                country: country_n,
                address: address_n,
                address2: address2_n,
                city: city_n,
                state: state_n,
                zip_code: zip_code_n,
                set_default: set_default_n,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                if (dataResult['data'] == "ERROR") {
                    alert("Error found!!");
                    $("#loading_bg").css("display", "none");
                    return false;
                }
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    $("#address_div").empty();
                    for (var i = 0; i < len; i++) {
                        var id = dataResult['data'][i].id;
                        var country = dataResult['data'][i].c_name;
                        var address = dataResult['data'][i].address;
                        var address2 = dataResult['data'][i].address2;
                        var city = dataResult['data'][i].city;
                        var state = dataResult['data'][i].s_name;
                        var zip_code = dataResult['data'][i].zip_code;
                        var innerHtml =
                            "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-right' onclick='del_address(" +
                            id +
                            ")'><i class='ico icon-bold-trash-bin-2' aria-hidden='true'></i></a>Country : " +
                            country + "<br />Address : " + address + "<br />Address2 : " + address2 +
                            "<br />City : " + city + "<br />State : " + state + "<br />PO Box : " +
                            zip_code + "</p></div>";
                        $("#address_div").append(innerHtml);
                    }


                    alert("Address Added!!");
                } else {
                    $("#address_div").empty();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }

    function del_address(id) {
        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('delete-supplier-script') }}";
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
                if (dataResult['data'] == "ERROR") {
                    alert("Error found!!");
                    $("#loading_bg").css("display", "none");
                    return false;
                }
                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }
                if (len > 0) {
                    $("#address_div").empty();
                    for (var i = 0; i < len; i++) {
                        var id = dataResult['data'][i].id;
                        var country = dataResult['data'][i].c_name;
                        var address = dataResult['data'][i].address;
                        var address2 = dataResult['data'][i].address2;
                        var city = dataResult['data'][i].city;
                        var state = dataResult['data'][i].s_name;
                        var zip_code = dataResult['data'][i].zip_code;
                        var innerHtml =
                            "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-right' onclick='del_address(" +
                            id + ")'><i class='fa fa-window-close' aria-hidden='true'></i></a>Country : " +
                            country + "<br />Address : " + address + "<br />Address2 : " + address2 +
                            "<br />City : " + city + "<br />State : " + state + "<br />PO Box : " +
                            zip_code + "</p></div>";
                        $("#address_div").append(innerHtml);
                    }
                    alert("Address Deleted!!");
                } else {
                    $("#address_div").empty();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }
</script>

<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize form validation for crm-deals-form
        FormValidator.init('customer-save-form-edit', {
            showAllErrors: true,
            scrollToFirst: true,
            highlightFields: true,
            toastrPosition: 'toast-top-right',
            toastrTimeout: 6000
        });
    });
</script>

<script>
    $(document).ready(function() {

        $(document).on("input", ".capitalize-words", function() {
            let val = $(this).val();

            val = val.replace(/\b\w/g, char => char.toUpperCase());

            $(this).val(val);
        });

    });
</script>
<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

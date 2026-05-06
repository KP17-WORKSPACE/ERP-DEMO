<?php try { ?>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhC6fAWLvqoE4znv7d8ovf8y3pMR0OG7s&libraries=places&language=en">
</script>



<script>
    $(document).ready(function() {
        const input = document.getElementById("customer_name");

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
                    if (types.includes("sublocality") || types.includes(
                        "sublocality_level_1")) {
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
            setFieldvalue("customer_name", place.name || "");
            setFieldvalue("customer_name_display", place.name.toUpperCase() || "");
            setFieldvalue("supplier_work", place.international_phone_number || "");
            setFieldvalue("supplier_mobile", place.international_phone_number || "");
            // setFieldvalue("mobile_code", mobileCode);
            setFieldvalue("customer_website", place.website || "");


            setFieldvalue("maps_location", place.url || "");

            // setFieldvalue("country", address.country);
            var targetName = address.country || ""; // or any value you want to match


            function normalize(str) {
                return str.toLowerCase().replace(/\s+/g, '');
            }

            // Select the option based on its text (the visible name)
            $('#country_telephone option').each(function() {
                if (normalize($(this).text()) === normalize(targetName)) {
                    $(this).prop('selected', true);
                    $('#country_telephone').trigger('change');
                }
            });

            setFieldvalue("state", address.state);

            var targetName = address.state || ""; // or any value you want to match


            // Delay execution to ensure options are loaded
            setTimeout(function() {
                var matched = false;

                $('#state option').each(function() {
                    if (normalize($(this).text()) === normalize(targetName)) {
                        $(this).prop('selected', true);
                        matched = true;
                        return false; // break the loop
                    }
                });

                if (matched) {
                    $('#state').trigger('change');
                }

                console.log("State selection attempted for:", targetName);
            }, 600); // adjust 300ms as needed


            setFieldvalue("city", address.city);
            setFieldvalue("billing_area", address.area);
            setFieldvalue("building_name", address.building_name);
            setFieldvalue("zip_code", address.postal_code);
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

{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'supplierForm', 'novalidate' => true]) }}




<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">


<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New - {{ @App\SysHelper::get_new_supplier_code() }}
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">

            <button type="submit" name="btnSubmit" value="createcustomer" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Save & Create Customer
            </button>

              <button type="submit" name="btnSubmit" value="save" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>



        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
              <div class="row gap-rows">

                <div class="col-12">

            <div class="row gap-rows g-2">

                <style>
                    #company_name_add_list ul {
                        width: 380px;
                        left: 29rem
                    }
                </style>

                <input type="hidden" id="place_id" name="place_id">

                 <div class="col-1">
                            <label for="" class="form-label">Sup. Type</label>

                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="account_type" required>

                                    <option value="1">Vendor</option>
                                    <option value="2">Forwarder</option>
                                    <option value="3">Courier</option>
                                </select>
                            </div>

                        </div>

                <div class="col-4">
                    <label for="" class="form-label">Supplier Name</label>

                    <input class="form-control" type="text" name="customer_name" id="customer_name"
                        placeholder="" required>
                    <div id="company_name_add_list">
                    </div>
                    <script>
                        $(document).ready(function() {

                            // KEYUP → fetch supplier autocomplete
                            $('#customer_name').keyup(function() {
                                var query = $(this).val();

                                if (query !== '') {
                                    var _token = $('input[name="_token"]').val();

                                    $.ajax({
                                        url: "{{ route('autocomplete.supplier_name') }}",
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

                            // CLICK ITEM → set input value
                            $('#company_name_add_list').on('click', 'li', function() {
                                $('#customer_name').val('');
                                $('#customer_name_display').val('');
                                toastr.info('Customer Already Exists.', 'Info');



                                $('#company_name_add_list').fadeOut();
                            });

                            // CLICK OUTSIDE → hide list
                            $(document).on('click', function(e) {
                                if (!$(e.target).closest('#customer_name').length &&
                                    !$(e.target).closest('#company_name_add_list').length) {
                                    $('#company_name_add_list').fadeOut();
                                }
                            });

                        });
                    </script>


                </div>

                <div class="col-4">

                    <label for="" class="form-label">Supplier Display Name</label>

                    <input class="form-control" type="text" name="customer_name_display" id="customer_name_display"
                        placeholder="" required>
                </div>

                 <div class="col-3">
                    <label for="" class="form-label">Supplier Website</label>
                    <div class="form-group">
                        <input type="text" name="customer_website" id="customer_website" value=""
                            class="form-control" placeholder="">
                    </div>
                </div>

                 <!-- Salutation -->
                        <div class="col-1">
                            <label for="" class="form-label">Salutation</label>

                             <select class="form-control select2 js-example-basic-single rounded-0  h-100" id="salutation"
                                            name="salutation">

                                            <option value="Mr" selected>Mr</option>
                                            <option value="Mrs">Mrs</option>
                                            <option value="Miss">Miss</option>
                                        </select>



                        </div>

                         <div class="col-2 ">
                            <label for="" class="form-label">First Name</label>
                           <input type="text" class="form-control rounded-0  capitalize-words"
                                        id="firstName" name="first_name" placeholder="">
                        </div>

                         <div class="col-2">
                            <label for="" class="form-label">Last Name</label>
                          <input type="text" class="form-control rounded-0 capitalize-words"
                                        id="lastName" name="last_name" placeholder="">
                        </div>

                            <div class="col-1">
                            <label class="form-label">Country</label>



                            <select class="form-select js-example-basic-single" name="country_telephone"
                                id="country_telephone" required>
                                <option value="" disabled selected>Select Country</option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ $value->iso2 }}|{{ $value->id }}">
                                        {{ $value->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-3">
                            <label for="" class="form-label">Supplier Phone</label>

                               <input class="form-control" type="text" name="mobile_code" id="supplier_work"
                                placeholder="" required>

                        </div>

                         <div class="col-3">
                            <label for="" class="form-label">Supplier Mobile</label>
                          <input class="form-control" type="text" name="mobile" id="supplier_mobile"
                                placeholder="">
                        </div>



                 <div class="col-3">
                    <label for="" class="form-label">Supplier Email</label>
                    <input class="form-control" type="text" name="email" id="company_email" placeholder=""
                        required>
                </div>

                <div class="col-3 mt-0">


                            <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                                <span>Designation</span>
                                <button type="button" class="btn btn-sm p-0 ms-2"
                                    style="border:none;background:none;" data-bs-toggle="modal"
                                    data-bs-target="#adddesignationModal2">
                                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                                </button>
                            </label>
                            <div class="form-group">
                               <select class="form-control js-example-basic-single" name="designation" id="company_designation"
                            required>
                            <option value="">Select</option>
                            @if (count($designation) > 0)
                                @foreach ($designation as $val)
                                    <option value="{{ $val->title }}">{{ $val->title }}</option>
                                @endforeach
                            @endif
                        </select>
                                {{-- <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i> --}}
                            </div>

                        </div>

                        <div class="col-3">
                    <label for="" class="form-label">Maps Location</label>
                    <div class="form-group">
                        <input type="text" name="maps_location" id="maps_location" value=""
                            class="form-control" placeholder="">
                    </div>
                </div>

                 <div class="col-1-5">

                            <label for="" class="form-label">Supplier Type</label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="type" id="type">
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

                            </div>


                        </div>

                 <div class="col-1-5">
                            <label for="" class="form-label">Internal Supplier</label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="internal">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>


                            </div>
                        </div>



                <div class="col-12">
                    <label for="" class="form-label">Company</label>
                    <select class="form-control js-example-basic-single" name="company_access[]" id="company_access"
                        multiple required>
                        @foreach ($company as $value)
                            <option value="{{ @$value->id }}" @if (session('logged_session_data.company_id') == @$value->id) selected @endif>
                                {{ @$value->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                 


               

                
                <script>
                    $('#customer_name').on('input', function() {
                        var txt = $('#customer_name').val();
                        $('#customer_name_display').val(txt.toUpperCase());
                        var txt2 = capitalizeFirstLetter(txt);
                        $('#customer_name').val(txt2);
                    });

                    function capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    }
                </script>




               

          

               

                


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

                            // Extract ID
                            var ctry_id = fullValue ? fullValue.split('|')[1] : '';

                            $('#country').val(ctry_id).trigger('change');

                            var code = countryCodes[iso2] || '';
                            var currentNumber = $('#supplier_work').val().replace(/^\+\d+\s?/,
                                ''); // remove previous code
                            var currentNumber2 = $('#supplier_mobile').val().replace(/^\+\d+\s?/,
                                ''); // remove previous code

                            $('#supplier_work').val(code ? '+' + code + ' ' + currentNumber : currentNumber);
                            $('#supplier_mobile').val(code ? '+' + code + ' ' + currentNumber2 : currentNumber2);



                           
                                    if (code) {
                                        $('#e_mobile_1').val('+' + code + ' ' + currentNumber2);
                                    }
                        });
                    });
                </script>


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
            <button class="nav-link" id="stl-details-tab" data-bs-toggle="tab" data-bs-target="#stl-details"
                type="button" role="tab" aria-controls="stl-details" aria-selected="false">Bank Details
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


            <script>
                $(document).ready(function() {

                    // When normal country changes → update VAT country + load states
                    $("#country").on("change", function() {

                        let countryId = $(this).val(); // ID value
                        let selectedValue = $(this).val(); // <-- YOU MISSED THIS LINE earlier

                        console.log("Country selected:", countryId);

                        // Set VAT Country same as normal country
                        $("#country_vat").val(countryId).trigger('change');

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
            <div class="row">

                <div class="col-md-6">
                    <p><b>Supplier Address</b></p>
                    <div class="row">
                        <div class="col-md-3">Country</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single" name="country"
                                id="country" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">State</div>
                        <div class="col-md-8">
                            <div id="sectionStateDiv">
                                <select class="form-control js-example-basic-single" name="state" id="state">
                                    <option data-display="" value=""></option>
                                    <?php    try { ?>
                                    @if (isset($editData) && $editData->vat_state != '')
                                        <option data-display="{{ $editData->vatstate->name }}"
                                            value="{{ $editData->vat_state }}" selected>
                                            {{ $editData->vatstate->name }}
                                        </option>
                                    @endif
                                    <?php    } catch (\Exception $e) {
    } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">City</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="city"
                                id="city" placeholder="" required></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">Area</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="billing_area"
                                id="billing_area" placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Building Name</div>
                        <div class="col-md-8"><input class="form-control" type="text"
                                name="billing_building_name" id="billing_building_name" placeholder="" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Flat/Office No</div>
                        <div class="col-md-8"><input class="form-control" type="text"
                                name="billing_flat_office_shop_no" id="billing_flat_office_shop_no" placeholder=""
                                required></div>
                    </div>
                    {{-- <div class="row mt-2">
                        <div class="col-md-3">Address 1</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address" placeholder=""
                                required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Address 2</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address2" placeholder=""
                                required></div>
                    </div> --}}


                    <div class="row mt-2">
                        <div class="col-md-3">Post Box</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="zip_code"
                                id="zip_code" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">



                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="mb-0 fw-bold">Warehouse Address</p>
                        <div class="d-flex justify-content-end align-items-center mb-2" style="gap: 16px;">
                            <!-- Checkbox -->
                            <p class="mb-0 d-flex align-items-center" style="gap: 6px; cursor: pointer;"
                                onclick="document.getElementById('same_billing_address').click();">
                                <input type="checkbox" id="same_billing_address" name="same_billing_address"
                                    value="1" style="pointer-events: none;">
                                <span>Same as Supplier Address</span>
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
                                    $('[name=shipping_area').val($('[name=billing_area]').val());
                                    $('[name=shipping_building_name').val($('[name=billing_building_name]').val());
                                    $('[name=shipping_flat_office_shop_no]').val($('[name=billing_flat_office_shop_no]').val());
                                    $('[name=city_ship]').val($('[name=city]').val());
                                    $('#select2-country_ship-container').html($('#country option:selected').text());
                                    $('#state_ship').append(new Option($('#state option:selected').text(), '0', true, true));
                                    $('[name=zip_code_ship]').val($('[name=zip_code]').val());
                                    $('#country_ship').removeAttr('required');
                                    // $('#address_ship').removeAttr('required');
                                    // $('#address2_ship').removeAttr('required');
                                    $('#city_ship').removeAttr('required');
                                    $('#state_ship').removeAttr('required');
                                    $('#zip_code_ship').removeAttr('required');
                                }
                                if (!this.checked) {
                                    // $('[name=address_ship]').val('');
                                    // $('[name=address2_ship]').val('');
                                    $('[name=shipping_area]').val('');
                                    $('[name=shipping_building_name]').val('');
                                    $('[name=shipping_flat_office_shop_no]').val('');
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
                                    <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                @endforeach
                            </select></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">State</div>
                        <div class="col-md-8">
                            <div id="sectionStateDiv_ship">
                                <select class="form-control js-example-basic-single" name="state_ship"
                                    id="state_ship">
                                    <option data-display="" value=""></option>
                                    <?php    try { ?>
                                    @if (isset($editData) && $editData->vat_state != '')
                                        <option data-display="{{ $editData->vatstate->name }}"
                                            value="{{ $editData->vat_state }}" selected>
                                            {{ $editData->vatstate->name }}
                                        </option>
                                    @endif
                                    <?php    } catch (\Exception $e) {
    } ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-3">City</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="city_ship"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Area</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="shipping_area"
                                id="shipping_area" placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Building Name</div>
                        <div class="col-md-8"><input class="form-control" type="text"
                                name="shipping_building_name" id="shipping_building_name" placeholder="" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Flat/Office No</div>
                        <div class="col-md-8"><input class="form-control" type="text"
                                name="shipping_flat_office_shop_no" id="shipping_flat_office_shop_no" placeholder=""
                                required></div>
                    </div>

                    {{-- <div class="row mt-2">
                        <div class="col-md-3">Address 1</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address_ship" placeholder=""
                                required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Address 2</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address2_ship"
                                placeholder="" required></div>
                    </div> --}}


                    <div class="row mt-2">
                        <div class="col-md-3">Post Box</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="zip_code_ship"
                                placeholder=""></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-4" id="address_div">
                @foreach ($address_cart as $itm)
                    <div class="col-md-4 col-lg-3">
                        <div class="card border h-100">
                            <div class="card-body p-3">

                                <a class="text-danger float-end" onclick="del_address({{ $itm->id }})">
                                    <i class="ico icon-bold-trash-bin-2"></i>
                                </a>

                                <p class="fw-bold mb-3">
                                    @if ($itm->is_shipping)
                                        Warehouse Address
                                    @else
                                        Supplier Address
                                    @endif
                                </p>

                                <table class="table table-sm table-borderless mb-0 small">
                                    <tr>
                                        <th>Country</th>
                                        <td>{{ $itm->c_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>State</th>
                                        <td>{{ $itm->state }}</td>
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
                @endforeach
            </div>
        </div>
        <div class="tab-pane fade" id="contact-person-info" role="tabpanel"
            aria-labelledby="contact-person-info-tab">
            <table class="table table-hover" id="pi-ret-table" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                        <th>@lang('Salutation')</th>
                        <th>@lang('First Name')</th>
                        <th>@lang('Last Name')</th>
                        <th>@lang('Email Address')</th>
                        <!-- <th>@lang('Work Phone')</th> -->
                        <th>@lang('Mobile')</th>
                        <th>@lang('Designation')</th>
                        <th>@lang('Department')</th>
                        {{-- <th><a class="btn-sm btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a></th> --}}
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @for ($r = 1; $r <= 5; $r++)
                        <tr id="pr_row_{{ $i }}">
                            <td><select class="form-control js-example-basic-single" name="e_salutation[]"
                                    id="e_salutation_{{ $i }}">
                         
                                    <option value="Mr.">Mr.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Miss.">Miss.</option>
                                </select></td>
                            <td><input type="text" class="form-control" name="e_first_name[]"
                                    id="e_first_name_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_last_name[]"
                                    id="e_last_name_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_email_address[]"
                                    id="e_email_address_{{ $i }}" value="" /></td>
                            <!-- <td><input type="text" class="form-control" name="e_work_phone[]"
                                    id="e_work_phone_{{ $i }}" value="" /></td> -->
                            <td><input type="text" class="form-control" name="e_mobile[]"
                                    id="e_mobile_{{ $i }}" value="" /></td>
                            <td><select class="form-control js-example-basic-single" name="e_designation[]"
                                    id="e_designation_{{ $i }}">
                                    <!-- <option value="">--Designation--</option> -->
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select></td>
                            <td><select class="form-control js-example-basic-single" name="e_department[]"
                                    id="e_department_{{ $i }}">
                                    <!-- <option value="">--Department--</option> -->
                                    @if (count($department) > 0)
                                        @foreach ($department as $val)
                                            <option value="{{ $val->name }}">{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select></td>

                        </tr>
                        <?php $i++; ?>
                    @endfor
                    <input type="hidden" value="{{ $i-- }}" id="pr_row_count" />
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
                                    <option value="{{ @$value->vat_country }}">{{ @$value->name }} </option>
                                @endforeach
                            </select></div>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-3">VAT State</div>
                        <div class="col-md-8">
                            <div id="sectionStateDiv"></div>
                            <select class="form-control js-example-basic-single" name="vat_state" id="vat_state"
                                required>
                                <option data-display="" value=""></option>

                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">VAT %</div>
                        <div class="col-md-2"><input class="form-control" type="number" name="vat_percentage"
                                id="vat_percentage" readonly required></div>
                        <div class="col-md-4 mt-2"><input type="checkbox" name="vat_percentage_fixed"
                                id="vat_percentage_fixed" value="1"> Fixed Rate</div>
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
                        <div class="col-md-3">Supplier Type</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single" name="supplier_type" id="supplier_type"
                                required>
                                <option data-display="" value=""></option>
                                @foreach ($supplier_type as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->id == 5) selected @endif>{{ @$value->title }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Purchase Type</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single" name="purchase_type" id="purchase_type"
                                required>
                                <option data-display="" value=""></option>
                                @foreach ($purchase_type as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->id == 6) selected @endif>{{ @$value->title }}
                                    </option>
                                @endforeach
                            </select></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">VAT Number</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="vat_number"></div>
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
                                <option value="Cash" selected>Cash</option>
                                <option value="Credit">Credit</option>
                            </select></div>
                    </div>
                    <div class="row mt-2 credit-fields">
                        <div class="col-md-3">Credit Limit</div>
                        <div class="col-md-8"><input class="form-control format-amount" type="text" name="credit_limit"
                                required></div>
                    </div>
                    <div class="row mt-2 credit-fields">
                        <div class="col-md-3">Credit Days</div>
                        <div class="col-md-8"><input class="form-control" type="number" name="credit_days"
                                required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Payment Terms</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single"
                                name="payment_terms" id="payment_terms">
                                @foreach ($paymentterms as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->id == 3) selected @endif>{{ @$value->title }}</option>
                                @endforeach
                            </select>
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
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="stl-details" role="tabpanel" aria-labelledby="stl-details-tab">

            <div class="row">
                <div class="col-md-6">
                    <div class="row mt-2">
                        <div class="col-md-3">Vendor Name</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="vendor_name"></div>
                    </div>
                    <div class="row mt-2">
                        <label class="col-md-3">Beneficiary Bank Name</label>
                        <div class="col-md-8"><input class="form-control" type="text" name="beneficiary_name">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Account No./ IBAN</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="iban"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Bank Swift Code</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="swift_code"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">City and Country</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="city_country"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row mt-2">
                        <div class="col-md-3">STL</div>
                        <div class="col-md-8">
                            <select class="form-control" name="stl" id="stl" onchange="fn_stl()">
                                <option value="0">Not Applicable</option>
                                <option value="1">Applicable</option>
                            </select>
                        </div>
                    </div>
                    <script>
                        function fn_stl() {
                            if ($('#stl').val() == 1) {
                                $('.stl_div').css('display', '');
                                $('#stl_bank').prop('required', true);
                                $('#stl_dept').prop('required', true);
                                $('#stl_limit').prop('required', true);
                                $('#stl_per_trn_limit').prop('required', true);
                                $('#stl_opb').prop('required', true);
                            } else {
                                $('.stl_div').css('display', 'none');
                                $('#stl_bank').prop('required', false);
                                $('#stl_dept').prop('required', false);
                                $('#stl_limit').prop('required', false);
                                $('#stl_per_trn_limit').prop('required', false);
                                $('#stl_opb').prop('required', false);
                            }
                        }
                    </script>
                    <div class="row mt-2 stl_div" style="display: none;">
                        <div class="col-md-3">Bank</div>
                        <div class="col-md-8">
                            <select class="form-control js-example-basic-single" type="text" name="stl_bank[]"
                                id="stl_bank" multiple onchange="generateFields()">
                                <option value="">Select</option>
                                @if (count($stl_bank) > 0)
                                    @foreach ($stl_bank as $s)
                                        <option value="{{ $s->id }}" data-name="{{ $s->account_name }}">
                                            {{ $s->account_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2 stl_div" style="display: none;" id="stl_dept_div">
                        <div class="col-md-3">STL Department</div>
                        <div class="col-md-8" id="stl_dept_container"></div>
                    </div>

                    <div class="row mt-2 stl_div" style="display: none;" id="stl_limit_div">
                        <div class="col-md-3">STL Limit</div>
                        <div class="col-md-8" id="stl_limit_container"></div>
                    </div>

                    <div class="row mt-2 stl_div" style="display: none;" id="stl_per_trn_limit_div">
                        <div class="col-md-3">Per Transaction Limit</div>
                        <div class="col-md-8" id="stl_per_trn_limit_container"></div>
                    </div>

                    <div class="row mt-2 stl_div" style="display: none;" id="stl_opb_div">
                        <div class="col-md-3">Opening Balance</div>
                        <div class="col-md-8" id="stl_opb_container"></div>
                    </div>

                    <script>
                        function generateFields() {
                            // Get selected bank IDs and their names
                            const selectedBanks = Array.from(document.getElementById('stl_bank').selectedOptions).map(option => ({
                                id: option.value,
                                name: option.getAttribute('data-name')
                            }));

                            // Show/hide divs based on selection
                            const fieldsToDisplay = selectedBanks.length > 0;
                            document.getElementById('stl_dept_div').style.display = fieldsToDisplay ? '' : 'none';
                            document.getElementById('stl_limit_div').style.display = fieldsToDisplay ? '' : 'none';
                            document.getElementById('stl_per_trn_limit_div').style.display = fieldsToDisplay ? '' : 'none';
                            document.getElementById('stl_opb_div').style.display = fieldsToDisplay ? '' : 'none';

                            // Clear existing inputs
                            document.getElementById('stl_dept_container').innerHTML = '';
                            document.getElementById('stl_limit_container').innerHTML = '';
                            document.getElementById('stl_per_trn_limit_container').innerHTML = '';
                            document.getElementById('stl_opb_container').innerHTML = '';

                            // Create input fields for each selected bank
                            selectedBanks.forEach((bank, index) => {
                                // Create STL Department input
                                const deptInput = document.createElement('input');
                                deptInput.type = 'text';
                                deptInput.name = `stl_dept[${bank.id}]`;
                                deptInput.classList.add('form-control');
                                deptInput.id = `stl_dept_${bank.id}`;
                                deptInput.placeholder = `STL Department for ${bank.name}`;
                                document.getElementById('stl_dept_container').appendChild(deptInput);

                                // Create STL Limit input
                                const limitInput = document.createElement('input');
                                limitInput.type = 'text';
                                limitInput.name = `stl_limit[${bank.id}]`;
                                limitInput.classList.add('form-control');
                                limitInput.id = `stl_limit_${bank.id}`;
                                limitInput.placeholder = `STL Limit for ${bank.name}`;
                                limitInput.onchange = fn_stl_limit; // Add any function you want to call on change
                                document.getElementById('stl_limit_container').appendChild(limitInput);

                                // Create Per Transaction Limit input
                                const perTrnLimitInput = document.createElement('input');
                                perTrnLimitInput.type = 'text';
                                perTrnLimitInput.name = `stl_per_trn_limit[${bank.id}]`;
                                perTrnLimitInput.classList.add('form-control');
                                perTrnLimitInput.id = `stl_per_trn_limit_${bank.id}`;
                                perTrnLimitInput.placeholder = `Per Transaction Limit for ${bank.name}`;
                                perTrnLimitInput.onchange = fn_stl_per_trn_limit; // Add any function you want to call on change
                                document.getElementById('stl_per_trn_limit_container').appendChild(perTrnLimitInput);

                                // Create Opening Balance input
                                const opbInput = document.createElement('input');
                                opbInput.type = 'text';
                                opbInput.name = `stl_opb[${bank.id}]`;
                                opbInput.classList.add('form-control');
                                opbInput.id = `stl_opb_${bank.id}`;
                                opbInput.placeholder = `Opening Balance for ${bank.name}`;
                                opbInput.onchange = fn_stl_opb; // Add any function you want to call on change
                                document.getElementById('stl_opb_container').appendChild(opbInput);
                            });
                        }

                        // Example placeholder functions for change event
                        function fn_stl_limit() {
                            // Your logic for STL Limit change
                            console.log('STL Limit changed');
                        }

                        function fn_stl_per_trn_limit() {
                            // Your logic for Per Transaction Limit change
                            console.log('Per Transaction Limit changed');
                        }

                        function fn_stl_opb() {
                            // Your logic for Opening Balance change
                            console.log('Opening Balance changed');
                        }
                    </script>

                    <script>
                        function fn_stl_limit() {
                            $('#stl_limit').val(formatAmount($('#stl_limit').val()));
                        }

                        function fn_stl_per_trn_limit() {
                            $('#stl_per_trn_limit').val(formatAmount($('#stl_per_trn_limit').val()));
                        }

                        function fn_stl_opb() {
                            $('#stl_opb').val(formatAmount($('#stl_opb').val()));
                        }
                    </script>
                </div>
            </div>

        </div>

        <div class="tab-pane fade" id="document-details" role="tabpanel" aria-labelledby="document-details-tab">

            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-light float-end text-dark" style="cursor: pointer;" onclick="add_doc_row()"><i
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

            <div class="row pb-2">
                <div class="col-md-3">
                    <input class="form-control" type="text" name="doc_name[]"
                        value="Trade License/Commercial Registration" readonly />
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="file" name="customer_documents_1" />
                </div>
                <div class="col-md-3">
                    <input class="form-control date-picker" type="text" name="doc_exp_date[]"
                        placeholder="Expiry Date" />
                </div>
                <div class="col-md-3">&nbsp;</div>
            </div>
            <div class="row pb-2">
                <div class="col-md-3">
                    <input class="form-control" type="text" name="doc_name[]" value="VAT Certificate" readonly />
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="file" name="customer_documents_2" />
                </div>
                <div class="col-md-3">&nbsp;</div>
            </div>

            @for ($i = 3; $i <= 10; $i++)
                <div class="row pb-2" id="d_{{ $i }}"
                    @if ($i > 3) style="display:none;" @endif>
                    <div class="col-md-3">
                        <input class="form-control" type="text" name="doc_name[]" value="Other Documents" />
                    </div>
                    <div class="col-md-3">
                        <input class="form-control" type="file" name="customer_documents_{{ $i }}" />
                    </div>
                    <div class="col-md-3">&nbsp;</div>
                </div>
            @endfor
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
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                        <div class="row">
                            <div class="col-md-3">Address Type</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    id="address_type_n">
                                    <option value="0">Supplier Address</option>
                                    <option value="1">Warehouse Address</option>
                                </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Country</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    id="country_n">
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
                                    <select class="form-control js-example-basic-single" id="state_n">
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
                            <div class="col-md-8"><input class="form-control" type="text" id="city_n"
                                    placeholder=""></div>
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
                            <div class="col-md-8"><input class="form-control" type="text" id="address_n"
                                    placeholder=""></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address2_n"
                                    placeholder=""></div>
                        </div> --}}


                        <div class="row mt-2">
                            <div class="col-md-3">PO Box</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="zip_code_n"
                                    placeholder=""></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Set Default</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single"
                                    id="set_default_n">
                                    <option value="0">None</option>
                                    <option value="1">Default Supplier Address</option>
                                    <option value="1">Default Warehouse Address</option>
                                </select></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btn_add_address" onclick="add_address()" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                </button>
            </div>
        </div>





    </div>
</div>

<script>
    function add_address() {
        if ($("#country_n").val() == "") {
            $("#country_n").focus();
            return false;
        }
        // if ($("#address_n").val() == "") {
        //     $("#address_n").focus();
        //     return false;
        // }
        // if ($("#address2_n").val() == "") {
        //     $("#address2_n").focus();
        //     return false;
        // }
        if ($("#city_n").val() == "") {
            $("#city_n").focus();
            return false;
        }
        if ($("#state_n").val() == "") {
            $("#state_n").focus();
            return false;
        }

        if ($("#area_n").val() == "") {
            $("#area_n").focus();
            return false;
        }

        if ($("#building_name_n").val() == "") {
            $("#building_name_n").focus();
            return false;
        }

        if ($("#flat_office_shop_no_n").val() == "") {
            $("#flat_office_shop_no_n").focus();
            return false;
        }

        $("#loading_bg").css("display", "block");
        var address_type_n = $("#address_type_n").val();
        var country_n = $("#country_n").val();
        var area_n = $("#area_n").val();
        var building_name_n = $("#building_name_n").val();
        var flat_office_shop_no_n = $("#flat_office_shop_no_n").val();
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
                area: area_n,
                building_name: building_name_n,
                flat_office_shop_no: flat_office_shop_no_n,
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
                        var area = dataResult['data'][i].area;
                        var building_name = dataResult['data'][i].building_name;
                        var flat_office_shop_no = dataResult['data'][i].flat_office_no;
                        var city = dataResult['data'][i].city;
                        var state = dataResult['data'][i].s_name;
                        var zip_code = dataResult['data'][i].zip_code;

                        var address_type = dataResult['data'][i].is_shipping == 1 ?
                            "Warehouse Address" :
                            "Supplier Address";

                        var innerHtml =
                            "<div class='col-md-4 col-lg-3'>" +
                            "<div class='card border h-100'>" +
                            "<div class='card-body p-3'>" +

                            "<a class='text-danger float-end' onclick='del_address(" + id + ")'>" +
                            "<i class='ico icon-bold-trash-bin-2'></i>" +
                            "</a>" +

                            "<p class='fw-bold mb-3'>" + address_type + "</p>" +

                            "<table class='table table-sm table-borderless mb-0 small'>" +
                            "<tr><th>Country</th><td>" + country + "</td></tr>" +
                            "<tr><th>State</th><td>" + state + "</td></tr>" +
                            "<tr><th>City</th><td>" + city + "</td></tr>" +
                            "<tr><th>Area</th><td>" + area + "</td></tr>" +
                            "<tr><th>Building</th><td>" + building_name + "</td></tr>" +
                            "<tr><th>Flat/Office</th><td>" + flat_office_shop_no + "</td></tr>" +
                            "<tr><th>PO Box</th><td>" + zip_code + "</td></tr>" +
                            "</table>" +

                            "</div>" +
                            "</div>" +
                            "</div>";


                        // var innerHtml =
                        //     "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-right' onclick='del_address(" +
                        //     id +
                        //     ")'><i class='ico icon-bold-trash-bin-2' aria-hidden='true'></i></a>Country : " +
                        //     country + "<br />Address : " + address + "<br />Address2 : " + address2 +
                        //     "<br />City : " + city + "<br />State : " + state + "<br />PO Box : " +
                        //     zip_code + "</p></div>";
                        $("#address_div").append(innerHtml);
                    }

                    toastr.success('Address Added Successfully!!');

                    $('#ModalAddress').modal('hide');

                    // Reset selects
                    $("#address_type_n").val("0").trigger("change");
                    $("#country_n").val("").trigger("change");
                    $("#state_n").val("").trigger("change");

                    // Reset text fields
                    $("#city_n").val("");
                    $("#area_n").val("");
                    $("#building_name_n").val("");
                    $("#flat_office_shop_no_n").val("");
                    $("#zip_code_n").val("");

                    // Reset default option
                    $("#set_default_n").val("0").trigger("change");


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
                        var area = dataResult['data'][i].area;
                        var building_name = dataResult['data'][i].building_name;
                        var flat_office_shop_no = dataResult['data'][i].flat_office_no;
                        // var address = dataResult['data'][i].address;
                        // var address2 = dataResult['data'][i].address2;
                        var city = dataResult['data'][i].city;
                        var state = dataResult['data'][i].s_name;
                        var zip_code = dataResult['data'][i].zip_code;
                        var address_type = dataResult['data'][i].is_shipping == 1 ?
                            "Warehouse Address" :
                            "Supplier Address";
                        var innerHtml =
                            "<div class='col-md-4 col-lg-3'>" +
                            "<div class='card border h-100'>" +
                            "<div class='card-body p-3'>" +

                            "<a class='text-danger float-end' onclick='del_address(" + id + ")'>" +
                            "<i class='ico icon-bold-trash-bin-2'></i>" +
                            "</a>" +

                            "<p class='fw-bold mb-3'>" + address_type + "</p>" +

                            "<table class='table table-sm table-borderless mb-0 small'>" +
                            "<tr><th>Country</th><td>" + country + "</td></tr>" +
                            "<tr><th>State</th><td>" + state + "</td></tr>" +
                            "<tr><th>City</th><td>" + city + "</td></tr>" +
                            "<tr><th>Area</th><td>" + area + "</td></tr>" +
                            "<tr><th>Building</th><td>" + building_name + "</td></tr>" +
                            "<tr><th>Flat/Office</th><td>" + flat_office_shop_no + "</td></tr>" +
                            "<tr><th>PO Box</th><td>" + zip_code + "</td></tr>" +
                            "</table>" +

                            "</div>" +
                            "</div>" +
                            "</div>";
                        $("#address_div").append(innerHtml);
                    }
                    toastr.success('Address Deleted Successfully!!');
                } else {
                    $("#address_div").empty();
                }
                $("#loading_bg").css("display", "none");
            }
        });
    }
</script>





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


        $(document).on("input", "#supplier_mobile", function() {

            let value = $(this).val();

            // Optional: normalize spaces
            value = value.replace(/\s+/g, ' ').trimStart();

            // Set value word-by-word (live)
            $("#e_mobile_1").val(value);

            console.log("Mobile typing:", value);
        });

        $(document).on("input", "#supplier_work", function() {

            let value = $(this).val();

            // Optional: normalize spaces
            value = value.replace(/\s+/g, ' ').trimStart();

            // Set value word-by-word (live)
            $("#e_work_phone_1").val(value);

            console.log("Mobile typing:", value);
        });


        // Live capitalize each word on inputs with .capitalize-words
        $(document).on("input", ".capitalize-words", function() {
            let val = $(this).val();
            val = val.replace(/\b\w/g, char => char.toUpperCase());
            $(this).val(val);
        });

        // Prevent Enter from submitting the form; focus next input instead
        $(document).on('keydown',
            '#supplierForm input, #supplierForm select, #supplierForm button',
            function(e) {
                if (e.key === 'Enter') {
                    var tag = e.target.tagName.toLowerCase();
                    if (tag === 'textarea') return; // allow newlines in textareas
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


<script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize form validation for crm-deals-form
        FormValidator.init('supplierForm', {
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
                $('.credit-fields input').prop('required', false).val('');
            } else if (type === 'Credit') {
                $('.credit-fields').show();
                $('.credit-fields input').prop('required', true);
            }
        }

        // Run on page load (edit case)
        toggleCreditFields();

        // Run when user changes option
        $('#transaction_type').on('change', function() {
            toggleCreditFields();
        });
    });
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

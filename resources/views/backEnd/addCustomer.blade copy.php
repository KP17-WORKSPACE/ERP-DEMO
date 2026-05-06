<?php try { ?>






{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'customer-save-form']) }}

<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">


<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            New - {{ @App\SysHelper::get_new_customer_code() }}
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
            </button>



        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">

            <div class="row gap-rows">

                <div class="col-9">
                    <div class="row gap-rows g-2">




                        <div class="col-2">
                            <label for="" class="form-label">Customer Type</label>

                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="account_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1">Reseller</option>
                                    <option value="2">Enduser</option>
                                    <option value="3">Ecommerce</option>
                                </select>
                            </div>

                        </div>



                        {{-- <div class="col-3">
                            <label for="" class="form-label">Company</label>
                            <select class="form-control js-example-basic-single" name="company_access[]"
                                id="company_access" multiple required>
                                @foreach ($company as $value)
                                    <option value="{{ @$value->id }}"
                                        @if (session('logged_session_data.company_id') == @$value->id) selected @endif>
                                        {{ @$value->company_name }}</option>
                                @endforeach
                            </select>
                        </div> --}}



  <style>
        #company_name_add_list ul {
            width: 380px
        }
    </style>

                        <div class="col-4">
                            <label for="" class="form-label">Company Name</label>

                            <input class="form-control" type="text" name="customer_name" id="customer_name"
                                placeholder="Company Name" required>
                            <div id="company_name_add_list">
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#customer_name').keyup(function() {
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
                                    $('#company_name_add_list').on('click', 'li', function() {
                                        $('#customer_name').val($(this).text());
                                        $('#company_name_add_list').fadeOut();
                                    });
                                      // CLICK OUTSIDE → hide list
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#customer_name').length && 
            !$(e.target).closest('#company_name_add_list').length) {
            $('#company_name_add_list').fadeOut();
        }
    });
                                });
                            </script>
                        </div>

                        <div class="col-4">

                            <label for="" class="form-label">Company Display Name</label>

                            <input class="form-control" type="text" name="customer_name_display"
                                id="customer_name_display" placeholder="Supplier Display Name" required>
                        </div>

                        <div class="col-2">

                            <label for="" class="form-label">Company Type</label>
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
                        <div class="col-6">
                            <div class="row">
                                <label class="form-label" >Primary
                                    Contact:</label>

                                <div class="col-md-12">
                                    <div class="row g-0 border rounded overflow-hidden">

                                        <!-- Salutation -->
                                        <div class="col-md-2 border-end">
                                            <div class="form-group">
                                                <select class="form-control select2 rounded-0 border-0 h-100"
                                                    id="salutation" name="customer_salutation">

                                                    <option value="Mr" selected>Mr</option>
                                                    <option value="Mrs">Mrs</option>
                                                    <option value="Miss">Miss</option>
                                                </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>

                                        <!-- First Name -->
                                        <div class="col-md-5 border-end">
                                            <input type="text" class="form-control rounded-0 border-0" id="firstName"
                                                name="first_name" placeholder="First Name">
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control rounded-0 border-0" id="lastName"
                                                name="last_name" placeholder="Last Name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-3">
                            <label class="form-label">Designation:</label>

                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="designation" required>
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






                        <div class="col-3">
                            <label for="" class="form-label">Company Email</label>
                            <input class="form-control" type="text" name="email" placeholder="Email" required>
                        </div>

{{-- 
                        <div class="col-4">
                            <label for="" class="form-label">Sales Persons</label>
                            <select class="form-control js-example-basic-single" name="sales_person[]"
                                id="sales_person" multiple required>
                                <option data-display="" value="">Select</option>
                                @foreach ($staffs as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
<div class="col-6">
    <label class="form-label">Customer Phone</label>

    <div class="row gap-0 g-0">
        <div class="col-6">
               <select class="form-select js-example-basic-single" style="width:30px" name="country_telephone" id="country_telephone" required>
            <option value="" disabled selected>Select Country</option>
            @foreach ($countries as $key => $value)
                <option value="{{ @$value->iso2 }}|{{ @$value->id }}">{{ @$value->name }}</option>
            @endforeach
        </select>

        </div>
        <div class="col-6 pl-0">
        <input class="form-control" type="text" name="mobile_code" id="mobile_code" placeholder="Customer Phone" required>

        </div>
    </div>
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
        var currentNumber = $('#mobile_code').val().replace(/^\+\d+\s?/, ''); // remove previous code
        var currentNumber = $('#company_mobile_phone').val().replace(/^\+\d+\s?/, ''); // remove previous code
        
        $('#mobile_code').val(code ? '+' + code + ' ' + currentNumber : currentNumber);
        $('#company_mobile_phone').val(code ? '+' + code + ' ' + currentNumber : currentNumber);

            if(code) {
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
                            <label for="" class="form-label">Customer Mobile</label>
                            <input class="form-control" type="text" id="company_mobile_phone" name="mobile" placeholder="Mobile">
                        </div>


                        <div class="col-3">
                            <label for="" class="form-label">Internal Customer</label>
                            <div class="form-group">
                                <select class="form-control js-example-basic-single" name="internal">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                             
                                
                            </div>
                        </div>






                    </div>

                </div>
                <div class="col-3">
                    <div class="row gap-rows">




                    


                        <div class="col-12">
                            <label for="" class="form-label">Company</label>
                            <select class="form-control js-example-basic-single" name="company_access[]"
                                id="company_access" multiple required>
                                @foreach ($company as $value)
                                    <option value="{{ @$value->id }}"
                                        @if (session('logged_session_data.company_id') == @$value->id) selected @endif>
                                        {{ @$value->company_name }}</option>
                                @endforeach
                            </select>
                        </div>











                 


                        <div class="col-12">
                            <label for="" class="form-label">Sales Persons</label>
                            <select class="form-control js-example-basic-single" name="sales_person[]"
                                id="sales_person" multiple required>
                                <option data-display="" value="">Select</option>
                                @foreach ($staffs as $value)
                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}</option>
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
                            $(document).ready(function () {

    // When normal country changes → update VAT country + load states
    $("#country").on("change", function () {
        
        let countryId = $(this).val();

        console.log(countryId)

        // Set VAT Country same as normal country
        $("#country_vat").val(countryId).trigger('change'); 
        


    });

    // When normal state changes → update VAT state
    $("#state").on("change", function () {
        let stateId = $(this).val();
        $("#vat_state").val(stateId).trigger("change");
    });

   

});

                        </script>

                <div class="col-md-6">
                    <p><b>Billing Address</b></p>
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
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Area</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="area"
                                placeholder="" required></div>
                    </div>
                     <div class="row mt-2">
                        <div class="col-md-3">Building Name</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="building_name"
                                placeholder="" required></div>
                    </div>
                      <div class="row mt-2">
                        <div class="col-md-3">Flat/Office No</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="flat_office_shop_no"
                                placeholder="" required></div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">Address 1</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Address 2</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address2"
                                placeholder="" required></div>
                    </div>
                   
                   
                    <div class="row mt-2">
                        <div class="col-md-3">Po Box</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="zip_code"
                                placeholder="">
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
                                    $('[name=address_ship]').val($('[name=address]').val());
                                    $('[name=address2_ship]').val($('[name=address2]').val());
                                    $('[name=city_ship]').val($('[name=city]').val());
                                    $('#select2-country_ship-container').html($('#country option:selected').text());
                                    $('#state_ship').append(new Option($('#state option:selected').text(), '0', true, true));
                                    $('[name=zip_code_ship]').val($('[name=zip_code]').val());
                                    $('#country_ship').removeAttr('required');
                                    $('#address_ship').removeAttr('required');
                                    $('#address2_ship').removeAttr('required');
                                    $('#city_ship').removeAttr('required');
                                    $('#state_ship').removeAttr('required');
                                    $('#zip_code_ship').removeAttr('required');
                                }
                                if (!this.checked) {
                                    $('[name=address_ship]').val('');
                                    $('[name=address2_ship]').val('');
                                    $('[name=city_ship]').val('');
                                    $('[name=country_ship]').val('');
                                    $('[name=state_ship]').val('');
                                    $('[name=zip_code_ship]').val('');
                                    $('#country_ship').attr('required');
                                    $('#address_ship').attr('required');
                                    $('#address2_ship').attr('required');
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
                        <div class="col-md-3">Address 1</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address_ship"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Address 2</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="address2_ship"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">City</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="city_ship"
                                placeholder="" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">State</div>
                        <div class="col-md-8">
                            <div id="sectionStateDiv_ship">
                                <select class="form-control js-example-basic-single" name="state_ship" id="state_ship">
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
                        <div class="col-md-3">Po Box</div>
                        <div class="col-md-8"><input class="form-control" type="text" name="zip_code_ship"
                                placeholder=""></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4" id="address_div">
                @if (count($address_cart) > 0)
                    @foreach ($address_cart as $itm)
                        <div class="col-md-3">
                            <p style="border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;"><a
                                    class="text-danger float-end" onclick="del_address({{ $itm->id }})"><i
                                        class="ico icon-bold-trash-bin-2" aria-hidden="true"></i></a>
                                Country : {{ $itm->c_name }}<br />Address : {{ $itm->address }}<br />Address2 :
                                {{ $itm->address2 }}<br />City : {{ $itm->city }}<br />State :
                                {{ $itm->state }}<br />PO Box : {{ $itm->zip_code }}</p>
                        </div>
                    @endforeach
                @endif
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
                        <th>@lang('Work Phone')</th>
                        <th>@lang('Mobile')</th>
                        <th>@lang('Designation')</th>
                        <th>@lang('Department')</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @for ($r = 1; $r <= 5; $r++)
                        <tr id="pr_row_{{ $i }}">
                            <td><select class="form-control js-example-basic-single" name="e_salutation[]"
                                    id="e_salutation_{{ $i }}">
                                    <option value="">-Salutation-</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Miss">Miss</option>
                                </select></td>
                            <td><input type="text" class="form-control" name="e_first_name[]"
                                    id="e_first_name_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_last_name[]"
                                    id="e_last_name_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_email_address[]"
                                    id="e_email_address_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_work_phone[]"
                                    id="e_work_phone_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_mobile[]"
                                    id="e_mobile_{{ $i }}" value="" /></td>
                            <td><select class="form-control js-example-basic-single" name="e_designation[]"
                                    id="e_designation_{{ $i }}">
                                    <option value="">--Designation--</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select></td>
                            <td><select class="form-control js-example-basic-single" name="e_department[]"
                                    id="e_department_{{ $i }}">
                                    <option value="">--Department--</option>
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
                            <select class="form-control js-example-basic-single" name="vat_state"
                                id="vat_state" required>
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
                        <div class="col-md-3">Customer Type</div>
                        <div class="col-md-8"><select class="form-control" name="customer_type" id="customer_type"
                                required>
                                <option data-display="" value=""></option>
                                @foreach ($customer_type as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($value->id == 5) selected @endif>{{ @$value->title }}
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
                                        @if ($value->id == 5) selected @endif>{{ @$value->title }}
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
                    <div class="row mt-2">
                        <div class="col-md-3">Credit Limit</div>
                        <div class="col-md-8"><input class="form-control" type="number" name="credit_limit"
                                required></div>
                    </div>
                    <div class="row mt-2">
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
                                        @if ($value->id == 3) selected @endif>{{ @$value->title }}
                                    </option>
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



        <div class="tab-pane fade" id="document-details" role="tabpanel" aria-labelledby="document-details-tab">

            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-light float-end text-dark" style="cursor: pointer;" onclick="add_doc_row()"><i
                            class="ico icon-outline-add-square text-success" style="font-size:15px" aria-hidden="true"></i> Add More</a>
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
                       <input class="form-control date-picker" type="text" name="doc_exp_date[]" placeholder="Expiry Date"
                         />
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



</div>

{{ Form::close() }}




<div class="modal fade" id="ModalAddress" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">

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
                                    <option value="0">Billing Address</option>
                                    <option value="1">Shipping Address</option>
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
                            <div class="col-md-3">Address 1</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address_n"
                                    placeholder=""></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address2_n"
                                    placeholder=""></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">City</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="city_n"
                                    placeholder=""></div>
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
                            <div class="col-md-3">PO Box</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="zip_code_n"
                                    placeholder=""></div>
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

        var action = "{{ URL::to('add-customer-script') }}";
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
                            "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-end' onclick='del_address(" +
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

        var action = "{{ URL::to('delete-customer-script') }}";
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
                            "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-end' onclick='del_address(" +
                            id +
                            ")'><i class='ico icon-bold-trash-bin-2' aria-hidden='true'></i></a>Country : " +
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


  <script>
$(document).on('click', '#customer-save-form button[type="submit"], #customer-save-form input[type="submit"]', function (e) {
    e.preventDefault();

    var $btn = $(this);
    var $form = $btn.closest('form');
    var hasError = false;
    var missingFields = [];

    // Helper to mark invalid element (and undo)
    function markInvalid($el, label) {
        hasError = true;
        missingFields.push(label);
        $el.addClass('is-invalid');
        // For Select2 field: highlight selection box
        if ($el.is('select') && $el.next('.select2').length) {
            $el.next('.select2').find('.select2-selection').css('border', '1px solid red');
        }
    }
    function clearInvalid($el) {
        $el.removeClass('is-invalid');
        if ($el.is('select') && $el.next('.select2').length) {
            $el.next('.select2').find('.select2-selection').css('border', '');
        }
    }

    // Validate normal inputs/selects/textareas with required attribute
    $form.find('input[required], textarea[required], select[required]').each(function () {
        var $el = $(this);
        var val = $el.val();

        // Normalize arrays (multi-select) and strings
        var empty = false;
        if (Array.isArray(val)) {
            empty = val.length === 0;
        } else if (val === null || typeof val === 'undefined') {
            empty = true;
        } else if (String(val).trim() === '') {
            empty = true;
        }

        // Readable label (prefer data-label, then placeholder, then name)
        var label = $el.data('label') || $el.attr('placeholder') || $el.attr('aria-label') || $el.attr('name') || 'Field';
        label = String(label).replace(/_/g, ' ');

        if (empty) {
            markInvalid($el, label);
        } else {
            clearInvalid($el);
        }
    });

        if (hasError) {
            var $first = $form.find('.is-invalid').first();
            if ($first.length) {
                if ($first.is('select') && $first.next('.select2').length) {
                    $first.next('.select2').find('.select2-selection').focus();
                } else {
                    $first.focus();
                }
                $('html, body').animate({
                    scrollTop: Math.max(0, $first.offset().top - 120)
                }, 250);
            }

            // Create bullet list of missing fields
            var listHtml = "<ul style='margin-left:15px; text-align:left;'>";
            missingFields.forEach(function (field) {
                listHtml += "<li>" + field + "</li>";
            });
            listHtml += "</ul>";

            toastr.error(
                'Please fill required fields:' + listHtml,
                'Validation Error',
                { timeOut: 4000 }
            );

            return false;
        }


    // All good -> submit form using native submit to avoid re-triggering handlers
    // Disable button to prevent double submit
    $btn.prop('disabled', true);
    $form[0].submit();
});
</script>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

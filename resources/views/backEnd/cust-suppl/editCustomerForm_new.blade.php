<?php try { ?>







<div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
    <div class="purchase-order-content-header d-flex align-items-center justify-content-between mb-1 gap-2">
        <h4 class="purchase-order-content-header-left">
            Edit
        </h4>
        <div class="purchase-order-content-header-right d-flex align-items-center justify-content-end gap-2">


            <form method="GET" action="{{ url('customers?customer_action=add') }}">
                <button type="submit" name="customer_action" value="add" class="btn btn-light">
                    <i class="ico icon-outline-add-square text-success"></i> Add
                </button>
            </form>


            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-form-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}


            <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="catid" id="catid" value="2">

            <button type="submit" name="btnSubmit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-opened text-warning"></i> Approve
            </button>



        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">

            <div class="row">
                <div class="col-9 "style="border-right: 1px solid #ccc;">
                    <div class="row g-2">
                        <div class="col-2">
                            <label for="" class="form-label">Customer Type</label>

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
                            <label for="" class="form-label">Company Name</label>
                            <input class="form-control" type="text" name="customer_name" id="customer_name"
                                placeholder="Company Name" value="{{ isset($editData) ? @$editData->name : '' }}"
                                required>

                        </div>

                        <div class="col-4">
                            <label for="" class="form-label">Customer Display Name</label>
                            <input class="form-control" type="text" name="customer_name_display"
                                id="customer_name_display" placeholder="Customer Display Name"
                                value="{{ isset($editData) ? @$editData->customer_name_display : '' }}" required>
                        </div>


                        <div class="col-2">
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

                        <div class="col-6">
                            <div class="row">
                                <label class="form-label mb-0 me-3" style="min-width: 120px;">Primary
                                    Contact:</label>

                                <div class="col-md-12">
                                    <div class="row g-0 border rounded overflow-hidden">

                                        <!-- Salutation -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select class="form-control select2 rounded-0 border-0 h-100"
                                                    id="salutation" name="customer_salutation">

                                                    <option value="Mr"
                                                        @if ($editData->customer_salutation == 'Mr') selected @endif>Mr
                                                    </option>
                                                    <option value="Mrs"
                                                        @if ($editData->customer_salutation == 'Mrs') selected @endif>Mrs
                                                    </option>
                                                    <option value="Miss"
                                                        @if ($editData->customer_salutation == 'Miss') selected @endif>
                                                        Miss</option>
                                                </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>

                                        <!-- First Name -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control rounded-0 border-0" id="firstName"
                                                name="first_name" placeholder="First Name"
                                                value="{{ isset($editData) ? @$editData->first_name : '' }}">
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-md-5">
                                            <input type="text" class="form-control rounded-0 border-0" id="lastName"
                                                name="last_name" placeholder="Last Name"
                                                value="{{ isset($editData) ? @$editData->last_name : '' }}">
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
                                            <option value="{{ $val->title }}"
                                                @if (strtolower(trim($editData->designation)) == strtolower(trim($val->title))) selected @endif>{{ $val->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
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
                            <label for="" class="form-label">Customer Email</label>
                            <input class="form-control" type="text" name="email" placeholder="Email"
                                value="{{ $editData->email }}" required>
                        </div>


                        <div class="col-3">
                            <label for="" class="form-check-label">Customer Phone</label>
                            <input class="form-control" type="text" name="mobile_code" placeholder="Work Phone"
                                value="{{ $editData->contcat_number }}" required>
                        </div>


                        <div class="col-3">
                            <label for="" class="form-check-label">Customer Mobile</label>
                            <input class="form-control" type="text" name="mobile" placeholder="Mobile"
                                value="{{ $editData->mobile }}">

                        </div>

                        <div class="col-3">
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

                    </div>

                </div>
                <div class="col-3">
                    <div class="row">
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
                                    <option value="{{ @$value->user_id }}"
                                        @if (isset($editData)) @foreach ($editAssign as $sp)  @if (@$sp->user_id == @$value->user_id) selected @endif
                                        @endforeach
                                @endif>{{ @$value->full_name }}</option>
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

            {{-- <div>
                <a class="float-end" style="cursor: pointer;" data-bs-toggle="modal"
                    data-bs-target="#ModalAddress"><i class="ico icon-bold-book-2"
                        style="font-size: 16px; line-height: 1;"></i> Add More</a>
            </div> --}}

            <div class="row mt-2">
                @if (count($editAddressbook) > 0)
                    @foreach ($editAddressbook as $itm)
                        <div class="col-md-3">
                            <p style="border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;"><a
                                    class="text-danger float-end"
                                    href="{{ url('delete-cust-suppl-address/' . $itm->id) }}"><i
                                        class="ico icon-bold-trash-bin-2" aria-hidden="true"></i></a>
                                <a class="text-success float-end" style="padding-right: 10px"
                                    onclick="edit_popup_data({{ $itm->id }})" style="cursor: pointer;"
                                    data-bs-toggle="modal" data-bs-target="#ModalAddressEdit"><i
                                        class="ico icon-outline-pen-2 text-success" aria-hidden="true"></i></a>
                                Country : {{ $itm->countryname->name }}<br />Address :
                                {{ $itm->address }}<br />Address2 : {{ $itm->address2 }}<br />City :
                                {{ $itm->city }}<br />State : {{ $itm->statename->name }}<br />PO Box :
                                {{ $itm->zip_code }}
                            </p>
                        </div>



                        <input type="hidden" id="country_n_e_{{ $itm->id }}" value="{{ $itm->country }}" />
                        <input type="hidden" id="address_type_n_e{{ $itm->id }}"
                            value="{{ $itm->is_shipping }}" />
                        <input type="hidden" id="address_n_e_{{ $itm->id }}" value="{{ $itm->address }}" />
                        <input type="hidden" id="address2_n_e_{{ $itm->id }}" value="{{ $itm->address2 }}" />
                        <input type="hidden" id="city_n_e_{{ $itm->id }}" value="{{ $itm->city }}" />
                        <input type="hidden" id="state_n_e_{{ $itm->id }}" value="{{ $itm->state }}" />
                        <input type="hidden" id="zip_code_n_e_{{ $itm->id }}" value="{{ $itm->zip_code }}" />
                        <input type="hidden" id="set_default_n_e_{{ $itm->id }}"
                            value="{{ $itm->set_default }}" />
                    @endforeach
                @endif
                <script>
                    function edit_popup_data(id) {
                        $('#cust_suppl_edit_id').val(id);
                        $('#country_n_e').val($('#country_n_e_' + id).val());
                        $('#address_type_n_e').val($('#address_type_n_e' + id).val());
                        $('#address_n_e').val($('#address_n_e_' + id).val());
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
                    @foreach ($editContact as $edt)
                        <tr id="pr_row_{{ $i }}">
                            <td><select class="form-control js-example-basic-single" name="e_salutation[]"
                                    id="e_salutation_{{ $i }}">
                                    <option value="">-Salutation-</option>
                                    <option value="Mr" @if ($edt->salutation == 'Mr') selected @endif>Mr
                                    </option>
                                    <option value="Mrs" @if ($edt->salutation == 'Mrs') selected @endif>Mrs
                                    </option>
                                    <option value="Miss" @if ($edt->salutation == 'Miss') selected @endif>Miss
                                    </option>
                                </select></td>
                            <td><input type="text" class="form-control" name="e_first_name[]"
                                    id="e_first_name_{{ $i }}" value="{{ $edt->first_name }}" /></td>
                            <td><input type="text" class="form-control" name="e_last_name[]"
                                    id="e_last_name_{{ $i }}" value="{{ $edt->last_name }}" /></td>
                            <td><input type="text" class="form-control" name="e_email_address[]"
                                    id="e_email_address_{{ $i }}" value="{{ $edt->email_address }}" />
                            </td>
                            <td><input type="text" class="form-control" name="e_work_phone[]"
                                    id="e_work_phone_{{ $i }}" value="{{ $edt->work_phone }}" /></td>
                            <td><input type="text" class="form-control" name="e_mobile[]"
                                    id="e_mobile_{{ $i }}" value="{{ $edt->mobile }}" /></td>
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
                        </tr>
                        <?php $i++; ?>
                    @endforeach
                    @for ($r = $i; $r <= 5; $r++)
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
                            <td>
                                <select class="form-control js-example-basic-single" name="e_designation[]"
                                    id="e_designation_{{ $i }}">
                                    <option value="">--Designation--</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_department[]"
                                    id="e_department_{{ $i }}">
                                    <option value="">--Designation--</option>
                                    @if (count($department) > 0)
                                        @foreach ($department as $val)
                                            <option value="{{ $val->name }}">{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
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
                                    <option value="{{ @$value->vat_country }}"
                                        @if ($editData->vat_country == $value->vat_country) selected @endif>{{ @$value->name }}
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
                                <option value="Credit" @if ($editData->transaction_type == 'Credit') selected @endif>Credit
                                </option>
                            </select></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Credit Limit</div>
                        <div class="col-md-8"><input class="form-control" type="number"
                                value="{{ $editData->credit_limit }}" name="credit_limit" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Credit Days</div>
                        <div class="col-md-8"><input class="form-control" type="number"
                                value="{{ $editData->credit_days }}" name="credit_days" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">Payment Terms</div>
                        <div class="col-md-8"><select class="form-control js-example-basic-single"
                                name="payment_terms" id="payment_terms">
                                @foreach ($paymentterms as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if ($editData->payment_terms == $value->id) selected @else @if ($value->id == 3) selected @endif
                                        @endif>{{ @$value->title }}</option>
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
                <div class="col-md-6">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        @if (count($editDoc) > 0)
                            @foreach ($editDoc as $doc)
                                <tr>
                                    <td>{{ $doc->doc_name }}</td>
                                    <td>{{ date('d/m/Y', strtotime(@$doc->doc_exp_date)) }}</td>
                                    <td><a class="btn-sm btn-primary text-white"
                                            href="{{ asset('public/uploads/cust-suppl/') }}/{{ $doc->doc_file }}"
                                            target="_blank"> Download</a>
                                        <a class="btn-sm btn-danger border-0 text-white"
                                            href="{{ url('delete-cust-suppl-doc/' . $doc->id) }}">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
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
                            value="@if ($i == 1) Trade License/Commercial Registration @elseif($i == 2) VAT Certificate @else Other Documents @endif"
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




@if (count($excisting_list) > 0)

    <div class="card mb-3">
        <div class="card-body">
            <hr>
            <b>Similer Customer Accounts</b>
            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Work Phone</th>
                    <th></th>
                </tr>
                @foreach ($excisting_list as $list)
                    <tr>
                        <td>{{ $list->code }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ $list->first_name }}</td>
                        <td>{{ $list->mobile }}</td>
                        <td>{{ $list->contcat_number }}</td>
                        <td><a class="btn btn-info pt-0 pb-0"
                                href="{{ url('customer-form-details/' . $list->id . '/merge/' . $editData->id . '') }}"
                                onclick="return confirm('Are you sure you want to merge this item?');">Update
                                with This</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endif




<div class="modal fade" id="ModalAddressEdit" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-customer-form-address', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}


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
                            <div class="col-md-3">Address 1</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address_n_e"
                                    name="address_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address2_n_e"
                                    name="address2_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">City</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="city_n_e"
                                    name="city_n_e" placeholder="" required></div>
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



<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

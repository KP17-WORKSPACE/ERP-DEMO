@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Currency Settings
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('currency-settings') }}">
                            <i class="ico icon-outline-add-square text-success"></i> Add
                        </a>

                         <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('company/policy') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Policy
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('/department') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Department
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/designation') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Designation
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/legal-entity') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Business Entity
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/industry') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Industry Type
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/business-activity') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Business Sector
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('role') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Role
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('module') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Module
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('base_setup') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Base Setup
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('daily-quotes.index') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Daily Quote
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-terms') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Payment Terms
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-cheque-print-template') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Cheque Print Templates
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('shipping-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Shipping
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('vat-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        VAT Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('accountgroup-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Main Heads
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Closed
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close-doc-number') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Close Doc No
                    </a>
                </li>


            </ul>
        </div>

                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if (isset($editmode))
                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'currency-settings/' . @$editmode->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                        @else
                                            @if (in_array(105, @$module_links) || Auth::user()->role_id == 1)
                                                {{ Form::open([
                                                    'class' => 'form-horizontal',
                                                    'files' => true,
                                                    'url' => 'currency-settings',
                                                    'method' => 'POST',
                                                    'enctype' => 'multipart/form-data',
                                                ]) }}
                                            @endif
                                        @endif
                                        <div class="white-box">
                                            <div class="add-visitor">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        @if (session()->has('message-success'))
                                                            <div class="alert alert-success">
                                                                {{ session()->get('message-success') }}
                                                            </div>
                                                        @elseif(session()->has('message-danger'))
                                                            <div class="alert alert-danger">
                                                                {{ session()->get('message-danger') }}
                                                            </div>
                                                        @endif

                                                        <div class="input-effect mb-2">
                                                            <label class="form-label">@lang('Currency Name')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="name"
                                                                autocomplete="off"
                                                                value="{{ isset($editmode) ? @$editmode->name : '' }}">
                                                            <input type="hidden" name="id"
                                                                value="{{ isset($editmode) ? $editmode->id : '' }}">
                                                            <span class="focus-border"></span>
                                                            @if ($errors->has('name'))
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $errors->first('name') }}</strong></span>
                                                            @endif
                                                        </div>

                                                        <div class="input-effect mb-2">
                                                            <label class="form-label">@lang('Code')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="code"
                                                                autocomplete="off"
                                                                value="{{ isset($editmode) ? @$editmode->code : '' }}">
                                                            <span class="focus-border"></span>
                                                            @if ($errors->has('code'))
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $errors->first('code') }}</strong></span>
                                                            @endif
                                                        </div>

                                                        <div class="input-effect mb-2">
                                                            <label class="form-label">@lang('Symbol')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="symbol"
                                                                autocomplete="off"
                                                                value="{{ isset($editmode) ? @$editmode->symbol : '' }}">
                                                            <span class="focus-border"></span>
                                                            @if ($errors->has('symbol'))
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $errors->first('symbol') }}</strong></span>
                                                            @endif
                                                        </div>

                                                        <div class="input-effect mb-2">
                                                            <label class="form-label">@lang('Rate')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="rate"
                                                                autocomplete="off"
                                                                value="{{ isset($editmode) ? @$editmode->rate : '' }}">
                                                            <span class="focus-border"></span>
                                                            @if ($errors->has('rate'))
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $errors->first('rate') }}</strong></span>
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                                @php
                                                    $tooltip = '';
                                                    if (in_array(105, $module_links) || Auth::user()->role_id == 1) {
                                                        $tooltip = '';
                                                    } else {
                                                        $tooltip = 'You have no permission to add';
                                                    }
                                                @endphp
                                                <div class="row mt-40">
                                                    <div class="col-lg-12">
                                                        <button class="btn btn-light" type="submit" id="btnSubmit">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            {{ isset($editmode) ? 'Update' : 'Save' }} @lang('Payment Terms')
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-9">

                                <div class="row">
                                    <div class="col-lg-12">

                                        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">

                                            <thead>
                                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                                    <tr>
                                                        <td colspan="2">
                                                            @if (session()->has('message-success-delete'))
                                                                <div class="alert alert-success">
                                                                    {{ session()->get('message-success-delete') }}
                                                                </div>
                                                            @elseif(session()->has('message-danger-delete'))
                                                                <div class="alert alert-danger">
                                                                    {{ session()->get('message-danger-delete') }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>@lang('Currency Name')</th>
                                                    <th>@lang('Code')</th>
                                                    <th>@lang('Symbol')</th>
                                                    <th>@lang('Rate')</th>
                                                    <th>@lang('Created By')</th>
                                                    <th class="text-center">@lang('lang.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($currencysettings as $cr)
                                                    <tr>
                                                        <td>{{ @$cr->name }}</td>
                                                        <td>{{ @$cr->code }}</td>
                                                        <td>{{ @$cr->symbol }}</td>
                                                        <td>
                                                            <a class=""
                                                                onclick="currency_popup({{ $cr->id }},'{{ $cr->code }}','{{ $cr->name }}')">Conversion
                                                                Rate</a>
                                                            {{-- {{@$cr->rate}} --}}
                                                        </td>
                                                        <td>{{ @$cr->createdby->full_name }}</td>
                                                        <td>
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <a class="btn btn-sm btn-light text-dark"
                                                                    href="{{ url('currency-settings', [@$cr->id]) }}"> <i
                                                                        class="ico icon-outline-pen-2 text-success"
                                                                        style="font-size: 16px;"></i> @lang('lang.edit')</a>
                                                                <a class="btn btn-sm btn-light text-dark"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#deleteDesignationModal{{ @$cr->id }}"
                                                                    href="#"> <i
                                                                        class="ico icon-bold-trash-bin-2 text-success"
                                                                        style="font-size: 16px;"></i> @lang('lang.delete')</a>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                    <div class="modal side-panel fade"
                                                        id="deleteDesignationModal{{ @$cr->id }}"
                                                        data-bs-backdrop="false" tabindex="-1"
                                                        aria-labelledby="editModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-sm">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Delete Currency</h4>
                                                                    <button type="button"
                                                                        id="ModalPaymentAdjustmentClose" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body text-center py-4">
                                                                    <div class="mb-3">
                                                                        <i class="ico icon-bold-trash-bin-2 text-danger"
                                                                            style="font-size: 40px;"></i>
                                                                    </div>
                                                                    <h5 class="fw-semibold mb-2">@lang('lang.are_you_sure_to_delete')</h5>
                                                                    <p class="text-muted small mb-0">This action cannot be
                                                                        undone.
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer">

                                                                    <div class="mt-2 text-center">

                                                                        {{ Form::open(['url' => 'payment-terms/' . @$cr->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                                                        <button type="submit"
                                                                            class="btn btn-light add-btn ms-2">
                                                                            <i
                                                                                class="ico icon-outline-trash-bin-minimalistic text-danger"></i>
                                                                            Delete
                                                                        </button>
                                                                        {{ Form::close() }}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <script>
                                            function currency_popup(id, code, name) {
                                                $('#from_currency').val(id);
                                                $('#lbl_code').text(code);
                                                $('#lbl_name').text(name);
                                                $('#currencyAdd').click();
                                                view_rate();

                                            }

                                            function edit_rate(id) {
                                                $('#c_update_btn').css('display', '');
                                                $('#c_add_btn').css('display', 'none');
                                                $('#from_currency').val($('#from_currency_' + id).val());
                                                $('#to_currency').val($('#to_currency_' + id).val());

                                                from_date = $('#from_date_' + id).val();

                                                

                                                $('#from_date').val(from_date ? from_date.split('-').reverse().join('/') : '');
                                                $('#rate').val($('#rate_' + id).val());
                                                $('#rate_id').val(id);
                                            }
                                            
                                        </script>

                                        <button class="text-primary" id="currencyAdd" data-bs-toggle="modal"
                                            data-bs-target="#currencyModal" hidden>Conversion Rate</button>
                                        <div class="modal side-panel fade" id="currencyModal" data-bs-backdrop="false"
                                            tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" style="max-width: 35rem;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="exampleModalLabel">From Currency -
                                                            <label id="lbl_name"></label> (<label
                                                                id="lbl_code"></label>)
                                                        </h4>
                                                        <button type="button" id="ModalPaymentAdjustmentClose"
                                                            class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body m-0 pt-3 ps-3">
                                                        <input type="hidden" id="from_currency" name="from_currency"
                                                            value="" />
                                                        <input type="hidden" id="rate_id" name="rate_id"
                                                            value="" />
                                                        
                                                            <div class="row">
                                                                <div class="col-lg-4 mb-20">
                                                                    <div class="input-effect">
                                                                        <label class="dynamicslbl"> @lang('To Currancy')
                                                                            <span>*</span> </label>
                                                                        <select class="form-control js-example-basic-single" name="to_currency"
                                                                            id="to_currency" required>
                                                                            <option value=""></option>
                                                                            @foreach ($currencysettings as $value)
                                                                                <option value="{{ @$value->id }}">
                                                                                    {{ @$value->code }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-20">
                                                                    <div class="input-effect">
                                                                        <label class="dynamicslbl"> @lang('Rate')
                                                                            <span>*</span> </label>
                                                                        <input class="form-control" type="rate"
                                                                            id="rate" name="rate"
                                                                            value="" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 mb-20">
                                                                    <div class="input-effect">
                                                                        <label class="dynamicslbl"> @lang('From Date')
                                                                            <span>*</span> </label>
                                                                        <input class="form-control date-picker" type="text" 
                                                                            id="from_date" name="from_date"
                                                                            value="{{ date('d/m/Y') }}" />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12 mt-2">
                                                                    <table id="att-table" class="table table-hover"
                                                                        width="100%" cellspacing="0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 30%;" class="text-start">To Currency</th>
                                                                                <th style="width: 20%;" class="">Rate</th>
                                                                                <th style="width: 30%;" class="text-center">Date</th>
                                                                                <th style="width: 20%;" class="text-center">Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>

                                                            <br />
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-light" type="button" id="c_add_btn"
                                                            onclick="add_rate()">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            Add Rate
                                                        </button>

                                                        <button class="btn btn-light" type="button" id="c_update_btn"
                                                            style="display: none;" onclick="update_rate()">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            Update Rate
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


    <script>
        function add_rate() {
            if ($('#to_currency').val() === '') {
                alert('To Currency is required');
                $('#to_currency').focus();
                return false;
            }

            if ($('#from_date').val() === '') {
                alert('From Date is required');
                $('#from_date').focus();
                return false;
            }

            if ($('#rate').val() === '') {
                alert('Rate is required');
                $('#rate').focus();
                return false;
            }
            $("#loading_bg").css("display", "block");

            var action = "{{ URL::to('add-currency-rate') }}";

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('from_currency', $('#from_currency').val());
            formData.append('to_currency', $('#to_currency').val());
            formData.append('rate', $('#rate').val());
            formData.append('from_date', $('#from_date').val());


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
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>" + dataResult['data'][i].code + "</td>\
                                    <td class=''>" + dataResult['data'][i].rate + "</td>\
                                    <td class='text-center'>" + get_format_date(dataResult['data'][i].from_date) + "</td>\
                                    <td class='text-center'> <a onclick='edit_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-pen-2 text-dark' style='font-size: 16px;'></i></a>\
                                        <a onclick='delete_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico  icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a>\
                                        <input type='hidden' id='from_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_currency + "'/>\
                                        <input type='hidden' id='to_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].to_currency + "'/>\
                                        <input type='hidden' id='rate_" + dataResult['data'][i].id + "' value='" +
                                dataResult[
                                    'data'][i].rate + "'/>\
                                        <input type='hidden' id='from_date_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_date + "'/>\
                                        </td>\
                                    </tr>";
                        }
                        $('#to_currency').val('');
                        $('#from_date').val('');
                        $('#rate').val('');
                        $('#rate_id').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                    } else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function update_rate() {
            if ($('#to_currency').val() === '') {
                alert('To Currency is required');
                $('#to_currency').focus();
                return false;
            }

            if ($('#from_date').val() === '') {
                alert('From Date is required');
                $('#from_date').focus();
                return false;
            }

            if ($('#rate').val() === '') {
                alert('Rate is required');
                $('#rate').focus();
                return false;
            }
            $("#loading_bg").css("display", "block");

            var action = "{{ URL::to('update-currency-rate') }}";

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('rate_id', $('#rate_id').val());
            formData.append('from_currency', $('#from_currency').val());
            formData.append('to_currency', $('#to_currency').val());
            formData.append('rate', $('#rate').val());
            formData.append('from_date', $('#from_date').val());


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
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>" + dataResult['data'][i].code + "</td>\
                                    <td class=''>" + dataResult['data'][i].rate + "</td>\
                                    <td class='text-center'>" + get_format_date(dataResult['data'][i].from_date) + "</td>\
                                    <td class='text-center'><a onclick='edit_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-pen-2 text-dark' style='font-size: 16px;'></i></a>\
                                        <a onclick='delete_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a>\
                                        <input type='hidden' id='from_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_currency + "'/>\
                                        <input type='hidden' id='to_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].to_currency + "'/>\
                                        <input type='hidden' id='rate_" + dataResult['data'][i].id + "' value='" +
                                dataResult[
                                    'data'][i].rate + "'/>\
                                        <input type='hidden' id='from_date_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_date + "'/>\
                                        </td>\
                                    </tr>";
                        }
                        $('#to_currency').val('');
                        $('#from_date').val('');
                        $('#rate').val('');
                        $('#rate_id').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);

                        $('#c_update_btn').css('display', 'none');
                        $('#c_add_btn').css('display', '');
                    } else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function view_rate() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('view-currency-rate') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    from_currency: $('#from_currency').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>" + dataResult['data'][i].code + "</td>\
                                    <td class=''>" + dataResult['data'][i].rate + "</td>\
                                    <td class='text-center'>" + get_format_date(dataResult['data'][i].from_date) + "</td>\
                                    <td class='text-center'> <a onclick='edit_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-pen-2 text-dark' style='font-size: 16px;'></i></a>\
                                       <a onclick='delete_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico  icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a>\
                                        <input type='hidden' id='from_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_currency + "'/>\
                                        <input type='hidden' id='to_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].to_currency + "'/>\
                                        <input type='hidden' id='rate_" + dataResult['data'][i].id + "' value='" +
                                dataResult[
                                    'data'][i].rate + "'/>\
                                        <input type='hidden' id='from_date_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_date + "'/>\
                                        </td>\
                                    </tr>";
                        }
                        $('#to_currency').val('');
                        $('#from_date').val('');
                        $('#rate').val('');
                        $('#rate_id').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                        $('#c_update_btn').css('display', 'none');
                        $('#c_add_btn').css('display', '');
                    } else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }

        function delete_rate(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('delete-currency-rate') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    from_currency: $('#from_currency').val(),
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var getSelectedRows = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            getSelectedRows += "<tr>\
                                    <td>" + dataResult['data'][i].code + "</td>\
                                    <td class=''>" + dataResult['data'][i].rate + "</td>\
                                    <td class='text-center'>" + get_format_date(dataResult['data'][i].from_date) + "</td>\
                                    <td class='text-center'><a onclick='edit_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico icon-outline-pen-2 text-dark' style='font-size: 16px;'></i></a>\
                                       <a onclick='delete_rate(" + dataResult['data'][i].id + ")' class='btn-sm btn-light'><i class='ico  icon-outline-trash-bin-minimalistic text-dark' style='font-size: 16px;'></i></a>\
                                        <input type='hidden' id='from_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_currency + "'/>\
                                        <input type='hidden' id='to_currency_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].to_currency + "'/>\
                                        <input type='hidden' id='rate_" + dataResult['data'][i].id + "' value='" +
                                dataResult[
                                    'data'][i].rate + "'/>\
                                        <input type='hidden' id='from_date_" + dataResult['data'][i].id + "' value='" +
                                dataResult['data'][i].from_date + "'/>\
                                        </td>\
                                    </tr>";
                        }
                        $('#to_currency').val('');
                        $('#from_date').val('');
                        $('#rate').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows);
                        $('#c_update_btn').css('display', 'none');
                        $('#c_add_btn').css('display', '');
                    } else {
                        $('#att-table tbody').empty();
                    }
                }
            });
            $("#loading_bg").css("display", "none");
        }
    </script>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $("#btnSubmit").click(function() {
                setTimeout(function() {
                    disableButton();
                }, 0);
            });

            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection

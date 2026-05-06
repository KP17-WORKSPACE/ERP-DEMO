@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        VAT Settings
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('vat-settings') }}">
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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('currency-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Manage Currency
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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
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
                            <div class="col-lg-4">
                                @if (isset($editData))
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'vat-settings-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                @else
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'vat-settings-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                @endif

                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                <input type="hidden" name="date_of_joining" id="date_of_joining"
                                    value="{{ date('Y-m-d') }}">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label class="form-label"> @lang('VAT Country') <span>*</span> </label>
                                        <select class="form-control js-example-basic-single" name="country" id="country">
                                            <option data-display="" value=""></option>
                                            @foreach ($countries as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    @if (isset($editData)) @if (@$editData->vat_country == $value->id) selected @endif
                                                    @endif>{{ @$value->name }} </option>
                                            @endforeach
                                        </select>
                                        {{-- <input class="primary-input form-control{{ $errors->has('vat_country') ? ' is-invalid' : '' }}" type="text"  name="vat_country" value="{{isset($editData)?@$editData->vat_country:old('vat_country')}}"> --}}
                                    </div>
                                    <div class="col-lg-8"></div>
                                    <div class="col-lg-12 mt-2" style="display: none;">
                                        <div class="input-effect" id="sectionStateDiv">
                                            <label class="form-label"> @lang('VAT State') <span>*</span> </label>
                                            <select class="form-control js-example-basic-single" name="state"
                                                id="state">
                                                <option data-display="" value=""></option>
                                                @if (isset($editData))
                                                    @foreach ($states as $key => $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if (isset($editData)) @if (@$editData->vat_state == $value->id) selected @endif
                                                            @endif>{{ @$value->name }} </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        {{-- <input class="primary-input form-control{{ $errors->has('vat_state') ? ' is-invalid' : '' }}" type="text"  name="vat_state" value="{{isset($editData)?@$editData->vat_state:old('vat_state')}}"> --}}
                                    </div>
                                    <div class="col-lg-12 mt-2" style="display: none;">
                                        <label class="form-label"> @lang('VAT Type') <span>*</span> </label>
                                        <select class="form-control js-example-basic-single" name="vat_type" id="vat_type">
                                            <option data-display="" value=""></option>
                                            @foreach ($vattype as $key => $value)
                                                <option value="{{ @$value->id }}"
                                                    @if (isset($editData)) @if (@$editData->vat_type == $value->id) selected @endif
                                                    @endif>{{ @$value->type }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-12 mt-2">
                                        <label class="form-label"> @lang('VAT %') <span>*</span> </label>
                                        <input class="form-control" type="number" name="vat_percentage"
                                            value="{{ isset($editData) ? @$editData->vat_percentage : old('vat_percentage') }}">
                                    </div>
                                    <div class="col-lg-8"></div>
                                    <div class="col-lg-12 mt-2">
                                        <label class="form-label"> @lang('VAT Calculate From') <span>*</span> </label>
                                        @php
                                            $value = date('d/m/Y');
                                            if (isset($editData) && !empty($editData->vat_from)) {
                                                @$value = @App\SysHelper::normalizeToYmd(@$editData->vat_from);
                                            }
                                        @endphp
                                        <input class="form-control date-picker" type="text" name="vat_from"
                                            value="{{ $value }}">
                                    </div>
                                    <div class="col-lg-8"></div>
                                    <div class="col-lg-12 mt-2">
                                        <label class="form-label"> @lang('Status') <span>*</span> </label>
                                        <select class="form-control" name="status" id="status">
                                            <option
                                                @if (isset($editData)) @if ($editData->status == 1) selected @endif
                                                @endif value="1">Active</option>
                                            <option
                                                @if (isset($editData)) @if ($editData->status == 2) selected @endif
                                                @endif value="2">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <button class="btn btn-light" type="submit" id="btnSubmit">
                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                            @if (isset($editData))
                                                @lang('Update VAT')
                                            @else
                                                @lang('Save VAT') @endif
                                        </button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            <div class="col-lg-8">
                                <h4 class="primary-color">@lang('VAT List'):</h4>
                                <table class="table table-hover" id="table_id2" style="width:100%;" cellspacing="0">
                                    <thead>
                                        @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                            <tr>
                                                <td colspan="6">
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
                                            <th> @lang('VAT Country')</th>
                                            <th> @lang('VAT %')</th>
                                            <th> @lang('Calculate From')</th>
                                            <th> @lang('Status')</th>
                                            <th class="text-center"> @lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if (isset($vat))
                                            @foreach ($vat as $value)
                                                <tr>
                                                    <td>
                                                        {{ @$value->country->name }}
                                                    </td>
                                                    <td>
                                                        {{ @$value->vat_percentage }}
                                                    </td>
                                                    <td>
                                                        {{ date('d/m/Y', strtotime(@$value->vat_from)) }}
                                                    </td>
                                                    <td>
                                                        @if (@$value->status == 1)
                                                            <span class="text-success font-weight-bold">Active</span>
                                                        @else
                                                            <span class="text-danger font-weight-bold">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a class="btn btn-sm btn-light"
                                                                href="{{ url('vat-settings/' . @$value->id . '/edit') }}">
                                                                <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i></a>
                                                            <a class="btn btn-sm btn-light" title="Delete VAT"
                                                                href="{{ url('vat-settings/' . @$value->id . '/delete') }}"
                                                                onclick="return confirm('Are you sure you want to delete this?')">
                                                                <i style="font-size: 16px" class="ico icon-outline-trash-bin-minimalistic text-danger"></i> </a>
                                                            <a class="btn btn-sm btn-light"
                                                                title="Apply To Excisting Customers & Suppliers"
                                                                href="{{ url('vat-settings/' . @$value->id . '/apply') }}">
                                                                @lang('Apply')</a>
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
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

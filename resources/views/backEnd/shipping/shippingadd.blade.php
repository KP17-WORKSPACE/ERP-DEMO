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
                        Shipping
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('shipping-add') }}">
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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
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
                        @if (isset($editData))
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'shipping-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'shipping-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif

                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                        <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="white-box">

                                    <div class="row">
                                        <div class="col-lg-12 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label"> @lang('Shipping Name') <span>*</span> </label>
                                                <input class="form-control" type="text" id="shipping_name"
                                                    name="shipping_name"
                                                    value="{{ isset($editData) ? @$editData->shipping_name : old('shipping_name') }}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('shipping_name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('shipping_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label"> @lang('Contact Name') <span>*</span> </label>
                                                <input class="form-control" type="text" id="contact_name"
                                                    name="contact_name"
                                                    value="{{ isset($editData) ? @$editData->contact_name : old('contact_name') }}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('contact_name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('contact_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label"> @lang('Contact No') <span>*</span> </label>
                                                <input class="form-control" type="number" id="contact_no" name="contact_no"
                                                    value="{{ isset($editData) ? @$editData->contact_no : old('contact_no') }}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('contact_no'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('contact_no') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label"> @lang('Address 1') <span>*</span> </label>
                                                <input class="form-control" type="text" id="address1" name="address1"
                                                    value="{{ isset($editData) ? @$editData->address1 : old('address1') }}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('address1'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('address1') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-2">
                                            <div class="input-effect">
                                                <label class="form-label"> @lang('Address 2') <span>*</span> </label>
                                                <input class="form-control" type="text" id="address2" name="address2"
                                                    value="{{ isset($editData) ? @$editData->address2 : old('address2') }}">
                                                <span class="focus-border"></span>
                                                @if ($errors->has('address2'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('address2') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bank Info Details -->

                                    <!-- end row -->

                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            <button class="btn btn-light" type="submit" id="btnSubmit">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                @if (isset($editData))
                                                    @lang('Update')
                                                @else
                                                    @lang('Add')
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <table class="table table-hover" id="long-list" width="100%" cellspacing="0">
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
                                            <th> @lang('Shipping Name')</th>
                                            <th> @lang('Contact Name')</th>
                                            <th> @lang('Contact No')</th>
                                            <th> @lang('Address 1')</th>
                                            <th> @lang('Address 2')</th>
                                            <th class="text-center"> @lang('lang.action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if (isset($shipping))
                                            @foreach ($shipping as $value)
                                                <tr>
                                                    <td>
                                                        {{ @$value->shipping_name }}
                                                    </td>
                                                    <td>
                                                        {{ @$value->contact_name }}
                                                    </td>
                                                    <td>
                                                        {{ @$value->contact_no }}
                                                    </td>
                                                    <td>
                                                        {{ @$value->address1 }}
                                                    </td>
                                                    <td>
                                                        {{ @$value->address2 }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            @if (in_array(163, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="btn btn-sm btn-light text-dark"
                                                                    href="{{ url('shipping/' . @$value->id . '/edit') }}"><i
                                                                        class="ico icon-outline-pen-2 text-success"
                                                                        style="font-size: 16px;"></i> </a>
                                                            @endif
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{ Form::close() }}
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

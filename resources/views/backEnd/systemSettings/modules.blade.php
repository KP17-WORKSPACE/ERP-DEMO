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
                        Modules
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light" href="{{ url('module') }}">
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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
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
                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'module/' . @$editmode->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                        @else
                                            @if (in_array(105, @$module_links) || Auth::user()->role_id == 1)
                                                {{ Form::open([
                                                    'class' => 'form-horizontal',
                                                    'files' => true,
                                                    'url' => 'module',
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
                                                        <div class="input-effect">
                                                            <label class="txtlbl">@lang('Module') @lang('Name')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="name"
                                                                autocomplete="off"
                                                                value="{{ isset($editmode) ? @$editmode->name : '' }}"
                                                                required>
                                                            <input type="hidden" name="id"
                                                                value="{{ isset($editmode) ? $editmode->id : '' }}">
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
                                                <div class="row mt-2">
                                                    <div class="col-lg-12">
                                                        <button class="btn btn-light" type="submit" id="btnSubmit">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            {{ isset($editmode) ? 'Update' : 'Save' }}
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

                                        <table class="table table-hover" id="long-list" width="100%" cellspacing="0">

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
                                                    <th>@lang('Module')</th>
                                                    {{-- <th>@lang('Created By')</th> --}}
                                                    <th class="text-center" style="width: 90px">@lang('Module Pages')</th>
                                                    <th class="text-center" style="width: 90px">@lang('lang.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($modulelist as $mdle)
                                                    <tr>
                                                        <td>{{ @$mdle->name }}</td>
                                                        {{-- <td>{{@$mdle->created_by->createdby }}</td> --}}
                                                        <td>
                                                            <a href="{{ url('module-pages', [@$mdle->id]) }}"
                                                                class="">
                                                                <button type="button" class="btn btn-sm btn-light">
                                                                    @lang('Manage Pages') </button>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <a class="btn-sm btn btn-light text-dark"
                                                                    href="{{ url('module', [@$mdle->id]) }}"><i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i> @lang('lang.edit')</a>

                                                            </div>
                                                            {{--  <div class="dropdown">
                                                <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                    @lang('lang.select')
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    @if (in_array(106, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="dropdown-item" href="{{url('module', [@$mdle->id
                                                        ])}}">@lang('lang.edit')</a>
                                                    @endif
                                                    @if (in_array(107, @$module_links) || Auth::user()->role_id == 1)
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#deleteDesignationModal{{@$mdle->id}}"
                                                        href="#">@lang('lang.delete')</a>
                                                    @endif
                                                </div>
                                            </div>  --}}
                                                        </td>
                                                    </tr>
                                                    <div class="modal fade admin-query"
                                                        id="deleteDesignationModal{{ @$mdle->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('lang.delete')
                                                                        @lang('lang.paymentterms')</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                                    </div>

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('lang.cancel')</button>
                                                                        {{ Form::open(['url' => 'module/' . @$mdle->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">@lang('lang.delete')</button>
                                                                        {{ Form::close() }}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </tbody>
                                        </table>
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

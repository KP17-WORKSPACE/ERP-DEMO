@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Main Heads
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" data-bs-toggle="modal"
                            data-bs-target="#accountModal"><i
                                class="ico icon-outline-add-square text-success"></i> Account</a>
                        <a class="btn btn-light text-dark"data-bs-toggle="modal"
                            data-bs-target="#subgroupModal"><i
                                class="ico icon-outline-add-square text-success"></i> Sub Group</a>
                        <a class="btn btn-light text-dark" data-bs-toggle="modal"
                            data-bs-target="#groupModal"><i
                                class="ico icon-outline-add-square text-success"></i> Group</a>


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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('vat-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        VAT Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
                    </a>
                </li>


            </ul>
        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        @if (isset($editData))
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroup-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'accountgroup-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                        <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">

                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Main Heads') <span>*</span> </label>
                                    <input class="form-control {{ $errors->has('title') ? 'is-invalid' : ' ' }}"
                                        type="text" id="title" name="title"
                                        value="{{ isset($editData) ? @$editData->title : old('title') }}">
                                    <span class="focus-border"></span>

                                    @if ($errors->has('title'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4 mt-4">
                                <button class="btn btn-light" type="submit" id="btnSubmit">
                                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                                    @if (isset($editData))
                                        @lang('lang.save')
                                    @else
                                        @lang('lang.add')
                                    @endif
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
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
                                    <th> @lang('Main Heads')</th>
                                    <th style="width:100px"> @lang('Status')</th>
                                    <th style="width:100px" class="text-center"> @lang('lang.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (isset($accountgroup))
                                    @foreach ($accountgroup as $value)
                                        <tr>
                                            <td>
                                                {{ @$value->title }}
                                            </td>
                                            <td>
                                                @if (@$value->status == 1)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if (Auth::user()->role_id == 1)
                                                        <a class="btn btn-sm btn-light"
                                                            href="{{ url('accountgroup/' . @$value->id . '/edit') }}"><i class="ico icon-outline-pen-2 text-success" style="font-size: 16px;"></i> </a>
                                                        <a class="btn btn-sm btn-light"
                                                            href="{{ url('accountgroup/' . @$value->id . '/delete') }}"
                                                            onclick="return confirm('Are you sure you want to delete this item?');"><i class="ico icon-outline-trash-bin-minimalistic text-danger" style="font-size: 16px;"></i></a>
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


            </div>
        </div>
    </div>

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


  @include('backEnd.accounts.accountgroupsubadd_form')
            @include('backEnd.accounts.accountgroupsub2add_form')
            @include('backEnd.chart-of-accounts.accountadd_form')

@endsection

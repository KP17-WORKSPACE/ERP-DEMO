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
                        Base Setup
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('base-setup') }}">
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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
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
                                        <div class="main-title">
                                            {{-- <h4 class="mb-30">@if (isset($base_setup))
                                        @lang('lang.edit')
    
                                    @else
                                        @lang('lang.add')
    
                                    @endif
                                    @lang('lang.base_setup')
                                </h4> --}}
                                        </div>
                                        @if (isset($base_setup))
                                            {{ Form::open([
                                                'class' => 'form-horizontal',
                                                'files' => true,
                                                'route' => 'base_setup_update',
                                                'method' => 'POST',
                                            ]) }}
                                        @else
                                            @if (in_array(201, @$module_links) || Auth::user()->role_id == 1)
                                                {{ Form::open([
                                                    'class' => 'form-horizontal',
                                                    'files' => true,
                                                    'route' => 'base_setup_store',
                                                    'method' => 'POST',
                                                ]) }}
                                            @endif
                                        @endif
                                        <div class="white-box">
                                            <div class="add-visitor">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        @if (session()->has('message-success'))
                                                            <div class="alert alert-success alert-dismissible fade show"
                                                                role="alert">
                                                                {{ session()->get('message-success') }}
                                                                <button type="button" class="close" data-dismiss="alert"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                        @elseif(session()->has('message-danger'))
                                                            <div class="alert alert-danger alert-dismissible fade show"
                                                                role="alert">
                                                                {{ session()->get('message-danger') }}
                                                                <button type="button" class="close" data-dismiss="alert"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                        @endif
                                                        <label class="form-label">@lang('Select') <span>*</span></label>

                                                        <select class="form-control" name="base_group">
                                                            <option data-display="@lang('lang.base_group') *" value="">
                                                                @lang('lang.base_group')*</option>
                                                            @foreach ($base_groups as $base_group)
                                                                @if (isset($base_setup))
                                                                    <option value="{{ $base_group->id }}"
                                                                        {{ @$base_group->id == @$base_setup->base_group_id ? 'selected' : '' }}>
                                                                        {{ @$base_group->name }}</option>
                                                                @else
                                                                    <option value="{{ @$base_group->id }}">
                                                                        {{ @$base_group->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="row  mt-2">
                                                    <div class="col-lg-12">
                                                        <div class="input-effect">
                                                            <label class="form-label">@lang('lang.name')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="name"
                                                                value="{{ isset($base_setup) ? @$base_setup->base_setup_name : '' }}">
                                                            <input type="hidden" name="id"
                                                                value="{{ isset($base_setup) ? @$base_setup->id : '' }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                @php
                                                    $tooltip = '';
                                                    if (in_array(201, @$module_links) || Auth::user()->role_id == 1) {
                                                        $tooltip = '';
                                                    } else {
                                                        $tooltip = 'You have no permission to add';
                                                    }
                                                @endphp

                                                <div class="row mt-2">
                                                    <div class="col-lg-12">
                                                        <button class="btn btn-light" type="submit" id="btnSubmit">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            @if (isset($base_setup))
                                                                @lang('lang.update')
                                                            @else
                                                                @lang('lang.save')
                                                            @endif

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
                                    <div class="col-lg-4 no-gutters">
                                        <div class="main-title">
                                            <h4 class="mb-0">@lang('lang.base_setup') @lang('lang.list')</h4>
                                        </div>
                                    </div>
                                </div>



                                <style>
                                    /* Accordion styling */
                                    .accordion-item {
                                        border: 1px solid #e5e7eb;
                                        margin-bottom: 12px;
                                        border-radius: 12px;
                                        overflow: hidden;
                                        background: #deebe1;
                                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                                    }

                                    .accordion-button {
                                        font-weight: 600;
                                        font-size: 15px;
                                        color: #212529;
                                        background-color: #f9fafb;
                                        padding: 1rem 1.25rem;
                                    }

                                    .accordion-button:focus {
                                        box-shadow: none;
                                    }

                                    .accordion-button:not(.collapsed) {
                                        background-color: #eef5ff;
                                        color: #0d6efd;
                                    }

                                    /* Inner list styled like table rows */
                                    .setup-row {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        padding: 0.9rem 1.25rem;
                                        border-bottom: 1px solid #f1f3f5;
                                        font-size: 14px;
                                    }

                                    .setup-row:last-child {
                                        border-bottom: none;
                                    }

                                    .setup-name {
                                        font-weight: 500;
                                        color: #374151;
                                    }

                                    .setup-actions .btn {
                                        font-size: 13px;
                                        padding: 0.25rem 0.6rem;
                                        border-radius: 6px;
                                    }

                                    /* Dropdown menu */
                                    .dropdown-menu {
                                        border-radius: 8px;
                                        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
                                        font-size: 14px;
                                    }
                                </style>


                                <div class="row base-setup">
                                    <div class="col-lg-12">

                                        {{-- Flash Messages --}}
                                        @if (session()->has('message-success-delete') || session()->has('message-danger-delete'))
                                            <div
                                                class="alert {{ session()->has('message-success-delete') ? 'alert-success' : 'alert-danger' }} mb-4">
                                                {{ session()->get('message-success-delete') ?? session()->get('message-danger-delete') }}
                                            </div>
                                        @endif

                                        {{-- Accordion --}}
                                        <div class="accordion" id="baseSetupAccordion">
                                            @php $i=0; @endphp
                                            @foreach ($base_groups as $base_group)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $base_group->id }}">
                                                        <button class="accordion-button {{ $i !== 0 ? 'collapsed' : '' }}"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#collapse{{ $base_group->id }}"
                                                            aria-expanded="{{ $i == 0 ? 'true' : 'false' }}"
                                                            aria-controls="collapse{{ $base_group->id }}">
                                                            {{ $base_group->name }}
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $base_group->id }}"
                                                        class="accordion-collapse collapse {{ $i++ == 0 ? 'show' : '' }}"
                                                        aria-labelledby="heading{{ $base_group->id }}"
                                                        data-bs-parent="#baseSetupAccordion">
                                                        <div class="accordion-body p-0">

                                                            @forelse($base_group->baseSetups as $base_setup)
                                                                <div class="setup-row">
                                                                    <div class="setup-name">
                                                                        {{ $base_setup->base_setup_name }}
                                                                    </div>
                                                                    <div class="setup-actions">
                                                                        <div class="dropdown">
                                                                            <button
                                                                                class="btn btn-light border dropdown-toggle"
                                                                                type="button" data-bs-toggle="dropdown">
                                                                                @lang('lang.action')
                                                                            </button>
                                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                                @if (in_array(202, @$module_links) || Auth::user()->role_id == 1)
                                                                                    <li>
                                                                                        <a class="dropdown-item"
                                                                                            href="{{ route('base_setup_edit', [$base_setup->id]) }}">
                                                                                             @lang('lang.edit')
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                                @if (in_array(203, @$module_links) || Auth::user()->role_id == 1)
                                                                                    <li>
                                                                                        <a class="dropdown-item deleteBaseSetupModal text-danger"
                                                                                            href="#"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#deleteBaseSetupModal"
                                                                                            data-id="{{ $base_setup->id }}">
                                                                                             @lang('lang.delete')
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div class="p-3 text-muted">No setups available</div>
                                                            @endforelse

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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

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
                        General Settings
                    </h4>
                    <div class="purchase-order-content-header-right">
                       
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">

                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body text-center">


                                        <div class="">
                                            <div class="main-title">
                                                <h5 class="primary-color">@lang('Change Logo'):</h5>
                                            </div>

                                            @if (in_array(183, @$module_links) || Auth::user()->role_id == 1)
                                                @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                                                @else
                                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-school-logo', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                @endif
                                            @endif

                                            <div class="white-box">
                                                <input type="hidden" name="url" id="url"
                                                    value="{{ URL::to('/') }}">
                                                <div class="text-center">
                                                    @if (isset($editData->logo) && !empty(@$editData->logo))
                                                        <img class="img-fluid Img-100" src="{{ @$editData->logo }}"
                                                            alt="">
                                                    @else
                                                        <img class="img-fluid"
                                                            src="{{ asset('public/uploads/settings/logo.png') }}"
                                                            alt="">
                                                    @endif
                                                </div>

                                                <div class="mt-4">
                                                    <div class="text-center">

                                                        <input type="file" class="form-control" name="main_school_logo"
                                                            id="upload_logo">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="d-flex justify-content-end">
                                                        @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                            <span class="d-inline-block" tabindex="0"
                                                                data-toggle="tooltip" title="Disabled For Demo">
                                                                <button class="btn btn-sm btn-light mt-2 small fix-gr-bg demo_view"
                                                                    style="pointer-events: none;" type="button" disabled>
                                                                    <i class="ico icon-outline-pen-2 text-dark"
                                                                        style="font-size: 16px;"></i>
                                                                    @lang('lang.change_logo')
                                                                </button>
                                                            </span>
                                                        @else
                                                            <button class="btn btn-sm btn-light mt-2">
                                                                <span class="ti-check"></span>
                                                                <i class="ico icon-outline-pen-2 text-dark"
                                                                    style="font-size: 16px;"></i>
                                                                @lang('lang.change_logo')
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                            {{ Form::close() }}
                                        </div>


                                    </div>
                                </div>

                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body text-center">

                                        <div class="">
                                            <div class="main-title">
                                                <h5 class="primary-color">@lang('Change Favicon'):</h5>
                                            </div>

                                            @if (in_array(184, @$module_links) || Auth::user()->role_id == 1)
                                                @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                                                @else
                                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-school-logo', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                @endif
                                            @endif

                                            <div class="white-box">
                                                <input type="hidden" name="url" id="url"
                                                    value="{{ URL::to('/') }}">
                                                <div class="text-center">
                                                    @if (isset($editData->favicon) && !empty(@$editData->favicon))
                                                        <img class="img-fluid Img-50" src="{{ @$editData->favicon }}"
                                                            alt="">
                                                    @else
                                                        <img class="img-fluid"
                                                            src="{{ asset('public/uploads/settings/favicon.png') }}"
                                                            alt="">
                                                    @endif
                                                </div>

                                                <div class="mt-4">
                                                    <div class="text-center">

                                                        <input type="file" class="form-control"
                                                            name="main_school_favicon" id="upload_favicon">
                                                    </div>
                                                </div>
                                                <div class="text-center gs_button">

                                                    <div class="d-flex justify-content-end">
 @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                                            title="Disabled For Demo "> <button
                                                                class="btn btn-sm btn-light mt-2 small fix-gr-bg  demo_view"
                                                                style="pointer-events: none;" type="button" disabled>
                                                                <i class="ico icon-outline-pen-2 text-dark"
                                                                    style="font-size: 16px;"></i> @lang('lang.change_fav')
                                                            </button></span>
                                                    @else
                                                        <button class="btn btn-sm btn-light mt-2">
                                                            <span class="ti-check"></span>
                                                            <i class="ico icon-outline-pen-2 text-dark"
                                                                style="font-size: 16px;"></i> @lang('lang.change_fav')
                                                        </button>
                                                    @endif
                                                    </div>




                                                   

                                                </div>
                                            </div>
                                            {{ Form::close() }}
                                        </div>
                                    </div>
                                </div>


                            </div>


                            <div class="col-lg-9">

                                <div class="card shadow-sm border-0 rounded-3">
                                    <div class="card-body">
                                        <h6 class="fw-semibold text-secondary mb-3">General Information</h6>
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle">
                                                <tbody class="text-muted">
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.school_name')</td>
                                                        <td>{{ @$editData->company_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.site_title')</td>
                                                        <td>{{ @$editData->site_title }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.address')</td>
                                                        <td>{{ @$editData->address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.phone')
                                                            @lang('lang.no')</td>
                                                        <td>{{ @$editData->phone }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.email')
                                                            @lang('lang.address')</td>
                                                        <td>{{ @$editData->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.language')</td>
                                                        <td>{{ @$editData->languages != '' ? @$editData->languages->language_name : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.date_format')</td>
                                                        <td>{{ @$editData->dateFormats != '' ? @$editData->dateFormats->normal_view : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.time_zone')</td>
                                                        <td>{{ @$editData->dateFormats != '' ? @$editData->timeZone->time_zone : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.currency')</td>
                                                        <td>{{ @$editData->currency }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.currency')
                                                            @lang('lang.symbol')</td>
                                                        <td>{{ @$editData->currency_symbol }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-dark">@lang('lang.copyright_text')</td>
                                                        <td>{{ @$editData->copyright_text }}</td>
                                                    </tr>
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
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

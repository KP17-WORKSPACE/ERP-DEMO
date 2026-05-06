@extends('backEnd.newmasterpage')
@section('mainContent')
    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>







    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>



    <div class="content-container">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="mb-2">SMS Settings
            </h4>
            <div class="search-filter-container mb-0">


                <button class="btn btn-light" onclick="toggleLongFilters()">
                    <i class="ico icon-outline-magnifer"></i>
                </button>
                <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
        </div>




        <section class="mb-40 student-details">

            <div class="row">


                <!-- Start Sms Details -->
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#select_sms_service" role="tab"
                                data-bs-toggle="tab">@lang('lang.select_a_SMS_service')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#clickatell_settings" role="tab"
                                data-bs-toggle="tab">@lang('lang.clickatell') @lang('lang.settings')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#twilio_settings" role="tab"
                                data-bs-toggle="tab">@lang('lang.twilio') @lang('lang.settings')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#msg91_settings" role="tab" data-bs-toggle="tab">MSG91
                                Settings</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane fade show active" id="select_sms_service">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-clickatell-data', 'id' => 'select_a_service']) }}
                            <div class="white-box">
                                <div class="row">
                                    <div class="col-lg-4 select_sms_services">
                                        <div class="input-effect mt-2">
                                            <label for="" class="form-label">Select a SMS Service</label>
                                            <select
                                                class="niceSelect w-100 bb form-control{{ $errors->has('book_category_id') ? ' is-invalid' : '' }}"
                                                name="sms_service" id="sms_service">
                                                <option data-display="@lang('lang.select_a_SMS_service')" value="">
                                                    @lang('lang.select_a_SMS_service')</option>
                                                @if (isset($sms_services))
                                                    @foreach ($sms_services as $value)
                                                        <option value="{{ @$value->id }}"
                                                            @if (isset($active_sms_service)) @if (@$active_sms_service->id == @$value->id) selected @endif
                                                            @endif >{{ @$value->gateway_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('book_category_id'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('book_category_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-8">

                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>

                        <div role="tabpanel" class="tab-pane fade" id="clickatell_settings">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-clickatell-data', 'id' => 'clickatell_form']) }}
                            <div class="white-box">
                                <div class="">
                                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                    <input type="hidden" name="clickatell_form" id="clickatell_form_url"
                                        value="update-clickatell-data">
                                    <input type="hidden" name="gateway_id" id="gateway_id" value="1">
                                    <div class="row mb-30">

                                        <div class="col-lg-4">
                                            <div class="row mt-2">
                                                <div class=" mb-30">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('lang.clickatell') @lang('lang.username')
                                                            <span>*</span>
                                                        </label>
                                                        <input
                                                            class="primary-input form-control{{ $errors->has('clickatell_username') ? ' is-invalid' : '' }}"
                                                            type="text" name="clickatell_username"
                                                            id="clickatell_username" autocomplete="off"
                                                            value="{{ isset($sms_services) ? @$sms_services[0]->clickatell_username : '' }}">

                                                        <span class="focus-border"></span>
                                                        <span class="modal_input_validation red_alert"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class=" mb-30">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('lang.clickatell') @lang('lang.password')
                                                            <span>*</span>
                                                        </label>
                                                        <input
                                                            class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                            type="text" name="clickatell_password"
                                                            id="clickatell_password" autocomplete="off"
                                                            value="{{ isset($sms_services) ? @$sms_services[0]->clickatell_password : '' }}">

                                                        <span class="focus-border"></span>
                                                        <span class="modal_input_validation red_alert"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('lang.clickatell') @lang('API')
                                                            @lang('ID')
                                                            <span>*</span> </label>
                                                        <input
                                                            class="primary-input form-control{{ $errors->has('clickatell_api_id') ? ' is-invalid' : '' }}"
                                                            type="text" name="clickatell_api_id"
                                                            id="clickatell_api_id" autocomplete="off"
                                                            value="{{ isset($sms_services) ? @$sms_services[0]->clickatell_api_id : '' }}">

                                                        <span class="focus-border"></span>
                                                        @if ($errors->has('clickatell_api_id'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('clickatell_api_id') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-lg-12 text-center">
                                                    <button class="btn btn-sm btn-light fix-gr-bg">
                                                        <span class="ti-check"></span>
                                                        <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                        @lang('lang.update')
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <div class="row justify-content-center">
                                                <img class="logo" width="" height=""
                                                    src="{{ URL::asset('public/backEnd/img/Clickatell.png') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>
                        <!-- End Profile Tab -->

                        <!-- Start Exam Tab -->
                        <div role="tabpanel" class="tab-pane fade" id="twilio_settings">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-twilio-data', 'id' => 'twilio_form']) }}
                            <div class="white-box">
                                <div class="">
                                    <input type="hidden" name="twilio_form" id="twilio_form_url"
                                        value="update-twilio-data">
                                    <input type="hidden" name="gateway_id" id="gateway_id" value="2">
                                    <div class="row mb-30">

                                        <div class="col-md-4">
                                            <div class="row mt-2">
                                                <div class=" mb-30">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('lang.twilio') @lang('lang.account')
                                                            @lang('lang.sid')
                                                            <span>*</span> </label>
                                                        <input
                                                            class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                            type="text" name="twilio_account_sid" autocomplete="off"
                                                            value="{{ isset($sms_services) ? @$sms_services[1]->twilio_account_sid : '' }}"
                                                            id="twilio_account_sid">

                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class=" mb-30">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('lang.authentication') @lang('lang.token')
                                                            <span>*</span>
                                                        </label>
                                                        <input
                                                            class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                            type="text" name="twilio_authentication_token"
                                                            autocomplete="off"
                                                            value="{{ isset($sms_services) ? @$sms_services[1]->twilio_authentication_token : '' }}"
                                                            id="twilio_authentication_token">

                                                        <span class="focus-border"></span>
                                                        @if ($errors->has('book_title'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('book_title') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('lang.registered_phone_number') <span>*</span>
                                                        </label>

                                                        <input
                                                            class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                            type="text" name="twilio_registered_no" autocomplete="off"
                                                            value="{{ isset($sms_services) ? @$sms_services[1]->twilio_registered_no : '' }}"
                                                            id="twilio_registered_no">
                                                        <span class="focus-border"></span>
                                                        @if ($errors->has('book_title'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('book_title') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-lg-12 text-center">
                                                    <button class="btn btn-sm btn-light fix-gr-bg">
                                                        <span class="ti-check"></span>
                                                        <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                        @lang('lang.update')
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <div class="row justify-content-center">
                                                <img class="logo"
                                                    src="{{ URL::asset('public/backEnd/img/twilio.png') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>


                        <div role="tabpanel" class="tab-pane fade" id="msg91_settings">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-msg91-data', 'method' => 'POST']) }}
                            <div class="white-box">
                                <input type="hidden" name="msg91_form" id="msg91_form_url" value="update-msg91-data">
                                <input type="hidden" name="gateway_id" id="gateway_id" value="3">
                                <div class="row mb-30">
                                    <div class="col-md-">
                                        <div class="row mt-2">
                                            <div class=" mb-30">
                                                <div class="input-effect">
                                                    <label class="form-label"> Authentication KEY SID <span>*</span>
                                                    </label>

                                                    <input
                                                        class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                        type="text" id="msg91_authentication_key_sid"
                                                        name="msg91_authentication_key_sid" autocomplete="off"
                                                        value="{{ isset($sms_services) ? @$sms_services[2]->msg91_authentication_key_sid : '' }}">
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('book_title'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('book_title') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class=" mb-30">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('lang.sender') @lang('lang.id')
                                                        <span>*</span> </label>

                                                    <input
                                                        class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                        type="text" name="msg91_sender_id" autocomplete="off"
                                                        value="{{ isset($sms_services) ? @$sms_services[2]->msg91_sender_id : '' }}"
                                                        id="msg91_sender_id">
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('book_title'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('book_title') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class=" mb-30">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('lang.route') <span>*</span> </label>
                                                    <input
                                                        class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                        type="text" name="msg91_route" autocomplete="off"
                                                        value="{{ isset($sms_services) ? @$sms_services[2]->msg91_route : '' }}"
                                                        id="msg91_route">

                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('book_title'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('book_title') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('lang.country_code') <span>*</span> </label>
                                                    <input
                                                        class="primary-input form-control{{ $errors->has('book_title') ? ' is-invalid' : '' }}"
                                                        type="text" name="msg91_country_code" autocomplete="off"
                                                        value="{{ isset($sms_services) ? @$sms_services[2]->msg91_country_code : '' }}"
                                                        id="msg91_country_code">

                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('book_title'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('book_title') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-lg-12 text-center">
                                                <button class="btn btn-sm btn-light fix-gr-bg" type="submit">
                                                    <span class="ti-check"></span>
                                                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                    @lang('lang.update')

                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="row justify-content-center">
                                            <img class="logo" width="" height=""
                                                src="{{ URL::asset('public/backEnd/img/MSG91-logo.png') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{ Form::close() }}
                    </div>

                </div>
            </div>
        
        </section>
    </div>


    





    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

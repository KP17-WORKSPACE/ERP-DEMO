<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('public/design') }}/images/erp-logo-icon.png" type="image/png" />
    <title>Employee Onboard - Syscom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_main.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_icomoon.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_header.css" />
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/_navbar.css" />
    {{-- <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/purchase-order.css" /> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


</head>

<body style="overflow: scroll; background: linear-gradient(120deg, #63c377 0%, #397c47 100%);">




    <style>
        /* Toastr Notification Styles */
        .toast-container {
            pointer-events: none;
        }

        .toast-container .toast {
            pointer-events: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border-radius: 8px !important;
            border: none !important;
            overflow: visible !important;
            padding: 15px 20px 15px 15px !important;
            opacity: 1 !important;
        }

        .toast-center-center {
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            position: fixed;
            z-index: 9999;
        }

        .toast-top-right {
            top: 85px;
            right: 20px;
            position: fixed;
            z-index: 9999;
        }

        .toast-top-right>.toast {
            margin-bottom: 12px;
            min-width: 320px;
            max-width: 400px;
        }

        .toast-title {
            font-size: 15px !important;
            font-weight: 600 !important;
            margin-bottom: 4px !important;
            color: #ffffff !important;
        }

        .toast-message {
            font-size: 13px !important;
            line-height: 1.5 !important;
            color: rgba(255, 255, 255, 0.95) !important;
            font-weight: 400 !important;
        }

        /* Success Notification */
        .toast-success {
            background-color: #22c55e !important;
            background-image: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        }

        /* Error Notification */
        .toast-error {
            background-color: #ef4444 !important;
            background-image: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        /* Warning Notification */
        .toast-warning {
            background-color: #f59e0b !important;
            background-image: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
        }

        /* Info Notification */
        .toast-info {
            background-color: #3b82f6 !important;
            background-image: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        }

        /* Progress Bar */
        .toast-progress {
            height: 4px !important;
            opacity: 0.6 !important;
            background-color: rgba(255, 255, 255, 0.4) !important;
        }

        /* Close Button */
        .toast-close-button {
            color: rgba(255, 255, 255, 0.95) !important;
            opacity: 0.8 !important;
            font-weight: 300 !important;
            text-shadow: none !important;
            font-size: 18px !important;
        }

        .toast-close-button:hover {
            opacity: 1 !important;
            color: #ffffff !important;
        }

        /* Animation */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast {
            animation: slideInRight 0.4s ease-out !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .toast-top-right>.toast {
                min-width: 280px;
                max-width: calc(100vw - 40px);
            }
        }
    </style>

    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "300",
            "timeOut": "6000",
            "extendedTimeOut": "2000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>




    <div id="loading_bg"
        style="width: 100vw; height: 100vh; background: #00000085; position: fixed; z-index:9999; text-align: center; display:none;">
        <img src="{!! asset('public/design/assets/images/loader.gif') !!}" style="margin: 20%;">
    </div>

    <div class="venus-app page-purchase-order" style="background: linear-gradient(120deg, #63c377 0%, #397c47 100%);">




        <section class="main-content mt-4">
            <div class="form-scroll container">
                <form id="staffAllForm" action="{{ url('save-onboarding-employee') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="company_id" value="{{ $company_id }}">

                    <div class="content-container col-12">
                        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

                            <!-- DATA DETAILS -->
                            <div role="tabpanel" aria-labelledby="data-tab" id="data-details"
                                class="tab-pane show active">
                                <div class="purchase-order-content-header mb-3">
                                    <h4 class="purchase-order-content-header-left text-white">Employee Onboarding</h4>
                                    <div class="purchase-order-content-header-right">
                                        <button type="submit" class="btn btn-light" id="btnSaveAll"><i
                                                class="ico icon-outline-bookmark-opened text-success"
                                                style="font-size: 16px"></i>Save</button>

                                    </div>
                                </div>

                                <div class="card mb-3" style="border-radius:10px">
                                    <div class="card-body">
                                        <strong>Personal Details</strong>
                                        <div class="row gy-3 mt-1">


                                            <div class="col-lg-2">
                                                <label class="form-label">Salutation<span>*</span></label>
                                                <select class="form-select form-select-sm js-example-basic-single"
                                                    name="salutation">
                                                    <option value="">Select</option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Miss.">Miss.</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">First Name<span>*</span></label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="first_name" value="" required>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Last Name</label>
                                                <input class="form-control form-control-sm" type="text" required
                                                    name="last_name" value="">
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Date of Birth<span>*</span></label>
                                                <input type="text" class="form-control flatpickr-input date-picker"
                                                    name="date_of_birth" value="">
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Place of Birth<span>*</span></label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="place_of_birth" value="">
                                            </div>

                                            <div class="col-lg-2">

                                                <label class="form-label">
                                                    <span>@lang('Religion')
                                                    </span>

                                                </label>
                                                <select class="form-select form-select-sm js-example-basic-single"
                                                    id="religion" name="religion">
                                                    <option value="">Select</option>
                                                    @foreach ($religions as $religion)
                                                        <option value="{{ $religion->base_setup_name }}">
                                                            {{ $religion->base_setup_name }}</option>
                                                    @endforeach
                                                    <option value="Others">Others</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Gender<span>*</span></label>
                                                <select class="form-select form-select-sm js-example-basic-single"
                                                    name="gender_id">
                                                    <option value="">Select</option>
                                                    <option value="1">Male</option>
                                                    <option value="2">Female</option>
                                                    <option value="3">Other</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Mobile<span>*</span></label>
                                                <input class="form-control form-control-sm" type="tel"
                                                    name="mobile" placeholder="+" value="+">
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Email<span>*</span></label>
                                                <input class="form-control form-control-sm" type="email" required
                                                    name="email" value="">
                                            </div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Marital Status</label>
                                                <select class="form-select form-select-sm js-example-basic-single"
                                                    id="marital_status" name="marital_status">
                                                    <option value="">Select</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="divorced">Divorced</option>
                                                    <option value="widowed">Widowed</option>
                                                </select>
                                            </div>

                                            <div class="col-1">
                                                <label class="form-label">User Photo</label>
                                                <input type="file" class="form-control form-control-sm"
                                                    name="staff_photo" accept="image/*">
                                            </div>

                                            <div class="col-1">
                                                <label class="form-label">Blood Group</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="blood_group" value="">
                                            </div>

                                            <div class="col-lg-2 position-relative">
                                                <label class="form-label">Password</label>
                                                <input type="text" class="form-control form-control-sm" required
                                                    name="password" id="password" autocomplete="new-password">
                                                {{-- Floating password validator (hidden by default) --}}
                                                <div id="password-validator" class="password-validator d-none"
                                                    role="status" aria-live="polite">
                                                    <div class="pv-arrow"></div>
                                                    <div class="pv-body">
                                                        <div class="pv-title">Choose a strong password</div>
                                                        <div class="pv-progress" aria-hidden="true">
                                                            <div class="pv-progress-bar" style="width:0%"></div>
                                                        </div>
                                                        <ul class="pv-list">
                                                            <li data-criteria="length" class="pv-item invalid"><span
                                                                    class="pv-dot"></span><span
                                                                    class="pv-text">Minimum <strong>8</strong>
                                                                    characters</span></li>
                                                            <li data-criteria="lower" class="pv-item invalid"><span
                                                                    class="pv-dot"></span><span class="pv-text">At
                                                                    least <strong>1 lowercase</strong> letter</span>
                                                            </li>
                                                            <li data-criteria="upper" class="pv-item invalid"><span
                                                                    class="pv-dot"></span><span class="pv-text">At
                                                                    least <strong>1 uppercase</strong> letter</span>
                                                            </li>
                                                            <li data-criteria="digit" class="pv-item invalid"><span
                                                                    class="pv-dot"></span><span class="pv-text">At
                                                                    least <strong>1 digit</strong></span>
                                                            </li>
                                                            <li data-criteria="special" class="pv-item invalid"><span
                                                                    class="pv-dot"></span><span class="pv-text">At
                                                                    least <strong>1 special</strong> character</span>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row row-cols-6 mt-4">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3"><strong>Father Details</strong></div>

                                            <div class="col">
                                                <label class="form-label"> First Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="father_first_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Last Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="father_last_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Mobile</label>
                                                <input class="form-control form-control-sm" type="tel"
                                                    name="father_mobile" value="+" placeholder="+">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Email</label>
                                                <input class="form-control form-control-sm" type="email"
                                                    name="father_email" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Doc (Govt. ID)</label>
                                                <input class="form-control form-control-sm" accept=".pdf,image/*"
                                                    type="file" name="father_attachment">
                                            </div>



                                        </div>

                                        <div class="row row-cols-6 mt-4">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3"><strong>Mother Details</strong></div>

                                            <div class="col">
                                                <label class="form-label"> First Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="mother_first_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Last Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="mother_last_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Mobile</label>
                                                <input class="form-control form-control-sm" type="tel"
                                                    name="mother_mobile" value="+" placeholder="+">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Email</label>
                                                <input class="form-control form-control-sm" type="email"
                                                    name="mother_email" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Doc (Govt. ID)</label>
                                                <input class="form-control form-control-sm" accept=".pdf,image/*"
                                                    type="file" name="mother_attachment">
                                            </div>



                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                function toggleSpouseSection() {
                                                    var maritalStatus = $('#marital_status').val();
                                                    if (maritalStatus === 'married') {
                                                        $('.spouse-section').show();
                                                    } else {
                                                        $('.spouse-section').hide();
                                                    }
                                                }

                                                // Initial check on page load
                                                toggleSpouseSection();

                                                // Listen for changes in marital status
                                                $('#marital_status').change(function() {
                                                    toggleSpouseSection();
                                                });
                                            });
                                        </script>

                                        <div class="row row-cols-6 mt-4 spouse-section">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3"><strong>Spouse Details</strong></div>

                                            <div class="col">
                                                <label class="form-label"> First Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="spouse_first_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Last Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="spouse_last_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Mobile</label>
                                                <input class="form-control form-control-sm" type="tel"
                                                    name="spouse_mobile" value="+" placeholder="+">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Email</label>
                                                <input class="form-control form-control-sm" type="email"
                                                    name="spouse_email" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label"> Doc (Govt. ID)</label>
                                                <input class="form-control form-control-sm" accept=".pdf,image/*"
                                                    type="file" name="spouse_attachment">
                                            </div>



                                        </div>

                                        <div class="row row-cols-6 mt-4">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3"><strong>Emergency Contact 1 </strong></div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Salutation<span>*</span></label>
                                                <select class="form-select form-select-sm js-example-basic-single"
                                                    name="emergency1_salutation">
                                                    <option value="">Select</option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Miss.">Miss.</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Full Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="emergency1_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Mobile</label>
                                                <input class="form-control form-control-sm" type="tel"
                                                    name="emergency1_mobile" value="+" placeholder="+">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Email</label>
                                                <input class="form-control form-control-sm" type="email"
                                                    name="emergency1_email" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Relationship</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="emergency1_relationship" value="">
                                            </div>



                                        </div>

                                        <div class="row row-cols-6 mt-4">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3"><strong>Emergency Contact 2 </strong></div>

                                            <div class="col-lg-2">
                                                <label class="form-label">Salutation<span>*</span></label>
                                                <select class="form-select form-select-sm js-example-basic-single"
                                                    name="emergency2_salutation">
                                                    <option value="">Select</option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Miss.">Miss.</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Full Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="emergency2_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Mobile</label>
                                                <input class="form-control form-control-sm" type="tel"
                                                    name="emergency2_mobile" value="+" placeholder="+">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Email</label>
                                                <input class="form-control form-control-sm" type="email"
                                                    name="emergency2_email" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Relationship</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="emergency2_relationship" value="">
                                            </div>



                                        </div>

                                        <div class="row  mt-4">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3" style="padding-right: 44px"><strong>Permanent
                                                    Address</strong></div>

                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label mb-1">Country</label>
                                                    <select class="form-control js-example-basic-single"
                                                        name="perm_country" id="perm_country">
                                                        <option value="">-Select-</option>

                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">
                                                                {{ $country->name }}</option>
                                                        @endforeach


                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label mb-1">State</label>
                                                    <div id="perm_state_div">
                                                        <select class="form-control js-example-basic-single"
                                                            name="perm_state" id="perm_state">
                                                            <option value="">-Select-</option>

                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->id }}">
                                                                    {{ $state->name }}</option>
                                                            @endforeach


                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <label class="form-label"> City </label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="perm_city" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Area</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="perm_area" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Building Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="perm_building_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Flat/Office No</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="perm_flat_office_no" value="">
                                            </div>



                                        </div>

                                        <div class="row  mt-4">

                                            <!-- Parent / Spouse details -->
                                            <div class="col mt-3" style="padding-right: 44px"><strong>Current
                                                    Address</strong></div>

                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label mb-1">Country</label>
                                                    <select class="form-control js-example-basic-single"
                                                        name="curr_country" id="curr_country">
                                                        <option value="">-Select-</option>

                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col">
                                                <div class="input-effect">
                                                    <label class="form-label mb-1">State</label>
                                                    <div id="sectionCurrStateDiv">
                                                        <select class="form-control js-example-basic-single"
                                                            name="curr_state" id="curr_state">
                                                            <option value="">-Select-</option>

                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->id }}">
                                                                    {{ $state->name }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col">
                                                <label class="form-label"> City </label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="curr_city" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Area</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="curr_area" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Building Name</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="curr_building_name" value="">
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Flat/Office No</label>
                                                <input class="form-control form-control-sm" type="text"
                                                    name="curr_flat_office_no" value="">
                                            </div>



                                        </div>
                                        <br><br>


                                        <div class="row mt-4">
                                            <div class="col-12">



                                                <div class="tab-wrap mb-3">
                                                    <ul class="nav nav-tabs" id="hrTabs" role="tablist">

                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="bank-tab"
                                                                data-bs-toggle="tab" data-bs-target="#bank-details"
                                                                type="button" role="tab"
                                                                aria-controls="bank-details" aria-selected="false">
                                                                Bank Details
                                                            </button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="edu-tab"
                                                                data-bs-toggle="tab"
                                                                data-bs-target="#educational-qualification"
                                                                type="button" role="tab"
                                                                aria-controls="educational-qualification"
                                                                aria-selected="false">
                                                                Educational Qualification
                                                            </button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="exp-tab"
                                                                data-bs-toggle="tab"
                                                                data-bs-target="#professional-experience"
                                                                type="button" role="tab"
                                                                aria-controls="professional-experience"
                                                                aria-selected="false">
                                                                Professional Experience
                                                            </button>
                                                        </li>






                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="docs-tab"
                                                                data-bs-toggle="tab" data-bs-target="#documentation"
                                                                type="button" role="tab"
                                                                aria-controls="documentation" aria-selected="false">
                                                                Documentation
                                                            </button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content border  bg-white" id="hrTabsContent">




                                                        {{-- ======================= TAB: BANK DETAILS ======================= --}}
                                                        <div class="tab-pane fade show active" id="bank-details"
                                                            role="tabpanel" aria-labelledby="bank-tab">

                                                            <div class="d-flex justify-content-end mb-2">
                                                                <button type="button" id="addBankBtn"
                                                                    class="btn btn-sm btn-light"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#bankModal">
                                                                    <i
                                                                        class="ico icon-outline-add-square text-success"></i>
                                                                    Add Bank Account
                                                                </button>
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-hover table-bordered align-middle"
                                                                    id="long-list">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Bank Name</th>
                                                                            <th>Branch</th>
                                                                            <th>Account Holder</th>
                                                                            <th>Account Number</th>
                                                                            <th>IBAN Number</th>
                                                                            <th>SWIFT Code</th>
                                                                            <th>Currency</th>
                                                                            <th>IBAN Letter</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="bankTableBody">
                                                                        <tr id="no-bank-row">
                                                                            <td colspan="9" class="text-center">
                                                                                No bank accounts added yet.
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div id="bankInputsContainer" style="display:none"></div>
                                                            <div id="educationInputsContainer" style="display:none"></div>
                                                            <div id="experienceInputsContainer" style="display:none"></div>
                                                        </div>

                                                        {{-- education qualification --}}

                                                        <div class="tab-pane fade" id="educational-qualification"
                                                            role="tabpanel" aria-labelledby="edu-tab">

                                                            <div class="d-flex justify-content-end mb-2">
                                                                <button type="button" id="addEducationBtn"
                                                                    class="btn btn-sm btn-light"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#educationModal">
                                                                    <i class="ico icon-outline-add-square text-success"
                                                                        style=""></i>
                                                                    Add Education
                                                                </button>
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-hover table-bordered align-middle"
                                                                    style="table-layout: fixed;width:100%"
                                                                    id="long-list">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th style="width: 150px;">
                                                                                Qualification <span
                                                                                    class="text-danger">*</span>
                                                                            </th>
                                                                            <th>Board / University <span
                                                                                    class="text-danger">*</span>
                                                                            </th>
                                                                            <th style="width: 130px;">
                                                                                Specialization</th>
                                                                            <th style="width: 100px;">Year</th>
                                                                            <th style="width: 100px;">Result
                                                                            </th>
                                                                            <th style="width: 80px;">GPA</th>
                                                                            <th style="width: 100px;">Mode</th>
                                                                            <th style="width: 120px;">Country
                                                                            </th>
                                                                            <th style="width: 90px;">Duration
                                                                            </th>
                                                                            <th style="width: 100px;">
                                                                                Certificate</th>
                                                                            <th style="width: 120px;">Action
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="educationTableBody">
                                                                        <tr class="no-education-row">
                                                                            <td colspan="11"
                                                                                class="text-center text-muted">
                                                                                No education records added yet.
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        {{-- educational qualification end --}}

                                                        {{-- professional experience --}}
                                                        <div class="tab-pane fade" id="professional-experience"
                                                            role="tabpanel" aria-labelledby="exp-tab">

                                                            <div class="d-flex justify-content-end mb-2">
                                                                <button type="button" id="addExperienceBtn"
                                                                    class="btn btn-sm btn-light"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#experienceModal">
                                                                    <i
                                                                        class="ico icon-outline-add-square text-success"></i>
                                                                    Add Experience
                                                                </button>
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-hover table-bordered align-middle"
                                                                    style="table-layout: fixed;width:100%"
                                                                    id="long-list">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Previous Organization <span
                                                                                    class="text-danger">*</span>
                                                                            </th>
                                                                            <th>Previous Designation</th>
                                                                            <th style="width: 180px;">
                                                                                Employment Duration (Y, M)</th>
                                                                            <th>Key Responsibilities</th>
                                                                            <th>
                                                                                Experience Certificate</th>
                                                                            <th style="width: 70px;">Action
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="experienceTableBody">
                                                                        <tr class="no-experience-row">
                                                                            <td colspan="6"
                                                                                class="text-center text-muted">
                                                                                No experience records added yet.
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        {{-- professional experience end --}}

                                                        {{--
                                                                    ========================================================================
                                                                    ATTENDANCE / LEAVE CONFIGURATION TAB - COMMENTED OUT
                                                                    Not used in Add Staff form. Will be used elsewhere.
                                                                    Contains fields for: Attendance Policy, Working Hours, Grace Period,
                                                                    Shift Times, Weekly Off Days, Leave Policy, Annual/Sick/Casual Leave,
                                                                    Comp-Off, Carry Forward, Leave Encashment, etc.
                                                                    ========================================================================
                                                                    --}}


                                                        {{-- documents tab --}}
                                                        <div class="tab-pane fade" id="documentation" role="tabpanel"
                                                            aria-labelledby="docs-tab">

                                                            {{-- 1. JOINING DOCUMENTS --}}
                                                            <h6 class="mt-1">Joining Documents</h6>
                                                            <div class="table-responsive mb-3">
                                                                <table class="table table-bordered align-middle"
                                                                    id="long-list">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th style="width:260px;">Document
                                                                            </th>
                                                                            <th style="width:160px;">Document Number
                                                                            </th>
                                                                            <th style="width:160px;">Expiry
                                                                                Date</th>
                                                                            <th style="width:220px;">
                                                                                Attachment
                                                                            </th>

                                                                            <th>Remarks</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>


                                                                        {{-- These are auto-prefilled from earlier tabs if available; user can Replace --}}
                                                                        <tr>
                                                                            <td>Photograph (Passport size)</td>
                                                                            <td>

                                                                            </td>
                                                                            <td></td>
                                                                            <td>
                                                                                <input type="file" id="docs_joining_photo_file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][photo][file]">
                                                                                <div>
                                                                                    <img id="joining_photo_preview" src="" alt="Photo preview"
                                                                                        style="display:none;max-width:120px;max-height:120px;margin-top:8px;border:1px solid #ddd;padding:4px;border-radius:6px;" />
                                                                                </div>
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][photo][remarks]"
                                                                                    placeholder="For ID card / records">
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Resume</td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td><input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][cv][file]">
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][cv][remarks]"
                                                                                    placeholder="Resume at the time of joining">
                                                                            </td>
                                                                        </tr>

                                                                        {{-- Required ones you always want new uploads for --}}
                                                                        <tr>
                                                                            <td>Passport Copy with Address<span
                                                                                    class="text-danger">*</span>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][passport_visa][number]"
                                                                                    placeholder="Passport Number">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control date-picker"
                                                                                    name="docs[joining][passport_visa][expiry]">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][passport_visa][file]">
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][passport_visa][remarks]"
                                                                                    placeholder="Passport bio page + UAE visa page">
                                                                            </td>
                                                                        </tr>


                                                                        <tr>
                                                                            <td>Offer Letter</td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td><input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][offer_letter][file]">
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][offer_letter][remarks]"
                                                                                    placeholder="Signed by employee & HR">
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Bank Account Details (IBAN
                                                                                Letter)</td>
                                                                            <td>
                                                                                {{-- <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][iban_letter][number]"
                                                                                    placeholder="IBAN Number"> --}}
                                                                            </td>
                                                                            <td></td>
                                                                            <td>
                                                                                <input type="file" id="docs_joining_iban_file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][iban_letter][file][]" multiple>
                                                                                <div id="iban_letter_docs_container" style="margin-top:8px"></div>
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][iban_letter][remarks]"
                                                                                    placeholder="Mandatory for payroll/WPS">
                                                                            </td>
                                                                        </tr>



                                                                        <tr>
                                                                            <td>Professional Certifications</td>
                                                                            <td>
                                                                                {{-- <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][prof_certs][number]"
                                                                                    placeholder="Certification Name/Number"> --}}
                                                                            </td>
                                                                            <td></td>
                                                                            <td>
                                                                                {{-- prefill will show existing certs and keep this visible to add more --}}
                                                                                <input type="file" id="docs_joining_prof_certs_file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][prof_certs][file][]" multiple>
                                                                                <div id="prof_certs_docs_container" style="margin-top:8px"></div>
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][prof_certs][remarks]"
                                                                                    placeholder="Optional for technical roles">
                                                                            </td>
                                                                        </tr>


                                                                        <tr>
                                                                            <td>Police NOC Certificate</td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td><input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][police_noc][file]">
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][police_noc][remarks]"
                                                                                    placeholder="If applicable">
                                                                            </td>
                                                                        </tr>

                                                                           <tr>
                                                                            <td>Relieving Letter</td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td><input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control" multiple
                                                                                    name="docs[joining][relieving_letter][file][]">
                                                                            </td>

                                                                            <td><input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][relieving_letter][remarks]"
                                                                                    placeholder="From previous employer(s)">
                                                                            </td>
                                                                        </tr>

                                                                      

                                                                        <tr>
                                                                            <td>Academic Certificates</td>
                                                                            <td>
                                                                                {{-- <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][academic][number]"
                                                                                    placeholder="Degree / Diploma Name/Number"> --}}
                                                                            </td>
                                                                            <td>
                                                                                {{-- <input type="text"
                                                                                    class="form-control date-picker"
                                                                                    name="docs[joining][academic][expiry]"> --}}
                                                                            </td>
                                                                            <td>
                                                                                <input type="file" id="docs_joining_academic_file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][academic][file][]" multiple>
                                                                                <div id="academic_docs_container" style="margin-top:8px"></div>
                                                                            </td>

                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][academic][remarks]"
                                                                                    placeholder="Verified/attested copies">
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>Medical Fitness Certificate</td>
                                                                            <td></td>
                                                                            <td>
                                                                                {{-- <input type="text"
                                                                                    class="form-control date-picker"
                                                                                    name="docs[joining][medical_fit][expiry]"> --}}
                                                                            </td>
                                                                            <td>
                                                                                <input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][medical_fit][file]">
                                                                            </td>

                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][medical_fit][remarks]"
                                                                                    placeholder="Required for visa processing">
                                                                            </td>
                                                                        </tr>

                                                                          <tr>
                                                                            <td>Employment Contract (optional)</td>
                                                                            <td></td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control date-picker"
                                                                                    name="docs[joining][emp_contract][expiry]">
                                                                            </td>
                                                                            <td>
                                                                                <input type="file"
                                                                                    accept=".pdf,image/*"
                                                                                    class="form-control"
                                                                                    name="docs[joining][emp_contract][file]">
                                                                            </td>

                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    name="docs[joining][emp_contract][remarks]"
                                                                                    placeholder="MOHRE / Free Zone contract">
                                                                            </td>
                                                                        </tr>



                                                                        {{-- Add your other rows (emp_contract, medical_fit, academic, etc.) the same way --}}
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
                    </div>

                    {{-- <div class="text-center my-4">
                        <button type="submit" id="btnSaveAllBottom"
                            class="btn btn-light btn-lg px-5 d-flex align-items-center justify-content-center mx-auto"
                            style="min-width:220px">
                            <i class="ico icon-outline-bookmark-opened text-success" style="font-size: 16px"></i>
                            <span>Save</span>
                        </button>
                    </div> --}}




                </form>
            </div>
        </section>

        {{-- Bank Modal --}}
        <div class="modal fade" id="bankModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Bank Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="bankForm">
                        @csrf
                        <input type="hidden" name="bank_id" id="bank_id">

                        <div class="modal-body">
                            <div class="row gy-2">
                                <div class="col-md-6">
                                    <label>Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label>Branch</label>
                                    <input type="text" class="form-control" id="branch_name" name="branch_name">
                                </div>

                                <div class="col-md-6">
                                    <label>Account Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="account_holder"
                                        name="account_holder" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Bank Account Number</label>
                                    <input type="text" class="form-control" id="account_number"
                                        name="account_number">
                                </div>

                                <div class="col-md-6">
                                    <label>IBAN Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="iban_number" name="iban_number"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label>SWIFT / IFSC Code</label>
                                    <input type="text" class="form-control" id="swift_code" name="swift_code">
                                </div>

                                <div class="col-md-6">
                                    <label>Currency</label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="currency"
                                            id="currency">
                                       
                                            @foreach ($currencies as $value)
                                                <option value="{{ @$value->id }}">
                                                    {{ @$value->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label>IBAN Letter (Attachment)</label>
                                    <input type="file" class="form-control" id="iban_letter" name="iban_letter"
                                        accept=".pdf,image/*">
                                    <small class="text-muted" id="existingIbanLetter">Allowed: PDF, images</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" id="btn_save_bank">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Education Modal --}}
        <div class="modal fade" id="educationModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Education</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="educationForm">
                        @csrf
                        <input type="hidden" name="education_id" id="education_id">

                        <div class="modal-body">
                            <div class="row gy-2">
                                <div class="col-md-6">
                                    <label>Highest Qualification <span class="text-danger">*</span></label>
                                    <select class="form-control js-example-basic-single" name="qualification"
                                        required>
                                        <option value="">-Select-</option>
                                        <option>High School</option>
                                        <option>Diploma</option>
                                        <option>Bachelor</option>
                                        <option>Master</option>
                                        <option>Certification</option>
                                        <option>PhD</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Board / University <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="university" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Specialization</label>
                                    <input type="text" class="form-control" name="specialization">
                                </div>

                                <div class="col-md-6">
                                    <label>Year of Completion</label>
                                    <input type="text" class="form-control" name="year" placeholder="YYYY">
                                </div>

                                <div class="col-md-6">
                                    <label>Result</label>
                                    <input type="text" class="form-control" name="result"
                                        placeholder="Pass / Division">
                                </div>

                                <div class="col-md-6">
                                    <label>GPA / CGPA</label>
                                    <input type="number" step="any" class="form-control" name="gpa">
                                </div>

                                <div class="col-md-6">
                                    <label>Mode of Study</label>
                                    <select class="form-control js-example-basic-single" name="mode">
                                        <option value="">-Select-</option>
                                        <option>Full-Time</option>
                                        <option>Part-Time</option>
                                        <option>Distance</option>
                                        <option>Online</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Country of Study</label>
                                    <select class="form-control js-example-basic-single" name="country"
                                        id="edu_country">
                                        <option value="">-Select-</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Duration (Years)</label>
                                    <input type="number" step="any" class="form-control" name="duration">
                                </div>

                                <div class="col-md-6">
                                    <label>Certificate Upload <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="education_certificate"
                                        name="certificate" accept=".pdf,image/*">
                                    <small class="text-muted" id="existingCertificate">Allowed: PDF, images</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" id="btn_save_education">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Experience Modal --}}
        <div class="modal fade" id="experienceModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Professional Experience</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="experienceForm">
                        @csrf
                        <input type="hidden" name="experience_id" id="experience_id">

                        <div class="modal-body">
                            <div class="row g-3">

                                <!-- LEFT COLUMN -->
                                <div class="col-lg-12 col-md-12">
                                    <div class="row g-3">

                                        <div class="col-6">
                                            <label class="form-label">
                                                Previous Organization Name<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" name="organization" required>
                                        </div>


                                        <div class="col-3">
                                            <label class="form-label">Previous Designation</label>
                                            <input type="text" class="form-control" name="designation">
                                        </div>

                                        <div class="col-3">
                                            <label class="form-label">Employment Duration</label>

                                            <div class="input-group">
                                                <input type="number" min="0" class="form-control"
                                                    name="years" placeholder="Years">



                                                <input type="number" min="0" max="11"
                                                    class="form-control" name="months" placeholder="Months">


                                            </div>
                                        </div>


                                        <div class="col-6">
                                            <label class="form-label">Experience Certificate (Attachment)</label>
                                            <input type="file" class="form-control" id="exp_certificate"
                                                name="certificate" accept=".pdf,image/*">
                                            <small class="text-muted d-block mt-1"
                                                id="existingExpCertificate">Allowed: PDF, images</small>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label">Key Responsibilities</label>
                                            <textarea class="form-control capitalize-title" name="responsibilities" rows="4"></textarea>
                                        </div>





                                    </div>
                                </div>

                                <!-- RIGHT COLUMN -->
                                {{-- <div class="col-lg-6 col-md-12">
                                    <div class="row g-3">
                                        



                                    </div>
                                </div> --}}

                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-light add-btn ms-2" id="btn_save_experience">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Religion Add Modal --}}
        <div class="modal side-panel fade" id="religionAddModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="religionAddModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="religionAddModalLabel">Enter Religion</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Form -->
                    <form id="religionAddForm">
                        @csrf

                        <div class="modal-body pt-3">

                            <!-- Department Name -->
                            <label class="form-label">
                                Religion Name <span class="text-danger">*</span>
                            </label>

                            <input type="text" class="form-control" id="religion_name" autofocus name="title"
                                required autocomplete="off" style="padding: 2px 5px;">

                            <!-- Footer -->
                            <div class="modal-footer d-flex justify-content-center p-0 pt-3">

                                <button type="submit" id="saveReligionBtn"
                                    class="btn btn-light add-btn d-flex align-items-center gap-2"
                                    style="
                                color: var(--color-btn-light);
                                border: 1px solid var(--color-btn-light-border);
                                background-color: var(--color-btn-light-bg);
                                font-size: 12px;
                                padding: 3px 10px;
                                border-radius: 8px;
                                min-height: 25px;
                            "
                                    data-busy-text="Saving...">

                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>

                                    <i class="ico icon-outline-bookmark-opened text-success"
                                        style="font-size:20px"></i>

                                    <span class="btn-text">Submit</span>
                                </button>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal  fade" id="declarationModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="declarationModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-lg draggable" style="left: 25%">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h4 class="modal-title" id="declarationModalLabel">Employee Declaration</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-4">
                        <!-- Declaration Content -->
                        <div class="declaration-box bg-light border rounded p-2 mb-4"
                            style="max-height: 300px; overflow-y: auto;">
                            <h6 class="fw-semibold mb-3 text-dark">Declaration Statement</h6>
                            <p class="text-dark mb-3" style="line-height: 1.8; text-align: justify;font-size:13px">
                                I hereby declare that all information provided by me in this onboarding form is true,
                                complete, and accurate to the best of my knowledge and belief.
                            </p>
                            <p class="text-dark mb-3" style="line-height: 1.8; text-align: justify;">
                                I understand and acknowledge that:
                            </p>
                            <ul class="text-dark mb-0" style="line-height: 1.8;">
                                <li class="mb-2" style="font-size:12px">Any false, misleading, or incomplete information may result in
                                    disciplinary action, up to and including termination of employment.</li>
                              
                                <li class="mb-0" style="font-size:12px">The company reserves the right to verify the information provided
                                    and may conduct background checks as necessary.</li>
                            </ul>
                        </div>

                       <!-- Declaration Box -->
<div class="p-3 bg-white border rounded">

    <div class="form-check mb-2">
        <input class="form-check-input declaration-agree-checkbox"
               type="checkbox"
               value="1"
               id="declarationAgreeCheckbox1">
        <label class="form-check-label fw-semibold text-dark"
               for="declarationAgreeCheckbox1"
               style="line-height: 1.6;">
            I confirm that the above information provided by me is true and correct.
        </label>
    </div>

    <div class="form-check">
        <input class="form-check-input declaration-agree-checkbox"
               type="checkbox"
               value="1"
               id="declarationAgreeCheckbox2">
        <label class="form-check-label fw-semibold text-dark"
               for="declarationAgreeCheckbox2"
               style="line-height: 1.6;">
            I understand that my personal data will be used only for employment, payroll, visa, and legal compliance purposes.
        </label>
    </div>

</div>



                    </div>

                    <div class="modal-footer">

                        <button type="button" id="declarationAgreeBtn" class="btn btn-light text-dark" disabled>
                            <i class="ico icon-outline-check-square text-success"></i>
                            Agree & Submit
                        </button>
                    </div>



                </div>
            </div>
        </div>








    </div>





    <!-- Flatpickr CSS DATE -->
    <link rel="stylesheet" href="{{ asset('public/design') }}/assets/css/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Flatpickr CSS DATE -->


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>




    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // When a modal is opened, reattach Select2 dropdown inside that modal
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.js-example-basic-single').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%'
                    });
                });
            });
        });
    </script>




    <script>
        $(document).ready(function() {

            // Initialize Bootstrap popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-popover="popover"]'));
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    delay: {
                        show: 500,
                        hide: 100
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {
            var bankIndex = 0;
            var educationIndex = 0;
            var experienceIndex = 0;

            function createHidden(name, value) {
                return $('<input>').attr({
                    type: 'hidden',
                    name: name,
                    value: value
                });
            }

            // Add Bank row from modal into the bank table
            $('#btn_save_bank').on('click', function(e) {
                e.preventDefault();

                var bank_name = $('#bank_name').val() || '';
                var branch_name = $('#branch_name').val() || '';
                var account_holder = $('#account_holder').val() || '';
                var account_number = $('#account_number').val() || '';
                var iban_number = $('#iban_number').val() || '';
                var swift_code = $('#swift_code').val() || '';
                // read currency from the bank modal specifically (value for submission, text for display)
                var currencyVal = $('#bankModal').find('select[name="currency"]').val() || '';
                var currencyText = $('#bankModal').find('select[name="currency"] option:selected').text() ||
                    currencyVal;

                if (!bank_name.trim()) {
                    toastr.error('Bank Name is required');
                    return;
                }

                var fileInput = $('#iban_letter');
                var fileName = '';
                var movedFile = false;
                if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                    var file = fileInput[0].files[0];
                    var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif',
                        'image/webp'
                    ];
                    if ($.inArray(file.type, allowedTypes) === -1) {
                        toastr.error('Only PDF or image files are allowed for IBAN Letter.');
                        return;
                    }
                    fileName = file.name;
                    movedFile = true;
                }

                var row = $('<tr class="bank-row"></tr>');
                var hidden = $('<div class="d-none"></div>');
                // use explicit index so PHP receives structured arrays: banks[0][bank_name], banks[0][iban_letter], etc.
                hidden.append(createHidden('banks[' + bankIndex + '][bank_name]', bank_name));
                hidden.append(createHidden('banks[' + bankIndex + '][branch_name]', branch_name));
                hidden.append(createHidden('banks[' + bankIndex + '][account_holder]', account_holder));
                hidden.append(createHidden('banks[' + bankIndex + '][account_number]', account_number));
                hidden.append(createHidden('banks[' + bankIndex + '][iban_number]', iban_number));
                hidden.append(createHidden('banks[' + bankIndex + '][swift_code]', swift_code));
                hidden.append(createHidden('banks[' + bankIndex + '][currency]', currencyVal));

                // move the actual file input into the hidden container so the file is submitted
                if (movedFile) {
                    // move the bank's file input into the bank hidden container so it is submitted as part of the bank
                    fileInput.attr('name', 'banks[' + bankIndex + '][iban_letter]');
                    fileInput.attr('id', 'iban_letter_' + bankIndex);
                    fileInput.detach();
                    hidden.append(fileInput);

                    // Also create a hidden file input copy for docs[joining][iban_letter][file][] so all IBAN letters are available in the documents section
                    try {
                        var dtDocs = new DataTransfer();
                        dtDocs.items.add(file);
                        var docsInput = $("<input type='file' class='d-none'>");
                        var docsId = 'docs_iban_file_' + bankIndex;
                        docsInput.attr('name', 'docs[joining][iban_letter][file][]');
                        docsInput.attr('id', docsId);
                        docsInput[0].files = dtDocs.files;
                        $('#bankInputsContainer').append(docsInput);

                        // Show a visual entry in the IBAN docs container for user awareness and allow removal
                        var $label = $("<div class='iban-doc-item d-flex align-items-center mb-1' data-docs-id='" + docsId + "'></div>");
                        $label.append($('<span>').text(fileName).css({'margin-right':'8px'}));
                        $label.append($('<button type="button" class="btn btn-sm btn-light remove-iban-doc" data-docs-id="'+docsId+'"><i class="ico icon-outline-minus-square text-danger"></i></button>'));
                        $('#iban_letter_docs_container').append($label);

                        // Link the bank row with this docs id so deletion can clean up both
                        row.attr('data-docs-id', docsId);
                    } catch (err) {
                        console.error('Failed to duplicate IBAN letter for docs:', err);
                    }
                }

                row.append($('<td>').text(bank_name).append(hidden));
                row.append($('<td>').text(branch_name));
                row.append($('<td>').text(account_holder));
                row.append($('<td>').text(account_number));
                row.append($('<td>').text(iban_number));
                row.append($('<td>').text(swift_code));
                row.append($('<td>').text(currencyText));
                row.append($('<td>').text(fileName));
                row.append($('<td>').html(
                    '<button type="button" class="btn btn-sm btn-light btn-delete-bank"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                ));

                // Remove placeholder row if present
                $('#bankTableBody').find('#no-bank-row').remove();

                $('#bankTableBody').append(row);

                // Reset the modal form. If we moved the file input, recreate a fresh one in the modal
                $('#bankForm')[0].reset();
                if (movedFile) {
                    var newFile = $(
                        '<input type="file" class="form-control" id="iban_letter" name="iban_letter" accept=".pdf,image/*">'
                    );
                    $('#existingIbanLetter').before(newFile);
                }

                bankIndex++;

                $('#bankModal').modal('hide');
            });

            // Remove bank row (also clean any associated docs IBAN hidden inputs and UI)
            $('#bankTableBody').on('click', '.btn-delete-bank', function() {
                var $tr = $(this).closest('tr');
                var docsId = $tr.attr('data-docs-id');
                if (docsId) {
                    // remove the hidden file input duplicated for docs
                    $('#' + docsId).remove();
                    // remove visible label in the docs container
                    $('#iban_letter_docs_container').find('[data-docs-id="' + docsId + '"]').remove();
                }

                $tr.remove();

                // if no bank rows remain, show the placeholder
                if ($('#bankTableBody').find('.bank-row').length === 0) {
                    $('#bankTableBody').append(
                        '<tr id="no-bank-row">\n                                                                            <td colspan="9" class="text-center text-muted">\n                                                                                No bank accounts added yet.\n                                                                            </td>\n                                                                        </tr>'
                    );
                }
            });

            // Allow user to manually remove a duplicated IBAN doc entry (clear hidden input and UI)
            $(document).on('click', '.remove-iban-doc', function() {
                var id = $(this).data('docs-id');
                if (id) {
                    $('#' + id).remove();
                    $(this).closest('.iban-doc-item').remove();
                }
            });

            // Allow user to manually remove a duplicated Prof-Cert doc entry (clear hidden input and UI)
            $(document).on('click', '.remove-prof-cert-doc', function() {
                var id = $(this).data('docs-id');
                if (id) {
                    $('#' + id).remove();
                    $(this).closest('.prof-cert-doc-item').remove();
                }
            });

            // Allow user to manually remove a duplicated Academic doc entry (clear hidden input and UI)
            $(document).on('click', '.remove-academic-doc', function() {
                var id = $(this).data('docs-id');
                if (id) {
                    $('#' + id).remove();
                    $(this).closest('.academic-doc-item').remove();
                }
            });

            // When user selects files directly in the visible IBAN docs input, list them and support per-file removal
            $(document).on('change', '#docs_joining_iban_file', function() {
                var $container = $('#iban_letter_docs_container');
                // remove only manual-selected items (preserve bank-added docs with data-docs-id)
                $container.find('.iban-doc-item[data-manual-id]').remove();
                var files = this.files || [];
                for (var i = 0; i < files.length; i++) {
                    (function(idx, f) {
                        var id = 'manual_iban_' + idx + '_' + Date.now();
                        var $item = $("<div class='iban-doc-item d-flex align-items-center mb-1' data-manual-id='"+id+"' data-index='"+idx+"'></div>");
                        $item.append($('<span>').text(f.name).css({'margin-right':'8px'}));
                        var $btn = $('<button type="button" class="btn btn-sm btn-light remove-manual-iban" data-index="'+idx+'"><i class="ico icon-outline-minus-square text-danger"></i></button>');
                        $item.append($btn);
                        $container.append($item);
                    })(i, files[i]);
                }
            });

            // When user selects files directly in the visible Prof-Certs docs input, list them and support per-file removal
            $(document).on('change', '#docs_joining_prof_certs_file', function() {
                var $container = $('#prof_certs_docs_container');
                // remove only manual-selected items (preserve experience-added docs with data-docs-id)
                $container.find('.prof-cert-doc-item[data-manual-id]').remove();
                var files = this.files || [];
                for (var i = 0; i < files.length; i++) {
                    (function(idx, f) {
                        var id = 'manual_prof_cert_' + idx + '_' + Date.now();
                        var $item = $("<div class='prof-cert-doc-item d-flex align-items-center mb-1' data-manual-id='"+id+"' data-index='"+idx+"'></div>");
                        $item.append($('<span>').text(f.name).css({'margin-right':'8px'}));
                        var $btn = $('<button type="button" class="btn btn-sm btn-light remove-manual-prof-cert" data-index="'+idx+'"><i class="ico icon-outline-minus-square text-danger"></i></button>');
                        $item.append($btn);
                        $container.append($item);
                    })(i, files[i]);
                }
            });

            // When user selects files directly in the visible Academic docs input, list them and support per-file removal
            $(document).on('change', '#docs_joining_academic_file', function() {
                var $container = $('#academic_docs_container');
                // remove only manual-selected items (preserve education-added docs with data-docs-id)
                $container.find('.academic-doc-item[data-manual-id]').remove();
                var files = this.files || [];
                for (var i = 0; i < files.length; i++) {
                    (function(idx, f) {
                        var id = 'manual_academic_' + idx + '_' + Date.now();
                        var $item = $("<div class='academic-doc-item d-flex align-items-center mb-1' data-manual-id='"+id+"' data-index='"+idx+"'></div>");
                        $item.append($('<span>').text(f.name).css({'margin-right':'8px'}));
                        var $btn = $('<button type="button" class="btn btn-sm btn-light remove-manual-academic" data-index="'+idx+'"><i class="ico icon-outline-minus-square text-danger"></i></button>');
                        $item.append($btn);
                        $container.append($item);
                    })(i, files[i]);
                }
            });

            // Remove a manual IBAN doc selected in the visible input (rebuild FileList without that index)
            $(document).on('click', '.remove-manual-iban', function() {
                var idx = parseInt($(this).data('index'));
                var input = document.getElementById('docs_joining_iban_file');
                if (!input) return;
                var dt = new DataTransfer();
                for (var i = 0; i < input.files.length; i++) {
                    if (i === idx) continue; // skip removed
                    dt.items.add(input.files[i]);
                }
                input.files = dt.files;
                // trigger change to refresh the listing
                $(input).trigger('change');
            });

            // Remove a manual prof-cert doc selected in the visible input (rebuild FileList without that index)
            $(document).on('click', '.remove-manual-prof-cert', function() {
                var idx = parseInt($(this).data('index'));
                var input = document.getElementById('docs_joining_prof_certs_file');
                if (!input) return;
                var dt = new DataTransfer();
                for (var i = 0; i < input.files.length; i++) {
                    if (i === idx) continue; // skip removed
                    dt.items.add(input.files[i]);
                }
                input.files = dt.files;
                // trigger change to refresh the listing
                $(input).trigger('change');
            });

            // Remove a manual academic doc selected in the visible input (rebuild FileList without that index)
            $(document).on('click', '.remove-manual-academic', function() {
                var idx = parseInt($(this).data('index'));
                var input = document.getElementById('docs_joining_academic_file');
                if (!input) return;
                var dt = new DataTransfer();
                for (var i = 0; i < input.files.length; i++) {
                    if (i === idx) continue; // skip removed
                    dt.items.add(input.files[i]);
                }
                input.files = dt.files;
                // trigger change to refresh the listing
                $(input).trigger('change');
            });

            // Keyboard navigation inside Bank Modal: Enter jumps to next field and triggers Save on last field
            $('#bankForm').on('keydown', 'input, select, textarea', function(e) {
                if (e.key !== 'Enter') return;
                var $el = $(this);
                // allow Enter inside textarea for newline
                if ($el.is('textarea')) return;
                e.preventDefault();

                var $focusables = $('#bankForm').find('input, select, textarea')
                    .filter(':visible:enabled')
                    .not(
                        '[type="file"], [type="hidden"], [type="checkbox"], [type="radio"], [type="submit"], [type="button"]'
                    );

                var idx = $focusables.index(this);
                if (idx === -1) return;

                if (idx + 1 < $focusables.length) {
                    $focusables.eq(idx + 1).focus();
                } else {
                    // We're at the last field — click the Save button
                    // $('#btn_save_bank').trigger('click');
                }
            });

            // Add Education row from modal into the education table
            $('#btn_save_education').on('click', function(e) {
                e.preventDefault();

                var $modal = $('#educationModal');
                var qualification = $modal.find('select[name="qualification"]').val() || '';
                var university = $modal.find('input[name="university"]').val() || '';
                var specialization = $modal.find('input[name="specialization"]').val() || '';
                var year = $modal.find('input[name="year"]').val() || '';
                var result = $modal.find('input[name="result"]').val() || '';
                var gpa = $modal.find('input[name="gpa"]').val() || '';
                var mode = $modal.find('select[name="mode"]').val() || '';
                var country = $modal.find('select[name="country"]').val() || '';
                var countryText = $modal.find('select[name="country"] option:selected').text() || '';
                var duration = $modal.find('input[name="duration"]').val() || '';

                if (!qualification.trim()) {
                    toastr.error('Qualification is required');
                    return;
                }
                if (!university.trim()) {
                    toastr.error('Board / University is required');
                    return;
                }

                var fileInput = $modal.find('#education_certificate');
                var fileName = '';
                var movedFile = false;
                if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                    var file = fileInput[0].files[0];
                    var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif',
                        'image/webp'
                    ];
                    if ($.inArray(file.type, allowedTypes) === -1) {
                        toastr.error('Only PDF or image files are allowed for Certificate.');
                        return;
                    }
                    fileName = file.name;
                    movedFile = true;
                }

                var row = $('<tr class="education-row"></tr>');
                var hidden = $('<div class="d-none"></div>');

                hidden.append(createHidden('educations[' + educationIndex + '][qualification]',
                    qualification));
                hidden.append(createHidden('educations[' + educationIndex + '][university]', university));
                hidden.append(createHidden('educations[' + educationIndex + '][specialization]',
                    specialization));
                hidden.append(createHidden('educations[' + educationIndex + '][year]', year));
                hidden.append(createHidden('educations[' + educationIndex + '][result]', result));
                hidden.append(createHidden('educations[' + educationIndex + '][gpa]', gpa));
                hidden.append(createHidden('educations[' + educationIndex + '][mode]', mode));
                hidden.append(createHidden('educations[' + educationIndex + '][country]', country));
                hidden.append(createHidden('educations[' + educationIndex + '][duration]', duration));

                if (movedFile) {
                    fileInput.attr('name', 'educations[' + educationIndex + '][certificate]');
                    fileInput.attr('id', 'education_certificate_' + educationIndex);
                    fileInput.detach();
                    hidden.append(fileInput);

                    // Duplicate this education certificate into docs[joining][academic][file][]
                    try {
                        var dtDocs = new DataTransfer();
                        dtDocs.items.add(file);
                        var docsInput = $("<input type='file' class='d-none'>");
                        var docsId = 'docs_academic_file_' + educationIndex;
                        docsInput.attr('name', 'docs[joining][academic][file][]');
                        docsInput.attr('id', docsId);
                        docsInput[0].files = dtDocs.files;
                        $('#educationInputsContainer').append(docsInput);

                        // Show a visible label in the academic docs container
                        var $label = $("<div class='academic-doc-item d-flex align-items-center mb-1' data-docs-id='" + docsId + "'></div>");
                        $label.append($('<span>').text(fileName).css({'margin-right':'8px'}));
                        $label.append($('<button type="button" class="btn btn-sm btn-light remove-academic-doc" data-docs-id="'+docsId+'"><i class="ico icon-outline-minus-square text-danger"></i></button>'));
                        $('#academic_docs_container').append($label);

                        // Link the education row with this docs id so deletion can clean up both
                        row.attr('data-docs-id', docsId);
                    } catch (err) {
                        console.error('Failed to duplicate education certificate for docs:', err);
                    }
                }

                row.append($('<td>').text(qualification).append(hidden));
                row.append($('<td>').text(university));
                row.append($('<td>').text(specialization));
                row.append($('<td>').text(year));
                row.append($('<td>').text(result));
                row.append($('<td>').text(gpa));
                row.append($('<td>').text(mode));
                row.append($('<td>').text(countryText));
                row.append($('<td>').text(duration));
                row.append($('<td>').text(fileName));
                row.append($('<td>').html(
                    '<button type="button" class="btn btn-sm btn-light btn-delete-education"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                ));

                // Remove placeholder row if present
                $('#educationTableBody').find('.no-education-row').remove();

                $('#educationTableBody').append(row);

                // Reset the modal form. If we moved the file input, recreate a fresh one in the modal
                $modal.find('form')[0].reset();
                if (movedFile) {
                    var newFile = $(
                        '<input type="file" class="form-control" id="education_certificate" name="certificate" accept=".pdf,image/*">'
                    );
                    $modal.find('#existingCertificate').before(newFile);
                }

                educationIndex++;

                $('#educationModal').modal('hide');
            });

            // Remove education row (also clean any associated docs Academic hidden inputs and UI)
            $('#educationTableBody').on('click', '.btn-delete-education', function() {
                var $tr = $(this).closest('tr');
                var docsId = $tr.attr('data-docs-id');
                if (docsId) {
                    // remove the hidden file input duplicated for docs
                    $('#' + docsId).remove();
                    // remove visible label in the academic docs container
                    $('#academic_docs_container').find('[data-docs-id="' + docsId + '"]').remove();
                }

                $tr.remove();
                // if no education rows remain, show the placeholder
                if ($('#educationTableBody').find('.education-row').length === 0) {
                    $('#educationTableBody').append(
                        '<tr class="no-education-row">\n                                                                            <td colspan="11" class="text-center text-muted">\n                                                                                No education records added yet.\n                                                                            </td>\n                                                                        </tr>'
                    );
                }
            });

            // Keyboard navigation inside Education Modal: Enter jumps to next field and triggers Save on last field
            $('#educationForm').on('keydown', 'input, select, textarea', function(e) {
                if (e.key !== 'Enter') return;
                var $el = $(this);
                // allow Enter inside textarea for newline
                if ($el.is('textarea')) return;
                e.preventDefault();

                var $focusables = $('#educationForm').find('input, select, textarea')
                    .filter(':visible:enabled')
                    .not(
                        '[type="file"], [type="hidden"], [type="checkbox"], [type="radio"], [type="submit"], [type="button"]'
                    );

                var idx = $focusables.index(this);
                if (idx === -1) return;

                if (idx + 1 < $focusables.length) {
                    var $next = $focusables.eq(idx + 1);
                    $next.focus();
                    // if next is a Select2, open it
                    if ($next.hasClass('js-example-basic-single')) {
                        setTimeout(function() {
                            $next.select2('open');
                        }, 60);
                    }
                } else {
                    // We're at the last field — click the Save button
                    // $('#btn_save_education').trigger('click');
                }
            });

            // Keyboard navigation inside Experience Modal: Enter jumps to next field and triggers Save on last field
            $('#experienceForm').on('keydown', 'input, select, textarea', function(e) {
                if (e.key !== 'Enter') return;
                var $el = $(this);
                // allow Enter inside textarea for newline
                if ($el.is('textarea')) return;
                e.preventDefault();

                var $focusables = $('#experienceForm').find('input, select, textarea')
                    .filter(':visible:enabled')
                    .not(
                        '[type="file"], [type="hidden"], [type="checkbox"], [type="radio"], [type="submit"], [type="button"]'
                    );

                var idx = $focusables.index(this);
                if (idx === -1) return;

                if (idx + 1 < $focusables.length) {
                    var $next = $focusables.eq(idx + 1);
                    $next.focus();
                    // if next is a Select2, open it
                    if ($next.hasClass('js-example-basic-single')) {
                        setTimeout(function() {
                            $next.select2('open');
                        }, 60);
                    }
                } else {
                    // We're at the last field — click the Save button
                    // $('#btn_save_experience').trigger('click');
                }
            });

            // Add Experience row from modal into the experience table
            $('#btn_save_experience').on('click', function(e) {
                e.preventDefault();

                var $modal = $('#experienceModal');
                var organization = $modal.find('input[name="organization"]').val() || '';
                var designation = $modal.find('input[name="designation"]').val() || '';
                var years = $modal.find('input[name="years"]').val() || '';
                var months = $modal.find('input[name="months"]').val() || '';
                var responsibilities = $modal.find('textarea[name="responsibilities"]').val() || '';

                if (!organization.trim()) {
                    toastr.error('Previous Organization is required');
                    return;
                }

                // Basic numeric checks for duration
                if (years !== '' && (!/^[0-9]+$/.test(years) || parseInt(years, 10) < 0)) {
                    toastr.error('Years must be a non-negative integer');
                    return;
                }
                if (months !== '' && (!/^[0-9]+$/.test(months) || parseInt(months, 10) < 0 || parseInt(
                        months, 10) > 12)) {
                    toastr.error('Months must be an integer between 1 and 12');
                    return;
                }

                var fileInput = $modal.find('#exp_certificate');
                var fileName = '';
                var movedFile = false;
                if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                    var file = fileInput[0].files[0];
                    var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif',
                        'image/webp'
                    ];
                    if ($.inArray(file.type, allowedTypes) === -1) {
                        toastr.error('Only PDF or image files are allowed for Experience Certificate.');
                        return;
                    }
                    fileName = file.name;
                    movedFile = true;
                }

                var row = $('<tr class="experience-row"></tr>');
                var hidden = $('<div class="d-none"></div>');

                hidden.append(createHidden('experiences[' + experienceIndex + '][organization]',
                    organization));
                hidden.append(createHidden('experiences[' + experienceIndex + '][designation]',
                    designation));
                hidden.append(createHidden('experiences[' + experienceIndex + '][years]', years));
                hidden.append(createHidden('experiences[' + experienceIndex + '][months]', months));
                hidden.append(createHidden('experiences[' + experienceIndex + '][responsibilities]',
                    responsibilities));

                if (movedFile) {
                    fileInput.attr('name', 'experiences[' + experienceIndex + '][certificate]');
                    fileInput.attr('id', 'exp_certificate_' + experienceIndex);
                    fileInput.detach();
                    hidden.append(fileInput);

                    // Also duplicate this certificate into docs[joining][prof_certs][file][] so it appears in Joining Documents
                    try {
                        var dtDocs = new DataTransfer();
                        dtDocs.items.add(file);
                        var docsInput = $("<input type='file' class='d-none'>");
                        var docsId = 'docs_prof_cert_file_' + experienceIndex;
                        docsInput.attr('name', 'docs[joining][prof_certs][file][]');
                        docsInput.attr('id', docsId);
                        docsInput[0].files = dtDocs.files;
                        $('#experienceInputsContainer').append(docsInput);

                        // Show a visual entry in the prof certs docs container for user awareness and allow removal
                        var $label = $("<div class='prof-cert-doc-item d-flex align-items-center mb-1' data-docs-id='" + docsId + "'></div>");
                        $label.append($('<span>').text(fileName).css({'margin-right':'8px'}));
                        $label.append($('<button type="button" class="btn btn-sm btn-light remove-prof-cert-doc" data-docs-id="'+docsId+'"><i class="ico icon-outline-minus-square text-danger"></i></button>'));
                        $('#prof_certs_docs_container').append($label);

                        // Link the experience row with this docs id so deletion can clean up both
                        row.attr('data-docs-id', docsId);
                    } catch (err) {
                        console.error('Failed to duplicate experience certificate for docs:', err);
                    }
                }

                row.append($('<td>').text(organization).append(hidden));
                row.append($('<td>').text(designation));
                row.append($('<td>').text((years ? years : 0) + ' Y, ' + (months ? months : 0) + ' M'));
                row.append($('<td>').text(responsibilities));
                row.append($('<td>').text(fileName));
                row.append($('<td>').html(
                    '<button type="button" class="btn btn-sm btn-light btn-delete-experience"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size: 16px;"></i></button>'
                ));

                // Remove placeholder row if present
                $('#experienceTableBody').find('.no-experience-row').remove();

                $('#experienceTableBody').append(row);

                // Reset the modal form. If we moved the file input, recreate a fresh one in the modal
                $modal.find('form')[0].reset();
                if (movedFile) {
                    var newFile = $(
                        '<input type="file" class="form-control" id="exp_certificate" name="certificate" accept=".pdf,image/*">'
                    );
                    $modal.find('#existingExpCertificate').before(newFile);
                }

                experienceIndex++;

                $('#experienceModal').modal('hide');
            });

            // Remove experience row (also clean any associated docs Prof-Cert hidden inputs and UI)
            $('#experienceTableBody').on('click', '.btn-delete-experience', function() {
                var $tr = $(this).closest('tr');
                var docsId = $tr.attr('data-docs-id');
                if (docsId) {
                    // remove the hidden file input duplicated for docs
                    $('#' + docsId).remove();
                    // remove visible label in the prof certs docs container
                    $('#prof_certs_docs_container').find('[data-docs-id="' + docsId + '"]').remove();
                }

                $tr.remove();
                // if no experience rows remain, show the placeholder
                if ($('#experienceTableBody').find('.experience-row').length === 0) {
                    $('#experienceTableBody').append(
                        '<tr class="no-experience-row">\n                                                                            <td colspan="6" class="text-center text-muted">\n                                                                                No experience records added yet.\n                                                                            </td>\n                                                                        </tr>'
                    );
                }
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#long-list > tbody > tr', function(e) {
                // prevent triggering when clicking inside a nested table
                if ($(e.target).closest('table').attr('id') !== 'long-list') {
                    return;
                }

                if ($(e.target).closest('td').hasClass('no-toggle')) {
                    return; // do nothing if inside excluded cells
                }

                $(this).toggleClass('expand');
            });

            // Setup country/state change handlers safely if that function exists
            if (typeof setupCountryStateChange === 'function') {
                setupCountryStateChange('#perm_country', '#perm_state');
                setupCountryStateChange('#curr_country', '#curr_state');
            }
        });
    </script>

    {{-- Toastr flash messages from controller --}}
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>

    {{-- Auto-lowercase email inputs (live + on submit) --}}
    <script>
        (function() {
            // Lowercase as user types, preserve cursor position when possible
            $(document).on('input', 'input[type="email"]', function() {
                var el = this;
                var val = el.value || '';
                var lower = val.toLowerCase();
                if (val !== lower) {
                    try {
                        var start = el.selectionStart;
                        var end = el.selectionEnd;
                        el.value = lower;
                        el.setSelectionRange(start, end);
                    } catch (e) {
                        // fallback if selection not supported
                        el.value = lower;
                    }
                }
            });

            // Extra safety: ensure all email inputs are lowercased before any form submit
            $(document).on('submit', 'form', function() {
                $(this).find('input[type="email"]').each(function() {
                    this.value = (this.value || '').toLowerCase();
                });

                // Also uppercase IBAN/SWIFT fields before submit as a safety net
                $(this).find('#iban_number, #swift_code').each(function() {
                    this.value = (this.value || '').toUpperCase();
                });
            });

            // ---------- Uppercase IBAN and SWIFT on input (live) ----------
            $(document).on('input', '#iban_number, #swift_code', function() {
                var el = this;
                var val = el.value || '';
                var upper = val.toUpperCase();
                if (val !== upper) {
                    try {
                        var s = el.selectionStart;
                        var e = el.selectionEnd;
                        el.value = upper;
                        el.setSelectionRange(s, e);
                    } catch (err) {
                        el.value = upper;
                    }
                }
            });
        })();
    </script>

    <script>
        document.querySelectorAll('input').forEach(i => {
            i.setAttribute('autocomplete', 'nope'); // or 'disable-autocomplete'
        });

        flatpickr(".date-picker", {
            dateFormat: "d/m/Y", // dd/mm/yyyy
            allowInput: true
        });
    </script>



    <script>
        $(document).ready(function() {
            // Auto-add class 'capitalize-title' to inputs that should be title-cased
            $('input').each(function() {
                var $i = $(this);
                var type = ($i.attr('type') || '').toLowerCase();
                var name = ($i.attr('name') || '').toLowerCase();

                // Skip excluded input types
                var excludedTypes = ['email', 'tel', 'date', 'file', 'number', 'hidden', 'checkbox',
                    'radio', 'submit', 'button', 'password'
                ];
                if (type && excludedTypes.indexOf(type) !== -1) return;

                // Skip date-picker, selects and inputs that look like contact details
                if ($i.hasClass('date-picker')) return;
                if (name.match(
                        /email|mobile|phone|password|iban_number|swift_code|currency|docs\[joining\]\[passport_visa\]\[number\]|docs\[joining\]\[iban_letter\]\[number\]|docs\[joining\]\[prof_certs\]\[number\]|docs\[joining\]\[academic\]\[number\]/
                    )) return;

                $i.addClass('capitalize-title');
            });

            // Title-case inputs having class 'capitalize-title'
            $(document).on('input', '.capitalize-title', function() {
                var val = $(this).val() || '';
                // Make everything lowercase first, then uppercase word-start letters
                val = val.toLowerCase().replace(/\b\w/g, function(c) {
                    return c.toUpperCase();
                });
                $(this).val(val);
            });
        });
    </script>

    <script>
        (function() {
            var $form = $('#staffAllForm');
            var allowSubmit = false;

            // Move focus to the next logical input on Enter (for inputs/selects only). Textareas and select2 search are excluded.
            $form.on('keydown', 'input, select, textarea, .select2-search__field', function(e) {
                if (e.key !== 'Enter') return;

                var $target = $(e.target);

                // Allow Enter inside textarea for newline
                if ($target.is('textarea')) return;

                // If typing in Select2 search, let Select2 handle selection (do not move focus here)
                if ($target.hasClass('select2-search__field')) return;

                // Prevent any default submit triggered by Enter
                e.preventDefault();

                // Build a list of focusable form controls (visible & enabled), exclude types we don't want to focus
                var focusable = $form.find('input, select, textarea, button, [tabindex]').filter(
                    ':visible:enabled').filter(function() {
                    var $el = $(this);
                    var t = ($el.attr('type') || '').toLowerCase();
                    if (['hidden', 'submit', 'button', 'file', 'checkbox', 'radio'].indexOf(t) !== -1)
                        return false;
                    return true;
                });

                var idx = focusable.index(this);

                // Find next focusable control
                var found = false;
                for (var i = idx + 1; i < focusable.length; i++) {
                    var $next = focusable.eq(i);
                    if ($next.length) {
                        $next.focus();
                        // If it's a Select2 field, open the dropdown search to make selection smooth
                        if ($next.hasClass('js-example-basic-single') || $next.hasClass('js-product-select') ||
                            $next.hasClass('js-account-select')) {
                            setTimeout(function($el) {
                                return function() {
                                    try {
                                        $el.select2('open');
                                    } catch (e) {}
                                };
                            }($next), 50);
                        }
                        found = true;
                        break;
                    }
                }

                // If there is no next field, focus the main Save button
                if (!found) {
                    $('#btnSaveAll').focus();
                }

            });

            // Only allow form submission when Save buttons are clicked (not via Enter)
            $('#btnSaveAll, #btnSaveAllBottom').on('click', function(e) {
                e.preventDefault();

                // Use HTML5 constraint validation for required fields
                if (typeof $form[0].checkValidity === 'function' && !$form[0].checkValidity()) {
                    // focus first invalid element and show user-friendly message
                    var firstInvalid = $form[0].querySelector(':invalid');
                    if (firstInvalid) {
                        $(firstInvalid).addClass('is-invalid').focus();
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Please fill all required fields before proceeding.');
                    } else {
                        alert('Please fill all required fields before proceeding.');
                    }
                    return;
                }

                // Show declaration modal and wire Agree handler to submit the form
                $('#declarationModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#declarationModal').modal('show');

                // Reset checkbox and button state each time
                $('.declaration-agree-checkbox').prop('checked', false);
                $('#declarationAgreeBtn').prop('disabled', true);

                // Ensure single binding
                $('#declarationAgreeBtn').off('click.declare').on('click.declare', function() {
                    // mark allowed and submit
                    allowSubmit = true;

                    // Prevent double clicks and visual feedback
                    $('#declarationAgreeBtn').prop('disabled', true).addClass('disabled');

                    $('#declarationModal').modal('hide');
                    setTimeout(function() {
                        // Use native submit to perform an actual form POST (this will respect HTML5 validation)
                        try {
                            $form[0].submit();
                        } catch (err) {
                            // Fallback: trigger jQuery submit event (should rarely be needed)
                            $form.trigger('submit');
                        }
                    }, 10);
                });
            });

            // Intercept any form submit and block unless allowed explicitly
            $form.on('submit', function(e) {
                if (!allowSubmit) {
                    e.preventDefault();
                    return false;
                }
                // reset flag to prevent accidental resubmits
                allowSubmit = false;
                return true;
            });

            // Extra safety: prevent implicit submits from other sources
            // (for example, plugins that call form.submit()), by overriding native submit
            var nativeSubmit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = function() {
                if (this.id === 'staffAllForm' && !allowSubmit) {
                    // Ignore programmatic submit unless allowed
                    return;
                }
                nativeSubmit.apply(this, arguments);
            };
        })();
    </script>



    <script>
        // Declaration modal behaviour: enable Agree only when ALL declaration checkboxes are checked
        $(document).off('change', '.declaration-agree-checkbox').on('change', '.declaration-agree-checkbox', function() {
            var total = $('.declaration-agree-checkbox').length;
            var checked = $('.declaration-agree-checkbox:checked').length;
            $('#declarationAgreeBtn').prop('disabled', checked !== total);
        });

        // Accessibility: allow Enter on checkbox to toggle and enable Agree
        $(document).off('keypress', '.declaration-agree-checkbox').on('keypress', '.declaration-agree-checkbox', function(e) {
            if (e.key === 'Enter') {
                $(this).prop('checked', !$(this).prop('checked')).trigger('change');
            }
        });

        $("#perm_country").on('change', function() {
            $("#loading_bg").css("display", "block");
            var country_id = $('#perm_country').val();
            var ajaxUrl = "{{ url('get_state_employee') }}";
            if (!country_id) {
                // clear dependent selects and stop
                $('#perm_state').find('option').not(':first').remove();
                $('#perm_city').find('option').not(':first').remove();
                $("#loading_bg").css("display", "none");
                return;
            }
            $.ajax({
                type: "GET",
                data: {
                    country_id: country_id
                },
                dataType: 'json',
                url: ajaxUrl,
                success: function(data) {
                    console.log(data);
                    var a = '';
                    $.each(data, function(i, item) {
                        if (item.length) {

                            $('#perm_state').find('option').not(':first').remove();
                            $('#sectionStateDiv ul').find('li').not(':first').remove();

                            $('#perm_city').find('option').not(':first').remove();
                            $('#sectionCityDiv ul').find('li').not(':first').remove();

                            $.each(item, function(i, pin) {
                                $('#perm_state').append($('<option>', {
                                    value: pin.id,
                                    text: pin.name
                                }));

                                $("#perm_state_div ul").append("<li data-value='" +
                                    pin.id + "' value='" + pin.id +
                                    "' class='option'>" + pin.name + "</li>");
                            });
                            GLOBAL_STATE_CHANGE_TRIGGER =
                                true; // Set the flag to true to indicate state change
                            if (window.SELECTED_STATE_ID) {
                                $('#perm_state').val(window.SELECTED_STATE_ID).trigger(
                                    'change');
                            }
                        } else {
                            $('#perm_state_div .current').html('');
                            $('#perm_state').find('option').not(':first').remove();
                            $('#sectionPermStateDiv ul').find('li').not(':first').remove();
                            $('#perm_city').find('option').not(':first').remove();
                            $('#sectionPermCityDiv .current').html('');


                        }
                    });
                    console.log(a);
                    $("#loading_bg").css("display", "none");
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
    </script>


    <script>
        $("#curr_country").on('change', function() {
            $("#loading_bg").css("display", "block");
            var country_id = $('#curr_country').val();
            var ajaxUrl = "{{ url('get_state_employee') }}";
            if (!country_id) {
                // clear dependent selects and stop
                $('#curr_state').find('option').not(':first').remove();
                $('#curr_city').find('option').not(':first').remove();
                $("#loading_bg").css("display", "none");
                return;
            }
            $.ajax({
                type: "GET",
                data: {
                    country_id: country_id
                },
                dataType: 'json',
                url: ajaxUrl,
                success: function(data) {
                    console.log(data);
                    var a = '';
                    $.each(data, function(i, item) {
                        if (item.length) {

                            $('#curr_state').find('option').not(':first').remove();
                            $('#sectionStateDiv ul').find('li').not(':first').remove();

                            $('#curr_city').find('option').not(':first').remove();
                            $('#sectionCurrStateDiv ul').find('li').not(':first').remove();

                            $.each(item, function(i, pin) {
                                $('#curr_state').append($('<option>', {
                                    value: pin.id,
                                    text: pin.name
                                }));

                                $("#curr_state_div ul").append("<li data-value='" +
                                    pin.id + "' value='" + pin.id +
                                    "' class='option'>" + pin.name + "</li>");
                            });
                            GLOBAL_STATE_CHANGE_TRIGGER =
                                true; // Set the flag to true to indicate state change
                            if (window.SELECTED_STATE_ID) {
                                $('#curr_state').val(window.SELECTED_STATE_ID).trigger(
                                    'change');
                            }
                        } else {
                            $('#curr_state_div .current').html('');
                            $('#curr_state').find('option').not(':first').remove();
                            $('#sectionCurrStateDiv ul').find('li').not(':first').remove();
                            $('#curr_city').find('option').not(':first').remove();
                            $('#sectionCurrCityDiv .current').html('');


                        }
                    });
                    console.log(a);
                    $("#loading_bg").css("display", "none");
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
    </script>




    <!-- Password validator styles + script -->
    <style>
        .password-validator {
            position: fixed;
            /* use fixed so it positions relative to viewport */
            z-index: 2500;
            min-width: 260px;
            max-width: 90vw;
            /* responsive */
            box-sizing: border-box;
            background: linear-gradient(180deg, #ffffff 0%, #fbfbfd 100%);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
            padding: 12px 14px;
            font-size: 13px;
            color: #111827;
            transition: transform 160ms ease, opacity 160ms ease;
        }

        .password-validator .pv-arrow {
            position: absolute;
            width: 12px;
            height: 12px;
            transform: rotate(45deg);
            background: #fff;
            left: 16px;
            top: -7px;
            border-left: 1px solid rgba(0, 0, 0, 0.06);
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .password-validator .pv-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--bs-gray-900, #111827);
            font-size: 14px
        }

        .pv-progress {
            height: 6px;
            background: rgba(15, 23, 42, 0.06);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .pv-progress-bar {
            width: 0%;
            height: 100%;
            background: #ef4444;
            border-radius: 6px;
            transition: width 240ms ease, background-color 240ms ease
        }

        .password-validator .pv-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: block
        }

        .password-validator .pv-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            color: #6b7280;
            font-size: 13px
        }

        .pv-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            font-size: 11px;
            line-height: 1
        }

        .pv-item.valid .pv-dot {
            background: #16a34a;
            color: #fff
        }

        .pv-item.valid .pv-dot::after {
            content: '\2713';
            font-size: 11px
        }

        .pv-item.invalid .pv-dot {
            background: rgba(0, 0, 0, 0.06);
            color: transparent
        }

        .pv-text {
            color: #374151
        }

        .pv-tip {
            margin-top: 8px;
            font-size: 12px
        }

        .password-validator.d-none {
            display: none;
        }
    </style>

    <script>
        (function($) {
            var $win = $(window);

            function updatePosition($input, $box) {
                if (!$input.length || !$box.length) return;
                var rect = $input[0].getBoundingClientRect();
                var boxW = $box.outerWidth();
                var boxH = $box.outerHeight();
                var margin = 8;
                var top = rect.bottom + margin;
                var left = rect.left;
                var viewportW = window.innerWidth || document.documentElement.clientWidth;
                var viewportH = window.innerHeight || document.documentElement.clientHeight;

                // Shift left if overflowing right edge
                if (left + boxW > viewportW - margin) {
                    left = Math.max(margin, viewportW - boxW - margin);
                }

                // If bottom overflows, place above input if possible
                if (top + boxH > viewportH - margin) {
                    var alt = rect.top - boxH - margin;
                    if (alt > margin) {
                        top = alt;
                    } else {
                        // clamp inside viewport
                        top = Math.max(margin, viewportH - boxH - margin);
                    }
                }

                // Apply fixed positioning so it stays visible with scrolling
                $box.css({
                    position: 'fixed',
                    top: Math.round(top) + 'px',
                    left: Math.round(left) + 'px'
                });
            }

            function checkPassword(p) {
                return {
                    length: p.length >= 8,
                    lower: /[a-z]/.test(p),
                    upper: /[A-Z]/.test(p),
                    digit: /\d/.test(p),
                    // exclude whitespace so spaces are not considered special characters
                    special: /[^A-Za-z0-9\s]/.test(p)
                };
            }

            $(function() {
                var $input = $('#password');
                var $box = $('#password-validator');
                if (!$input.length || !$box.length) return;

                // show on focus / typing, hide on blur or when all valid (after short delay)
                $input.on('focus input', function(e) {
                    // show with subtle animation
                    $box.removeClass('d-none').css({
                        opacity: 0,
                        transform: 'translateY(6px)'
                    }).animate({
                        opacity: 1,
                        opacity: 1
                    }, 120, function() {
                        $box.css('transform', 'translateY(0)');
                    });
                    updatePosition($input, $box);
                    var p = $input.val() || '';
                    var state = checkPassword(p);
                    var total = 5;
                    var matched = 0;
                    $box.find('[data-criteria]').each(function() {
                        var c = $(this).data('criteria');
                        var ok = !!state[c];
                        $(this).toggleClass('valid', ok).toggleClass('invalid', !ok);
                        if (ok) matched++;
                    });
                    var pct = Math.round((matched / total) * 100);
                    $box.find('.pv-progress-bar').css({
                        width: pct + '%'
                    });
                    // set color and title by strength
                    if (pct === 100) {
                        $box.find('.pv-progress-bar').css('background', '#16a34a');
                        $box.find('.pv-title').text('Excellent password');
                    } else if (pct >= 75) {
                        $box.find('.pv-progress-bar').css('background', '#65a30d');
                        $box.find('.pv-title').text('Strong password');
                    } else if (pct >= 50) {
                        $box.find('.pv-progress-bar').css('background', '#f59e0b');
                        $box.find('.pv-title').text('Weak password');
                    } else {
                        $box.find('.pv-progress-bar').css('background', '#ef4444');
                        $box.find('.pv-title').text('Choose a stronger password');
                    }
                    if (pct === 100) {
                        setTimeout(function() {
                            $box.addClass('d-none');
                        }, 700);
                    }
                });

                $input.on('blur', function() {
                    setTimeout(function() {
                        $box.addClass('d-none');
                    }, 250);
                });

                // reposition on window resize / scroll while visible
                $win.on('resize scroll', function() {
                    if (!$box.hasClass('d-none')) updatePosition($input, $box);
                });
            });
        })(jQuery);
    </script>

    <script>
        function isValidPassword(password) {
            const minLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasDigit = /\d/.test(password);
            const hasSpecial = /[^A-Za-z0-9\s]/.test(password); // ❌ excludes space

            return minLength && hasUpper && hasLower && hasDigit && hasSpecial;
        }

        // 🚫 Prevent space from being entered
        $(document).on('keydown', '#password', function(e) {
            if (e.key === ' ') {
                e.preventDefault();
            }
        });

        // 🔄 Realtime validation (typing)
        $(document).on('input', '#password', function() {
            const val = $(this).val();
            const valid = isValidPassword(val);

            $(this).toggleClass('is-invalid', !valid);
            $(this).toggleClass('is-valid', valid);
        });

        // ⛔ Final submit guard (VERY IMPORTANT)
        $(document).on('submit', 'form', function(e) {
            const password = $('#password').val();

            if (password && !isValidPassword(password)) {
                e.preventDefault();

                toastr.error(
                    'Password must have at least 8 characters, 1 uppercase, 1 lowercase, 1 digit and 1 special character (no spaces).'
                );

                $('#password')
                    .addClass('is-invalid')
                    .focus();

                return false;
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            var $religionSel = $('#religion');

            // When user selects 'Others', open modal to add a custom religion
            $religionSel.on('change', function() {
                var val = $(this).val();
                if (val === 'Others') {
                    $('#religion_name').val('');
                    $('#religionAddModal').modal('show');
                    setTimeout(function() {
                        $('#religion_name').focus();
                    }, 500);
                }
            });

            // Handle adding the custom religion (UI only, not persisted to DB)
            $('#religionAddForm').on('submit', function(e) {
                e.preventDefault();
                var name = ($('#religion_name').val() || '').trim();
                if (!name) {
                    toastr.error('Religion name is required');
                    $('#religion_name').focus();
                    return;
                }

                // Case-insensitive check for existing option
                var found = null;
                $religionSel.find('option').each(function() {
                    if ($(this).text().trim().toLowerCase() === name.toLowerCase()) {
                        found = $(this);
                        return false;
                    }
                });

                if (found) {
                    // Option exists - select it
                    $religionSel.val(found.val()).trigger('change');
                } else {
                    // Add new option and select it
                    var safeVal = name; // use the exact text as the value to make storage straightforward
                    var $opt = $('<option>').val(safeVal).text(name);
                    $religionSel.append($opt);

                    // Select it
                    $religionSel.val(safeVal).trigger('change');
                }

                // Keep a hidden input so server can easily detect a custom religion if needed
                if ($('#religion_custom_input').length) {
                    $('#religion_custom_input').val(name);
                } else {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'religion_custom_input',
                        name: 'religion_custom',
                        value: name
                    }).appendTo('#staffAllForm');
                }

                // If using Select2, ensure it refreshes (select2 listens to change)
                if ($religionSel.hasClass('js-example-basic-single') && typeof $religionSel.select2 ===
                    'function') {
                    $religionSel.trigger('change.select2');
                }

                toastr.success('Religion "' + name + '" added successfully');
                $('#religionAddModal').modal('hide');
            });

            // If user closes modal without adding, revert the select if it still shows 'Others'
            $('#religionAddModal').on('hidden.bs.modal', function() {
                if ($religionSel.val() === 'Others') {
                    $religionSel.val('').trigger('change');
                }
            });

            // If user selects a different existing religion, remove the custom hidden input
            $religionSel.on('change', function() {
                var v = $(this).val();
                var $hid = $('#religion_custom_input');
                if ($hid.length && $hid.val() !== v) {
                    $hid.remove();
                }
            });
        });
    </script>

    <script>
        (function() {

            let dragging = false;
            let startX, startY, startLeft, startTop;
            let currentModal = null;

            // Bind drag start
            $(document).on('mousedown', '.modal-dialog .modal-header', function(e) {
                currentModal = $(this).closest('.modal-dialog');

                dragging = true;

                startX = e.clientX;
                startY = e.clientY;

                const offset = currentModal.offset();
                startLeft = offset.left;
                startTop = offset.top;

                $('body').addClass('unselectable'); // Prevents text selection while dragging
            });

            // Dragging movement
            $(document).on('mousemove', function(e) {
                if (!dragging || !currentModal) return;

                let newLeft = startLeft + (e.clientX - startX);
                let newTop = startTop + (e.clientY - startY);

                currentModal.offset({
                    top: newTop,
                    left: newLeft
                });
            });

            // Stop drag
            $(document).on('mouseup', function() {
                dragging = false;
                $('body').removeClass('unselectable');
            });

            // Reset modal on open (production behavior)
            $(document).on('show.bs.modal', '.modal', function() {
                let dialog = $(this).find('.modal-dialog.draggable');
                dialog.css({
                    top: '10%',
                    // left: '65%',
                    transform: 'translateX(-50%)'
                });
            });

        })();
    </script>

    <script>
        $(document).ready(function() {
            // Mirror staff photo into the joining docs input and show a preview
            var staffInput = document.querySelector('input[name="staff_photo"]');
            var joiningInput = document.querySelector('input[name="docs[joining][photo][file]"]');
            var preview = document.getElementById('joining_photo_preview');
            var lastPreviewUrl = null;

            function clearPreview() {
                if (lastPreviewUrl) {
                    try { URL.revokeObjectURL(lastPreviewUrl); } catch (e) {}
                    lastPreviewUrl = null;
                }
                if (preview) {
                    preview.src = '';
                    preview.style.display = 'none';
                }
            }

            function updatePreviewFromFile(file) {
                if (!preview) return;
                if (!file) { clearPreview(); return; }
                if (file.type && file.type.indexOf('image/') === 0) {
                    var url = URL.createObjectURL(file);
                    clearPreview();
                    lastPreviewUrl = url;
                    preview.src = url;
                    preview.style.display = 'block';
                } else {
                    // Not an image — clear preview (could show icon in future)
                    clearPreview();
                }
            }

            if (staffInput && joiningInput) {
                $(staffInput).on('change', function() {
                    var dt = new DataTransfer();
                    if (staffInput.files && staffInput.files.length > 0) {
                        for (var i = 0; i < staffInput.files.length; i++) {
                            dt.items.add(staffInput.files[i]);
                        }
                        joiningInput.files = dt.files;
                        updatePreviewFromFile(staffInput.files[0]);
                    } else {
                        joiningInput.files = dt.files;
                        clearPreview();
                    }
                });

                // If user selects a file directly into the joining docs input, update preview too
                $(joiningInput).on('change', function() {
                    if (joiningInput.files && joiningInput.files.length > 0) {
                        updatePreviewFromFile(joiningInput.files[0]);
                    } else {
                        clearPreview();
                    }
                });

                // If the form is reset, make sure to clear the joined file & preview as well
                $(document).on('reset', '#staffAllForm', function() {
                    var dt = new DataTransfer();
                    joiningInput.files = dt.files;
                    clearPreview();

                    // remove any docs IBAN hidden inputs added when banks were added
                    $('#bankInputsContainer').empty();
                    $('#iban_letter_docs_container').empty();

                    // remove any duplicated prof-cert hidden inputs added when experiences were added
                    $('#experienceInputsContainer').empty();
                    $('#prof_certs_docs_container').empty();

                    // remove any duplicated academic hidden inputs added when educations were added
                    $('#educationInputsContainer').empty();
                    $('#academic_docs_container').empty();

                    // also clear any manual-selected file inputs
                    var docsIban = document.getElementById('docs_joining_iban_file');
                    if (docsIban) docsIban.value = '';
                    var docsProf = document.getElementById('docs_joining_prof_certs_file');
                    if (docsProf) docsProf.value = '';
                    var docsAcad = document.getElementById('docs_joining_academic_file');
                    if (docsAcad) docsAcad.value = '';
                });

                // If page loads with a pre-filled file input (editing flow), try to preview it
                if (joiningInput.files && joiningInput.files.length > 0) {
                    updatePreviewFromFile(joiningInput.files[0]);
                }
            }
        });
    </script>

</html>

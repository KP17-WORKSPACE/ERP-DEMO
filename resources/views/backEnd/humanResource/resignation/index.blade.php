@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <style>
        .form-scroll {
            overflow-y: auto;
            padding-right: 6px;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
            color: #dc3545;
        }

        .small-dropdown-font option {
            font-size: 10px !important;
        }

        .small-dropdown-font {
            font-size: 10px !important;
        }

        .select2-results__option {
            font-size: 11px !important;
        }

        .tab-wrap {
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }

        .tab-wrap .nav.nav-tabs {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 4px !important;
            min-width: max-content !important;
            padding: 0 15px !important;
            white-space: nowrap !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .tab-wrap .nav.nav-tabs .nav-item {
            flex: 0 0 auto !important;
        }

        .tab-wrap .nav.nav-tabs .nav-link {
            white-space: nowrap !important;
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .tab-wrap::-webkit-scrollbar {
            height: 4px;
        }

        .tab-wrap::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }
    </style>
    @php
        $editMode = isset($editMode) && $editMode === true;
        $staffData = $staffData ?? (isset($editData) ? $editData : null);
        $job = $job ?? (isset($jobRow) ? $jobRow : null);
        if (!isset($requestNo)) {
            $companyId = session('logged_session_data.company_id') ?: (Auth::user()->company_id ?? 1);
            $companyCode = DB::table('sys_company')->where('id', $companyId)->value('other_code') ?: 'D';
            $requestNo = 'ES' . $companyCode . '-NEW';
        }
    @endphp
    <div class="form-scroll">
        <form id="goods-receipt-note-store-form" novalidate action="{{ route('staff.resignation.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="saved_staff_id" name="staff_id" value="{{ $staffData->id ?? '' }}">
            @if($editMode && isset($eosData['main']))
                <input type="hidden" name="eos_id" value="{{ $eosData['main']->id }}">
            @endif

            <div class="content-container col-12">
                <div class="tab-content display-flex-tabs" id="endOfServiceTabContent">
                    <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                        <div class="purchase-order-content-header">
                            <h4 class="purchase-order-content-header-left">
                                Staff End of Service {{ $editMode ? '(' . $requestNo . ')' : '(New ' . $requestNo . ')' }}
                            </h4>
                            <span id="saveAllMsg" class="ms-2"></span>
                            <div class="purchase-order-content-header-right d-flex align-items-center">
                                <button type="submit" class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                                    id="btnSaveAll">
                                    <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                                    <span class="btn-text">Save</span>
                                </button>
                                <div class="dropdown" style="display:inline-block;margin-left:5px;">
                                    <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false" title="Menu">
                                        <i class="ico icon-outline-hamburger-menu"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('staff.resignation.list') }}">
                                                <i class="ico icon-outline-list-down text-success"></i> End Of Service List
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ url('staff-directory') }}">
                                                <i class="ico icon-outline-users-group-rounded text-success"></i> Staff
                                                Directory
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
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
                                        <div class="white-box">
                                            <div class="staff">
                                                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                                                <div class="row mb-30">
                                                    @include('backEnd.humanResource.resignation.partials._basic_details', ['staffs' => $staffs, 'departments' => $departments, 'eosData' => $eosData, 'staffData' => $staffData, 'editMode' => $editMode, 'isAdmin' => $isAdmin ?? false, 'job' => $job ?? null])
                                                    {{-- TABS SECTION --}}
                                                    <div class="tab-wrap mb-3">
                                                        <ul class="nav nav-tabs mt-4" id="eosTab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="notice-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#notice"
                                                                    type="button" role="tab">
                                                                    Resignation & Notice Period
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="handover-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#handover"
                                                                    type="button" role="tab">
                                                                    Handover Process
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="asset-tab" data-bs-toggle="tab"
                                                                    data-bs-target="#asset" type="button" role="tab">
                                                                    Asset Clearance
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="it-tab" data-bs-toggle="tab"
                                                                    data-bs-target="#it" type="button" role="tab">
                                                                    IT & Access Clearance
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="eos-calc-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#eos-calc"
                                                                    type="button" role="tab">
                                                                    EOS Calculation
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="final-settlement-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#final-settlement"
                                                                    type="button" role="tab">
                                                                    Final Settlement
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="legal-tab" data-bs-toggle="tab"
                                                                    data-bs-target="#legal" type="button" role="tab">
                                                                    Legal & Compliance
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="exit-interview-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#exit-interview"
                                                                    type="button" role="tab">
                                                                    Exit Interview
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="approval-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#approval"
                                                                    type="button" role="tab">
                                                                    Approval Status
                                                                </button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="documents-tab"
                                                                    data-bs-toggle="tab" data-bs-target="#documents"
                                                                    type="button" role="tab">
                                                                    Documents
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="tab-content mt-3" id="eosTabContent">
                                                        {{-- TAB 1: Resignation & Notice Period --}}
                                                        @include('backEnd.humanResource.resignation.partials._notice_period', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._handover_process', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._asset_clearance', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._it_access_clearance', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._eos_calculation', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._final_settlement', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._legal_compliance', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._exit_interview', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        @include('backEnd.humanResource.resignation.partials._approval_status', ['eosData' => $eosData, 'editMode' => $editMode, 'staffs' => $staffs])
                                                        @include('backEnd.humanResource.resignation.partials._documents', ['eosData' => $eosData, 'editMode' => $editMode])
                                                        {{-- END TABS SECTION --}}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {

            var staffDetailsMap = {!! json_encode($staffs->mapWithKeys(function ($staff) {
        return [
            $staff->id => [
                'department_id' => $staff->department_id,
                'designation_id' => $staff->designation_id,
                'reporting_manager' => $staff->reporting_manager
            ]
        ];
    })) !!};

            var pendingDesignationId = @json(old('designation_id', isset($eosData['main']) ? $eosData['main']->designation_id : null));

            // Auto-populate when employee changes
            $('#employee_id').on('change', function () {
                var staffId = $(this).val();
                if (staffId && staffDetailsMap[staffId]) {
                    var details = staffDetailsMap[staffId];
                    if (details.department_id) {
                        pendingDesignationId = details.designation_id;
                        $('#department_id').val(details.department_id).trigger('change');
                    }
                    if (details.reporting_manager) {
                        $('#reporting_manager').val(details.reporting_manager).trigger('change');
                    }
                }
            });

            // Initialize Select2
            $('.js-example-basic-single').select2();

            // Form validation
            $('#endOfServiceForm').on('submit', function (e) {
                let isValid = true;

                // Check required fields
                $('[required]').each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    toastr.error('Please fill all required fields');
                }
            });

            // Department change - load designations
            $('#department_id').on('change', function () {
                var departmentId = $(this).val();
                var designationSelect = $('#designation_id');

                // Clear current designations
                designationSelect.empty();
                designationSelect.append('<option value="">Select Designation</option>');

                if (departmentId) {
                    $.ajax({
                        url: "{{ route('staff.resignation.getDesignations') }}",
                        type: 'POST',
                        data: {
                            department_id: departmentId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                $.each(response.data, function (index, designation) {
                                    designationSelect.append('<option value="' +
                                        designation.id + '">' + designation.title +
                                        '</option>');
                                });

                                if (pendingDesignationId) {
                                    designationSelect.val(pendingDesignationId).trigger('change');
                                    pendingDesignationId = null;
                                }
                            }
                        },
                        error: function (xhr) {
                            console.error('Error loading designations');
                        }
                    });
                }
            });

            // Asset table - Add row
            var assetRowIndex = 3;
            $('#addAssetRow').on('click', function () {
                var newRow = `
                        <tr>
                            <td><input type="text" class="form-control form-control-sm" name="assets[${assetRowIndex}][name]" placeholder="Asset Name"></td>
                            <td>
                                <select class="form-select form-select-sm" name="assets[${assetRowIndex}][applicable]">
                                    <option value="na">N/A</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control form-control-sm" name="assets[${assetRowIndex}][serial_number]"></td>
                            <td><input type="text" class="form-control form-control-sm date-picker" name="assets[${assetRowIndex}][return_date]"></td>
                            <td>
                                <select class="form-select form-select-sm asset-condition" name="assets[${assetRowIndex}][condition]">
                                    <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="missing">Missing</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" name="assets[${assetRowIndex}][recovery_amount]"></td>
                            <td>
                                <select class="form-select form-select-sm" name="assets[${assetRowIndex}][verified_by]">
                                    <option value="">Select</option>
                                    <option value="1">IT Admin</option>
                                    <option value="2">HR Manager</option>
                                </select>
                            </td>
                            <td><textarea class="form-control form-control-sm damage-remarks" name="assets[${assetRowIndex}][damage_remarks]" rows="1"></textarea></td>
                            <td><button type="button" class="btn btn-sm btn-light remove-asset-row"><i class="ico icon-outline-trash-bin-minimalistic text-dark"></i></button></td>
                        </tr>
                    `;
                $('#assetTable tbody').append(newRow);
                assetRowIndex++;
            });

            // Asset table - Remove row
            $(document).on('click', '.remove-asset-row', function () {
                $(this).closest('tr').remove();
            });

            // Asset condition change - make remarks mandatory if damaged/missing
            $(document).on('change', '.asset-condition', function () {
                var remarksField = $(this).closest('tr').find('.damage-remarks');
                if ($(this).val() === 'damaged' || $(this).val() === 'missing') {
                    remarksField.attr('required', true);
                    remarksField.addClass('border-warning');
                } else {
                    remarksField.attr('required', false);
                    remarksField.removeClass('border-warning');
                }
            });

            if ($('#department_id').val()) {
                $('#department_id').trigger('change');
            }

        }); // End of document.ready
    </script>


    <script src="{{ asset('public/js/form-validation-toastr.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Initialize form validation for crm-deals-form
            FormValidator.init('goods-receipt-note-store-form', {
                showAllErrors: true,
                scrollToFirst: true,
                highlightFields: true,
                toastrPosition: 'toast-top-right',
                toastrTimeout: 6000
            });
        });
    </script>

@endsection
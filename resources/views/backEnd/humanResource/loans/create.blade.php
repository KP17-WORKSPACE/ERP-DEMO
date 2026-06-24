@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    use Carbon\Carbon;
    $isEdit = isset($loan);
    $employee = $employee ?? optional(Auth::user())->staff;
    $employeeName = optional($employee)->full_name ?: trim(optional($employee)->first_name . ' ' . optional($employee)->last_name);
    $requestDate = $isEdit && $loan->date ? $loan->date : date('Y-m-d');
    $repaymentStart = old('repayment_start', $isEdit && $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('Y-m') : '');
    $repaymentEnd = old('repayment_end_month', $isEdit && $loan->repayment_end_month ? Carbon::parse($loan->repayment_end_month)->format('Y-m') : '');
    $disbursementDate = old('requested_disbursement_date', $isEdit ? $loan->requested_disbursement_date : date('Y-m-d'));
    $reportingManager = '';
    if ($employee && $employee->reporting_manager) {
        $managerIds = array_filter(array_map('trim', explode(',', (string) $employee->reporting_manager)));
        $reportingManager = \App\SmStaff::whereIn('id', $managerIds)->pluck('full_name')->filter()->implode(', ');
        if (!$reportingManager) {
            $reportingManager = \App\User::whereIn('id', $managerIds)->pluck('full_name')->filter()->implode(', ');
        }
    }
@endphp

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="loanTabContent">
        <form id="loanForm" action="{{ $isEdit ? route('employee.loans.update', $loan->id) : route('employee.loans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEdit) @method('PUT') @endif
            <input type="hidden" name="action_type" id="action_type" value="submit">

            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    {{ $isEdit ? 'Edit Loan / Advance Request' : 'Add Loan / Advance Request' }}
                </h4>
                <div class="purchase-order-content-header-right">
                    <button type="button" class="btn btn-light text-dark loan-submit-btn" data-action="draft" data-busy-text="Saving...">
                        <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        <span class="btn-text">Save as Draft</span>
                    </button>
                    <button type="button" class="btn btn-light text-dark loan-submit-btn" data-action="submit" data-busy-text="Submitting...">
                        <i class="ico icon-outline-check text-success btn-icon"></i>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        <span class="btn-text">Submit for Approval</span>
                    </button>
                    <a class="btn btn-light" href="{{ route('employee.loans.index', $isEdit ? ['active' => $loan->id] : []) }}">Cancel</a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif
            <div id="loanFormMsg" class="mb-2"></div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Employee Information</h6>
                    <div class="row row-cols-1 row-cols-lg-4 g-3">
                        <div class="col">
                            <label class="form-label">Request Number</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ $requestNumber }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Request Date</label>
                            <input type="date" class="form-control form-control-sm" readonly value="{{ $requestDate }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Employee Name</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ $employeeName ?: Auth::user()->full_name }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ optional(optional($employee)->departments)->name }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ optional(optional($employee)->designations)->title }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Reporting Manager</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ $reportingManager }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Employment Type</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ optional($employee)->employment_type }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Date of Joining</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ optional($employee)->date_of_joining ? \App\SysHelper::normalizeToDmy($employee->date_of_joining) : '' }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Basic Salary</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ optional($employee)->basic_salary }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Gross Salary</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ optional($employee)->basic_salary }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Current Loan Balance</label>
                            <input type="text" class="form-control form-control-sm" readonly value="{{ number_format((float) $currentLoanBalance, 2) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Loan / Advance Request</h6>
                    <div class="row row-cols-1 row-cols-lg-4 g-3">
                        <div class="col">
                            <label class="form-label">Request Type <span class="text-danger">*</span></label>
                            <select name="request_type" class="form-select form-select-sm" required>
                                <option value="">-- Select --</option>
                                @foreach(['Loan','Salary Advance'] as $type)
                                    <option value="{{ $type }}" {{ old('request_type', $isEdit ? $loan->request_type : 'Loan') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Loan Category <span class="text-danger">*</span></label>
                            <select name="loan_category" class="form-select form-select-sm" required>
                                <option value="">-- Select --</option>
                                @foreach($loanCategories as $category)
                                    <option value="{{ $category }}" {{ old('loan_category', $isEdit ? $loan->loan_category : 'Personal') === $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Amount Requested <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control form-control-sm" value="{{ old('amount', $isEdit ? $loan->amount : '') }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Installment Number <span class="text-danger">*</span></label>
                            <input type="number" min="1" name="installments" id="installments" class="form-control form-control-sm" value="{{ old('installments', $isEdit ? $loan->installments : '') }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Monthly Deduction Amount</label>
                            <input type="number" step="0.01" name="amount_per_month" id="amount_per_month" class="form-control form-control-sm" value="{{ old('amount_per_month', $isEdit ? $loan->amount_per_month : '') }}" readonly>
                        </div>
                        <div class="col">
                            <label class="form-label">Repayment Start Month <span class="text-danger">*</span></label>
                            <input type="month" name="repayment_start" id="repayment_start" class="form-control form-control-sm" value="{{ $repaymentStart }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Repayment End Month</label>
                            <input type="month" name="repayment_end_month" id="repayment_end_month" class="form-control form-control-sm" value="{{ $repaymentEnd }}" readonly>
                        </div>
                        <div class="col">
                            <label class="form-label">Repayment Mode <span class="text-danger">*</span></label>
                            <select name="repayment_mode" class="form-select form-select-sm" required>
                                <option value="">-- Select --</option>
                                @foreach(['Salary Deduction','Bank Transfer','Cash Payment','Adjustment'] as $mode)
                                    <option value="{{ $mode }}" {{ old('repayment_mode', $isEdit ? $loan->repayment_mode : 'Salary Deduction') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Disbursement Date <span class="text-danger">*</span></label>
                            <input type="date" name="requested_disbursement_date" id="requested_disbursement_date" class="form-control form-control-sm" value="{{ $disbursementDate }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Urgency Level <span class="text-danger">*</span></label>
                            <select name="urgency_level" class="form-select form-select-sm" required>
                                <option value="">-- Select --</option>
                                @foreach(['Normal','Urgent','Critical'] as $urgency)
                                    <option value="{{ $urgency }}" {{ old('urgency_level', $isEdit ? $loan->urgency_level : 'Urgent') === $urgency ? 'selected' : '' }}>{{ $urgency }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Early Settlement Allowed</label>
                            <select name="early_settlement_allowed" class="form-select form-select-sm">
                                @foreach(['No','Yes'] as $flag)
                                    <option value="{{ $flag }}" {{ old('early_settlement_allowed', $isEdit ? $loan->early_settlement_allowed : 'No') === $flag ? 'selected' : '' }}>{{ $flag }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">Grace Period Required</label>
                            <select name="grace_period_required" id="grace_period_required" class="form-select form-select-sm">
                                @foreach(['No','Yes'] as $flag)
                                    <option value="{{ $flag }}" {{ old('grace_period_required', $isEdit ? $loan->grace_period_required : 'No') === $flag ? 'selected' : '' }}>{{ $flag }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col" id="grace_period_months_wrap">
                            <label class="form-label">Grace Period Months</label>
                            <input type="number" min="1" name="grace_period_months" class="form-control form-control-sm" value="{{ old('grace_period_months', $isEdit ? $loan->grace_period_months : '') }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <label class="form-label">Reason / Purpose <span class="text-danger">*</span></label>
                            <textarea name="purpose" rows="3" class="form-control form-control-sm" required>{{ old('purpose', $isEdit ? $loan->purpose : '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Guarantor Details</h6>
                    <div class="row row-cols-1 row-cols-lg-4 g-3">
                        <div class="col">
                            <label class="form-label">Guarantor Employee</label>
                            <select name="guarantor_employee_id" id="guarantor_employee_id" class="form-select form-select-sm js-example-basic-single">
                                <option value="">-- Select --</option>
                                @foreach($employees as $staff)
                                    <option value="{{ $staff->id }}" {{ old('guarantor_employee_id', $isEdit ? $loan->guarantor_employee_id : '') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->full_name ?: trim($staff->first_name . ' ' . $staff->last_name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col"><label class="form-label">Guarantor Employee ID</label><input type="text" name="guarantor_employee_no" id="guarantor_employee_no" class="form-control form-control-sm" value="{{ old('guarantor_employee_no', $isEdit ? $loan->guarantor_employee_no : '') }}" readonly></div>
                        <div class="col"><label class="form-label">Guarantor Department</label><input type="text" name="guarantor_department" id="guarantor_department" class="form-control form-control-sm" value="{{ old('guarantor_department', $isEdit ? $loan->guarantor_department : '') }}" readonly></div>
                        <div class="col"><label class="form-label">Guarantor Contact Number</label><input type="text" name="guarantor_contact_number" id="guarantor_contact_number" class="form-control form-control-sm" value="{{ old('guarantor_contact_number', $isEdit ? $loan->guarantor_contact_number : '') }}" readonly></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Attachment with Remarks</h6>
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label class="form-label">Attachment</label>
                            <input type="file" name="attachment" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            @if($isEdit && !empty($loan->attachment))
                                <small class="d-block mt-1">Current: <a href="{{ asset('public/uploads/loan_docs/'.$loan->attachment) }}" target="_blank">View File</a></small>
                            @endif
                        </div>
                        <div class="col-lg-8">
                            <label class="form-label">Remarks</label>
                            <textarea name="attachment_remarks" rows="2" class="form-control form-control-sm">{{ old('attachment_remarks', $isEdit ? $loan->attachment_remarks : '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Employee Declaration</h6>
                    <p class="small mb-2">I confirm that the information provided is true and accurate. I authorize the company to deduct the approved monthly installment from my salary. I agree to follow the company loan/advance policy. I agree that any outstanding balance may be recovered from final settlement if I leave the company. I understand false information may lead to rejection or disciplinary action.</p>
                    @foreach([
                        'declaration_info_confirmed' => 'I confirm that the information provided is true and accurate.',
                        'declaration_salary_deduction_authorized' => 'I authorize salary deduction for approved monthly installments.',
                        'declaration_policy_agreed' => 'I agree to the company loan / salary advance policy.',
                        'declaration_final_settlement_agreed' => 'I agree that outstanding balance may be recovered from my final settlement in case of resignation/termination.',
                    ] as $name => $label)
                        <div class="form-check mb-2">
                            <input class="form-check-input declaration-check" type="checkbox" name="{{ $name }}" value="1" id="{{ $name }}" {{ old($name, $isEdit ? $loan->{$name} : false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $name }}">{{ $label }} <span class="text-danger">*</span></label>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    var $form = $('#loanForm');
    var $msg = $('#loanFormMsg');
    var guarantorTpl = @json(route('employee.loans.guarantor', ['id' => ':id']));

    function setError(text) {
        $msg.html('<div class="alert alert-danger py-2 mb-2">' + text + '</div>');
        $('html, body').animate({ scrollTop: $msg.offset().top - 90 }, 150);
    }

    function clearError() { $msg.html(''); }

    function calcMonthly() {
        var amount = parseFloat($('#amount').val());
        var installments = parseInt($('#installments').val(), 10);
        if (amount > 0 && installments > 0) {
            $('#amount_per_month').val((amount / installments).toFixed(2));
        } else {
            $('#amount_per_month').val('');
        }
        calcRepaymentEnd();
    }

    function calcRepaymentEnd() {
        var start = $('#repayment_start').val();
        var installments = parseInt($('#installments').val(), 10);
        if (!start || !(installments > 0)) {
            $('#repayment_end_month').val('');
            return;
        }
        var parts = start.split('-');
        var date = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, 1);
        date.setMonth(date.getMonth() + installments - 1);
        $('#repayment_end_month').val(date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0'));
    }

    function toggleGrace() {
        var show = $('#grace_period_required').val() === 'Yes';
        $('#grace_period_months_wrap').toggle(show);
        if (!show) $('input[name="grace_period_months"]').val('');
    }

    function validateClient() {
        clearError();
        var required = [
            ['select[name="request_type"]', 'Select Request Type.'],
            ['select[name="loan_category"]', 'Select Loan Category.'],
            ['#amount', 'Enter Amount Requested.'],
            ['#installments', 'Enter Installment Number.'],
            ['#amount_per_month', 'Monthly Deduction Amount is required.'],
            ['#repayment_start', 'Select Repayment Start Month.'],
            ['select[name="repayment_mode"]', 'Select Repayment Mode.'],
            ['#requested_disbursement_date', 'Select Requested Disbursement Date.'],
            ['textarea[name="purpose"]', 'Enter Reason / Purpose.'],
            ['select[name="urgency_level"]', 'Select Urgency Level.']
        ];
        for (var i = 0; i < required.length; i++) {
            if (!$(required[i][0]).val()) { setError(required[i][1]); return false; }
        }
        if (!(parseFloat($('#amount').val()) > 0)) { setError('Amount Requested must be greater than 0.'); return false; }
        if (!(parseInt($('#installments').val(), 10) > 0)) { setError('Installment Number must be greater than 0.'); return false; }
        if ($('#grace_period_required').val() === 'Yes' && !$('input[name="grace_period_months"]').val()) {
            setError('Grace Period Months is required when Grace Period Required is Yes.'); return false;
        }
        if ($('.declaration-check:checked').length !== $('.declaration-check').length) {
            setError('All employee declaration consent checkboxes are required.'); return false;
        }
        return true;
    }

    $('#amount, #installments').on('input', calcMonthly);
    $('#repayment_start').on('change', calcRepaymentEnd);
    $('#grace_period_required').on('change', toggleGrace);

    $('#guarantor_employee_id').on('change', function () {
        var id = $(this).val();
        $('#guarantor_employee_no,#guarantor_department,#guarantor_contact_number').val('');
        if (!id) return;
        $.get(guarantorTpl.replace(':id', encodeURIComponent(id)), function (data) {
            $('#guarantor_employee_no').val(data.employee_id || '');
            $('#guarantor_department').val(data.department || '');
            $('#guarantor_contact_number').val(data.contact_number || '');
        });
    });

    $('.loan-submit-btn').on('click', function () {
        if (!validateClient()) return;
        $('#action_type').val($(this).data('action'));
        var $btn = $(this);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.btn-icon').addClass('d-none');
        $btn.find('.btn-text').text($btn.data('busy-text') || 'Saving...');
        $form.submit();
    });

    calcMonthly();
    toggleGrace();
});
</script>
@endsection

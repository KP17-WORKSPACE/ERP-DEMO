@php
    $leave = $leave ?? null;
    $employees = $employees ?? collect();
    $leaveTypes = $leaveTypes ?? collect();
    $reportingManager = $reportingManager ?? collect();
    $leaveApplicationNo = old('leave_application_no', $leave->leave_application_no ?? ($leaveApplicationNo ?? ''));
    $fmtDate = function ($value) {
        if (empty($value))
            return '';
        try {
            return \App\SysHelper::normalizeToDmy($value);
        } catch (\Exception $e) {
            return '';
        }
    };
    $field = function ($name, $default = '') use ($leave) {
        return old($name, $leave ? ($leave->{$name} ?? $default) : $default);
    };
@endphp

<input type="hidden" name="leave_application_no" value="{{ $leaveApplicationNo }}">

<div class="row gap-rows leave-application-form">
    <div class="col-md-3">
        <label class="form-label">Leave Type*</label>
        <select class="form-control js-example-basic-single" name="type_id" id="leave_type_id" required>
            <option value="">-Select-</option>
            @foreach ($leaveTypes as $lt)
                <option value="{{ $lt->id }}" data-code="{{ $lt->code ?? '' }}" {{ (string) old('type_id', $leave->type_id ?? '') === (string) $lt->id ? 'selected' : '' }}>
                    {{ $lt->name ?? $lt->type ?? ('Type #' . $lt->id) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Leave From Date*</label>
        <input type="text" class="form-control date-picker leave-date-field" name="leave_from" id="leave_from_date"
            value="{{ old('leave_from', $fmtDate($leave->leave_from ?? null)) }}" autocomplete="off" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Leave To Date*</label>
        <input type="text" class="form-control date-picker leave-date-field" name="leave_to" id="leave_to_date"
            value="{{ old('leave_to', $fmtDate($leave->leave_to ?? null)) }}" autocomplete="off" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Number of Leave Days</label>
        <input type="text" class="form-control text-end" name="days_display" id="leave_days_display"
            value="{{ old('days_display', $leave ? $leave->days : '') }}" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Return To Work Date</label>
        <input type="text" class="form-control date-picker" name="return_to_work_date" id="return_to_work_date"
            value="{{ old('return_to_work_date', $fmtDate($leave->return_to_work_date ?? null)) }}" autocomplete="off"
            readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">Leave Category*</label>
        <select class="form-control" name="leave_category" required>
            @foreach (['Paid', 'Unpaid'] as $option)
                <option value="{{ $option }}" {{ $field('leave_category', 'Paid') === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Urgency Level*</label>
        <select class="form-control" name="urgency_level" required>
            @foreach (['Normal', 'Urgent', 'Critical'] as $option)
                <option value="{{ $option }}" {{ $field('urgency_level', 'Normal') === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Nature of Leave*</label>
        <select class="form-control" name="nature_of_leave" required>
            @foreach (['Planned', 'Emergency'] as $option)
                <option value="{{ $option }}" {{ $field('nature_of_leave', 'Planned') === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Requested During Notice Period</label>
        <select class="form-control" name="notice_period">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('notice_period', 'No') === $option ? 'selected' : '' }}>{{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Expected Availability During Leave*</label>
        <select class="form-control" name="availability_during_leave" required>
            @foreach (['Available', 'Limited', 'Not Available'] as $option)
                <option value="{{ $option }}" {{ $field('availability_during_leave', 'Limited') === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Half-Day Leave</label>
        <select class="form-control" name="half_session" id="leave_half_session">
            <option value="NONE" {{ old('half_session', $leave->half_session ?? 'NONE') === 'NONE' || empty(old('half_session', $leave->half_session ?? 'NONE')) ? 'selected' : '' }}>None</option>
            <option value="FIRST_HALF" {{ old('half_session', $leave->half_session ?? '') === 'FIRST_HALF' ? 'selected' : '' }}>First Half</option>
            <option value="SECOND_HALF" {{ old('half_session', $leave->half_session ?? '') === 'SECOND_HALF' ? 'selected' : '' }}>Second Half</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Employee Contact Number During Leave</label>
        <input type="text" class="form-control" name="contact_number_during_leave"
            value="{{ $field('contact_number_during_leave') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Employee Email During Leave</label>
        <input type="email" class="form-control" name="email_during_leave" value="{{ $field('email_during_leave') }}">
    </div>


    <div class="col-md-3">
        <label class="form-label">Handover Required</label>
        <select class="form-control" name="handover_required" id="leave_handover_required">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('handover_required', 'No') === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3" id="leave_handover_employee_box" style="display:none">
        <label class="form-label">To Employee*</label>
        <select class="form-control js-example-basic-single" name="handover_employee_id"
            id="leave_handover_employee_id">
            <option value="">-Select-</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" {{ (string) old('handover_employee_id', $leave->handover_employee_id ?? '') === (string) $employee->id ? 'selected' : '' }}>
                    {{ $employee->full_name ?: trim($employee->first_name . ' ' . $employee->last_name) }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leave-handover-detail-field" style="display:none">
        <label class="form-label">Pending Tasks</label>
        <select class="form-control" name="pending_tasks">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('pending_tasks', 'No') === $option ? 'selected' : '' }}>{{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leave-handover-detail-field" style="display:none">
        <label class="form-label">Access Transfer Required</label>
        <select class="form-control" name="access_transfer_required">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('access_transfer_required', 'No') === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leave-handover-detail-field" style="display:none">
        <label class="form-label">Completion Confirmation</label>
        <select class="form-control" name="handover_completion_confirmation">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('handover_completion_confirmation', 'No') === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leave-handover-detail-field" style="display:none">
        <label class="form-label">Manager Verification</label>
        <select class="form-control" name="manager_verification_of_handover">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('manager_verification_of_handover', 'No') === $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 leave-handover-detail-field" style="display:none">
        <label class="form-label">Client Responsibilities</label>
        <textarea class="form-control" name="client_responsibilities"
            rows="2">{{ $field('client_responsibilities', '') }}</textarea>
    </div>
    <div class="col-md-6 leave-handover-detail-field" style="display:none">
        <label class="form-label">Additional Remarks</label>
        <textarea class="form-control" name="handover_additional_remarks"
            rows="2">{{ $field('handover_additional_remarks', '') }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label">Leaving The Country</label>
        <select class="form-control" name="leaving_country" id="leave_leaving_country">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ $field('leaving_country', 'No') === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leave-travel-field" style="display:none">
        <label class="form-label">Destination Country*</label>
        <input type="text" class="form-control" name="destination_country" id="leave_destination_country"
            value="{{ $field('destination_country') }}">
    </div>
    <div class="col-md-3 leave-travel-field" id="leave_departure_wrapper" style="display:none">
        <label class="form-label">Departure Date*</label>
        <input type="text" class="form-control date-picker" name="departure_date" id="leave_departure_date"
            value="{{ old('departure_date', $fmtDate($leave->departure_date ?? null)) }}" autocomplete="off">
    </div>

    <div class="col-md-3 leave-travel-field" id="leave_return_wrapper" style="display:none">
        <label class="form-label">Expected Return Date*</label>
        <input type="text" class="form-control date-picker" name="expected_return_date" id="leave_expected_return_date"
            value="{{ old('expected_return_date', $fmtDate($leave->expected_return_date ?? null)) }}"
            autocomplete="off">
    </div>
    <div class="col-md-3 leave-travel-field" style="display:none">
        <label class="form-label">Travel Ticket Attached</label>
        @php
            $hasTicket = !empty($leave->travel_ticket_file) ? 'Yes' : 'No';
        @endphp
        <select class="form-control" name="travel_ticket_attached" id="leave_travel_ticket_attached">
            @foreach (['No', 'Yes'] as $option)
                <option value="{{ $option }}" {{ old('travel_ticket_attached', $hasTicket) === $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 leave-travel-ticket-file-box" style="display:none">
        <label class="form-label">Attach Ticket</label>
        <input type="file" class="form-control" name="travel_ticket_file" id="leave_travel_ticket_file"
            accept=".pdf,.jpg,.jpeg,.png">
    </div>
    <div class="col-md-6 leave-travel-field" id="leave_accommodation_wrapper" style="display:none">
        <label class="form-label">Accommodation Address</label>
        <textarea class="form-control" name="accommodation_address"
            rows="2">{{ $field('accommodation_address') }}</textarea>
    </div>


    <div class="col-md-3">
        <label class="form-label">Emergency Contact Person</label>
        <input type="text" class="form-control" name="emergency_contact_person"
            value="{{ $field('emergency_contact_person') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Emergency Contact Number</label>
        <input type="text" class="form-control" name="emergency_contact_number"
            value="{{ $field('emergency_contact_number') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Relationship</label>
        <input type="text" class="form-control" name="emergency_contact_relationship"
            value="{{ $field('emergency_contact_relationship') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Attachment / Supporting Documents</label>
        <input type="file" class="form-control" name="file" accept=".pdf,.jpg,.jpeg,.png">
    </div>
    <div class="col-md-6" id="leave_reason_box">
        <label class="form-label" id="leave_reason_label">Reason For Leave</label>
        <textarea class="form-control" name="reason" rows="2"
            id="leave_reason_textarea">{{ $field('reason') }}</textarea>
    </div>

</div>

<script>
    (function () {
        function parseDmy(value) {
            var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec((value || '').trim());
            if (!m) return null;
            var d = parseInt(m[1], 10), mo = parseInt(m[2], 10), y = parseInt(m[3], 10);
            var date = new Date(y, mo - 1, d);
            if (date.getFullYear() !== y || date.getMonth() !== mo - 1 || date.getDate() !== d) return null;
            return date;
        }
        function formatDmy(date) {
            var d = ('0' + date.getDate()).slice(-2);
            var m = ('0' + (date.getMonth() + 1)).slice(-2);
            return d + '/' + m + '/' + date.getFullYear();
        }
        function calculateLeaveDates() {
            var from = parseDmy($('#leave_from_date').val());
            var to = parseDmy($('#leave_to_date').val());
            var half = $('#leave_half_session').val();
            if (half && half !== 'NONE' && from) {
                $('#leave_to_date').val(formatDmy(from));
                $('#leave_days_display').val('0.5');
                var halfReturn = new Date(from.getTime());
                halfReturn.setDate(halfReturn.getDate() + 1);
                $('#return_to_work_date').val(formatDmy(halfReturn));
                return;
            }
            if (!from || !to) {
                $('#leave_days_display').val('');
                $('#return_to_work_date').val('');
                return;
            }
            if (to.getTime() < from.getTime()) {
                to = from;
                $('#leave_to_date').val(formatDmy(to));
            }
            var days = Math.round((to.getTime() - from.getTime()) / 86400000) + 1;
            $('#leave_days_display').val(days);
            var returnDate = new Date(to.getTime());
            returnDate.setDate(returnDate.getDate() + 1);
            $('#return_to_work_date').val(formatDmy(returnDate));
        }
        function toggleHandover() {
            var show = $('#leave_handover_required').val() === 'Yes';
            $('#leave_handover_employee_box').toggle(show);
            $('.leave-handover-detail-field').hide();
            if (!show) {
                $('#leave_handover_employee_id').val('').trigger('change');
                $('[name="pending_tasks"],[name="access_transfer_required"],[name="handover_completion_confirmation"],[name="manager_verification_of_handover"]').val('No');
                $('[name="client_responsibilities"],[name="handover_additional_remarks"]').val('');
            }
        }
        function toggleTravel() {
            var show = $('#leave_leaving_country').val() === 'Yes';
            $('.leave-travel-field').toggle(show);
            if (!show) {
                $('#leave_destination_country,#leave_departure_date,#leave_expected_return_date,#leave_travel_ticket_file').val('');
                $('#leave_travel_ticket_attached').val('No');
                $('textarea[name="accommodation_address"]').val('');
            }

            $('#leave_reason_textarea').prop('required', show);
            $('#leave_reason_label').text(show ? 'Reason For Leave*' : 'Reason For Leave');

            toggleTravelTicket();
        }
        function toggleTravelTicket() {
            var show = $('#leave_travel_ticket_attached').val() === 'Yes' && $('#leave_leaving_country').val() === 'Yes';
            $('.leave-travel-ticket-file-box').toggle(show);
            if (!show) {
                $('#leave_travel_ticket_file').val('');
            }
        }
        $(document).on('change keyup blur', '#leave_from_date,#leave_to_date,#leave_half_session', calculateLeaveDates);
        $(document).on('change', '#leave_handover_required', toggleHandover);
        $(document).on('change', '#leave_leaving_country', toggleTravel);
        $(document).on('change', '#leave_travel_ticket_attached', toggleTravelTicket);
        $(function () {
            calculateLeaveDates();
            toggleHandover();
            toggleTravel();
            toggleTravelTicket();

        });


    })();
</script>

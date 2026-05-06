@php
    $hrSettings = null;
    if(isset($company) && $company && $company->id) {
        $hrSettings = \App\SysCompanyHrPayrollSetting::where('company_id', $company->id)->first();
    }
@endphp

<style>
    .hr-payroll-labels {
        font-size: 11px;
    }
    .setting-input option {
        font-size: 11px;
    }
    /* For Select2 dropdowns */
    .select2-results__option {
        font-size: 11px !important;
    }
    .select2-selection__rendered {
        font-size: 11px !important;
    }
</style>

{{-- ================================== LEAVES POLICY ================================== --}}
<h6 class="mb-3">
    <i class="ico icon-outline-leaves text-primary me-1"></i>
    Leave Policy Types
</h6>

<div class="row gy-2 mb-4">
    <!-- Leave Policy -->
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Leave Policy Type</label>
        <select class="form-select form-select-sm setting-input" name="leave_policy_type">
            <option value="">Select</option>
            <option value="default" {{ old('leave_policy_type', optional($hrSettings)->leave_policy_type ?? '') == 'default' ? 'selected' : '' }}>Default</option>
            <option value="custom" {{ old('leave_policy_type', optional($hrSettings)->leave_policy_type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Annual Leave (AL)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="annual_leave"
               value="{{ old('annual_leave', optional($hrSettings)->annual_leave_cl_sl ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Sick Leave (SL)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="sick_leave"
               value="{{ old('sick_leave', optional($hrSettings)->sick_leave_sl ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Casual Leave (CL)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="casual_leave"
               value="{{ old('casual_leave', optional($hrSettings)->casual_leave_cl ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Comp-Off Allowed</label>
        <select class="form-select form-select-sm setting-input" name="comp_off_allowed">
            <option value="">Select</option>
            <option value="yes" {{ old('comp_off_allowed', (optional($hrSettings)->comp_off_allowed == 1) ? 'yes' : ((optional($hrSettings)->comp_off_allowed == 0) ? 'no' : '')) == 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ old('comp_off_allowed', (optional($hrSettings)->comp_off_allowed == 1) ? 'yes' : ((optional($hrSettings)->comp_off_allowed == 0) ? 'no' : '')) == 'no' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    <!-- Leave Carry Forward -->
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Carry Forward Unused Leaves</label>
        <select class="form-select form-select-sm setting-input" name="carry_forward">
            <option value="">Select</option>
            <option value="yes" {{ old('carry_forward', (optional($hrSettings)->carry_forward_unused_leaves == 1) ? 'yes' : ((optional($hrSettings)->carry_forward_unused_leaves == 0) ? 'no' : '')) == 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ old('carry_forward', (optional($hrSettings)->carry_forward_unused_leaves == 1) ? 'yes' : ((optional($hrSettings)->carry_forward_unused_leaves == 0) ? 'no' : '')) == 'no' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Max Carry Forward (Days)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="max_carry_forward"
               value="{{ old('max_carry_forward', optional($hrSettings)->max_carry_forward_days ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Encashable Leaves</label>
        <select class="form-select form-select-sm setting-input" name="leave_encashment">
            <option value="">Select</option>
            <option value="yes" {{ old('leave_encashment', (optional($hrSettings)->encashable_leaves == 1) ? 'yes' : ((optional($hrSettings)->encashable_leaves == 0) ? 'no' : '')) == 'yes' ? 'selected' : '' }}>Yes</option>
            <option value="no" {{ old('leave_encashment', (optional($hrSettings)->encashable_leaves == 1) ? 'yes' : ((optional($hrSettings)->encashable_leaves == 0) ? 'no' : '')) == 'no' ? 'selected' : '' }}>No</option>
        </select>
    </div>
</div>

{{-- ================================== ATTENDANCE POLICY ================================== --}}
<h6 class="mb-3">
    <i class="ico icon-outline-attendance text-primary me-1"></i>
    Attendance Policy
</h6>

<div class="row gy-2 mb-4">
    {{-- Attendance Policy --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Attendance Policy</label>
        <select class="form-select form-select-sm setting-input" name="attendance_policy">
            <option value="">Select</option>
            <option value="standard" {{ old('attendance_policy', optional($hrSettings)->attendance_policy ?? '') == 'standard' ? 'selected' : '' }}>Standard</option>
            <option value="flexible" {{ old('attendance_policy', optional($hrSettings)->attendance_policy ?? '') == 'flexible' ? 'selected' : '' }}>Flexible</option>
            <option value="strict" {{ old('attendance_policy', optional($hrSettings)->attendance_policy ?? '') == 'strict' ? 'selected' : '' }}>Strict</option>
        </select>
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Minimum Working Hours</label>
        <input type="number" step="0.1" class="form-control form-control-sm setting-input" name="min_working_hours"
               value="{{ old('min_working_hours', optional($hrSettings)->minimum_working_hours ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Grace Period (Minutes)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="grace_period"
               value="{{ old('grace_period', optional($hrSettings)->grace_period_minutes ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Half Day After (Hours)</label>
        <input type="number" step="0.1" class="form-control form-control-sm setting-input" name="half_day_after"
               value="{{ old('half_day_after', optional($hrSettings)->half_day_after_hours ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Mark Absent If Hours Below</label>
        <input type="number" step="0.1" class="form-control form-control-sm setting-input" name="absent_below_hours"
               value="{{ old('absent_below_hours', optional($hrSettings)->absent_if_hours_below ?? '') }}">
    </div>

    {{-- Late Mark Rules --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Late Mark Count Allowed (per month)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="late_mark_allowed"
               value="{{ old('late_mark_allowed', optional($hrSettings)->late_mark_count_allowed ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Consecutive Late Mark → Half Day</label>
        <input type="number" class="form-control form-control-sm setting-input" name="late_mark_halfday"
               value="{{ old('late_mark_halfday', optional($hrSettings)->consecutive_late_to_halfday ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Auto Mark Absent After (Days)</label>
        <input type="number" class="form-control form-control-sm setting-input" name="auto_absent_after"
               value="{{ old('auto_absent_after', optional($hrSettings)->auto_mark_absent_after_days ?? '') }}">
    </div>

        <div class="col-lg-2">
   
        <label class="form-label mb-0 d-flex justify-content-between align-items-center">
                <span>Working Shifts</span>
                <button type="button" class="btn btn-sm p-0 ms-2" style="border:none;background:none;" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                    <i class="ico icon-outline-add-square text-success" style="font-size:18px;"></i>
                </button>
            </label>
            @php
                $working_shifts = @App\WorkingShift::all();
       
            @endphp
        <select class="form-select form-select-sm setting-input" name="shift_id">
            <option value="">Select</option>

              @foreach($working_shifts as $shift)
        <option value="{{ $shift->id }}" {{ (string)old('shift_id', optional($company)->shift_id ?? '') === (string)$shift->id ? 'selected' : '' }}>
            {{ $shift->shift_name }}
            ({{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}
            -
            {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }})
        </option>
    @endforeach

            
        </select>
    </div>

    {{-- SHIFT & WEEKLY OFF SETTINGS --}}
    {{-- <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Shift Start Time</label>
        <input type="time" class="form-control form-control-sm setting-input"
               name="shift_start_time" value="{{ old('shift_start_time', optional($hrSettings)->shift_start_time ?? '') }}">
    </div>

    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Shift End Time</label>
        <input type="time" class="form-control form-control-sm setting-input"
               name="shift_end_time" value="{{ old('shift_end_time', optional($hrSettings)->shift_end_time ?? '') }}">
    </div> --}}

    <div class="col-lg-4">
        <label class="form-label mb-1 hr-payroll-labels">Weekly Off</label>
        <select name="hr_weekly_off[]"
                class="form-select form-select-sm setting-input js-example-basic-single" multiple>
            <option value="monday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('monday_all') ? 'selected' : '' }}>
                Monday (All)
            </option>
            <option value="1_3_monday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_monday') ? 'selected' : '' }}>
                1 & 3 Monday (Only 1 & 3)
            </option>
            <option value="2_4_monday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_monday') ? 'selected' : '' }}>
                2 & 4 Monday (Only 2 & 4)
            </option>
            <option value="tuesday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('tuesday_all') ? 'selected' : '' }}>
                Tuesday (All)
            </option>
            <option value="1_3_tuesday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_tuesday') ? 'selected' : '' }}>
                1 & 3 Tuesday (Only 1 & 3)
            </option>
            <option value="2_4_tuesday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_tuesday') ? 'selected' : '' }}>
                2 & 4 Tuesday (Only 2 & 4)
            </option>
            <option value="wednesday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('wednesday_all') ? 'selected' : '' }}>
                Wednesday (All)
            </option>
            <option value="1_3_wednesday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_wednesday') ? 'selected' : '' }}>
                1 & 3 Wednesday (Only 1 & 3)
            </option>
            <option value="2_4_wednesday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_wednesday') ? 'selected' : '' }}>
                2 & 4 Wednesday (Only 2 & 4)
            </option>
            <option value="thursday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('thursday_all') ? 'selected' : '' }}>
                Thursday (All)
            </option>
            <option value="1_3_thursday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_thursday') ? 'selected' : '' }}>
                1 & 3 Thursday (Only 1 & 3)
            </option>
            <option value="2_4_thursday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_thursday') ? 'selected' : '' }}>
                2 & 4 Thursday (Only 2 & 4)
            </option>
            <option value="friday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('friday_all') ? 'selected' : '' }}>
                Friday (All)
            </option>
            <option value="1_3_friday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_friday') ? 'selected' : '' }}>
                1 & 3 Friday (Only 1 & 3)
            </option>
            <option value="2_4_friday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_friday') ? 'selected' : '' }}>
                2 & 4 Friday (Only 2 & 4)
            </option>
            <option value="saturday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('saturday_all') ? 'selected' : '' }}>
                Saturday (All)
            </option>
            <option value="1_3_saturday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_saturday') ? 'selected' : '' }}>
                1 & 3 Saturday (Only 1 & 3)
            </option>
            <option value="2_4_saturday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_saturday') ? 'selected' : '' }}>
                2 & 4 Saturday (Only 2 & 4)
            </option>
            <option value="sunday_all"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('sunday_all') ? 'selected' : '' }}>
                Sunday (All)
            </option>
            <option value="1_3_sunday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('1_3_sunday') ? 'selected' : '' }}>
                1 & 3 Sunday (Only 1 & 3)
            </option>
            <option value="2_4_sunday"
                {{ collect(old('hr_weekly_off', is_string(optional($hrSettings)->weekly_off_day) ? [optional($hrSettings)->weekly_off_day] : json_decode(optional($hrSettings)->weekly_off_day ?? '[]', true)))->contains('2_4_sunday') ? 'selected' : '' }}>
                2 & 4 Sunday (Only 2 & 4)
            </option>
        </select>
    </div>
</div>

{{-- ================================== PAYROLL CONFIGURATION ================================== --}}
<h6 class="mb-3">
    <i class="ico icon-outline-money text-primary me-1"></i>
    Payroll Configuration
</h6>

<div class="row gy-2 mb-4">

    {{-- WPS Establishment ID --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">WPS Establishment ID <span class="text-danger">*</span></label>
        <input type="text" name="hr_wps_establishment_id"
               class="form-control form-control-sm setting-input"
               value="{{ old('hr_wps_establishment_id', optional($hrSettings)->wps_establishment_id ?? '') }}">
    </div>

    {{-- WPS Bank --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">WPS Bank <span class="text-danger">*</span></label>
        <input type="text" name="hr_wps_bank"
               class="form-control form-control-sm setting-input"
               value="{{ old('hr_wps_bank', optional($hrSettings)->wps_bank ?? '') }}">
    </div>

    {{-- Salary File Code --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">WPS Salary File Code</label>
        <input type="text" name="hr_wps_salary_file_code"
               class="form-control form-control-sm setting-input"
               value="{{ old('hr_wps_salary_file_code', optional($hrSettings)->wps_salary_file_code ?? '') }}">
    </div>

    {{-- Payroll Cycle --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Payroll Cycle <span class="text-danger">*</span></label>
        <select name="hr_payroll_cycle"
                class="form-select form-select-sm setting-input">
            <option value="">Select</option>
            <option value="monthly"
                {{ old('hr_payroll_cycle', optional($hrSettings)->payroll_cycle ?? '') == 'monthly' ? 'selected' : '' }}>
                Monthly
            </option>
            <option value="bi-weekly"
                {{ old('hr_payroll_cycle', optional($hrSettings)->payroll_cycle ?? '') == 'bi-weekly' ? 'selected' : '' }}>
                Bi-Weekly
            </option>
            <option value="weekly"
                {{ old('hr_payroll_cycle', optional($hrSettings)->payroll_cycle ?? '') == 'weekly' ? 'selected' : '' }}>
                Weekly
            </option>
        </select>
    </div>

    {{-- Payroll Start --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Payroll Start Date</label>
        <select name="hr_payroll_start"
                class="form-select form-select-sm setting-input">
            <option value="">Select</option>
            @for($i = 1; $i <= 30; $i++)
                <option value="{{ $i }}"
                    {{ old('hr_payroll_start', optional($hrSettings)->payroll_start_day ?? '') == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>

    {{-- Payroll End --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Payroll End Date</label>
        <select name="hr_payroll_end"
                class="form-select form-select-sm setting-input">
            <option value="">Select</option>
            @for($i = 1; $i <= 30; $i++)
                <option value="{{ $i }}"
                    {{ old('hr_payroll_end', optional($hrSettings)->payroll_end_day ?? '') == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>



    {{-- Gratuity --}}
    <div class="col-lg-2">
        <label class="form-label mb-1 hr-payroll-labels">Gratuity Calculation Method</label>
        <select name="hr_gratuity_method"
                class="form-select form-select-sm setting-input">
            <option value="">Select</option>
            <option value="basic_salary"
                {{ old('hr_gratuity_method', optional($hrSettings)->gratuity_calculation_method ?? '') == 'basic_salary' ? 'selected' : '' }}>
                Basic Salary
            </option>
            <option value="gross_salary"
                {{ old('hr_gratuity_method', optional($hrSettings)->gratuity_calculation_method ?? '') == 'gross_salary' ? 'selected' : '' }}>
                Gross Salary
            </option>
        </select>
    </div>

    {{-- Insurance Provider --}}
   

</div>


<div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="top:10%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Add Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Document Form -->
                <form id="documentForm">
                    <input type="hidden" id="documentEditIndex" value="-1">
                    <div class="row gy-2">
                        <div class="col-12">
                            <label for="document_name" class="form-label mb-1">Shift Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="add_shiftname" name="add_shiftname" placeholder="">
                        </div>
                        <div class="col-12 ">
                            <label for="shift_start_time" class="form-label mb-1">Start Time<span class="text-danger">*</span></label>
                            <input type="time" class="form-control form-control-sm" id="add_shift_start_time" name="add_shift_start_time" placeholder="">   
                        </div>
                        <div class="col-12">
                            <label for="shift_end_time" class="form-label mb-1">End Time<span class="text-danger">*</span></label>
                            <input type="time" class="form-control form-control-sm" id="add_shift_end_time" name="add_shift_end_time" placeholder="">
                        </div>
                    </div>
                </form>

                <!-- Add Document Button -->
                <div class="mt-3 text-center">
                    <button type="button" class="btn btn-light d-inline-flex align-items-center gap-2" id="addShiftBtn">
                     
                        <i class="ico icon-outline-bookmark-opened text-success"></i>
                        <span>Save</span>
                    </button>
                </div>

                
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        function clearShiftErrors(){ $('.shift-error').remove(); }

        $('#addShiftBtn').on('click', function(){
            clearShiftErrors();
            var $btn = $(this);
            var name = $('#add_shiftname').val() ? $('#add_shiftname').val().trim() : '';
            var start = $('#add_shift_start_time').val();
            var end = $('#add_shift_end_time').val();
            var hasError = false;
            var timeRegex = /^([01]\d|2[0-3]):[0-5]\d$/;

            if (!name) {
                $('#add_shiftname').after('<div class="text-danger mt-1 shift-error">Shift name is required</div>');
                hasError = true;
            }
            if (!timeRegex.test(start)) {
                $('#add_shift_start_time').after('<div class="text-danger mt-1 shift-error">Start time is invalid</div>');
                hasError = true;
            }
            if (!timeRegex.test(end)) {
                $('#add_shift_end_time').after('<div class="text-danger mt-1 shift-error">End time is invalid</div>');
                hasError = true;
            }
            if (!hasError && start >= end) {
                $('#add_shift_end_time').after('<div class="text-danger mt-1 shift-error">End time must be after start time</div>');
                hasError = true;
            }
            if (hasError) return;

            // disable & spinner
            $btn.prop('disabled', true).append('<span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>');

            $.ajax({
                url: '{{ url("/company/working-shifts/store") }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    shift_name: name,
                    start_time: start,
                    end_time: end
                },
                success: function(res){
                    if (res && res.ok) {
                        var s = res.shift;
                        var text = s.shift_name + ' (' + s.start_time + ' - ' + s.end_time + ')';
                        var $select = $('[name="shift_id"]');
                        // append and select new option
                        $select.append(new Option(text, s.id, true, true));
                        $select.trigger('change');
                        // close modal and reset
                        $('#addShiftModal').modal('hide');
                        $('#add_shiftname,#add_shift_start_time,#add_shift_end_time').val('');

                        // small inline toast fallback
                        var $msg = $('<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">Shift added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                        $('.modal-body').first().prepend($msg);
                        setTimeout(function(){ $msg.alert('close'); }, 3000);

                    } else if (res && res.errors) {
                        $.each(res.errors, function(k,v){
                            var msg = Array.isArray(v) ? v[0] : v;
                            if (k === 'shift_name') $('#add_shiftname').after('<div class="text-danger mt-1 shift-error">'+msg+'</div>');
                            if (k === 'start_time') $('#add_shift_start_time').after('<div class="text-danger mt-1 shift-error">'+msg+'</div>');
                            if (k === 'end_time') $('#add_shift_end_time').after('<div class="text-danger mt-1 shift-error">'+msg+'</div>');
                        });
                    } else {
                        alert('Could not add shift. Please try again.');
                    }
                },
                error: function(xhr){
                    if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        var errs = xhr.responseJSON.errors;
                        $.each(errs, function(k,v){ var msg = v[0]; if (k === 'shift_name') $('#add_shiftname').after('<div class="text-danger mt-1 shift-error">'+msg+'</div>'); if (k === 'start_time') $('#add_shift_start_time').after('<div class="text-danger mt-1 shift-error">'+msg+'</div>'); if (k === 'end_time') $('#add_shift_end_time').after('<div class="text-danger mt-1 shift-error">'+msg+'</div>'); });
                    } else {
                        alert('Server error. Please try again later.');
                    }
                },
                complete: function(){
                    $btn.prop('disabled', false); $btn.find('.spinner-border').remove();
                }
            });
        });

        // submit on Enter key
        $('#documentForm').on('keydown', function(e){
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#addShiftBtn').click();
            }
        });
    });
</script>

{{-- ================================== LOANS & ADVANCES ================================== --}}
<h6 class="mb-3">
    <i class="ico icon-outline-loan text-primary me-1"></i>
    Loans & Advances
</h6>
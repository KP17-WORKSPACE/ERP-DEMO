@csrf
@if(isset($leave))
    @method('PUT')
@endif
@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp
<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        {{ isset($leave) ? 'Edit Leave Application' : 'Leave Application Form' }}
    </h4>

    <span id="saveAllMsg" class="ms-2"></span>
    <div class="purchase-order-content-header-right">
        <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
            id="btnSaveAllCompany" data-busy-text="{{ isset($leave) ? 'Updating...' : 'Saving...' }}">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
            <span class="btn-text">{{ isset($leave) ? 'Update' : 'Apply' }}</span>
        </button>
        <a class="btn btn-light" href="{{ url('employee/leaves') }}">My Leaves</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">

        {{-- ===== Line 1 & 2: exactly 5 by 5 ===== --}}
        <div class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
            {{-- 1 --}}
            <div class="col">
                <label class="form-label">Employee Name</label>
                <input type="text" class="form-control form-control-sm" name="employee_name"
                       value="{{ $authUser->full_name ?? (Auth::user()->name ?? '') }}" readonly>
            </div>

            {{-- 2 --}}
            <div class="col">
                <label class="form-label">Department</label>
                <input type="text" class="form-control form-control-sm" name="department"
                       value="{{ $authUser->staff->departments->name ?? '' }}" readonly>
            </div>

            {{-- 3 --}}
            <div class="col">
                <label class="form-label">Designation</label>
                <input type="text" class="form-control form-control-sm" name="designation"
                       value="{{ $authUser->staff->designations->title ?? '' }}" readonly>
            </div>

            {{-- 4 --}}
            <div class="col">
                <label class="form-label">Reporting Manager</label>
                <select class="form-select form-select-sm" name="reporting_manager" required>
                    <option value="">-- Select Manager --</option>
                    @foreach ($reportingManager as $user)
                        <option value="{{ $user->id }}"
                            {{ old('reporting_manager', $leave->reporting_manager ?? null) == $user->id ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 5 --}}
            <div class="col">
                <label class="form-label">Type of Leave</label>
                <select id="type_of_leave" name="type_id" class="form-select form-select-sm" required>
                    <option value="">-- Select --</option>
                    @foreach ($leaveTypes as $lt)
                        <option value="{{ $lt->id }}"
                            data-code="{{ $lt->code }}"
                            {{ old('type_id', $leave->type_id ?? null) == $lt->id ? 'selected' : '' }}>
                            {{ $lt->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Normal Leave Fields Wrapper --}}
        <div id="normalLeaveFields" class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
            <div class="col">
                <label class="form-label">Leave From</label>
                <input type="text" name="leave_from" class="form-control form-control-sm date-picker"
                       value="{{ old('leave_from', isset($leave) ? $leave->leave_from : '') }}">
            </div>

            <div class="col">
                <label class="form-label">Leave To</label>
                <input type="text" name="leave_to" class="form-control form-control-sm date-picker"
                       value="{{ old('leave_to', isset($leave) ? $leave->leave_to : '') }}">
            </div>

            <div class="col">
                <label class="form-label">Number of Days</label>
                <input type="text" id="days" name="days" class="form-control form-control-sm"
                       value="{{ old('days', isset($leave) ? $leave->days : '') }}" readonly>
            </div>

            <div class="col">
                <label class="form-label">Total Leave Taken</label>
                <input type="text" name="total_leave_taken" class="form-control form-control-sm"
                       value="{{ $totalLeaveTaken ?? '' }}" readonly>
            </div>
            <div class="col">
                <label class="form-label">Handover To</label>
                <input type="text" name="handover_to" class="form-control form-control-sm"
                       value="{{ old('handover_to', isset($leave) ? $leave->handover_to : '') }}">
            </div>
        </div>

        {{-- Reason | Note | Attachment --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-4">
                <label class="form-label">Reason</label>
                <textarea name="reason" rows="1" class="form-control form-control-sm">{{ old('reason', $leave->reason ?? '') }}</textarea>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Note</label>
                <textarea name="note" rows="1" class="form-control form-control-sm">{{ old('note', $leave->note ?? '') }}</textarea>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Attachment</label>
                <input type="file" name="file" class="form-control form-control-sm"
                       accept=".pdf,.jpg,.jpeg,.png">
                @if(isset($leave) && $leave->file)
                    <small class="d-block mt-1">Current: <a href="{{ asset('storage/'.$leave->file) }}" target="_blank">View File</a></small>
                @endif
            </div>
        </div>

        {{-- Emergency Contacts --}}
        <div class="card mt-3">
            <div class="card-header py-2"><strong>Emergency Contacts</strong></div>
            <div class="card-body">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="row row-cols-1 row-cols-lg-5 g-3 mb-2">
                        <div class="col">
                            <label class="form-label mb-0">Contact {{ $i }}</label>
                        </div>
                        <div class="col">
                            <input type="text" name="emergency_name_{{ $i }}"
                                   placeholder="Name" class="form-control form-control-sm"
                                   value="{{ old('emergency_name_'.$i, $leave->{'emergency_name_'.$i} ?? '') }}">
                        </div>
                        <div class="col">
                            <input type="text" name="emergency_relation_{{ $i }}"
                                   placeholder="Relationship" class="form-control form-control-sm"
                                   value="{{ old('emergency_relation_'.$i, $leave->{'emergency_relation_'.$i} ?? '') }}">
                        </div>
                        <div class="col">
                            <input type="text" name="emergency_phone_{{ $i }}"
                                   placeholder="Contact No" class="form-control form-control-sm"
                                   value="{{ old('emergency_phone_'.$i, $leave->{'emergency_phone_'.$i} ?? '') }}">
                        </div>
                        <div class="col">
                            <input type="text" name="emergency_country_{{ $i }}"
                                   placeholder="Country" class="form-control form-control-sm"
                                   value="{{ old('emergency_country_'.$i, $leave->{'emergency_country_'.$i} ?? '') }}">
                        </div>
                    </div>
                @endfor
            </div>
        </div>

    </div>
</div>

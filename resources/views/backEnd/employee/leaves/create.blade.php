    @extends('backEnd.newmasterpage')
    @section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        $isEdit = isset($leave);
        $leaveFromVal = old('leave_from', ($isEdit && $leave->leave_from) ? $leave->leave_from->format('d/m/Y') : '');
        $leaveToVal   = old('leave_to',   ($isEdit && $leave->leave_to)   ? $leave->leave_to->format('d/m/Y')   : '');
    @endphp

    <div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

        <form id="leaveForm"
           action="{{ $isEdit ? route('employee.leaves.update', $leave->id) : route('employee.leaves.store') }}"
@if($isEdit) @method('PUT') @endif
            method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="purchase-order-content-header">
            <h4 class="purchase-order-content-header-left">
            {{ $isEdit ? 'Edit Leave Application' : 'Leave Application Form' }}
            </h4>

            <span id="saveAllMsg" class="ms-2"></span>
            <div class="purchase-order-content-header-right">
            <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                    id="btnSaveAllCompany" data-busy-text="{{ $isEdit ? 'Updating...' : 'Saving...' }}">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
                <span class="btn-text">{{ $isEdit ? 'Save' : 'Apply' }}</span>
            </button>
            <a class="btn btn-light" href="{{ url('employee/leaves') }}">My Leaves</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
            {{-- Top row (5 cols) --}}
            <div class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
                <div class="col">
                <label class="form-label">Employee Name</label>
                <input type="text" class="form-control form-control-sm" name="employee_name"
                        value="{{ $authUser->full_name ?? (Auth::user()->name ?? '') }}" readonly>
                </div>

                <div class="col">
                <label class="form-label">Department</label>
                <input type="text" class="form-control form-control-sm" name="department"
                        value="{{ $authUser->staff->departments->name ?? '' }}" readonly>
                </div>

                <div class="col">
                <label class="form-label">Designation</label>
                <input type="text" class="form-control form-control-sm" name="designation"
                        value="{{ $authUser->staff->designations->title ?? '' }}" readonly>
                </div>

                <div class="col">
                <label class="form-label">Reporting Manager</label>
                <select class="form-select form-select-sm {{ $errors->has('reporting_manager_id') ? 'is-invalid' : '' }}"
                        name="reporting_manager_id">
                    <option value="">-- Select Manager --</option>
                    @foreach ($reportingManager as $user)
                    <option value="{{ $user->id }}"
                        {{ (string)old('reporting_manager_id', $isEdit ? $leave->reporting_manager_id : '') === (string)$user->id ? 'selected' : '' }}>
                        {{ $user->full_name }}
                    </option>
                    @endforeach
                </select>
                @if ($errors->has('reporting_manager_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('reporting_manager_id') }}</div>
                @endif
                </div>

                <div class="col">
                <label class="form-label">Type of Leave</label>
                <select id="type_of_leave" name="type_id"
                        class="form-select form-select-sm {{ $errors->has('type_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Select --</option>
                    @foreach ($leaveTypes as $lt)
                    <option value="{{ $lt->id }}"
                            data-code="{{ $lt->code }}"
                        {{ (string)old('type_id', $isEdit ? $leave->type_id : '') === (string)$lt->id ? 'selected' : '' }}>
                        {{ $lt->name }}
                    </option>
                    @endforeach
                </select>
                @if ($errors->has('type_id'))
                    <div class="invalid-feedback d-block">{{ $errors->first('type_id') }}</div>
                @endif
                </div>
            </div>

            {{-- Normal Leave Fields (hide for Half-Day/Early Logout) --}}
            <div id="normalLeaveFields" class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
                <div class="col">
                <label class="form-label">Leave From</label>
                <input type="text" name="leave_from"
                        class="form-control form-control-sm date-picker {{ $errors->has('leave_from') ? 'is-invalid' : '' }}"
                        value="{{ $leaveFromVal }}">
                @if ($errors->has('leave_from'))
                    <div class="invalid-feedback d-block">{{ $errors->first('leave_from') }}</div>
                @endif
                </div>

                <div class="col">
                <label class="form-label">Leave To</label>
                <input type="text" name="leave_to"
                        class="form-control form-control-sm date-picker {{ $errors->has('leave_to') ? 'is-invalid' : '' }}"
                        value="{{ $leaveToVal }}">
                @if ($errors->has('leave_to'))
                    <div class="invalid-feedback d-block">{{ $errors->first('leave_to') }}</div>
                @endif
                </div>

                <div class="col">
                <label class="form-label">Number of Days</label>
                <input type="text" id="days" name="days" class="form-control form-control-sm"
                        value="{{ old('days', $isEdit ? $leave->days : '') }}" readonly>
                </div>

                <div class="col">
                <label class="form-label">Total Leave Taken</label>
                <input type="text" name="total_leave_taken" class="form-control form-control-sm"
                        value="{{ $totalLeaveTaken ?? '' }}" readonly>
                </div>

                <div class="col">
                <label class="form-label">Handover To</label>
                <input type="text" name="handover_to" class="form-control form-control-sm"
                        value="{{ old('handover_to', $isEdit ? $leave->handover_to : '') }}">
                </div>
            </div>

            {{-- Reason | Note | Attachment --}}
            <div class="row g-2 mb-4">
                
                <div class="col-5">
                <label class="form-label">Reason</label>
                <textarea name="reason" rows="1" class="form-control form-control-sm">{{ old('reason', $isEdit ? $leave->reason : '') }}</textarea>
                </div>
                <div class="col">
                <label class="form-label">Note</label>
                <textarea name="note" rows="1" class="form-control form-control-sm">{{ old('note', $isEdit ? $leave->note : '') }}</textarea>
                </div>
                <div class="col-lg-2">
                <label class="form-label">Attachment</label>
                <input type="file" name="file"
                        class="form-control form-control-sm {{ $errors->has('file') ? 'is-invalid' : '' }}"
                        accept=".pdf,.jpg,.jpeg,.png">
                @if ($errors->has('file'))
                    <div class="invalid-feedback d-block">{{ $errors->first('file') }}</div>
                @endif

                @if($isEdit && !empty($leave->file_path))
                    <small class="d-block mt-1">
                    Current: <a href="{{ Storage::disk('public')->url($leave->file_path) }}" target="_blank">View file</a>
                    </small>
                @endif
                </div>
            </div>

            {{-- Emergency Contacts --}}
            <div class="card mt-3">
                <div class="card-header py-2"><strong>Emergency Contacts</strong></div>
                <div class="card-body">
                @for ($i = 1; $i <= 3; $i++)
                    @php
                    $existing = null;
                    if ($isEdit && $leave->emergency_contacts) {
                        $arr = json_decode($leave->emergency_contacts, true) ?: [];
                        $existing = isset($arr[$i-1]) ? $arr[$i-1] : null;
                    }
                    @endphp
                    <div class="row row-cols-1 row-cols-lg-5 g-3 mb-2">
                    <div class="col">
                        <label class="form-label mb-0">Contact {{ $i }}</label>
                    </div>
                    <div class="col">
                        <input type="text" name="emergency_name_{{ $i }}" placeholder="Name"
                            class="form-control form-control-sm"
                            value="{{ old('emergency_name_'.$i, $existing ? $existing['name'] : '') }}">
                    </div>
                    <div class="col">
                        <input type="text" name="emergency_relation_{{ $i }}" placeholder="Relationship"
                            class="form-control form-control-sm"
                            value="{{ old('emergency_relation_'.$i, $existing ? $existing['relation'] : '') }}">
                    </div>
                    <div class="col">
                        <input type="text" name="emergency_phone_{{ $i }}" placeholder="Contact No"
                            class="form-control form-control-sm"
                            value="{{ old('emergency_phone_'.$i, $existing ? $existing['phone'] : '') }}">
                    </div>
                    <div class="col">
                        <input type="text" name="emergency_country_{{ $i }}" placeholder="Country"
                            class="form-control form-control-sm"
                            value="{{ old('emergency_country_'.$i, $existing ? $existing['country'] : '') }}">
                    </div>
                    </div>
                @endfor
                </div>
            </div>

            </div>
        </div>
        </form>

    </div>
    </div>

    {{-- ===== FULL jQuery (DD/MM/YYYY + Half-Day/Early Logout hide/show + day calc) ===== --}}
    <script>
    $(function () {
    var $form  = $('#leaveForm');
    var $btn   = $('#btnSaveAllCompany');
    var $from  = $('input[name="leave_from"]');
    var $to    = $('input[name="leave_to"]');
    var $type  = $('#type_of_leave');
    var $days  = $('#days');
    var $msg   = $('#saveAllMsg');
    var $normalFields = $('#normalLeaveFields');

    // Parse dd/mm/yyyy safely (no Date(...))
    function isValidDMY(s) {
        return /^(\d{2})\/(\d{2})\/(\d{4})$/.test(s);
    }
    function parseDMYParts(s) {
        var m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (!m) return null;
        var d = parseInt(m[1], 10), mo = parseInt(m[2], 10), y = parseInt(m[3], 10);
        if (mo < 1 || mo > 12) return null;
        var maxDay = [31, (y%4===0 && (y%100!==0 || y%400===0))?29:28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][mo-1];
        if (d < 1 || d > maxDay) return null;
        return { y:y, m:mo, d:d };
    }
    function toUTC(parts) { return Date.UTC(parts.y, parts.m - 1, parts.d); }

    function isHalfDayOrEarlyLogout() {
        var $opt = $type.find('option:selected');
        var code = ($opt.data('code') || '').toString().toUpperCase();
        var text = ($opt.text() || '').trim().toLowerCase();
        // Prefer code (HD / EL), fallback to name match
        return code === 'HD' || code === 'EL' ||
            text === 'half-day leave' || text === 'early logout';
    }

    function toggleFields() {
    var $opt = $('#type_of_leave option:selected');
    var code = ($opt.data('code') || '').toUpperCase();
    var text = ($opt.text() || '').trim().toLowerCase();

    if (code === 'HD' || code === 'EL' || text === 'half-day leave' || text === 'early logout') {
        $('#normalLeaveFields').hide();
        $('#halfDaySessionWrapper').show();
        $('#days').val('0.5');
        $('input[name="leave_to"]').val($('input[name="leave_from"]').val());
    } else {
        $('#normalLeaveFields').show();
        $('#halfDaySessionWrapper').hide();
        calcDays();
    }
    }


    function calcDays() {
        if (isHalfDayOrEarlyLogout()) {
        var vf = $from.val().trim();
        if (isValidDMY(vf) && parseDMYParts(vf)) {
            $to.val(vf);
            $days.val('0.5');
        } else {
            $days.val('');
        }
        return;
        }

        var vf = $from.val().trim();
        var vt = $to.val().trim();
        if (!isValidDMY(vf) || !parseDMYParts(vf)) { $days.val(''); return; }
        if (!isValidDMY(vt) || !parseDMYParts(vt)) { $days.val(''); return; }

        var pf = parseDMYParts(vf);
        var pt = parseDMYParts(vt);
        var uf = toUTC(pf), ut = toUTC(pt);

        if (ut < uf) { $to.val(vf); ut = uf; }
        var diffDays = Math.round((ut - uf) / 86400000) + 1; // inclusive
        $days.val(String(diffDays));
    }

    function setError(t){ $msg.html('<span class="text-danger">'+t+'</span>'); }
    function clearError(){ $msg.html(''); }

    function clientValidate() {
        clearError();
        if (!$type.val()) { setError('Select Type of Leave.'); return false; }
        if (!$('select[name="reporting_manager_id"]').val()) { setError('Select Reporting Manager.'); return false; }

        var vf = $from.val().trim();
        if (!isValidDMY(vf) || !parseDMYParts(vf)) {
        setError('Enter date in DD/MM/YYYY.'); return false;
        }

        if (!isHalfDayOrEarlyLogout()) {
        var vt = $to.val().trim();
        if (!isValidDMY(vt) || !parseDMYParts(vt)) {
            setError('Enter Leave To in DD/MM/YYYY.'); return false;
        }
        }

        calcDays();
        if (!$days.val() || isNaN(Number($days.val()))) {
        setError('Number of days could not be calculated.'); return false;
        }
        return true;
    }

    // Bind
    $type.on('change', toggleFields);
    $from.on('keyup change blur', function(){ toggleFields(); calcDays(); });
    $to.on('keyup change blur', calcDays);

    $btn.on('click', function(){
        if (!clientValidate()) return;
        var $icon = $(this).find('.btn-icon');
        var $spin = $(this).find('.spinner-border');
        var $txt  = $(this).find('.btn-text');
        var busy  = $(this).data('busy-text') || 'Saving...';
        $spin.removeClass('d-none');
        $icon.addClass('d-none');
        $txt.text(busy);
        $form.submit();
    });

    // Initial
    toggleFields();
    calcDays();
    });
    </script>
    @endsection

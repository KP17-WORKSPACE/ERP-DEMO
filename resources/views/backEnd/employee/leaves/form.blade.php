@extends('backEnd.newmasterpage')

@section('mainContent')
@php
    $isEdit = isset($leave);
    $leaveFromVal = old('leave_from', ($isEdit && $leave->leave_from) ? \Carbon\Carbon::parse($leave->leave_from)->format('d/m/Y') : '');
    $leaveToVal   = old('leave_to', ($isEdit && $leave->leave_to) ? \Carbon\Carbon::parse($leave->leave_to)->format('d/m/Y') : '');
    
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="content-container col-12">
<div class="tab-content display-flex-tabs">

    <form id="leaveForm"
          action="{{ $isEdit ? route('employee.leaves.update', $leave->id) : route('employee.leaves.store') }}"
          method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        @if($isEdit)
            {{ method_field('PUT') }}
        @endif

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
                    <span class="btn-text">{{ $isEdit ? 'Save Changes' : 'Apply' }}</span>
                </button>
                <a class="btn btn-light" href="{{ route('employee.leaves.index') }}">My Leaves</a>
            </div>
        </div>

        <div class="card mb-3">
        <div class="card-body">

            {{-- EMPLOYEE INFO --}}
            <div class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
                <div class="col">
                    <label class="form-label">Employee Name</label>
                    <input type="text" class="form-control form-control-sm" readonly
                           value="{{ $authUser->full_name ?? Auth::user()->name }}">
                </div>

                <div class="col">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control form-control-sm" readonly
                           value="{{ isset($authUser->staff->departments->name) ? $authUser->staff->departments->name : '-' }}">
                </div>

                <div class="col">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control form-control-sm" readonly
                           value="{{ isset($authUser->staff->designations->title) ? $authUser->staff->designations->title : '-' }}">
                </div>

                <div class="col">
                    <label class="form-label">Reporting Manager</label>
                    <select name="reporting_manager_id"
                            class="form-select form-select-sm {{ $errors->has('reporting_manager_id') ? 'is-invalid' : '' }}">
                        <option value="">-- Select Manager --</option>
                        @foreach ($reportingManager as $user)
                            <option value="{{ $user->id }}"
                                {{ (string)old('reporting_manager_id', $isEdit ? $leave->reporting_manager_id : '') === (string)$user->id ? 'selected' : '' }}>
                                {{ $user->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('reporting_manager_id'))
                        <div class="invalid-feedback d-block">{{ $errors->first('reporting_manager_id') }}</div>
                    @endif
                </div>

                <div class="col">
                    <label class="form-label">Type of Leave</label>
                    <select id="type_of_leave" name="type_id"
                            class="form-select form-select-sm {{ $errors->has('type_id') ? 'is-invalid' : '' }}">
                        <option value="">-- Select --</option>
                        @foreach ($leaveTypes as $lt)
                            <option value="{{ $lt->id }}" data-code="{{ $lt->code }}"
                                {{ (string)old('type_id', $isEdit ? $leave->type_id : '') === (string)$lt->id ? 'selected' : '' }}>
                                {{ $lt->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('type_id'))
                        <div class="invalid-feedback d-block">{{ $errors->first('type_id') }}</div>
                    @endif
                </div>
            </div>

            {{-- DATE + HANDOVER --}}
            <div id="normalLeaveFields" class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
                <div class="col">
                    <label class="form-label">Leave From</label>
                    <input type="text" name="leave_from"
                           class="form-control form-control-sm date-picker {{ $errors->has('leave_from') ? 'is-invalid' : '' }}"
                           value="{{ $leaveFromVal }}">
                    @if($errors->has('leave_from'))
                        <div class="invalid-feedback d-block">{{ $errors->first('leave_from') }}</div>
                    @endif
                </div>

                <div class="col">
                    <label class="form-label">Leave To</label>
                    <input type="text" name="leave_to"
                           class="form-control form-control-sm date-picker {{ $errors->has('leave_to') ? 'is-invalid' : '' }}"
                           value="{{ $leaveToVal }}">
                    @if($errors->has('leave_to'))
                        <div class="invalid-feedback d-block">{{ $errors->first('leave_to') }}</div>
                    @endif
                </div>

                <div class="col">
                    <label class="form-label">Days</label>
                    <input type="text" id="days" name="days" class="form-control form-control-sm"
                           value="{{ old('days', $isEdit ? $leave->days : '') }}" readonly>
                </div>

                <div class="col">
                    <label class="form-label">Total Leave Taken</label>
                    <input type="text" class="form-control form-control-sm" readonly
                           value="{{ isset($totalLeaveTaken) ? $totalLeaveTaken : '' }}">
                </div>

                <div class="col">
                    <label class="form-label">Handover To</label>
                    <input type="text" name="handover_to" class="form-control form-control-sm"
                           value="{{ old('handover_to', $isEdit ? $leave->handover_to : '') }}">
                </div>
            </div>

            {{-- REASON | NOTE | ATTACHMENT --}}
            <div class="row g-2 mb-4">
                <div class="col-md-5">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" rows="1" class="form-control form-control-sm">{{ old('reason', $isEdit ? $leave->reason : '') }}</textarea>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Note</label>
                    <textarea name="note" rows="1" class="form-control form-control-sm">{{ old('note', $isEdit ? $leave->note : '') }}</textarea>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="file" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                    @if($errors->has('file'))
                        <div class="invalid-feedback d-block">{{ $errors->first('file') }}</div>
                    @endif

                    @if($isEdit && !empty($leave->file))
                        <small class="d-block mt-1">
                            Current:
                            <a href="{{ asset('storage/'.$leave->file) }}" target="_blank">View file</a>
                        </small>
                    @endif
                </div>
            </div>

            {{-- EMERGENCY CONTACTS --}}
            <div class="card mt-3">
                <div class="card-header py-2"><strong>Emergency Contacts</strong></div>
                <div class="card-body">
                    @php
                        $contacts = $isEdit
                            ? (is_array($leave->emergency_contacts) ? $leave->emergency_contacts : (json_decode($leave->emergency_contacts, true) ?: []))
                            : [];
                    @endphp
                    @for($i=1; $i<=3; $i++)
                        @php $existing = isset($contacts[$i-1]) ? $contacts[$i-1] : null; @endphp
                        <div class="row row-cols-1 row-cols-lg-5 g-3 mb-2">
                            <div class="col"><label class="form-label mb-0">Contact {{ $i }}</label></div>
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

{{-- BASIC JS --}}
<script>
$(document).ready(function(){
    var $form  = $('#leaveForm');
    var $btn   = $('#btnSaveAllCompany');
    var $from  = $('input[name="leave_from"]');
    var $to    = $('input[name="leave_to"]');
    var $type  = $('#type_of_leave');
    var $days  = $('#days');
    var $msg   = $('#saveAllMsg');

    function isValidDMY(s){ return /^(\d{2})\/(\d{2})\/(\d{4})$/.test(s); }
    function parseDMY(s){
        var m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if(!m) return null;
        return {d:parseInt(m[1]), m:parseInt(m[2]), y:parseInt(m[3])};
    }
    function toUTC(p){ return Date.UTC(p.y, p.m-1, p.d); }

    function calcDays(){
        var vf = $.trim($from.val());
        var vt = $.trim($to.val());
        var typeText = $.trim($('#type_of_leave option:selected').text()).toLowerCase();
        var typeCode = ($('#type_of_leave option:selected').data('code') || '').toUpperCase();

        if(typeCode==='HD' || typeCode==='EL' || typeText.indexOf('half')>=0){
            $days.val('0.5');
            return;
        }
        if(!isValidDMY(vf) || !isValidDMY(vt)) return;
        var pf = parseDMY(vf), pt = parseDMY(vt);
        if(!pf || !pt) return;
        var uf = toUTC(pf), ut = toUTC(pt);
        if(ut < uf){ ut = uf; $to.val(vf); }
        var diff = Math.round((ut - uf)/86400000) + 1;
        $days.val(diff);
    }

    $type.on('change', calcDays);
    $from.on('change keyup blur', calcDays);
    $to.on('change keyup blur', calcDays);

    $btn.on('click', function(){
        $msg.html('');
        if(!$type.val()){ $msg.html('<span class="text-danger">Select Type of Leave</span>'); return false; }
        if(!isValidDMY($from.val())){ $msg.html('<span class="text-danger">Invalid From date</span>'); return false; }
        calcDays();
        $(this).find('.spinner-border').removeClass('d-none');
        $(this).find('.btn-icon').addClass('d-none');
        $(this).find('.btn-text').text($(this).data('busy-text'));
        $form.submit();
    });

    calcDays();
});
</script>
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $isEdit = isset($editData);
@endphp

<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="attendanceMasterTabContent">
        <div class="" role="tabpanel" id="data-details">
            
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Attendance Master
                </h4>
                
            </div>

            <div class="card mb-3">
                <div class="card-body">

                    {{-- Flash Messages --}}
                    @if(session('message-success'))
                        <div class="alert alert-success">{{ session('message-success') }}</div>
                    @elseif(session('message-danger'))
                        <div class="alert alert-danger">{{ session('message-danger') }}</div>
                    @endif

                    {{-- Form Start --}}
                    @if($isEdit)
                        {{ Form::open(['url' => route('attendance-master.update', $editData->id), 'method' => 'PUT', 'class' => 'form-horizontal']) }}
                    @else
                        {{ Form::open(['url' => route('attendance-master.store'), 'method' => 'POST', 'class' => 'form-horizontal']) }}
                    @endif

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="white-box">

                                {{-- Code --}}
                                <div class="mb-2">
                                    <label class="form-label">Code <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="code" 
                                        value="{{ $isEdit ? $editData->code : old('code') }}">
                                    @if ($errors->has('code'))
                                        <span class="text-danger small">{{ $errors->first('code') }}</span>
                                    @endif
                                </div>

                                {{-- Name --}}
                                <div class="mb-2">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name"
                                        value="{{ $isEdit ? $editData->name : old('name') }}">
                                    @if ($errors->has('name'))
                                        <span class="text-danger small">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>

                                {{-- Attendance Policy --}}
                                <div class="mb-2">
                                    <label class="form-label">Attendance Policy</label>
                                    <select class="form-select" name="attendance_policy">
                                        <option value="">Select</option>
                                        <option value="standard" {{ old('attendance_policy', $editData->attendance_policy ?? '') == 'standard' ? 'selected' : '' }}>Standard</option>
                                        <option value="flexible" {{ old('attendance_policy', $editData->attendance_policy ?? '') == 'flexible' ? 'selected' : '' }}>Flexible</option>
                                        <option value="strict" {{ old('attendance_policy', $editData->attendance_policy ?? '') == 'strict' ? 'selected' : '' }}>Strict</option>
                                    </select>
                                </div>

                                {{-- Shift Type --}}
                                <div class="mb-2">
                                    <label class="form-label">Shift Type</label>
                                    <select class="form-select" name="shift_type">
                                        <option value="">Select</option>
                                        <option value="Fixed" {{ old('shift_type', $editData->shift_type ?? '') == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="Rotational" {{ old('shift_type', $editData->shift_type ?? '') == 'Rotational' ? 'selected' : '' }}>Rotational</option>
                                        <option value="Custom" {{ old('shift_type', $editData->shift_type ?? '') == 'Custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>

                                {{-- Start / End Time --}}
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="start_time"
                                            value="{{ $isEdit ? $editData->start_time : old('start_time') }}">
                                        @if ($errors->has('start_time'))
                                            <span class="text-danger small">{{ $errors->first('start_time') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="end_time"
                                            value="{{ $isEdit ? $editData->end_time : old('end_time') }}">
                                        @if ($errors->has('end_time'))
                                            <span class="text-danger small">{{ $errors->first('end_time') }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Work Hours / Grace Period --}}
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Work Hours / Day</label>
                                        <input class="form-control" type="number" step="0.5" name="work_hours_per_day"
                                            value="{{ $isEdit ? $editData->work_hours_per_day : old('work_hours_per_day') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Grace Period (min)</label>
                                        <input class="form-control" type="number" name="grace_period"
                                            value="{{ $isEdit ? $editData->grace_period : old('grace_period') }}">
                                    </div>
                                </div>

                                {{-- Half Day / Absent --}}
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Half Day After (hrs)</label>
                                        <input class="form-control" type="number" step="0.5" name="half_day_after"
                                            value="{{ $isEdit ? $editData->half_day_after : old('half_day_after') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Absent Below (hrs)</label>
                                        <input class="form-control" type="number" step="0.5" name="absent_below_hours"
                                            value="{{ $isEdit ? $editData->absent_below_hours : old('absent_below_hours') }}">
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="mb-2">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="2">{{ $isEdit ? $editData->description : old('description') }}</textarea>
                                </div>

                                {{-- Active --}}
                                <div class="mb-3">
                                    <label class="form-label">Active</label><br>
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $editData->is_active ?? 1) ? 'checked' : '' }}>
                                    <small class="text-muted">Enable/Disable this attendance setup</small>
                                </div>

                                {{-- Buttons --}}
                                <div class="row mt-3">
                                    <div class="col-lg-12 d-flex justify-content-end align-items-center">
                                        {{-- <a href="{{ route('attendance-master.index') }}" class="btn btn-light text-dark me-2">
                                            <i class="ico icon-outline-list text-primary"></i> View List
                                        </a> --}}
                                        <button class="btn btn-light text-dark" type="submit" id="btnSubmit">
                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                            {{ $isEdit ? 'Update' : 'Add' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT TABLE LIST --}}
                        <div class="col-lg-8">
                            <table class="table table-hover" id="long-list" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Policy</th>
                                        <th>Grace</th>
                                        <th>Active</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records ?? [] as $row)
                                        <tr>
                                            <td>{{ $row->code }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->start_time }}</td>
                                            <td>{{ $row->end_time }}</td>
                                            <td>{{ ucfirst($row->attendance_policy) ?: '—' }}</td>
                                            <td>{{ $row->grace_period }} min</td>
                                            <td>
                                                @if($row->is_active)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('attendance-master.edit', $row->id) }}" class="btn btn-sm btn-light text-dark">
                                                    <i class="ico icon-outline-pen-2 text-success"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function(){
    $("#btnSubmit").click(function(){
        setTimeout(function(){ $("#btnSubmit").prop('disabled', true); }, 0);
    });
});
</script>
@endsection
